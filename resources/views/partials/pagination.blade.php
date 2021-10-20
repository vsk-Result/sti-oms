@if ($paginator->hasPages())
    <ul class="pagination">
        {{-- Previous Page Link --}}
        @if ($paginator->onFirstPage())
            <li class="page-item previous disabled" aria-label="@lang('pagination.previous')">
                <a href="#" class="page-link">
                    <i class="previous"></i>
                </a>
            </li>
        @else
            <li class="page-item previous" rel="prev" aria-label="@lang('pagination.previous')">
                <a href="{{ $paginator->previousPageUrl() }}" class="page-link">
                    <i class="previous"></i>
                </a>
            </li>
        @endif

        {{-- Pagination Elements --}}
        @foreach ($elements as $element)
            {{-- "Three Dots" Separator --}}
            @if (is_string($element))
                <li class="page-item disable"><span class="page-link">...</span></li>
            @endif

            {{-- Array Of Links --}}
            @if (is_array($element))
                @foreach ($element as $page => $url)
                    @if ($page == $paginator->currentPage())
                        <li class="page-item active" aria-current="page"><a href="#" class="page-link">{{ $page }}</a></li>
                    @else
                        <li class="page-item"><a href="{{ $url }}" class="page-link">{{ $page }}</a></li>
                    @endif
                @endforeach
            @endif
        @endforeach

        {{-- Next Page Link --}}
        @if ($paginator->hasMorePages())
            <li class="page-item next" rel="next" aria-label="@lang('pagination.next')">
                <a href="{{ $paginator->nextPageUrl() }}" class="page-link">
                    <i class="next"></i>
                </a>
            </li>
        @else
            <li class="page-item next disabled" aria-label="@lang('pagination.next')">
                <a href="#" class="page-link">
                    <i class="next"></i>
                </a>
            </li>
        @endif
    </ul>
@endif
