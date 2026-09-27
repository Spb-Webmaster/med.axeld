@extends('layouts.layout')
<x-seo.meta title="Личный кабинет" description="Личный кабинет"/>

{{-- Главная кабинета: свои данные.
     Контроллер — App\Http\Controllers\CabinetController::index.
     Оболочка с внутренним меню — x-cabinet.shell. --}}
@section('content')
    <x-cabinet.shell title="Личный кабинет" lead="Здравствуйте, {{ $user->name }}!">

        <div class="cab-card">
            <div class="cab-card__head">
                <h2 class="cab-card__title">Личные данные</h2>
                <a class="btn btn_outline btn_sm" href="{{ route('cabinet.edit') }}">Изменить</a>
            </div>

            <dl class="cab-list">
                <div>
                    <dt>ФИО</dt>
                    <dd>{{ $user->name }}</dd>
                </div>
                <div>
                    <dt>Email</dt>
                    <dd>{{ $user->email }}</dd>
                </div>
                <div>
                    <dt>Телефон</dt>
                    <dd>{{ $user->phone ? format_phone($user->phone) : '—' }}</dd>
                </div>
                <div>
                    <dt>Дата рождения</dt>
                    <dd>{{ $user->birth_date?->format('d.m.Y') ?: '—' }}</dd>
                </div>
                <div>
                    <dt>В системе с</dt>
                    <dd>{{ $user->created_at?->format('d.m.Y') }}</dd>
                </div>
            </dl>
        </div>

        <div class="cab-card">
            <div class="cab-card__head">
                <h2 class="cab-card__title">Пароль</h2>
                {{-- Якорь #password — настройки откроются сразу на блоке смены пароля,
                     а не сверху формы (см. id в cabinet/edit.blade.php) --}}
                <a class="btn btn_outline btn_sm" href="{{ route('cabinet.edit') }}#password">Изменить</a>
            </div>

            <p class="cab-note">
                Пароль хранится в зашифрованном виде и нигде не показывается.
                Сменить его можно в настройках — там потребуется ввести текущий пароль.
                Если вы его не помните, новый выдаст администратор.
            </p>
        </div>

    </x-cabinet.shell>
@endsection
