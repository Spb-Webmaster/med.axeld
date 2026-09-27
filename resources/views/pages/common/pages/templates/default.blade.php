@if($items->currentPage() === 1)
    @if($page->desc)
        <div class="desc">{!! $page->desc !!}</div>
    @endif
@endif
