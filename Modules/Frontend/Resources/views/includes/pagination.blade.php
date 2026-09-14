@if ($paginator->hasPages())
    <ul class="pagination justify-content-center p-0">
        <li class="page-item {{ $paginator->onFirstPage() ? ' disabled' : '' }}">
            <a class="page-link" href="{{ $paginator->previousPageUrl() }}">@lang('pagination.previous')</a>
        </li>
        @for ($i = 1; $i <= $paginator->lastPage(); $i++)
            <li class="page-item {{ ($paginator->currentPage() == $i) ? ' active' : '' }}">
                <a class="page-link" href="{{ $paginator->url($i) }}">{{ $i }}</a>
            </li>
        @endfor
        <li class="page-item {{ $paginator->hasMorePages() ? '' : ' disabled' }}">
            <a class="page-link" href="{{ $paginator->nextPageUrl() }}" >@lang('pagination.next')</a>
        </li>
    </ul>
@endif
