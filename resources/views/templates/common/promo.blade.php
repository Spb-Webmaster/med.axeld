@extends('layouts.layout')

@section('content')
    @if ($page->img)
        <div class="promo-hero" style="background-image: url('{{ Storage::url($page->img) }}')">
            <div class="promo-hero__inner">
                <h1>{{ $page->title }}</h1>
                @if ($page->subtitle)
                    <p>{{ $page->subtitle }}</p>
                @endif
            </div>
        </div>
    @else
        <div class="container">
            <h1>{{ $page->title }}</h1>
            @if ($page->subtitle)
                <p class="subtitle">{{ $page->subtitle }}</p>
            @endif
        </div>
    @endif

    <div class="container">
        @if ($page->short_desc)
            <div class="short-desc">{!! $page->short_desc !!}</div>
        @endif

        @if ($page->desc)
            <div class="desc">{!! $page->desc !!}</div>
        @endif
    </div>
@endsection
