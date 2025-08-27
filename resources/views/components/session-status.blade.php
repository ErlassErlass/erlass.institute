@if(session()->has('success'))
    <x-alert type="success" dismissible="true">
        <strong>Berhasil!</strong> {{ session('success') }}
    </x-alert>
@endif

@if(session()->has('error'))
    <x-alert type="error" dismissible="true">
        <strong>Error!</strong> {{ session('error') }}
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