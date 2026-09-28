@extends('layouts.layout')
<x-seo.meta title="Дневник давления" description="Дневник давления: замеры давления и пульса по дням"/>

{{-- Дневник давления в кабинете.
     Контроллер — App\Http\Controllers\Cabinet\BloodPressureController::index.
     Тот же компонент стоит и на главной — там он показывает замеры
     вошедшего пользователя, а гостю остаётся пустым. --}}
@section('content')
    <x-cabinet.shell title="Дневник давления"
                     lead="Нажмите на день в открытом месяце, чтобы записать или изменить замер.">

        <x-calendar.export/>

        {{-- Заголовок гасим пустой строкой, а не null: у @props значение по умолчанию
             подставляется и вместо null, и тогда «Дневник давления» выводится дважды --}}
        <x-calendar.bp-calendar title=""/>

    </x-cabinet.shell>
@endsection
