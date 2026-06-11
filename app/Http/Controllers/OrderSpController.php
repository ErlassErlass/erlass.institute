<?php

namespace App\Http\Controllers;

use App\Models\OrderSp;
use App\Models\OrderItem;
use App\Models\Salesman;
use App\Models\Sekolah;
use App\Models\Product;
use App\Imports\OrderSpImport;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use Maatwebsite\Excel\Facades\Excel;
use Exception;

class OrderSpController extends Controller
{
    /**
     * Display a listing of SP orders.
     * 
     * @param Request $request
     * @return \Illuminate\View\View
     */
    public function index(Request $request)
    {
        $search = $request->input('search');
        $status = $request->input('status');

        $query = OrderSp::with(['sekolah', 'salesman', 'items.product']);

        // Role-based filtering for sales
        if (auth()->user()->role === 'sales') {
            $salesman = Salesman::where('user_id', auth()->id())->first();
            $salesmanId = $salesman ? $salesman->id : 0;
            $query->where('salesman_id', $salesmanId);
        }

        if ($search) {
            $query->where(function ($q) use ($search) {
                $q->where('nomor_sp', 'like', "%$search%")
                  ->orWhereHas('sekolah', function ($qSchool) use ($search) {
                      $qSchool->where('namasekolah', 'like', "%$search%");
                  })
                  ->orWhereHas('salesman', function ($qSales) use ($search) {
                      $qSales->where('nama_salesman', 'like', "%$search%");
                  });
            });
        }

        if ($status) {
            $query->where('status', $status);
        }

        $orders = $query->orderBy('tanggal_sp', 'desc')->paginate(25);

        return view('orders_sp.index', compact('orders'));
    }

    /**
     * Show the form for creating a new SP order.
     * 
     * @return \Illuminate\View\View
     */
    public function create()
    {
        $sekolah = Sekolah::orderBy('namasekolah')->get();
        $products = Product::orderBy('nama_produk')->get();
        
        if (auth()->user()->role === 'sales') {
            $salesman = Salesman::where('user_id', auth()->id())->first();
            $salesmen = $salesman ? collect([$salesman]) : collect();
        } else {
            $salesmen = Salesman::orderBy('nama_salesman')->get();
        }

        return view('orders_sp.create', compact('sekolah', 'products', 'salesmen'));
    }

    /**
     * Store a newly created SP order in storage.
     * 
     * @param Request $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'nomor_sp' => 'required|string|unique:orders_sp,nomor_sp',
            'tanggal_sp' => 'required|date',
            'sekolah_kodlan' => 'required|exists:sekolah,kodlan',
            'salesman_id' => 'required|exists:salesmen,id',
            'jumlah_peserta_estimasi' => 'required|integer|min:0',
            'jenis_kegiatan' => 'required|in:eskul,inkul',
            'lokasi_pembelajaran' => 'required|string',
            'tanggal_mulai_rencana' => 'required|date',
            'jumlah_pertemuan' => 'required|integer|min:1',
            'catatan_khusus' => 'nullable|string',
            'products' => 'required|array|min:1',
            'products.*.product_id' => 'required|exists:products,id',
            'products.*.harga_satuan' => 'required|numeric|min:0',
        ]);

        // Security check for sales role
        if (auth()->user()->role === 'sales') {
            $salesman = Salesman::where('user_id', auth()->id())->first();
            if (!$salesman || $salesman->id != $request->input('salesman_id')) {
                abort(403, 'Akses ditolak. Sales hanya dapat membuat SP atas namanya sendiri.');
            }
        }

        try {
            DB::transaction(function () use ($validated) {
                $orderSp = OrderSp::create([
                    'nomor_sp' => $validated['nomor_sp'],
                    'tanggal_sp' => $validated['tanggal_sp'],
                    'sekolah_kodlan' => $validated['sekolah_kodlan'],
                    'salesman_id' => $validated['salesman_id'],
                    'jumlah_peserta_estimasi' => $validated['jumlah_peserta_estimasi'],
                    'jenis_kegiatan' => $validated['jenis_kegiatan'],
                    'lokasi_pembelajaran' => $validated['lokasi_pembelajaran'],
                    'tanggal_mulai_rencana' => $validated['tanggal_mulai_rencana'],
                    'jumlah_pertemuan' => $validated['jumlah_pertemuan'],
                    'catatan_khusus' => $validated['catatan_khusus'],
                    'status' => 'draft',
                    'created_by' => auth()->id(),
                    'updated_by' => auth()->id(),
                ]);

                foreach ($validated['products'] as $item) {
                    OrderItem::create([
                        'order_sp_id' => $orderSp->id,
                        'product_id' => $item['product_id'],
                        'harga_satuan' => $item['harga_satuan'],
                    ]);
                }
            });

            return redirect()->route('orders-sp.index')->with('success', 'Surat Pesanan (SP) berhasil dibuat!');
        } catch (Exception $e) {
            return redirect()->back()->withInput()->with('error', 'Terjadi kesalahan: ' . $e->getMessage());
        }
    }

    /**
     * Display the specified SP order.
     * 
     * @param OrderSp $ordersSp
     * @return \Illuminate\View\View
     */
    public function show(OrderSp $ordersSp)
    {
        $ordersSp->load(['sekolah', 'salesman', 'items.product', 'creator', 'updater']);
        
        // Security check for sales role
        if (auth()->user()->role === 'sales') {
            $salesman = Salesman::where('user_id', auth()->id())->first();
            if (!$salesman || $salesman->id != $ordersSp->salesman_id) {
                abort(403, 'Akses ditolak.');
            }
        }

        return view('orders_sp.show', compact('ordersSp'));
    }

    /**
     * Show the form for editing the specified SP order.
     * 
     * @param OrderSp $ordersSp
     * @return \Illuminate\View\View
     */
    public function edit(OrderSp $ordersSp)
    {
        if ($ordersSp->status !== 'draft') {
            return redirect()->route('orders-sp.show', $ordersSp->id)->with('error', 'Hanya SP berstatus Draft yang dapat diubah.');
        }

        // Security check for sales role
        if (auth()->user()->role === 'sales') {
            $salesman = Salesman::where('user_id', auth()->id())->first();
            if (!$salesman || $salesman->id != $ordersSp->salesman_id) {
                abort(403, 'Akses ditolak.');
            }
        }

        $sekolah = Sekolah::orderBy('namasekolah')->get();
        $products = Product::orderBy('nama_produk')->get();
        
        if (auth()->user()->role === 'sales') {
            $salesman = Salesman::where('user_id', auth()->id())->first();
            $salesmen = $salesman ? collect([$salesman]) : collect();
        } else {
            $salesmen = Salesman::orderBy('nama_salesman')->get();
        }

        $ordersSp->load('items');

        return view('orders_sp.edit', compact('ordersSp', 'sekolah', 'products', 'salesmen'));
    }

    /**
     * Update the specified SP order in storage.
     * 
     * @param Request $request
     * @param OrderSp $ordersSp
     * @return \Illuminate\Http\RedirectResponse
     */
    public function update(Request $request, OrderSp $ordersSp)
    {
        if ($ordersSp->status !== 'draft') {
            return redirect()->route('orders-sp.show', $ordersSp->id)->with('error', 'Hanya SP berstatus Draft yang dapat diubah.');
        }

        // Security check for sales role
        if (auth()->user()->role === 'sales') {
            $salesman = Salesman::where('user_id', auth()->id())->first();
            if (!$salesman || $salesman->id != $ordersSp->salesman_id) {
                abort(403, 'Akses ditolak.');
            }
        }

        $validated = $request->validate([
            'nomor_sp' => 'required|string|unique:orders_sp,nomor_sp,' . $ordersSp->id,
            'tanggal_sp' => 'required|date',
            'sekolah_kodlan' => 'required|exists:sekolah,kodlan',
            'salesman_id' => 'required|exists:salesmen,id',
            'jumlah_peserta_estimasi' => 'required|integer|min:0',
            'jenis_kegiatan' => 'required|in:eskul,inkul',
            'lokasi_pembelajaran' => 'required|string',
            'tanggal_mulai_rencana' => 'required|date',
            'jumlah_pertemuan' => 'required|integer|min:1',
            'catatan_khusus' => 'nullable|string',
            'products' => 'required|array|min:1',
            'products.*.product_id' => 'required|exists:products,id',
            'products.*.harga_satuan' => 'required|numeric|min:0',
        ]);

        try {
            DB::transaction(function () use ($ordersSp, $validated) {
                $ordersSp->update([
                    'nomor_sp' => $validated['nomor_sp'],
                    'tanggal_sp' => $validated['tanggal_sp'],
                    'sekolah_kodlan' => $validated['sekolah_kodlan'],
                    'salesman_id' => $validated['salesman_id'],
                    'jumlah_peserta_estimasi' => $validated['jumlah_peserta_estimasi'],
                    'jenis_kegiatan' => $validated['jenis_kegiatan'],
                    'lokasi_pembelajaran' => $validated['lokasi_pembelajaran'],
                    'tanggal_mulai_rencana' => $validated['tanggal_mulai_rencana'],
                    'jumlah_pertemuan' => $validated['jumlah_pertemuan'],
                    'catatan_khusus' => $validated['catatan_khusus'],
                    'updated_by' => auth()->id(),
                ]);

                // Sync products
                $ordersSp->items()->delete();
                foreach ($validated['products'] as $item) {
                    OrderItem::create([
                        'order_sp_id' => $ordersSp->id,
                        'product_id' => $item['product_id'],
                        'harga_satuan' => $item['harga_satuan'],
                    ]);
                }
            });

            return redirect()->route('orders-sp.show', $ordersSp->id)->with('success', 'Surat Pesanan (SP) berhasil diperbarui!');
        } catch (Exception $e) {
            return redirect()->back()->withInput()->with('error', 'Terjadi kesalahan: ' . $e->getMessage());
        }
    }

    /**
     * Remove the specified SP order from storage.
     * 
     * @param OrderSp $ordersSp
     * @return \Illuminate\Http\RedirectResponse
     */
    public function destroy(OrderSp $ordersSp)
    {
        if ($ordersSp->status !== 'draft') {
            return redirect()->route('orders-sp.show', $ordersSp->id)->with('error', 'Hanya SP berstatus Draft yang dapat dihapus.');
        }

        // Security check for sales role
        if (auth()->user()->role === 'sales') {
            $salesman = Salesman::where('user_id', auth()->id())->first();
            if (!$salesman || $salesman->id != $ordersSp->salesman_id) {
                abort(403, 'Akses ditolak.');
            }
        }

        $ordersSp->delete();

        return redirect()->route('orders-sp.index')->with('success', 'Surat Pesanan (SP) berhasil dihapus.');
    }

    /**
     * Submit SP order for academic validation.
     * 
     * @param OrderSp $ordersSp
     * @return \Illuminate\Http\RedirectResponse
     */
    public function submit(OrderSp $ordersSp)
    {
        if ($ordersSp->status !== 'draft') {
            return redirect()->route('orders-sp.show', $ordersSp->id)->with('error', 'Hanya SP berstatus Draft yang dapat diajukan.');
        }

        // Security check for sales role
        if (auth()->user()->role === 'sales') {
            $salesman = Salesman::where('user_id', auth()->id())->first();
            if (!$salesman || $salesman->id != $ordersSp->salesman_id) {
                abort(403, 'Akses ditolak.');
            }
        }

        $ordersSp->update([
            'status' => 'menunggu_validasi',
            'updated_by' => auth()->id(),
        ]);

        return redirect()->route('orders-sp.show', $ordersSp->id)->with('success', 'Surat Pesanan (SP) berhasil diajukan untuk validasi akademik!');
    }

    /**
     * Import SP orders from Excel file.
     * 
     * @param Request $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function import(Request $request)
    {
        $request->validate([
            'file' => 'required|mimes:xlsx,xls,csv|max:5120',
        ]);

        try {
            Excel::import(new OrderSpImport, $request->file('file'));
            return redirect()->route('orders-sp.index')->with('success', 'Data Surat Pesanan (SP) berhasil diimpor!');
        } catch (Exception $e) {
            return redirect()->route('orders-sp.index')->with('error', 'Terjadi kesalahan saat mengimpor: ' . $e->getMessage());
        }
    }
}
