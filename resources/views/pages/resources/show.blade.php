@extends('layouts.layout')
<x-seo.meta
    title="{{ $item->metatitle ?: $item->title }}"
    description="{{ $item->description }}"
    keywords="{{ $item->keywords }}"
/>
@section('content')

    <div class="content_page">
        <div class="block">

            <div class="block_content__breadcrumbs">{{ Breadcrumbs::render(Route::currentRouteName(), $item) }}</div>
            @if($item->title)
                <h1 class="h1">{{ $item->title }}</h1>
            @endif
            @if($item->subtitle)
                <div class="content_page__subtitle">{{ $item->subtitle }}</div>
            @endif

            @include($item->template->view($resource), ['item' => $item])

        </div>
    </div>

@endsection
