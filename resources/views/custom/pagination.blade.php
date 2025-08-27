@if ($paginator->hasPages())
    <nav role="navigation" aria-label="{{ __('Pagination Navigation') }}" class="d-flex align-items-center justify-content-between">
        <div class="d-flex justify-content-between flex-fill d-sm-none">
            {{-- Previous Page Link (Mobile) --}}
            @if ($paginator->onFirstPage())
                <span class="btn btn-outline-secondary disabled" aria-disabled="true">
                    <i class="bi bi-chevron-left me-1"></i>
                    <span class="d-none d-sm-inline">Sebelumnya</span>
                </span>
            @else
                <a href="{{ $paginator->previousPageUrl() }}" class="btn btn-outline-primary">
                    <i class="bi bi-chevron-left me-1"></i>
                    <span class="d-none d-sm-inline">Sebelumnya</span>
                </a>
            @endif

            {{-- Next Page Link (Mobile) --}}
            @if ($paginator->hasMorePages())
                <a href="{{ $paginator->nextPageUrl() }}" class="btn btn-outline-primary">
                    <span class="d-none d-sm-inline">Selanjutnya</span>
                    <i class="bi bi-chevron-right ms-1"></i>
                </a>
            @else
                <span class="btn btn-outline-secondary disabled" aria-disabled="true">
                    <span class="d-none d-sm-inline">Selanjutnya</span>
                    <i class="bi bi-chevron-right ms-1"></i>
                </span>
            @endif
        </div>

        <div class="d-none d-sm-flex-1 d-sm-flex d-sm-align-items-center d-sm-justify-content-between">
            <div>
                <p class="small text-muted mb-0">
                    Menampilkan <span class="fw-semibold">{{ $paginator->firstItem() }}</span>
                    sampai <span class="fw-semibold">{{ $paginator->lastItem() }}</span>
                    dari <span class="fw-semibold">{{ $paginator->total() }}</span> hasil
                </p>
            </div>

            <div>
                <ul class="pagination pagination-sm mb-0">
                    {{-- Previous Page Link --}}
                    @if ($paginator->onFirstPage())
                        <li class="page-item disabled" aria-disabled="true" aria-label="@lang('pagination.previous')">
                            <span class="page-link" aria-hidden="true">
                                <i class="bi bi-chevron-left"></i>
                            </span>
                        </li>
                    @else
                        <li class="page-item">
                            <a class="page-link" href="{{ $paginator->previousPageUrl() }}" rel="prev" 
                               aria-label="@lang('pagination.previous')" title="Halaman Sebelumnya">
                                <i class="bi bi-chevron-left"></i>
                            </a>
                        </li>
                    @endif

                    {{-- Pagination Elements --}}
                    @foreach ($elements as $element)
                        {{-- "Three Dots" Separator --}}
                        @if (is_string($element))
                            <li class="page-item disabled" aria-disabled="true">
                                <span class="page-link">{{ $element }}</span>
                            </li>
                        @endif

                        {{-- Array Of Links --}}
                        @if (is_array($element))
                            @foreach ($element as $page => $url)
                                @if ($page == $paginator->currentPage())
                                    <li class="page-item active" aria-current="page">
                                        <span class="page-link fw-semibold">{{ $page }}</span>
                                    </li>
                                @else
                                    <li class="page-item">
                                        <a class="page-link" href="{{ $url }}" title="Ke halaman {{ $page }}">{{ $page }}</a>
                                    </li>
                                @endif
                            @endforeach
                        @endif
                    @endforeach

                    {{-- Next Page Link --}}
                    @if ($paginator->hasMorePages())
                        <li class="page-item">
                            <a class="page-link" href="{{ $paginator->nextPageUrl() }}" rel="next" 
                               aria-label="@lang('pagination.next')" title="Halaman Selanjutnya">
                                <i class="bi bi-chevron-right"></i>
                            </a>
                        </li>
                    @else
                        <li class="page-item disabled" aria-disabled="true" aria-label="@lang('pagination.next')">
                            <span class="page-link" aria-hidden="true">
                                <i class="bi bi-chevron-right"></i>
                            </span>
                        </li>
                    @endif
                </ul>
            </div>
        </div>
    </nav>
@endif