@props([
    'src'    => null,
    'poster' => null,
    'url'    => null,
    'class'  => '',
])

<div class="media-video {{ $class }}">
    @if($url)
        @php
            preg_match('/(?:youtube\.com\/(?:watch\?v=|embed\/)|youtu\.be\/)([a-zA-Z0-9_-]{11})/', $url, $matches);
            $youtubeId = $matches[1] ?? null;
        @endphp
        @if($youtubeId)
            <div class="media-video__youtube-wrap">
                <iframe class="media-video__youtube"
                        src="https://www.youtube.com/embed/{{ $youtubeId }}"
                        allowfullscreen
                        frameborder="0"
                        allow="accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture">
                </iframe>
            </div>
        @endif
    @elseif($src)
        <video class="media-video__player" controls preload="metadata"
               @if($poster) poster="{{ Storage::url($poster) }}" @endif>
            <source src="{{ Storage::url($src) }}">
        </video>
    @endif
</div>
