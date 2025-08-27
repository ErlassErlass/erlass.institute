@props([
    'paginator',
    'showInfo' => true,
    'class' => '',
    'infoClass' => 'text-muted small',
])

@if ($paginator->hasPages())
    <div {{ $attributes->merge(['class' => 'card-footer ' . $class]) }}>
        @if ($showInfo && $paginator->total() > 0)
            <div class="d-flex justify-content-between align-items-center">
                <div class="{{ $infoClass }}">
                    Menampilkan <span class="fw-semibold">{{ $paginator->firstItem() }}</span>
                    sampai <span class="fw-semibold">{{ $paginator->lastItem() }}</span>
                    dari <span class="fw-semibold">{{ $paginator->total() }}</span> entri
                </div>
                <div>
                    {{ $paginator->links() }}
                </div>
            </div>
        @else
            {{ $paginator->links() }}
        @endif
    </div>
@endif