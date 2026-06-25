<?php

namespace App\Http\Controllers;

use App\Models\Product;
use Illuminate\Http\Request;

class ProductController extends Controller
{
    /**
     * Display a listing of the products.
     * 
     * @param Request $request
     * @return \Illuminate\View\View
     */
    public function index(Request $request)
    {
        if (!in_array(auth()->user()->role, ['webmaster', 'admin_sistem', 'admin'])) {
            abort(403, 'Akses ditolak.');
        }

        $search = $request->input('search');
        $filterStatus = $request->input('filter_status'); // 'aktif', 'nonaktif', or null (all)

        $products = Product::when($search, function ($query) use ($search) {
                return $query->where('nama_produk', 'like', "%$search%")
                    ->orWhere('kode_produk', 'like', "%$search%")
                    ->orWhere('jenis', 'like', "%$search%");
            })
            ->when($filterStatus === 'aktif', fn ($q) => $q->where('is_aktif', true))
            ->when($filterStatus === 'nonaktif', fn ($q) => $q->where('is_aktif', false))
            ->paginate(25);

        return view('products.index', compact('products', 'filterStatus'));
    }

    /**
     * Show the form for creating a new product.
     * 
     * @return \Illuminate\View\View
     */
    public function create()
    {
        if (!in_array(auth()->user()->role, ['webmaster', 'admin_sistem', 'admin'])) {
            abort(403, 'Akses ditolak.');
        }
        return view('products.create');
    }

    /**
     * Store a newly created product in storage.
     * 
     * @param Request $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function store(Request $request)
    {
        if (!in_array(auth()->user()->role, ['webmaster', 'admin_sistem', 'admin'])) {
            abort(403, 'Akses ditolak.');
        }

        $validated = $request->validate([
            'kode_produk' => 'required|string|unique:products,kode_produk',
            'nama_produk' => 'required|string',
            'jenis' => 'required|string',
            'harga' => 'required|numeric|min:0',
            'durasi_bulan' => 'nullable|integer|min:1',
            'jenis_kegiatan' => 'required|in:eskul,inkul',
            'standar_durasi_menit' => 'required|integer|min:1',
            'tanggal' => 'nullable|date',
            'is_aktif' => 'sometimes|boolean',
        ]);

        $validated['is_aktif'] = $request->boolean('is_aktif', true);

        Product::create($validated);

        return redirect()->route('products.index')->with('success', 'Produk berhasil ditambahkan!');
    }

    /**
     * Show the form for editing the specified product.
     * 
     * @param Product $product
     * @return \Illuminate\View\View
     */
    public function edit(Product $product)
    {
        if (!in_array(auth()->user()->role, ['webmaster', 'admin_sistem', 'admin'])) {
            abort(403, 'Akses ditolak.');
        }
        return view('products.edit', compact('product'));
    }

    /**
     * Update the specified product in storage.
     * 
     * @param Request $request
     * @param Product $product
     * @return \Illuminate\Http\RedirectResponse
     */
    public function update(Request $request, Product $product)
    {
        if (!in_array(auth()->user()->role, ['webmaster', 'admin_sistem', 'admin'])) {
            abort(403, 'Akses ditolak.');
        }

        $validated = $request->validate([
            'kode_produk' => 'required|string|unique:products,kode_produk,' . $product->id,
            'nama_produk' => 'required|string',
            'jenis' => 'required|string',
            'harga' => 'required|numeric|min:0',
            'durasi_bulan' => 'nullable|integer|min:1',
            'jenis_kegiatan' => 'required|in:eskul,inkul',
            'standar_durasi_menit' => 'required|integer|min:1',
            'tanggal' => 'nullable|date',
            'is_aktif' => 'sometimes|boolean',
        ]);

        $validated['is_aktif'] = $request->boolean('is_aktif');

        $product->update($validated);

        return redirect()->route('products.index')->with('success', 'Produk berhasil diperbarui!');
    }

    /**
     * Remove the specified product from storage.
     * 
     * @param Product $product
     * @return \Illuminate\Http\RedirectResponse
     */
    public function destroy(Product $product)
    {
        if (!in_array(auth()->user()->role, ['webmaster', 'admin_sistem', 'admin'])) {
            abort(403, 'Akses ditolak.');
        }

        if ($product->orderItems()->exists()) {
            return redirect()->route('products.index')->with('error', 'Produk tidak dapat dihapus karena sudah digunakan dalam SP.');
        }

        $product->delete();

        return redirect()->route('products.index')->with('success', 'Produk berhasil dihapus!');
    }

    /**
     * Toggle status aktif/nonaktif produk.
     *
     * @param Product $product
     * @return \Illuminate\Http\RedirectResponse
     */
    public function toggleAktif(Product $product)
    {
        if (!in_array(auth()->user()->role, ['webmaster', 'admin_sistem', 'admin'])) {
            abort(403, 'Akses ditolak.');
        }

        $product->update(['is_aktif' => !$product->is_aktif]);

        $statusLabel = $product->is_aktif ? 'diaktifkan' : 'dinonaktifkan';
        return redirect()->back()->with('success', "Produk '{$product->nama_produk}' berhasil {$statusLabel}.");
    }
}
