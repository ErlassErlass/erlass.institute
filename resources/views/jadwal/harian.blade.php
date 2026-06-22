@extends('layouts.app')

@section('title', 'Jadwal Harian')

@section('content')
<div class="container-fluid py-4">
    <div class="d-flex flex-column flex-md-row justify-content-between align-items-center mb-4 gap-3">
        <div>
            <h1 class="h3 fw-bold mb-1"><span class="text-gradient-primary">Jadwal Harian</span></h1>
            <p class="text-muted mb-0">Monitor kegiatan ekstrakurikuler harian.</p>
        </div>
        
        <div class="d-flex gap-2 bg-white p-2 rounded-3 shadow-sm border">
            <form action="{{ route('jadwal.harian') }}" method="GET" class="d-flex align-items-center gap-2">
                <input type="text" name="date" class="form-control datepicker border-0 bg-transparent py-1" 
                       value="{{ $date->format('Y-m-d') }}" 
                       placeholder="DD-MM-YYYY"
                       onchange="this.form.submit()" style="max-width: 150px;">
            </form>
            <div class="vr mx-1"></div>
            <button onclick="copyScheduleToClipboard()" class="btn btn-sm btn-success rounded-pill px-3" title="Salin Jadwal ke WhatsApp">
                <i class="bi bi-whatsapp me-1"></i> Copy
            </button>
        </div>
    </div>

    <div class="card glass-card border-0 mb-4">
        <div class="card-header bg-transparent border-bottom border-light py-3">
            <h6 class="m-0 fw-bold text-dark d-flex align-items-center gap-2">
                <i class="bi bi-calendar-check text-primary"></i>
                {{ $date->isoFormat('dddd, D MMMM Y') }}
            </h6>
        </div>
        <div class="card-body p-0">
            @if($sessions->isEmpty())
                <div class="text-center py-5">
                    <div class="mb-3">
                        <div class="bg-light rounded-circle d-inline-flex align-items-center justify-content-center" style="width: 80px; height: 80px;">
                            <i class="bi bi-calendar-x text-muted" style="font-size: 2.5rem; opacity: 0.3"></i>
                        </div>
                    </div>
                    <h5 class="fw-bold text-gray-800">Tidak Ada Jadwal</h5>
                    <p class="text-muted">Tidak ada kegiatan terjadwal untuk tanggal ini.</p>
                </div>
            @else
                <div class="table-responsive">
                    <table class="table table-modern align-middle mb-0" id="dataTable" width="100%" cellspacing="0">
                        <thead>
                            <tr>
                                <th width="15%">Jam</th>
                                <th width="20%">Sekolah & Program</th>
                                <th width="20%">Rombel</th>
                                <th width="25%">Instruktur</th>
                                <th width="10%">Status</th>
                                <th width="10%">Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($sessions as $session)
                            <tr>
                                <td>
                                    <div class="d-flex align-items-center gap-2">
                                        <i class="bi bi-clock text-muted small"></i>
                                        <span class="fw-bold text-dark">
                                            {{ $session->jam_mulai_terjadwal->format('H:i') }} - {{ $session->jam_selesai_terjadwal->format('H:i') }}
                                        </span>
                                    </div>
                                </td>
                                <td>
                                    <div class="fw-bold text-dark">{{ $session->ekstrakurikuler->sekolah->namasekolah ?? '-' }}</div>
                                    <div class="d-flex align-items-center flex-wrap gap-2 mt-1">
                                        <span class="badge bg-primary bg-opacity-10 text-primary border border-primary border-opacity-25 rounded-pill px-2 py-1 small">
                                            {{ $session->ekstrakurikuler->kategori_program }}
                                        </span>
                                        @if($session->ekstrakurikuler->google_maps_link)
                                            <a href="{{ $session->ekstrakurikuler->google_maps_link }}" target="_blank" class="text-primary d-inline-flex align-items-center" title="Buka Google Maps">
                                                <i class="bi bi-geo-alt-fill"></i>
                                            </a>
                                        @endif
                                        @if($session->ekstrakurikuler->no_telepon)
                                            @php
                                                $cleanPhone = preg_replace('/[^0-9]/', '', $session->ekstrakurikuler->no_telepon);
                                                if (str_starts_with($cleanPhone, '0')) {
                                                    $cleanPhone = '62' . substr($cleanPhone, 1);
                                                }
                                                $waText = urlencode("Halo " . $session->ekstrakurikuler->penanggung_jawab . ", saya instruktur Erlass untuk ekstrakurikuler " . $session->ekstrakurikuler->kategori_program . ".");
                                            @endphp
                                            <a href="whatsapp://send?phone={{ $cleanPhone }}&text={{ $waText }}" target="_blank" rel="noopener" class="text-success d-inline-flex align-items-center" title="WhatsApp PJ: {{ $session->ekstrakurikuler->penanggung_jawab }}">
                                                <i class="bi bi-whatsapp"></i>
                                            </a>
                                        @endif
                                    </div>
                                </td>
                                <td>
                                    <div class="fw-medium text-dark">{{ $session->rombel->nama_rombel }}</div>
                                    <small class="text-muted">Ke-{{ $session->nomor_pertemuan }}</small>
                                </td>
                                <td>
                                    <div class="d-flex align-items-center gap-2 mb-1">
                                        <div class="avatar-circle small bg-gradient-primary shadow-sm" style="width: 24px; height: 24px; font-size: 0.7rem;">
                                            <i class="bi bi-person"></i>
                                        </div>
                                        <span class="text-dark small">{{ $session->instruktur->nama_lengkap ?? '-' }}</span>
                                    </div>
                                    @if($session->asisten)
                                    <div class="d-flex align-items-center gap-2">
                                        <div class="avatar-circle small bg-secondary shadow-sm" style="width: 24px; height: 24px; font-size: 0.7rem;">
                                            <i class="bi bi-people"></i>
                                        </div>
                                        <span class="text-muted small">{{ $session->asisten->nama_lengkap }}</span>
                                    </div>
                                    @endif
                                </td>
                                <td>
                                    @php
                                        $statusClass = match($session->status) {
                                            'terjadwal' => 'secondary',
                                            'berlangsung' => 'primary',
                                            'selesai' => 'success',
                                            'dibatalkan' => 'danger',
                                            'ditunda' => 'warning',
                                            default => 'secondary'
                                        };
                                        $statusIcon = match($session->status) {
                                            'terjadwal' => 'bi-calendar',
                                            'berlangsung' => 'bi-play-circle',
                                            'selesai' => 'bi-check-circle',
                                            'dibatalkan' => 'bi-x-circle',
                                            'ditunda' => 'bi-clock-history',
                                            default => 'bi-circle'
                                        };
                                    @endphp
                                    <span class="badge bg-{{ $statusClass }} bg-opacity-10 text-{{ $statusClass }} border border-{{ $statusClass }} border-opacity-25 rounded-pill px-2 py-1">
                                        <i class="bi {{ $statusIcon }} me-1"></i> {{ ucfirst($session->status) }}
                                    </span>
                                </td>
                                <td>
                                    <a href="{{ route('ekstrakurikuler.sessions.show', $session->id) }}" class="btn btn-icon btn-light text-primary border shadow-sm" title="Lihat Detail">
                                        <i class="bi bi-eye"></i>
                                    </a>
                                </td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            @endif
        </div>
    </div>
</div>

@push('scripts')
<script>
    function copyScheduleToClipboard() {
        const date = "{{ $date->isoFormat('dddd, D MMMM Y') }}";
        let text = `*JADWAL HARIAN EKSTRAKURIKULER*\nTanggal: ${date}\n\n`;

        @foreach($sessions as $session)
            text += `*${{ $session->jam_mulai_terjadwal->format('H:i') }} - {{ $session->jam_selesai_terjadwal->format('H:i') }}*\n`;
            text += `Sekolah: {{ $session->ekstrakurikuler->sekolah->namasekolah ?? 'Nama Sekolah' }}\n`;
            text += `Program: {{ $session->ekstrakurikuler->kategori_program }}\n`;
            text += `Rombel: {{ $session->rombel->nama_rombel }} (Ke-{{ $session->nomor_pertemuan }})\n`;
            text += `Instruktur: {{ $session->instruktur->nama_lengkap ?? '-' }}\n`;
            @if($session->asisten)
            text += `Asisten: {{ $session->asisten->nama_lengkap }}\n`;
            @endif
            text += `Status: {{ ucfirst($session->status) }}\n`;
            text += `--------------------------------\n`;
        @endforeach

        navigator.clipboard.writeText(text).then(function() {
            alert('Jadwal berhasil disalin ke clipboard!');
        }, function(err) {
            console.error('Async: Could not copy text: ', err);
            // Fallback for older browsers if needed, though most support API now
            alert('Gagal menyalin. Silakan salin manual.');
        });
    }
</script>
@endpush
@endsection
