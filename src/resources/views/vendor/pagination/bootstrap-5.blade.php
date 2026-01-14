@if ($paginator->hasPages())
    <nav role="navigation" aria-label="{{ __('Pagination Navigation') }}" class="d-flex justify-content-between align-items-center">
        <div class="d-flex justify-content-between flex-fill d-sm-none">
            <span>
                @if ($paginator->onFirstPage())
                    <span class="btn btn-outline-secondary disabled">
                        <i class="bi bi-chevron-left"></i> Previous
                    </span>
                @else
                    <a href="{{ $paginator->previousPageUrl() }}" class="btn btn-outline-primary">
                        <i class="bi bi-chevron-left"></i> Previous
                    </a>
                @endif
            </span>

            <span>
                @if ($paginator->hasMorePages())
                    <a href="{{ $paginator->nextPageUrl() }}" class="btn btn-outline-primary">
                        Next <i class="bi bi-chevron-right"></i>
                    </a>
                @else
                    <span class="btn btn-outline-secondary disabled">
                        Next <i class="bi bi-chevron-right"></i>
                    </span>
                @endif
            </span>
        </div>

        <div class="d-none flex-sm-fill d-sm-flex align-items-sm-center justify-content-sm-between">
            <div>
                <p class="small text-muted mb-0">
                    Showing
                    <span class="fw-semibold">{{ $paginator->firstItem() }}</span>
                    to
                    <span class="fw-semibold">{{ $paginator->lastItem() }}</span>
                    of
                    <span class="fw-semibold">{{ $paginator->total() }}</span>
                    results
                </p>
            </div>

            <div>
                <ul class="pagination mb-0">
                    {{-- Previous Page Link --}}
                    @if ($paginator->onFirstPage())
                        <li class="page-item disabled" aria-disabled="true" aria-label="@lang('pagination.previous')">
                            <span class="page-link">
                                <i class="bi bi-chevron-left"></i>
                                <span class="d-none d-md-inline ms-1">Previous</span>
                            </span>
                        </li>
                    @else
                        <li class="page-item">
                            <a class="page-link" href="{{ $paginator->previousPageUrl() }}" rel="prev" aria-label="@lang('pagination.previous')">
                                <i class="bi bi-chevron-left"></i>
                                <span class="d-none d-md-inline ms-1">Previous</span>
                            </a>
                        </li>
                    @endif

                    {{-- Pagination Elements --}}
                    @foreach ($elements as $element)
                        {{-- "Three Dots" Separator --}}
                        @if (is_string($element))
                            <li class="page-item disabled" aria-disabled="true"><span class="page-link">{{ $element }}</span></li>
                        @endif

                        {{-- Array Of Links --}}
                        @if (is_array($element))
                            @foreach ($element as $page => $url)
                                @if ($page == $paginator->currentPage())
                                    <li class="page-item active" aria-current="page"><span class="page-link">{{ $page }}</span></li>
                                @else
                                    <li class="page-item"><a class="page-link" href="{{ $url }}">{{ $page }}</a></li>
                                @endif
                            @endforeach
                        @endif
                    @endforeach

                    {{-- Next Page Link --}}
                    @if ($paginator->hasMorePages())
                        <li class="page-item">
                            <a class="page-link" href="{{ $paginator->nextPageUrl() }}" rel="next" aria-label="@lang('pagination.next')">
                                <span class="d-none d-md-inline me-1">Next</span>
                                <i class="bi bi-chevron-right"></i>
                            </a>
                        </li>
                    @else
                        <li class="page-item disabled" aria-disabled="true" aria-label="@lang('pagination.next')">
                            <span class="page-link">
                                <span class="d-none d-md-inline me-1">Next</span>
                                <i class="bi bi-chevron-right"></i>
                            </span>
                        </li>
                    @endif
                </ul>
            </div>
        </div>
    </nav>
@endif
