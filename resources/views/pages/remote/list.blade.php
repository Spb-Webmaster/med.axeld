@extends('layouts.layout')
<x-seo.meta-paginated :page="$page" :items="$items" />
@section('content')
    <div class="content_page">
        <div class="block">
            <div class="block_content__breadcrumbs">{{ Breadcrumbs::render(Route::currentRouteName()) }}</div>
            @if($page->title)
                <h1 class="h1">{{ $page->title }}</h1>
            @endif

            @foreach($items as $item)
                @include($teaser_template->view($section), ['item' => $item, 'route' => $route])
            @endforeach
            {{ $items->withQueryString()->links('pagination::default') }}

            @include($template->view($section))
        </div>
    </div>
@endsection
