@extends('layouts.layout')
{{-- Значения по умолчанию нужны для чистой базы: Setting::getGroup('home')
     заводит группу с пустым data, и без ?? страница падала бы на первом же ключе.
     Пустую строку компонент x-seo.meta заменяет на настройки из config/seo. --}}
<x-seo.meta
    title="{{ $home['metatitle'] ?? '' }}"
    description="{{ $home['description'] ?? '' }}"
    keywords="{{ $home['keywords'] ?? '' }}"
/>
@section('content')

    <x-calendar.bp-calendar/>

@endsection
