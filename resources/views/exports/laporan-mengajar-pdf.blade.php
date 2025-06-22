@php use Illuminate\Support\Str; @endphp

<!DOCTYPE html>
<html>
<head>
    <title>Laporan Mengajar</title>
    <style>
        body { font-family: Arial, sans-serif; }
        table { width: 100%; border-collapse: collapse; }
        th, td { border: 1px solid #ddd; padding: 8px; }
        th { background-color: #f2f2f2; text-align: left; }
        .text-center { text-align: center; }
        .badge {
            padding: 3px 6px;
            border-radius: 3px;
            font-size: 12px;
        }
    </style>
</head>
<body>
    <h2>Laporan Mengajar</h2>
    <p>Tanggal Export: {{ now()->format('d/m/Y H:i') }}</p>
    
    <table>
        <thead>
            <tr>
                <th>Tanggal</th>
                <th>Instruktur</th>
                <th>Sekolah</th>
                <th>Rombel</th>
                <th>Kategori</th>
                <th>Jam</th>
                <th>Materi</th>
            </tr>
        </thead>
<tbody>
    @forelse($laporan as $item)
        <tr>
            <td>{{ \Carbon\Carbon::parse($item->jadwal_mengajar)->format('d/m/Y') }}</td>
            <td>
                {{ $item->instruktur->nama_lengkap ?? 'N/A' }}
                @if($item->asisten)
                    <br><small>Asisten: {{ $item->asisten->nama_lengkap }}</small>
                @endif
            </td>
            <td>
                {{ $item->sekolah->namasekolah ?? 'N/A' }}
                <br><small>{{ $item->sekolah->kec ?? '' }}, {{ $item->sekolah->kotkab ?? '' }}</small>
            </td>
            <td>{{ $item->rombel }}</td>
            <td>
                @php
                    $badgeClass = [
                        'Reguler' => 'background-color: #0d6efd; color: white;',
                        'Remedial' => 'background-color: #ffc107; color: black;',
                        'Pengayaan' => 'background-color: #0dcaf0; color: black;'
                    ][$item->kategori_pengajaran] ?? 'background-color: #6c757d; color: white;';
                @endphp
                <span style="{{ $badgeClass }}">{{ $item->kategori_pengajaran }}</span>
            </td>
            <td>{{ $item->jam_mulai }} - {{ $item->jam_selesai }}</td>
            <td>{{ Str::limit($item->materi_pengajaran, 50) }}</td>
        </tr>
    @empty
        <tr>
            <td colspan="7" class="text-center">Tidak ada data</td>
        </tr>
    @endforelse
</tbody>
    </table>
</body>
</html>