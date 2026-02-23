<table>
    <thead>
    <tr>
        <th colspan="2" style="font-weight: bold; font-size: 14px;">Rekap Absensi Ekstrakurikuler</th>
    </tr>
    <tr>
        <td colspan="2">Sekolah: {{ $selectedSekolahData->namasekolah ?? 'Semua Sekolah' }}</td>
    </tr>
    <tr>
        <td colspan="2">Rombel: {{ $selectedRombel }}</td>
    </tr>
    <tr></tr>
    <tr>
        <th rowspan="2" style="font-weight: bold; border: 1px solid #000000; text-align: center; vertical-align: middle;">No</th>
        <th rowspan="2" style="font-weight: bold; border: 1px solid #000000; text-align: left; vertical-align: middle; width: 30px;">Nama Siswa</th>
        @foreach($rekapData as $period)
            <th colspan="1" style="font-weight: bold; border: 1px solid #000000; text-align: center;">Periode {{ $period['index'] }}</th>
        @endforeach
    </tr>
    <tr>
        @foreach($rekapData as $period)
            <th style="font-style: italic; border: 1px solid #000000; text-align: center;">
                {{ $period['dates'] }}
            </th>
        @endforeach
    </tr>
    </thead>
    <tbody>
    @foreach($students as $index => $student)
        <tr>
            <td style="border: 1px solid #000000; text-align: center;">{{ $index + 1 }}</td>
            <td style="border: 1px solid #000000;">{{ $student->nama_lengkap }}</td>
            @foreach($rekapData as $period)
                @php
                    $stats = $period['student_stats'][$student->id] ?? ['count' => 0, 'is_billable' => false];
                    $bgColor = $stats['is_billable'] ? '#d1e7dd' : '#ffffff'; // Light green for billable
                @endphp
                <td style="border: 1px solid #000000; text-align: center; background-color: {{ $bgColor }};">
                    {{ $stats['count'] }} / 4
                    <br>
                    <small>{{ $stats['is_billable'] ? 'Billable' : '-' }}</small>
                </td>
            @endforeach
        </tr>
    @endforeach
    </tbody>
</table>
