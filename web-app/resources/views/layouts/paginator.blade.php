@if ($paginator->hasPages())
    <ul class="pagination pagination-rounded mb-1">
        @if (!$paginator->onFirstPage())
            <li class="page-item">
                <a class="page-link" tabindex="-1" href="{{ $paginator->previousPageUrl() }}" aria-label="Previous">
                    <span aria-hidden="true">&laquo;</span>
                </a>
            </li>
        @endif

        @foreach ($elements as $element)
            @if (is_string($element))
                <li class="disabled page-item" tabindex="-1"><span>{{ $element }}</span></li>
            @endif

            @if (is_array($element))
                @foreach ($element as $page => $url)
                    @if ($page == $paginator->currentPage())
                        <li class="active btn-primary page-link" tabindex="-1"><span>{{ $page }}</span></li>
                    @else
                        <li><a class="page-link" tabindex="-1" href="{{ $url }}">{{ $page }}</a>
                        </li>
                    @endif
                @endforeach
            @endif
        @endforeach

        @if ($paginator->hasMorePages())
            <li>
                <a class="page-link" tabindex="-1" href="{{ $paginator->nextPageUrl() }}" rel="next">
                    <span aria-hidden="true">&raquo;</span>
                </a>
            </li>
        @endif
    </ul>
@endif
