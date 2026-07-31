@props([
    'paginator',
    'showInfo' => true,
    'class' => '',
    'infoClass' => 'text-muted small',
])

@if (isset($paginator) && $paginator->total() > 0)
    <div {{ $attributes->merge(['class' => 'card-footer bg-white border-top py-3 ' . $class]) }}>
        <div class="d-flex flex-column flex-md-row justify-content-between align-items-center gap-3">
            @if ($showInfo)
                <div class="{{ $infoClass }}">
                    Menampilkan <span class="fw-semibold text-dark">{{ $paginator->firstItem() }}</span>
                    sampai <span class="fw-semibold text-dark">{{ $paginator->lastItem() }}</span>
                    dari <span class="fw-semibold text-dark">{{ $paginator->total() }}</span> entri
                </div>
            @endif

            @if ($paginator->hasPages())
                <div class="pagination-wrapper-links">
                    {{ $paginator->links() }}
                </div>
            @endif
        </div>
    </div>
@endif