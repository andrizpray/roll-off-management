@if ($paginator->hasPages())
    <nav class="flex items-center gap-1">
        {{-- Previous --}}
        @if ($paginator->onFirstPage())
            <span class="page-btn disabled"><i class="fas fa-chevron-left text-xs"></i></span>
        @else
            <a href="{{ $paginator->previousPageUrl() }}" class="page-btn" rel="prev"><i class="fas fa-chevron-left text-xs"></i></a>
        @endif

        {{-- Pages --}}
        @foreach ($elements as $element)
            @if (is_string($element))
                <span class="page-btn disabled">...</span>
            @endif
            @if (is_array($element))
                @foreach ($element as $page => $url)
                    @if ($page == $paginator->currentPage())
                        <span class="page-btn active">{{ $page }}</span>
                    @else
                        <a href="{{ $url }}" class="page-btn">{{ $page }}</a>
                    @endif
                @endforeach
            @endif
        @endforeach

        {{-- Next --}}
        @if ($paginator->hasMorePages())
            <a href="{{ $paginator->nextPageUrl() }}" class="page-btn" rel="next"><i class="fas fa-chevron-right text-xs"></i></a>
        @else
            <span class="page-btn disabled"><i class="fas fa-chevron-right text-xs"></i></span>
        @endif
    </nav>
@endif
