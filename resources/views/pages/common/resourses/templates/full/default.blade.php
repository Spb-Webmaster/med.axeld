<div class="content_page__default">


    @if($item->img)
        <div class="content_page__img">
            <x-picture.responsive
                :sizes="['480x250', '768x400', '1200x630']"
                :src="$item->img"
                :alt="$item->title"
            />
        </div>
    @endif

    @if($item->desc)
        <div class="content_page__desc desc">
            {!! $item->desc !!}
        </div>
    @endif

    @if($item->img2)
        <div class="content_page__img">
            <img src="{{ Storage::url($item->img2) }}" alt="{{ $item->title }}">
        </div>
    @endif
        @if($item->html)
            <div class="content_page__html">
                {!! $item->html !!}
            </div>
        @endif
    @if($item->desc2)
        <div class="content_page__desc desc">
            {!! $item->desc2 !!}
        </div>
    @endif



    @if($item->html2)
        <div class="content_page__html">
            {!! $item->html2 !!}
        </div>
    @endif

    @if(!empty($item->video))
        @php $video = $item->video[0] ?? []; @endphp
        @if(!empty($video['url']) || !empty($video['file']))
            <x-media.video
                :src="$video['file'] ?? null"
                :poster="$video['poster'] ?? null"
                :url="$video['url'] ?? null"
            />
        @endif
    @endif

    @if(!empty($item->gallery))
        <x-gallery.grid :items="$item->gallery" />
    @endif

    @if(!empty($item->files))
        <x-files.download :files="$item->files" />
    @endif

    @if($item->custom_field)
        <div class="content_page__custom">
            {!! $item->custom_field !!}
        </div>
    @endif

    @if($item->custom_field2)
        <div class="content_page__custom">
            {!! $item->custom_field2 !!}
        </div>
    @endif

    @if($item->custom_field3)
        <div class="content_page__custom">
            {!! $item->custom_field3 !!}
        </div>
    @endif

    @if(!empty($item->faq))
        <x-modules.faq :items="$item->faq" />
    @endif

</div>

@if($item->script)
    <script>{!! $item->script !!}</script>
@endif
