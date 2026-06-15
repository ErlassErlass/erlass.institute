@extends('layouts.app')

@section('content')
<div class="container-fluid py-4">
    <div class="row justify-content-center">
        <div class="col-lg-10">
            <div class="card shadow-sm border-0">
                <div class="card-header bg-white py-3 border-bottom">
                    <div class="d-flex align-items-center gap-2">
                        <a href="{{ route('orders-sp.show', $ordersSp->id) }}" class="btn btn-sm btn-outline-secondary">
                            <i class="bi bi-arrow-left"></i>
                        </a>
                        <h5 class="card-title mb-0 fw-bold text-dark">Ubah Surat Pesanan (SP) - {{ $ordersSp->nomor_sp }}</h5>
                    </div>
                </div>
                <div class="card-body p-4">
                    @if (session('error'))
                        <div class="alert alert-danger alert-dismissible fade show mb-4" role="alert">
                            <i class="bi bi-exclamation-triangle-fill me-1"></i> {{ session('error') }}
                            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                        </div>
                    @endif

                    <form method="POST" action="{{ route('orders-sp.update', $ordersSp->id) }}" id="sp-form">
                        @csrf
                        @method('PUT')

                        <div class="row g-3 mb-4">
                            <!-- Left Column (Order Details) -->
                            <div class="col-md-6">
                                <h6 class="fw-bold text-dark border-bottom pb-2 mb-3">Informasi SP</h6>

                                <div class="mb-3">
                                    <label for="nomor_sp" class="form-label fw-semibold text-muted small">Nomor Surat Pesanan (SP)</label>
                                    <input 
                                        type="text" 
                                        class="form-control @error('nomor_sp') is-invalid @enderror" 
                                        id="nomor_sp" 
                                        name="nomor_sp" 
                                        value="{{ old('nomor_sp', $ordersSp->nomor_sp) }}" 
                                        required
                                    >
                                    @error('nomor_sp')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>

                                <div class="mb-3">
                                    <label for="tanggal_sp" class="form-label fw-semibold text-muted small">Tanggal SP</label>
                                    <input 
                                        type="date" 
                                        class="form-control @error('tanggal_sp') is-invalid @enderror" 
                                        id="tanggal_sp" 
                                        name="tanggal_sp" 
                                        value="{{ old('tanggal_sp', $ordersSp->tanggal_sp) }}" 
                                        required
                                    >
                                    @error('tanggal_sp')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>

                                <div class="mb-3">
                                    <label for="sekolah_kodlan" class="form-label fw-semibold text-muted small">Sekolah Pelanggan</label>
                                    <select 
                                        class="form-select @error('sekolah_kodlan') is-invalid @enderror" 
                                        id="sekolah_kodlan" 
                                        name="sekolah_kodlan" 
                                        required
                                    >
                                        <option value="">Ketik nama sekolah atau kode...</option>
                                        @php
                                            $selectedKodlan = old('sekolah_kodlan', $ordersSp->sekolah_kodlan);
                                            $selectedSekolah = \App\Models\Sekolah::where('kodlan', $selectedKodlan)->first();
                                        @endphp
                                        @if($selectedSekolah)
                                            <option value="{{ $selectedKodlan }}" selected>
                                                {{ $selectedSekolah->namasekolah }} ({{ $selectedKodlan }})
                                            </option>
                                        @endif
                                    </select>
                                    @error('sekolah_kodlan')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>

                                <div class="mb-3">
                                    <label for="salesman_id" class="form-label fw-semibold text-muted small">Salesman</label>
                                    <select 
                                        class="form-select @error('salesman_id') is-invalid @enderror" 
                                        id="salesman_id" 
                                        name="salesman_id" 
                                        required
                                    >
                                        @foreach($salesmen as $sm)
                                            <option value="{{ $sm->id }}" {{ old('salesman_id', $ordersSp->salesman_id) == $sm->id ? 'selected' : '' }}>
                                                {{ $sm->nama_salesman }} ({{ $sm->kode_salesman }})
                                            </option>
                                        @endforeach
                                    </select>
                                    @error('salesman_id')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>

                            <!-- Right Column (Operational Details) -->
                            <div class="col-md-6">
                                <h6 class="fw-bold text-dark border-bottom pb-2 mb-3">Rincian Operasional</h6>

                                <div class="mb-3">
                                    <label for="jumlah_peserta_estimasi" class="form-label fw-semibold text-muted small">Estimasi Jumlah Siswa/Peserta</label>
                                    <input 
                                        type="number" 
                                        class="form-control @error('jumlah_peserta_estimasi') is-invalid @enderror" 
                                        id="jumlah_peserta_estimasi" 
                                        name="jumlah_peserta_estimasi" 
                                        value="{{ old('jumlah_peserta_estimasi', $ordersSp->jumlah_peserta_estimasi) }}" 
                                        min="0"
                                        required
                                    >
                                    @error('jumlah_peserta_estimasi')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>

                                <div class="mb-3">
                                    <label for="jenis_kegiatan" class="form-label fw-semibold text-muted small">Jenis Kegiatan</label>
                                    <select 
                                        class="form-select @error('jenis_kegiatan') is-invalid @enderror" 
                                        id="jenis_kegiatan" 
                                        name="jenis_kegiatan" 
                                        required
                                    >
                                        <option value="eskul" {{ old('jenis_kegiatan', $ordersSp->jenis_kegiatan) === 'eskul' ? 'selected' : '' }}>Ekstrakurikuler</option>
                                        <option value="inkul" {{ old('jenis_kegiatan', $ordersSp->jenis_kegiatan) === 'inkul' ? 'selected' : '' }}>Intrakurikuler</option>
                                    </select>
                                    @error('jenis_kegiatan')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>

                                <div class="mb-3">
                                    <label for="lokasi_pembelajaran" class="form-label fw-semibold text-muted small">Lokasi Pembelajaran</label>
                                    <input 
                                        type="text" 
                                        class="form-control @error('lokasi_pembelajaran') is-invalid @enderror" 
                                        id="lokasi_pembelajaran" 
                                        name="lokasi_pembelajaran" 
                                        value="{{ old('lokasi_pembelajaran', $ordersSp->lokasi_pembelajaran) }}" 
                                        required
                                    >
                                    @error('lokasi_pembelajaran')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>

                                <div class="row">
                                    <div class="col-md-6 mb-3">
                                        <label for="tanggal_mulai_rencana" class="form-label fw-semibold text-muted small">Rencana Mulai</label>
                                        <input 
                                            type="date" 
                                            class="form-control @error('tanggal_mulai_rencana') is-invalid @enderror" 
                                            id="tanggal_mulai_rencana" 
                                            name="tanggal_mulai_rencana" 
                                            value="{{ old('tanggal_mulai_rencana', $ordersSp->tanggal_mulai_rencana) }}" 
                                            required
                                        >
                                        @error('tanggal_mulai_rencana')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>
                                    <div class="col-md-6 mb-3">
                                        <label for="jumlah_pertemuan" class="form-label fw-semibold text-muted small">Jumlah Pertemuan</label>
                                        <input 
                                            type="number" 
                                            class="form-control @error('jumlah_pertemuan') is-invalid @enderror" 
                                            id="jumlah_pertemuan" 
                                            name="jumlah_pertemuan" 
                                            value="{{ old('jumlah_pertemuan', $ordersSp->jumlah_pertemuan) }}" 
                                            min="1"
                                            required
                                        >
                                        @error('jumlah_pertemuan')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Catatan Khusus -->
                        <div class="mb-4">
                            <label for="catatan_khusus" class="form-label fw-semibold text-muted small">Catatan Khusus</label>
                            <textarea name="catatan_khusus" id="catatan_khusus" class="form-control" rows="2" placeholder="Catatan opsional...">{{ old('catatan_khusus', $ordersSp->catatan_khusus) }}</textarea>
                        </div>

                        <!-- Products (Items) Section -->
                        <div class="card shadow-sm border-0 mb-4 bg-light">
                            <div class="card-body p-4">
                                <h6 class="fw-bold text-dark border-bottom pb-2 mb-3">Pilih Produk & Koreksi Tarif</h6>
                                
                                <div class="row g-2 mb-3 align-items-end">
                                    <div class="col-md-6">
                                        <label class="form-label small fw-bold text-muted">Produk</label>
                                        <select class="form-select" id="product-selector">
                                            <option value="">-- Pilih Produk --</option>
                                            @foreach($products as $p)
                                                <option value="{{ $p->id }}" data-kode="{{ $p->kode_produk }}" data-nama="{{ $p->nama_produk }}" data-harga="{{ $p->harga }}">
                                                    [{{ $p->kode_produk }}] {{ $p->nama_produk }} - Rp {{ number_format($p->harga, 2, ',', '.') }}
                                                </option>
                                            @endforeach
                                        </select>
                                    </div>
                                    <div class="col-md-4">
                                        <label class="form-label small fw-bold text-muted">Tarif Kesepakatan (Harga Satuan)</label>
                                        <div class="input-group">
                                            <span class="input-group-text bg-white">Rp</span>
                                            <input type="number" class="form-control" id="product-price" placeholder="Tarif khusus/umum">
                                        </div>
                                    </div>
                                    <div class="col-md-2">
                                        <button type="button" class="btn btn-success w-100" id="add-product-btn">
                                            <i class="bi bi-plus-circle me-1"></i> Tambah
                                        </button>
                                    </div>
                                </div>

                                <div class="table-responsive">
                                    <table class="table table-bordered bg-white align-middle" id="products-table">
                                        <thead class="table-light">
                                            <tr>
                                                <th width="20%">Kode Produk</th>
                                                <th width="40%">Nama Produk</th>
                                                <th width="25%">Harga Satuan Kesepakatan</th>
                                                <th width="15%" class="text-center">Aksi</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <!-- Pre-populated dynamic rows -->
                                            @php $productIndex = 0; @endphp
                                            @foreach($ordersSp->items as $item)
                                                <tr id="row-product-{{ $item->product_id }}">
                                                    <td class="font-monospace fw-bold">{{ $item->product->kode_produk }}</td>
                                                    <td>
                                                        {{ $item->product->nama_produk }}
                                                        <input type="hidden" name="products[{{ $productIndex }}][product_id]" value="{{ $item->product_id }}">
                                                    </td>
                                                    <td>
                                                        <div class="input-group input-group-sm">
                                                            <span class="input-group-text bg-light">Rp</span>
                                                            <input type="number" name="products[{{ $productIndex }}][harga_satuan]" class="form-control" value="{{ $item->harga_satuan }}" required>
                                                        </div>
                                                    </td>
                                                    <td class="text-center">
                                                        <button type="button" class="btn btn-sm btn-outline-danger remove-product-btn" data-id="{{ $item->product_id }}">
                                                            <i class="bi bi-trash"></i> Hapus
                                                        </button>
                                                    </td>
                                                </tr>
                                                @php $productIndex++; @endphp
                                            @endforeach

                                            <tr id="empty-row" style="{{ $ordersSp->items->count() > 0 ? 'display: none;' : '' }}">
                                                <td colspan="4" class="text-center text-muted py-3">Belum ada produk ditambahkan. Pilih produk di atas.</td>
                                            </tr>
                                        </tbody>
                                    </table>
                                </div>
                                @error('products')
                                    <div class="text-danger small mt-2">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>

                        <!-- Submit Buttons -->
                        <div class="d-flex justify-content-end gap-2 mt-4">
                            <a href="{{ route('orders-sp.show', $ordersSp->id) }}" class="btn btn-outline-secondary px-4">Batal</a>
                            <button type="submit" class="btn btn-primary px-5">
                                <i class="bi bi-save me-1"></i> Perbarui SP
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script>
$(document).ready(function() {
    $('#sekolah_kodlan').select2({
        theme: 'bootstrap-5',
        placeholder: 'Ketik nama sekolah atau kode...',
        allowClear: true,
        ajax: {
            url: "{{ route('api.sekolah.search') }}",
            dataType: 'json',
            delay: 250,
            data: function (params) {
                return {
                    q: params.term
                };
            },
            processResults: function (data) {
                return {
                    results: data.results
                };
            },
            cache: true
        }
    });

    let productIndex = {{ $productIndex }};

    // Handle selector change to pre-fill price
    $('#product-selector').change(function() {
        const selectedOption = $(this).find('option:selected');
        if (selectedOption.val()) {
            const price = parseFloat(selectedOption.data('harga'));
            $('#product-price').val(price);
        } else {
            $('#product-price').val('');
        }
    });

    // Add product row to table
    $('#add-product-btn').click(function() {
        const select = $('#product-selector');
        const priceInput = $('#product-price');
        const productId = select.val();
        const price = priceInput.val();

        if (!productId) {
            alert('Silakan pilih produk terlebih dahulu.');
            return;
        }

        if (!price || parseFloat(price) < 0) {
            alert('Silakan masukkan tarif kesepakatan yang valid.');
            return;
        }

        // Check if product already added
        if ($(`#row-product-${productId}`).length > 0) {
            alert('Produk ini sudah ada di dalam daftar.');
            return;
        }

        const option = select.find('option:selected');
        const kode = option.data('kode');
        const nama = option.data('nama');

        // Hide empty row
        $('#empty-row').hide();

        // Append new row
        const newRow = `
            <tr id="row-product-${productId}">
                <td class="font-monospace fw-bold">${kode}</td>
                <td>
                    ${nama}
                    <input type="hidden" name="products[${productIndex}][product_id]" value="${productId}">
                </td>
                <td>
                    <div class="input-group input-group-sm">
                        <span class="input-group-text bg-light">Rp</span>
                        <input type="number" name="products[${productIndex}][harga_satuan]" class="form-control" value="${price}" required>
                    </div>
                </td>
                <td class="text-center">
                    <button type="button" class="btn btn-sm btn-outline-danger remove-product-btn" data-id="${productId}">
                        <i class="bi bi-trash"></i> Hapus
                    </button>
                </td>
            </tr>
        `;

        $('#products-table tbody').append(newRow);
        productIndex++;

        // Reset selector
        select.val('');
        priceInput.val('');
    });

    // Handle removing product rows
    $(document).on('click', '.remove-product-btn', function() {
        const productId = $(this).data('id');
        $(`#row-product-${productId}`).remove();

        // Check if table empty
        // We look for any row that is NOT empty-row
        if ($('#products-table tbody tr').not('#empty-row').length === 0) {
            $('#empty-row').show();
        }
    });
});
</script>
@endsection
