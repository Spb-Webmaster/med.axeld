@props([
    'items'  => [],
    'dir'    => 'content',
    'group'  => 'gallery',
    'thumb'  => '400x400',
])

@if(!empty($items))
    <div class="gallery-grid">
        @foreach($items as $photo)
            @if(!empty($photo['image']))
                <a class="gallery-grid__item"
                   href="{{ Storage::url($photo['image']) }}"
                   data-fancybox="{{ $group }}"
                   @if(!empty($photo['label'])) data-caption="{{ $photo['label'] }}" @endif>
                    <img class="gallery-grid__thumb"
                         loading="lazy"
                         src="{{ asset(intervention($thumb, $photo['image'], $dir)) }}"
                         alt="{{ $photo['label'] ?? '' }}">
                </a>
            @endif
        @endforeach
    </div>
@endif
