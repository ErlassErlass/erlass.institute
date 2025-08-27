@extends('layouts.app')

@section('content')
<div class="container py-4">
    <h2 class="mb-4">📄 Rekap Absensi @if(!Auth::user()->hasAdminAccess()) Anda @endif</h2>

<table class="table table-bordered table-hover datatable" id="rekap-absensi-table">
    <thead class="table-light">
        <tr>
            <th>#</th>
            <th>Tanggal</th>
            <th>Aksi</th>
        </tr>
    </thead>
    <tbody>
        @forelse ($absensi_per_tanggal as $index => $row)
        <tr>
            <td>{{ $loop->iteration }}</td>
            <td>{{ \Carbon\Carbon::parse($row->tanggal)->format('d M Y') }}</td>
            <td>
                <a href="{{ route('absensi.rekap.tanggal', ['tanggal' => $row->tanggal]) }}" class="btn btn-primary btn-sm">
                    Lihat Detail
                </a>
            </td>
        </tr>
        @empty
        <tr>
            <td colspan="3" class="text-center">Belum ada data absensi.</td>
        </tr>
        @endforelse
    </tbody>
</table>


</div>
@endsection

@push('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function() {
        // Initialize DataTable for Attendance Recap table
        if (typeof window.DataTableManager !== 'undefined') {
            const dataTableManager = new window.DataTableManager();
            dataTableManager.init('#rekap-absensi-table', {
                order: [[1, 'desc']], // Sort by Date column (newest first)
                columnDefs: [
                    { orderable: false, targets: [0, 2] }, // Disable sorting for # and Actions columns
                    { type: 'date', targets: [1] } // Date sorting for date column
                ],
                pageLength: 15
            });
        }
    });
</script>
@endpush
