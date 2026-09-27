@props([
    'page',
    'items',
])
@php
    $currentPage = $items->currentPage();
    $suffix      = $currentPage > 1 ? ' — Страница ' . $currentPage : '';

    $baseTitle       = $page->metatitle ?: $page->title ?: '';
    $baseDescription = $page->description ?: '';
    $baseKeywords    = $page->keywords ?: '';
@endphp
<x-seo.meta
    title="{{ $baseTitle ? $baseTitle . $suffix : '' }}"
    description="{{ $baseDescription ? $baseDescription . $suffix : '' }}"
    keywords="{{ $baseKeywords ? $baseKeywords . $suffix : '' }}"
/>
