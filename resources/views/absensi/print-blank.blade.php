<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Absensi - {{ $programName }} - {{ $monthName }}</title>
    <style>
        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            font-size: 10pt;
            margin: 0;
            padding: 20px;
            color: #333;
        }
        .text-center { text-align: center; }
        .text-bold { font-weight: bold; }
        .uppercase { text-transform: uppercase; }
        
        .header {
            margin-bottom: 25px;
            border-bottom: 2px solid #333;
            padding-bottom: 15px;
        }
        .header h1 {
            margin: 0;
            font-size: 16pt;
            color: #000;
            letter-spacing: 1px;
        }
        .header h2 {
            margin: 5px 0 0;
            font-size: 12pt;
            font-weight: 500;
            color: #555;
        }

        .meta-grid {
            display: grid;
            grid-template-columns: 1.2fr 0.8fr;
            gap: 30px;
            margin-bottom: 25px;
        }
        .meta-row {
            display: flex;
            margin-bottom: 8px;
            align-items: flex-start;
        }
        .meta-label {
            width: 140px;
            font-weight: 600;
            color: #444;
            flex-shrink: 0;
        }
        .meta-value {
            flex: 1;
            font-weight: 500;
        }

        .table-container {
            width: 100%;
            margin-bottom: 30px;
        }
        table {
            width: 100%;
            border-collapse: collapse;
            font-size: 9.5pt;
        }
        th, td {
            border: 1px solid #444;
            padding: 4px 8px;
            vertical-align: middle;
        }
        th {
            background-color: #f8f9fa;
            color: #000;
            text-align: center;
            font-weight: 700;
            padding: 8px;
            border-bottom: 2px solid #333;
        }
        /* Uniform row height for empty rows */
        tbody tr {
            height: 32px; 
        }
        
        .col-no { width: 35px; text-align: center; }
        .col-nama { } 
        .col-kelas { width: 80px; text-align: center; }
        .col-meeting { width: 100px; text-align: center; }
        .col-ket { width: 60px; text-align: center; }

        .footer {
            margin-top: 40px;
            display: flex;
            justify-content: space-between;
            padding: 0 40px;
            page-break-inside: avoid;
        }
        .signature-box {
            text-align: center;
            width: 250px;
        }
        .signature-space {
            height: 80px;
        }
        .signature-line {
            border-top: 1px solid #000;
            margin-top: 5px;
            padding-top: 5px;
        }

        @media print {
            @page {
                size: A4 landscape;
                margin: 10mm;
            }
            body { padding: 0; }
            .no-print { display: none; }
        }
        
        .btn-print {
            background: #212529;
            color: white;
            border: none;
            padding: 12px 24px;
            border-radius: 6px;
            cursor: pointer;
            margin-bottom: 20px;
            font-size: 14px;
            font-weight: 600;
            display: inline-flex;
            align-items: center;
            gap: 8px;
            box-shadow: 0 2px 4px rgba(0,0,0,0.1);
        }
        .btn-print:hover {
            background: #000;
        }
    </style>
</head>
<body>

    <button onclick="window.print()" class="no-print btn-print">
        <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" viewBox="0 0 16 16">
            <path d="M2.5 8a.5.5 0 1 0 0-1 .5.5 0 0 0 0 1"/>
            <path d="M5 1a2 2 0 0 0-2 2v2H2a2 2 0 0 0-2 2v3a2 2 0 0 0 2 2h1v1a2 2 0 0 0 2 2h6a2 2 0 0 0 2-2v-1h1a2 2 0 0 0 2-2V7a2 2 0 0 0-2-2h-1V3a2 2 0 0 0-2-2zM4 3a1 1 0 0 1 1-1h6a1 1 0 0 1 1 1v2H4zm1 5a2 2 0 0 0-2 2v1H2a1 1 0 0 1-1-1V7a1 1 0 0 1 1-1h12a1 1 0 0 1 1 1v3a1 1 0 0 1-1 1h-1v-1a2 2 0 0 0-2-2zm7 2v3a1 1 0 0 1-1 1H5a1 1 0 0 1-1-1v-3a1 1 0 0 1 1-1h6a1 1 0 0 1 1 1"/>
        </svg>
        Cetak Dokumen
    </button>

    <div class="header text-center">
        <h1 class="uppercase">DAFTAR HADIR EKSKUL {{ $programName }}</h1>
        <h2 class="uppercase">{{ $schoolName }} — TAHUN AJARAN {{ $academicYear }}</h2>
    </div>

    <!-- Info Grid matching Excel layout -->
    <div class="meta-grid">
        <div class="left-col">
            <div class="meta-row">
                <span class="meta-label">Nama Sekolah</span>
                <span class="meta-value">: {{ $schoolName }}</span>
            </div>
            <div class="meta-row">
                <span class="meta-label">PIC Sekolah</span>
                <span class="meta-value">: {{ $picName }}</span>
            </div>
            <div class="meta-row">
                <span class="meta-label">Rombel</span>
                <span class="meta-value">: {{ $rombelNumber }} ({{ $rombelName }})</span>
            </div>
        </div>
        <div class="right-col">
            <div class="meta-row">
                <span class="meta-label">Instruktur</span>
                <span class="meta-value">: {{ $instructorName }}</span>
            </div>
            <div class="meta-row">
                <span class="meta-label">Sales Representative</span>
                <span class="meta-value">: {{ $salesName }}</span>
            </div>
             <div class="meta-row">
                <span class="meta-label">Bulan</span>
                <span class="meta-value">: {{ $monthName }}</span>
            </div>
        </div>
    </div>

    @php
        // Prepare at least 4 columns
        $sessions = $monthlySessions;
        $maxColumns = max(4, $sessions->count());
        
        // Count Logic
        $totalRows = 30; // Minimum rows requested
        $currentCount = $students->count();
        $emptyRowsNeeded = max(0, $totalRows - $currentCount);
    @endphp

    <div class="table-container">
        <table>
            <thead>
                <tr>
                    <th rowspan="2" class="col-no">No</th>
                    <th rowspan="2" class="col-nama">Nama Lengkap</th>
                    <th rowspan="2" class="col-kelas">Kelas</th>
                    @for($i = 0; $i < $maxColumns; $i++)
                        <th class="col-meeting">
                            Pert. {{ isset($sessions[$i]) ? $sessions[$i]->nomor_pertemuan : ($i + 1) }}
                        </th>
                    @endfor
                    <th rowspan="2" class="col-ket">Ket</th>
                </tr>
                <tr>
                    @for($i = 0; $i < $maxColumns; $i++)
                        <th style="font-weight: normal; font-size: 9pt;">
                            <span style="color: #666;">Tgl:</span> 
                            {{ isset($sessions[$i]) && ($sessions[$i]->tanggal_terjadwal ?? $sessions[$i]->tanggal_pelaksanaan) ? ($sessions[$i]->tanggal_terjadwal ?? $sessions[$i]->tanggal_pelaksanaan)->format('d/m') : '-' }}
                        </th>
                    @endfor
                </tr>
            </thead>
            <tbody>
                @foreach($students as $index => $student)
                <tr>
                    <td class="col-no">{{ $index + 1 }}</td>
                    <td class="col-nama">{{ $student->nama_lengkap }}</td>
                    <td class="col-kelas">{{ $student->rombel }}</td>
                    
                    @for($i = 0; $i < $maxColumns; $i++)
                        <td style="text-align: center;">
                            @if(isset($sessions[$i]))
                                @php
                                    $sId = $sessions[$i]->id;
                                    $stId = $student->id;
                                    $status = $attendanceMap[$sId][$stId] ?? null;
                                @endphp
                                
                                @if($status === 1 || $status === true || $status === '1')
                                    <span>&#10003;</span> <!-- Checkmark -->
                                @elseif($status === 0 || $status === false || $status === '0')
                                    <span style="color: red;">x</span>
                                @endif
                            @endif
                        </td>
                    @endfor
                    
                    <td class="col-ket"></td>
                </tr>
                @endforeach
                
                <!-- Fill empty rows to reach minimum 30 -->
                 @for($i = 0; $i < $emptyRowsNeeded; $i++)
                <tr>
                    <td class="col-no" style="color: #ccc;">{{ $currentCount + $i + 1 }}</td>
                    <td></td>
                    <td></td>
                    @for($j = 0; $j < $maxColumns; $j++)
                        <td></td>
                    @endfor
                    <td></td>
                </tr>
                @endfor
            </tbody>
        </table>
    </div>

    <div class="footer">
        <div class="signature-box">
            <div>Mengetahui,<br>PIC Sekolah / Koordinator</div>
            <div class="signature-space"></div>
            <div class="signature-line"><strong>{{ $picName }}</strong></div>
        </div>

        <div class="signature-box">
            <div>{{ $city }}, {{ $printDate }}<br>Instruktur Pengajar</div>
            <div class="signature-space"></div>
            <div class="signature-line"><strong>{{ $instructorName }}</strong></div>
        </div>
    </div>

</body>
</html>
