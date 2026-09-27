@props([
    'sizes'  => [],
    'src',
    'alt'    => '',
    'class'  => 'content_page__img-main',
    'dir'    => 'content',
    'method' => 'cover',
])

@php
    $all    = collect($sizes);
    $main   = $all->last();
    $sources = $all->slice(0, -1);
    [$mainW, $mainH] = explode('x', $main);
@endphp

<picture>
    @foreach($sources as $size)
        @php [$w] = explode('x', $size); @endphp
        <source media="(max-width: {{ $w }}px)"
                srcset="{{ asset(intervention($size, $src, $dir, $method)) }}">
    @endforeach
    <img loading="lazy"
         class="{{ $class }}"
         src="{{ asset(intervention($main, $src, $dir, $method)) }}"
         alt="{{ $alt }}"
         width="{{ $mainW }}"
         height="{{ $mainH }}">
</picture>
