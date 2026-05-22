@if ($paginator->hasPages())
<nav class="pagination-nav" role="navigation" aria-label="Pagination">
    <span class="pagination-info">
        {{ $paginator->firstItem() }}–{{ $paginator->lastItem() }} / {{ $paginator->total() }}
    </span>

    <ul class="pagination-list">
        {{-- Previous --}}
        <li>
            @if ($paginator->onFirstPage())
                <span class="page-item disabled" aria-disabled="true">&#8592;</span>
            @else
                <a class="page-item" href="{{ $paginator->previousPageUrl() }}" rel="prev" aria-label="Previous">&#8592;</a>
            @endif
        </li>

        {{-- Page numbers --}}
        @foreach ($elements as $element)
            @if (is_string($element))
                <li><span class="page-item dots">{{ $element }}</span></li>
            @endif
            @if (is_array($element))
                @foreach ($element as $page => $url)
                    <li>
                        @if ($page == $paginator->currentPage())
                            <span class="page-item active" aria-current="page">{{ $page }}</span>
                        @else
                            <a class="page-item" href="{{ $url }}" aria-label="Go to page {{ $page }}">{{ $page }}</a>
                        @endif
                    </li>
                @endforeach
            @endif
        @endforeach

        {{-- Next --}}
        <li>
            @if ($paginator->hasMorePages())
                <a class="page-item" href="{{ $paginator->nextPageUrl() }}" rel="next" aria-label="Next">&#8594;</a>
            @else
                <span class="page-item disabled" aria-disabled="true">&#8594;</span>
            @endif
        </li>
    </ul>
</nav>
@endif
