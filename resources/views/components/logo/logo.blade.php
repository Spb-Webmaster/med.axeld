@props(['prefix' => 'logo'])

@php
    $isHome = request()->routeIs('home');
@endphp

@if ($isHome)
    <div {{ $attributes->merge(['class' => $prefix]) }}>
@else
    <a href="{{ route('home') }}" {{ $attributes->merge(['class' => $prefix]) }}>
@endif

    <img src="{{ Storage::url('/images/ic_logo.svg') }}" alt="{{ config('site.constants.logo.alt') }}" class="{{ $prefix }}-icon" />
    <div class="{{ $prefix }}-text">
        <div class="{{ $prefix }}-name">{{ config('site.constants.logo.name') }} <span class="{{ $prefix }}-accent">{{ config('site.constants.logo.accent') }}</span></div>
        <div class="{{ $prefix }}-sub">{{ config('site.constants.logo.sub') }}</div>
    </div>

@if ($isHome)
    </div>
@else
    </a>
@endif
