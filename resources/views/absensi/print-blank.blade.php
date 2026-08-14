<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Absensi - {{ $programName }} - {{ $monthName }}</title>
    <style>
        * {
            box-sizing: border-box;
        }
        
        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            font-size: 8pt;
            margin: 0;
            padding: 10px;
            background-color: #f1f5f9;
            color: #1e293b;
            line-height: 1.15;
        }

        .page-container {
            width: 100%;
            max-width: 210mm;
            margin: 0 auto;
            background: #ffffff;
            padding: 6mm 10mm;
            box-shadow: 0 4px 15px rgba(0, 0, 0, 0.08);
        }

        .text-center { text-align: center; }
        .text-bold { font-weight: bold; }
        .text-end { text-align: right; }
        .uppercase { text-transform: uppercase; }
        
        .header {
            margin-bottom: 6px;
            border-bottom: 1.5px solid #0f172a;
            padding-bottom: 4px;
        }
        .header h1 {
            margin: 0;
            font-size: 11pt;
            color: #0f172a;
            letter-spacing: 0.5px;
            font-weight: 700;
        }
        .header h2 {
            margin: 2px 0 0;
            font-size: 8.5pt;
            font-weight: 600;
            color: #475569;
        }

        .meta-grid {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 4px 16px;
            margin-bottom: 6px;
            font-size: 7.8pt;
        }
        .meta-row {
            display: flex;
            margin-bottom: 2px;
            align-items: baseline;
        }
        .meta-label {
            width: 115px;
            font-weight: 600;
            color: #334155;
            flex-shrink: 0;
        }
        .meta-value {
            flex: 1;
            font-weight: 500;
            word-break: break-word;
        }

        .table-container {
            width: 100%;
            margin-bottom: 6px;
        }
        
        table {
            width: 100%;
            border-collapse: collapse;
            font-size: 7.5pt;
            table-layout: fixed;
        }
        
        th, td {
            border: 1px solid #334155;
            padding: 1.5px 3px;
            vertical-align: middle;
            word-wrap: break-word;
            overflow-wrap: break-word;
            line-height: 1.1;
            font-size: 7.5pt;
            height: 15.5px;
        }
        
        th {
            background-color: #f8fafc;
            color: #0f172a;
            text-align: center;
            font-weight: 700;
            padding: 2.5px 2px;
            border-bottom: 1.5px solid #0f172a;
            font-size: 7.5pt;
        }
        
        /* Uniform row height for empty and filled rows */
        tbody tr {
            height: 15.5px;
        }

        .col-no { text-align: center; width: 3.5%; }
        .col-nama { text-align: left; padding-left: 5px; white-space: nowrap; overflow: hidden; text-overflow: ellipsis; } 
        .col-kelas { text-align: center; }
        .col-meeting { text-align: center; }
        .col-ket { text-align: center; }

        .row-paraf {
            height: 20px;
            background-color: #f8fafc;
        }
        .row-paraf td {
            border-top: 1.5px solid #0f172a;
            font-size: 7.5pt;
        }

        .footer {
            margin-top: 6px;
            display: flex;
            justify-content: space-between;
            padding: 0 15px;
            page-break-inside: avoid;
        }
        .signature-box {
            text-align: center;
            width: 180px;
            font-size: 7.5pt;
        }
        .signature-space {
            height: 28px;
        }
        .signature-line {
            border-top: 1px solid #0f172a;
            margin-top: 2px;
            padding-top: 2px;
            font-weight: 600;
        }

        @media print {
            @page {
                size: A4 portrait;
                margin: 4mm 6mm;
            }
            body {
                background: #ffffff;
                padding: 0;
                margin: 0;
                font-size: 7.5pt;
            }
            .page-container {
                max-width: 100% !important;
                width: 100% !important;
                padding: 0 !important;
                margin: 0 !important;
                box-shadow: none !important;
            }
            .no-print { display: none !important; }
            table {
                width: 100% !important;
                table-layout: fixed !important;
            }
        }
        
        .btn-print {
            background: #0f172a;
            color: white;
            border: none;
            padding: 8px 16px;
            border-radius: 6px;
            cursor: pointer;
            margin-bottom: 10px;
            font-size: 12px;
            font-weight: 600;
            display: inline-flex;
            align-items: center;
            gap: 6px;
            box-shadow: 0 2px 4px rgba(0,0,0,0.1);
        }
        .btn-print:hover {
            background: #000;
        }
    </style>
</head>
<body>

    <button onclick="window.print()" class="no-print btn-print">
        <svg xmlns="http://www.w3.org/2000/svg" width="14" height="14" fill="currentColor" viewBox="0 0 16 16">
            <path d="M2.5 8a.5.5 0 1 0 0-1 .5.5 0 0 0 0 1"/>
            <path d="M5 1a2 2 0 0 0-2 2v2H2a2 2 0 0 0-2 2v3a2 2 0 0 0 2 2h1v1a2 2 0 0 0 2 2h6a2 2 0 0 0 2-2v-1h1a2 2 0 0 0 2-2V7a2 2 0 0 0-2-2h-1V3a2 2 0 0 0-2-2zM4 3a1 1 0 0 1 1-1h6a1 1 0 0 1 1 1v2H4zm1 5a2 2 0 0 0-2 2v1H2a1 1 0 0 1-1-1V7a1 1 0 0 1 1-1h12a1 1 0 0 1 1 1v3a1 1 0 0 1-1 1h-1v-1a2 2 0 0 0-2-2zm7 2v3a1 1 0 0 1-1 1H5a1 1 0 0 1-1-1v-3a1 1 0 0 1 1-1h6a1 1 0 0 1 1 1"/>
        </svg>
        Cetak Dokumen (A4)
    </button>

    <div class="page-container">
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
                    <span class="meta-label">Bulan / Periode</span>
                    <span class="meta-value">: {{ $monthName }}</span>
                </div>
            </div>
        </div>

        @php
            $sessions = $monthlySessions;
            $maxColumns = max(4, $sessions->count());
            
            // Optimized width calculation for A4 Portrait (Total 100%)
            $noPercent = 4.0;       // ~30px on A4
            $kelasPercent = 15;     // ~110px on A4
            $ketPercent = 6.0;      // ~45px on A4
            $meetingPercent = 7.5;  // ~55px on A4 per meeting column (x4 = 30%)
            
            // Distribute remaining width to student name (~45%)
            $namaPercent = round(100 - $noPercent - $kelasPercent - $ketPercent - ($maxColumns * $meetingPercent), 2);
            
            // Target max 30 students per sheet
            $totalRows = 30; 
            $currentCount = $students->count();
            $emptyRowsNeeded = max(0, $totalRows - $currentCount);
        @endphp

        <div class="table-container">
            <table>
                <colgroup>
                    <col style="width: {{ $noPercent }}%;">
                    <col style="width: {{ $namaPercent }}%;">
                    <col style="width: {{ $kelasPercent }}%;">
                    @for($i = 0; $i < $maxColumns; $i++)
                        <col style="width: {{ $meetingPercent }}%;">
                    @endfor
                    <col style="width: {{ $ketPercent }}%;">
                </colgroup>
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
                            <th style="font-weight: normal; font-size: 7pt;">
                                <span style="color: #64748b;">Tgl:</span> 
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
                        <td class="col-kelas">{{ $student->kelas ?? $student->rombel ?? '-' }}</td>
                        
                        @for($i = 0; $i < $maxColumns; $i++)
                            <td style="text-align: center;">
                                @if(isset($sessions[$i]))
                                    @php
                                        $sId = $sessions[$i]->id;
                                        $stId = $student->id;
                                        $status = $attendanceMap[$sId][$stId] ?? null;
                                    @endphp
                                    
                                    @if($status === 1 || $status === true || $status === '1')
                                        <span style="font-weight: bold;">&#10003;</span>
                                    @elseif($status === 0 || $status === false || $status === '0')
                                        <span style="color: #ef4444; font-weight: bold;">x</span>
                                    @endif
                                @endif
                            </td>
                        @endfor
                        
                        <td class="col-ket"></td>
                    </tr>
                    @endforeach
                    
                    <!-- Fill empty rows up to 30 for manual entry in next meetings -->
                    @for($i = 0; $i < $emptyRowsNeeded; $i++)
                    <tr>
                        <td class="col-no" style="color: #94a3b8;">{{ $currentCount + $i + 1 }}</td>
                        <td class="col-nama"></td>
                        <td class="col-kelas"></td>
                        @for($j = 0; $j < $maxColumns; $j++)
                            <td></td>
                        @endfor
                        <td class="col-ket"></td>
                    </tr>
                    @endfor
                </tbody>
                <tfoot>
                    <tr class="row-paraf">
                        <td colspan="3" class="text-bold text-end" style="font-size: 7.5pt; color: #0f172a; padding-right: 6px;">Paraf PIC Ekskul:</td>
                        @for($i = 0; $i < $maxColumns; $i++)
                            <td style="text-align: center; vertical-align: bottom; padding-bottom: 2px;"></td>
                        @endfor
                        <td></td>
                    </tr>
                </tfoot>
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
    </div>

</body>
</html>
