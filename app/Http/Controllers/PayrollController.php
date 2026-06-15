<?php

namespace App\Http\Controllers;

use App\Models\PayrollBatch;
use App\Models\PayrollItem;
use App\Models\EkstrakurikulerSession;
use App\Services\PayrollCalculatorService;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class PayrollController extends Controller
{
    protected $calculator;

    public function __construct(PayrollCalculatorService $calculator)
    {
        $this->calculator = $calculator;
    }

    /**
     * Display a listing of payroll batches (Admin).
     */
    public function index(Request $request)
    {
        if (!in_array(auth()->user()->role, ['webmaster', 'admin_sistem', 'admin'])) {
            abort(403, 'Akses ditolak.');
        }

        $batches = PayrollBatch::orderBy('periode', 'desc')->paginate(15);

        // Calculate some statistics for dashboard summary
        $totalProcessed = PayrollBatch::where('status', 'processed')->count();
        $totalPaid = PayrollBatch::where('status', 'paid')->count();
        
        // Quick estimate of pending unpaid sessions count
        $unpaidSessionsCount = EkstrakurikulerSession::where('payment_status', 'unpaid')
            ->where('status', EkstrakurikulerSession::STATUS_SELESAI)
            ->whereHas('laporanMengajar')
            ->count();

        return view('payroll.index', compact('batches', 'totalProcessed', 'totalPaid', 'unpaidSessionsCount'));
    }

    /**
     * Create a new payroll batch draft (Admin).
     */
    public function storeBatch(Request $request)
    {
        if (!in_array(auth()->user()->role, ['webmaster', 'admin_sistem', 'admin'])) {
            abort(403, 'Akses ditolak.');
        }

        $request->validate([
            'month' => 'required|date_format:Y-m',
            'notes' => 'nullable|string',
        ]);

        $periodeStr = $request->input('month') . '-01';
        $periode = Carbon::parse($periodeStr);
        $code = 'PAY-' . $periode->format('Ym');

        // Check if batch already exists
        $existing = PayrollBatch::where('code', $code)->first();
        if ($existing) {
            return redirect()->route('admin.payroll.batches.index')->with('error', 'Batch payroll untuk periode ini sudah ada!');
        }

        return DB::transaction(function () use ($periode, $code, $request) {
            // Create batch
            $batch = PayrollBatch::create([
                'code' => $code,
                'periode' => $periode->toDateString(),
                'status' => 'draft',
                'notes' => $request->input('notes'),
            ]);

            // Compile payroll items
            $itemsCompiled = $this->calculator->generateMonthlyPayroll($batch);

            if ($itemsCompiled === 0) {
                // Rollback if no sessions found
                $batch->delete();
                return redirect()->route('admin.payroll.batches.index')->with('warning', 'Tidak ada sesi mengajar yang selesai dengan laporan lengkap di periode ini.');
            }

            return redirect()->route('admin.payroll.batches.show', $batch->id)
                ->with('success', "Batch payroll {$code} berhasil dibuat dengan {$itemsCompiled} instruktur!");
        });
    }

    /**
     * Show payroll batch details (Admin).
     */
    public function showBatch($id)
    {
        if (!in_array(auth()->user()->role, ['webmaster', 'admin_sistem', 'admin'])) {
            abort(403, 'Akses ditolak.');
        }

        $batch = PayrollBatch::with(['items.instruktur', 'processor', 'payer'])->findOrFail($id);

        return view('payroll.show', compact('batch'));
    }

    /**
     * Process / verify a payroll batch (Admin).
     */
    public function processBatch($id)
    {
        if (!in_array(auth()->user()->role, ['webmaster', 'admin_sistem', 'admin'])) {
            abort(403, 'Akses ditolak.');
        }

        $batch = PayrollBatch::findOrFail($id);

        if ($batch->status !== 'draft') {
            return redirect()->route('admin.payroll.batches.show', $batch->id)
                ->with('error', 'Hanya batch berstatus Draft yang dapat diproses.');
        }

        $batch->update([
            'status' => 'processed',
            'processed_at' => now(),
            'processed_by' => auth()->id(),
        ]);

        // Update items status
        PayrollItem::where('payroll_batch_id', $batch->id)->update(['status' => 'approved']);

        return redirect()->route('admin.payroll.batches.show', $batch->id)
            ->with('success', 'Batch payroll berhasil diverifikasi dan diproses.');
    }

    /**
     * Mark a payroll batch as fully paid (Admin).
     */
    public function payBatch($id)
    {
        if (!in_array(auth()->user()->role, ['webmaster', 'admin_sistem', 'admin'])) {
            abort(403, 'Akses ditolak.');
        }

        $batch = PayrollBatch::findOrFail($id);

        if ($batch->status !== 'processed') {
            return redirect()->route('admin.payroll.batches.show', $batch->id)
                ->with('error', 'Hanya batch berstatus Processed yang dapat dicairkan.');
        }

        DB::transaction(function () use ($batch) {
            $batch->update([
                'status' => 'paid',
                'paid_at' => now(),
                'paid_by' => auth()->id(),
            ]);

            // Mark payroll items as paid
            PayrollItem::where('payroll_batch_id', $batch->id)->update(['status' => 'paid']);

            // Update associated sessions payment_status to paid
            EkstrakurikulerSession::where('payroll_item_id', '!=', null)
                ->whereIn('payroll_item_id', function ($query) use ($batch) {
                    $query->select('id')->from('payroll_items')->where('payroll_batch_id', $batch->id);
                })
                ->update(['payment_status' => 'paid']);
        });

        return redirect()->route('admin.payroll.batches.show', $batch->id)
            ->with('success', 'Batch payroll berhasil dicairkan dan ditandai Lunas!');
    }

    /**
     * Display slip salaries / kompensasi portal (Instructor).
     */
    public function mySalaries(Request $request)
    {
        $user = auth()->user();
        if ($user->role !== 'instruktur' && !in_array($user->role, ['webmaster', 'admin_sistem', 'admin'])) {
            abort(403, 'Akses ditolak.');
        }

        // Fetch payroll items for this instructor
        $items = PayrollItem::with(['batch'])
            ->where('user_id_instruktur', $user->id)
            ->orderBy('created_at', 'desc')
            ->paginate(12);

        return view('payroll.my_salaries', compact('items'));
    }

    /**
     * View detailed slip salary modal/view for a single payroll item.
     */
    public function showSlip($id)
    {
        $item = PayrollItem::with(['batch', 'instruktur', 'sessions.ekstrakurikuler', 'sessions.rombel'])->findOrFail($id);

        // Ensure instructors can only view their own slips
        if (auth()->user()->role === 'instruktur' && $item->user_id_instruktur !== auth()->id()) {
            abort(403, 'Akses ditolak.');
        }

        return view('payroll.slip_detail', compact('item'));
    }
}
