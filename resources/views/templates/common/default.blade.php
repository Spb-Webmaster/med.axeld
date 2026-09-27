@if($item->img)
    <div class="item-img">
        <img src="{{ Storage::url($item->img) }}" alt="{{ $item->title }}">
    </div>
@endif

@if($item->short_desc)
    <div class="short-desc">{!! $item->short_desc !!}</div>
@endif

@if($item->desc)
    <div class="desc">{!! $item->desc !!}</div>
@endif
