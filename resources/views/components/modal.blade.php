@props([
    'name',
    'show' => false,
    'maxWidth' => 'lg'
])

@php
$maxWidth = [
    'sm' => 'modal-sm',
    'md' => '',
    'lg' => 'modal-lg',
    'xl' => 'modal-xl',
    '2xl' => 'modal-xl',
][$maxWidth];
@endphp

<div class="modal fade" id="{{ $name }}" tabindex="-1" aria-labelledby="{{ $name }}Label" aria-hidden="true"
     @if($show) x-data="{}" x-init="new bootstrap.Modal($el).show()" @endif>
    <div class="modal-dialog {{ $maxWidth }}">
        <div class="modal-content">
            {{ $slot }}
        </div>
    </div>
</div>

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    // Listen for custom open modal events
    window.addEventListener('open-modal', function(event) {
        if (event.detail === '{{ $name }}') {
            new bootstrap.Modal(document.getElementById('{{ $name }}')).show();
        }
    });
    
    // Listen for custom close modal events
    window.addEventListener('close-modal', function(event) {
        if (event.detail === '{{ $name }}') {
            bootstrap.Modal.getInstance(document.getElementById('{{ $name }}'))?.hide();
        }
    });
});
</script>
@endpush