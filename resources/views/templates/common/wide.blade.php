@extends('layouts.layout')

@section('content')
    <div class="container container--wide">
        <h1>{{ $page->title }}</h1>

        @if ($page->subtitle)
            <p class="subtitle">{{ $page->subtitle }}</p>
        @endif

        @if ($page->img)
            <img src="{{ Storage::url($page->img) }}" alt="{{ $page->title }}" class="img-wide">
        @endif

        @if ($page->short_desc)
            <div class="short-desc">{!! $page->short_desc !!}</div>
        @endif

        @if ($page->html)
            {!! $page->html !!}
        @endif

        @if ($page->desc)
            <div class="desc">{!! $page->desc !!}</div>
        @endif

        @if ($page->img2)
            <img src="{{ Storage::url($page->img2) }}" alt="{{ $page->title }}" class="img-wide">
        @endif

        @if ($page->desc2)
            <div class="desc">{!! $page->desc2 !!}</div>
        @endif
    </div>
@endsection
