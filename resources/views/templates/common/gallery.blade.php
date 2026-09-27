@extends('layouts.layout')

@section('content')
    <div class="container">
        <h1>{{ $page->title }}</h1>

        @if ($page->subtitle)
            <p class="subtitle">{{ $page->subtitle }}</p>
        @endif

        @if ($page->short_desc)
            <div class="short-desc">{!! $page->short_desc !!}</div>
        @endif

        @if ($page->gallery)
            <div class="gallery">
                @foreach ($page->gallery as $item)
                    <div class="gallery__item">
                        @if (!empty($item['image']))
                            <img src="{{ Storage::url($item['image']) }}" alt="{{ $item['label'] ?? '' }}">
                        @endif
                        @if (!empty($item['label']))
                            <p>{{ $item['label'] }}</p>
                        @endif
                    </div>
                @endforeach
            </div>
        @endif

        @if ($page->desc)
            <div class="desc">{!! $page->desc !!}</div>
        @endif
    </div>
@endsection
