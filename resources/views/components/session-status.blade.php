@if(session()->has('success'))
    <x-alert type="success" dismissible="true">
        <strong>Berhasil!</strong> {{ session('success') }}
    </x-alert>
@endif

@if(session()->has('error'))
    <x-alert type="error" dismissible="true">
        <div>
            <strong>Pemberitahuan:</strong> {{ session('error') }}
            @if(session()->has('oldest_unreported_session_id'))
                <div class="mt-2">
                    <a href="{{ route('ekstrakurikuler.sessions.report.create', session('oldest_unreported_session_id')) }}" class="btn btn-sm btn-danger text-white fw-bold px-3 py-1.5 rounded-pill shadow-xs">
                        <i class="bi bi-arrow-right-circle me-1"></i> Isi Laporan Sesi Terdahulu Sekarang
                    </a>
                </div>
            @endif
        </div>
    </x-alert>
@endif

@if(session()->has('warning'))
    <x-alert type="warning" dismissible="true">
        <strong>Peringatan!</strong> {{ session('warning') }}
    </x-alert>
@endif

@if(session()->has('info'))
    <x-alert type="info" dismissible="true">
        <strong>Info:</strong> {{ session('info') }}
    </x-alert>
@endif

@if(session()->has('status'))
    <x-alert type="info" dismissible="true">
        {{ session('status') }}
    </x-alert>
@endif