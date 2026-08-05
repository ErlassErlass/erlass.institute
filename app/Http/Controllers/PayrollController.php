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
        if (!auth()->user()->isPrimaryAdmin()) {
            abort(403, 'Akses ditolak. Pengelolaan dan pembuatan Batch Payroll hanya dapat dilakukan oleh Admin Utama (Adinda Wardania).');
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
        if (!auth()->user()->isPrimaryAdmin()) {
            abort(403, 'Akses ditolak. Verifikasi Batch Payroll hanya dapat dilakukan oleh Admin Utama (Adinda Wardania).');
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
        if (!auth()->user()->isPrimaryAdmin()) {
            abort(403, 'Akses ditolak. Pencairan Batch Payroll hanya dapat dilakukan oleh Admin Utama (Adinda Wardania).');
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
     * Delete a payroll batch draft (Admin).
     */
    public function destroyBatch($id)
    {
        if (!auth()->user()->isPrimaryAdmin()) {
            abort(403, 'Akses ditolak. Penghapusan Batch Payroll hanya dapat dilakukan oleh Admin Utama (Adinda Wardania).');
        }

        $batch = PayrollBatch::findOrFail($id);

        if ($batch->status !== 'draft') {
            return redirect()->route('admin.payroll.batches.index')
                ->with('error', 'Hanya batch berstatus Draft yang dapat dihapus.');
        }

        DB::transaction(function () use ($batch) {
            // Find items
            $itemIds = PayrollItem::where('payroll_batch_id', $batch->id)->pluck('id');

            // Reset associated sessions to unpaid
            EkstrakurikulerSession::whereIn('payroll_item_id', $itemIds)
                ->orWhere(function ($q) use ($batch) {
                    $period = Carbon::parse($batch->periode);
                    $q->whereMonth('tanggal_pelaksanaan', $period->month)
                      ->whereYear('tanggal_pelaksanaan', $period->year)
                      ->where('payment_status', 'processing');
                })
                ->update([
                    'payment_status' => 'unpaid',
                    'payroll_item_id' => null,
                ]);

            // Delete payroll items
            PayrollItem::where('payroll_batch_id', $batch->id)->delete();

            // Delete batch
            $batch->delete();
        });

        return redirect()->route('admin.payroll.batches.index')
            ->with('success', "Batch payroll {$batch->code} berhasil dihapus.");
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

        $user = auth()->user();
        $isOwner = $item->user_id_instruktur === $user->id;
        $isAdmin = in_array($user->role, ['webmaster', 'admin_sistem', 'admin']);

        if (!$isOwner && !$isAdmin) {
            abort(403, 'Akses ditolak.');
        }

        return view('payroll.slip_detail', compact('item'));
    }

    /**
     * Export Payroll Batch to Excel (.xlsx) with 3 Worksheets.
     */
    public function exportExcel($id)
    {
        if (!in_array(auth()->user()->role, ['webmaster', 'admin_sistem', 'admin'])) {
            abort(403, 'Akses ditolak.');
        }

        $batch = PayrollBatch::with([
            'items.instruktur.instructorProfile',
            'items.sessions.ekstrakurikuler.sekolah',
            'items.sessions.rombel',
            'payer'
        ])->findOrFail($id);

        $spreadsheet = new \PhpOffice\PhpSpreadsheet\Spreadsheet();

        // -------------------------------------------------------------
        // SHEET 1: REKAP TRANSFER BANK
        // -------------------------------------------------------------
        $sheet1 = $spreadsheet->getActiveSheet();
        $sheet1->setTitle('Transfer Bank');
        
        $sheet1->setCellValue('A1', 'REKAPITULASI TRANSFER BANK PAYROLL INSTRUKTUR ERLASS');
        $sheet1->mergeCells('A1:J1');
        $sheet1->getStyle('A1')->getFont()->setBold(true)->setSize(14);

        $sheet1->setCellValue('A2', "Kode Batch: {$batch->code} | Periode: " . $batch->periode->format('F Y') . " | Status: " . strtoupper($batch->status));
        $sheet1->mergeCells('A2:J2');
        $sheet1->getStyle('A2')->getFont()->setItalic(true)->setSize(11);

        $headers1 = ['No', 'ID Instruktur', 'Nama Lengkap Instruktur', 'Nama Bank', 'Nomor Rekening', 'Nama Pemilik Rekening', 'No HP / WA', 'Total Sesi', 'Nominal Netto (Rp)', 'Keterangan'];
        $sheet1->fromArray($headers1, NULL, 'A4');
        $sheet1->getStyle('A4:J4')->getFont()->setBold(true);
        $sheet1->getStyle('A4:J4')->getFill()->setFillType(\PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID)->getStartColor()->setRGB('4CAF50');
        $sheet1->getStyle('A4:J4')->getFont()->getColor()->setRGB('FFFFFF');

        $rowIdx = 5;
        $no = 1;
        $totalSessionsAll = 0;
        $totalNetSalaryAll = 0;

        foreach ($batch->items as $item) {
            $instructor = $item->instruktur;
            $profile = $instructor->instructorProfile ?? null;

            $sheet1->setCellValue("A{$rowIdx}", $no++);
            $sheet1->setCellValue("B{$rowIdx}", $instructor->instructor_id ?? $instructor->id);
            $sheet1->setCellValue("C{$rowIdx}", $instructor->nama_lengkap);
            $sheet1->setCellValue("D{$rowIdx}", $profile->nama_bank ?? '-');
            $sheet1->setCellValueExplicit("E{$rowIdx}", $profile->no_rekening ?? '-', \PhpOffice\PhpSpreadsheet\Cell\DataType::TYPE_STRING);
            $sheet1->setCellValue("F{$rowIdx}", $profile->nama_pemilik_rekening ?? $instructor->nama_lengkap);
            $sheet1->setCellValueExplicit("G{$rowIdx}", $instructor->no_telephone ?? '-', \PhpOffice\PhpSpreadsheet\Cell\DataType::TYPE_STRING);
            $sheet1->setCellValue("H{$rowIdx}", $item->total_sessions);
            $sheet1->setCellValue("I{$rowIdx}", $item->net_salary);
            $sheet1->setCellValue("J{$rowIdx}", "Honor " . $batch->code);

            $totalSessionsAll += $item->total_sessions;
            $totalNetSalaryAll += $item->net_salary;
            $rowIdx++;
        }

        $sheet1->setCellValue("A{$rowIdx}", 'TOTAL');
        $sheet1->mergeCells("A{$rowIdx}:G{$rowIdx}");
        $sheet1->setCellValue("H{$rowIdx}", $totalSessionsAll);
        $sheet1->setCellValue("I{$rowIdx}", $totalNetSalaryAll);
        $sheet1->getStyle("A{$rowIdx}:J{$rowIdx}")->getFont()->setBold(true);
        $sheet1->getStyle("I5:I{$rowIdx}")->getNumberFormat()->setFormatCode('#,##0');

        foreach (range('A', 'J') as $col) {
            $sheet1->getColumnDimension($col)->setAutoSize(true);
        }

        // -------------------------------------------------------------
        // SHEET 2: JURNAL AKUNTANSI
        // -------------------------------------------------------------
        $sheet2 = $spreadsheet->createSheet();
        $sheet2->setTitle('Jurnal Akuntansi');

        $sheet2->setCellValue('A1', 'RINCIAN JURNAL AKUNTANSI PAYROLL INSTRUKTUR ERLASS');
        $sheet2->mergeCells('A1:L1');
        $sheet2->getStyle('A1')->getFont()->setBold(true)->setSize(14);

        $sheet2->setCellValue('A2', "Kode Batch: {$batch->code} | Periode: " . $batch->periode->format('F Y'));
        $sheet2->mergeCells('A2:L2');
        $sheet2->getStyle('A2')->getFont()->setItalic(true);

        $headers2 = ['No', 'Kode Batch', 'Periode', 'ID Instruktur', 'Nama Instruktur', 'Total Sesi', 'Honor Dasar (Rp)', 'Bonus (Rp)', 'Transport (Rp)', 'Denda (Rp)', 'Gaji Netto (Rp)', 'Status'];
        $sheet2->fromArray($headers2, NULL, 'A4');
        $sheet2->getStyle('A4:L4')->getFont()->setBold(true);
        $sheet2->getStyle('A4:L4')->getFill()->setFillType(\PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID)->getStartColor()->setRGB('2196F3');
        $sheet2->getStyle('A4:L4')->getFont()->getColor()->setRGB('FFFFFF');

        $rowIdx2 = 5;
        $no2 = 1;

        foreach ($batch->items as $item) {
            $instructor = $item->instruktur;

            $sheet2->setCellValue("A{$rowIdx2}", $no2++);
            $sheet2->setCellValue("B{$rowIdx2}", $batch->code);
            $sheet2->setCellValue("C{$rowIdx2}", $batch->periode->format('Y-m'));
            $sheet2->setCellValue("D{$rowIdx2}", $instructor->instructor_id ?? $instructor->id);
            $sheet2->setCellValue("E{$rowIdx2}", $instructor->nama_lengkap);
            $sheet2->setCellValue("F{$rowIdx2}", $item->total_sessions);
            $sheet2->setCellValue("G{$rowIdx2}", $item->total_base_fee);
            $sheet2->setCellValue("H{$rowIdx2}", $item->total_product_bonus);
            $sheet2->setCellValue("I{$rowIdx2}", $item->total_transport_fee);
            $sheet2->setCellValue("J{$rowIdx2}", $item->total_penalty);
            $sheet2->setCellValue("K{$rowIdx2}", $item->net_salary);
            $sheet2->setCellValue("L{$rowIdx2}", strtoupper($batch->status));

            $rowIdx2++;
        }

        $sheet2->setCellValue("A{$rowIdx2}", 'TOTAL');
        $sheet2->mergeCells("A{$rowIdx2}:E{$rowIdx2}");
        $sheet2->setCellValue("F{$rowIdx2}", "=SUM(F5:F" . ($rowIdx2-1) . ")");
        $sheet2->setCellValue("G{$rowIdx2}", "=SUM(G5:G" . ($rowIdx2-1) . ")");
        $sheet2->setCellValue("H{$rowIdx2}", "=SUM(H5:H" . ($rowIdx2-1) . ")");
        $sheet2->setCellValue("I{$rowIdx2}", "=SUM(I5:I" . ($rowIdx2-1) . ")");
        $sheet2->setCellValue("J{$rowIdx2}", "=SUM(J5:J" . ($rowIdx2-1) . ")");
        $sheet2->setCellValue("K{$rowIdx2}", "=SUM(K5:K" . ($rowIdx2-1) . ")");
        $sheet2->getStyle("A{$rowIdx2}:L{$rowIdx2}")->getFont()->setBold(true);
        $sheet2->getStyle("G5:K{$rowIdx2}")->getNumberFormat()->setFormatCode('#,##0');

        foreach (range('A', 'L') as $col) {
            $sheet2->getColumnDimension($col)->setAutoSize(true);
        }

        // -------------------------------------------------------------
        // SHEET 3: RINCIAN PER SESI MENGAJAR
        // -------------------------------------------------------------
        $sheet3 = $spreadsheet->createSheet();
        $sheet3->setTitle('Rincian Sesi Mengajar');

        $sheet3->setCellValue('A1', 'AUDIT RINCIAN PER SESI MENGAJAR PAYROLL ERLASS');
        $sheet3->mergeCells('A1:L1');
        $sheet3->getStyle('A1')->getFont()->setBold(true)->setSize(14);

        $sheet3->setCellValue('A2', "Kode Batch: {$batch->code}");
        $sheet3->mergeCells('A2:L2');

        $headers3 = ['No', 'ID Sesi', 'Tanggal Sesi', 'Sekolah Mitra', 'Program / Rombel', 'ID Instruktur', 'Nama Instruktur', 'Honor Dasar (Rp)', 'Transport (Rp)', 'Denda Checkin (Rp)', 'Net Fee Sesi (Rp)', 'Status Sesi'];
        $sheet3->fromArray($headers3, NULL, 'A4');
        $sheet3->getStyle('A4:L4')->getFont()->setBold(true);
        $sheet3->getStyle('A4:L4')->getFill()->setFillType(\PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID)->getStartColor()->setRGB('FF9800');
        $sheet3->getStyle('A4:L4')->getFont()->getColor()->setRGB('FFFFFF');

        $rowIdx3 = 5;
        $no3 = 1;
        $calculator = app(\App\Services\PayrollCalculatorService::class);

        foreach ($batch->items as $item) {
            foreach ($item->sessions as $session) {
                $calc = $calculator->calculateSessionFee($session);
                $baseFee = $session->override_fee !== null ? (float)$session->override_fee : (float)$calc['calculated_fee'];
                $netFee = max(0, $baseFee + $calc['transport_fee'] - $calc['actual_checkin_penalty']);

                $sekolahName = optional(optional(optional($session->rombel)->ekstrakurikuler)->sekolah)->namasekolah ?? 'Kegiatan Office / Ad-Hoc';
                $programName = optional(optional($session->rombel)->ekstrakurikuler)->kategori_program ?? 'Ad-Hoc';

                $sheet3->setCellValue("A{$rowIdx3}", $no3++);
                $sheet3->setCellValue("B{$rowIdx3}", $session->id);
                $sheet3->setCellValue("C{$rowIdx3}", \Carbon\Carbon::parse($session->tanggal_pelaksanaan ?? $session->tanggal_terjadwal)->format('d/m/Y'));
                $sheet3->setCellValue("D{$rowIdx3}", $sekolahName);
                $sheet3->setCellValue("E{$rowIdx3}", $programName . ' (' . optional($session->rombel)->nama_rombel . ')');
                $sheet3->setCellValue("F{$rowIdx3}", $item->instruktur->instructor_id ?? $item->instruktur->id);
                $sheet3->setCellValue("G{$rowIdx3}", $item->instruktur->nama_lengkap);
                $sheet3->setCellValue("H{$rowIdx3}", $baseFee);
                $sheet3->setCellValue("I{$rowIdx3}", $calc['transport_fee']);
                $sheet3->setCellValue("J{$rowIdx3}", $calc['actual_checkin_penalty']);
                $sheet3->setCellValue("K{$rowIdx3}", $netFee);
                $sheet3->setCellValue("L{$rowIdx3}", strtoupper($session->status));

                $rowIdx3++;
            }
        }

        $sheet3->setCellValue("A{$rowIdx3}", 'TOTAL');
        $sheet3->mergeCells("A{$rowIdx3}:G{$rowIdx3}");
        $sheet3->setCellValue("H{$rowIdx3}", "=SUM(H5:H" . ($rowIdx3-1) . ")");
        $sheet3->setCellValue("I{$rowIdx3}", "=SUM(I5:I" . ($rowIdx3-1) . ")");
        $sheet3->setCellValue("J{$rowIdx3}", "=SUM(J5:J" . ($rowIdx3-1) . ")");
        $sheet3->setCellValue("K{$rowIdx3}", "=SUM(K5:K" . ($rowIdx3-1) . ")");
        $sheet3->getStyle("A{$rowIdx3}:L{$rowIdx3}")->getFont()->setBold(true);
        $sheet3->getStyle("H5:K{$rowIdx3}")->getNumberFormat()->setFormatCode('#,##0');

        foreach (range('A', 'L') as $col) {
            $sheet3->getColumnDimension($col)->setAutoSize(true);
        }

        $spreadsheet->setActiveSheetIndex(0);

        $filename = "Payroll_Batch_{$batch->code}_" . date('Ymd_His') . ".xlsx";

        header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
        header("Content-Disposition: attachment; filename=\"{$filename}\"");
        header('Cache-Control: max-age=0');

        $writer = new \PhpOffice\PhpSpreadsheet\Writer\Xlsx($spreadsheet);
        $writer->save('php://output');
        exit;
    }

    /**
     * Export Payroll Batch to CSV (.csv) for Bank Mass Transfer.
     */
    public function exportCsv($id)
    {
        if (!in_array(auth()->user()->role, ['webmaster', 'admin_sistem', 'admin'])) {
            abort(403, 'Akses ditolak.');
        }

        $batch = PayrollBatch::with(['items.instruktur.instructorProfile'])->findOrFail($id);

        $filename = "Transfer_Bank_{$batch->code}_" . date('Ymd_His') . ".csv";

        header('Content-Type: text/csv; charset=utf-8');
        header("Content-Disposition: attachment; filename=\"{$filename}\"");

        $output = fopen('php://output', 'w');

        // Headers
        fputcsv($output, ['No', 'ID Instruktur', 'Nama Lengkap', 'Nama Bank', 'Nomor Rekening', 'Pemilik Rekening', 'No HP', 'Total Sesi', 'Nominal Netto (Rp)', 'Keterangan']);

        $no = 1;
        foreach ($batch->items as $item) {
            $instructor = $item->instruktur;
            $profile = $instructor->instructorProfile ?? null;

            fputcsv($output, [
                $no++,
                $instructor->instructor_id ?? $instructor->id,
                $instructor->nama_lengkap,
                $profile->nama_bank ?? '-',
                "'" . ($profile->no_rekening ?? '-'),
                $profile->nama_pemilik_rekening ?? $instructor->nama_lengkap,
                "'" . ($instructor->no_telephone ?? '-'),
                $item->total_sessions,
                $item->net_salary,
                "Honor Batch " . $batch->code,
            ]);
        }

        fclose($output);
        exit;
    }

    /**
     * Export / Print Payroll Batch PDF View.
     */
    public function exportPdf($id)
    {
        if (!in_array(auth()->user()->role, ['webmaster', 'admin_sistem', 'admin'])) {
            abort(403, 'Akses ditolak.');
        }

        $batch = PayrollBatch::with([
            'items.instruktur.instructorProfile',
            'items.sessions.ekstrakurikuler.sekolah',
            'items.sessions.rombel',
            'payer'
        ])->findOrFail($id);

        return view('payroll.export_pdf', compact('batch'));
    }
}
