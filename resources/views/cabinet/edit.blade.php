@extends('layouts.layout')
<x-seo.meta title="Настройки профиля" description="Изменение своих данных в личном кабинете"/>

{{-- Две отдельные формы: данные профиля и смена пароля. Разделены намеренно —
     чтобы для правки телефона не требовалось трогать пароль.
     Контроллер — App\Http\Controllers\CabinetController::update / updatePassword. --}}
@section('content')
    <x-cabinet.shell title="Настройки профиля"
                     lead="Здесь меняются данные вашей учётной записи.">

        <div class="cab-card">
            <h2 class="cab-card__title">Личные данные</h2>

            <form class="cab-form" method="POST" action="{{ route('cabinet.update') }}">
                @csrf
                @method('PUT')

                <x-form.form-input name="name" label="ФИО" autocomplete="name"
                                   value="{{ old('name', $user->name) }}" :required="true"/>

                {{-- Короткие поля ставим парами: так форма ниже и не тянется одной колонкой --}}
                <div class="cab-row">
                    <x-form.form-input name="email" type="email" label="Email" autocomplete="email"
                                       value="{{ old('email', $user->email) }}" :required="true"/>

                    <x-form.form-input name="phone" type="tel" label="Телефон" autocomplete="tel"
                                       value="{{ old('phone', format_phone($user->phone)) }}"/>
                </div>

                {{-- Нижней границы у даты рождения нет: дневник ведут и за ребёнка.
                     Верхняя — сегодня, по ней календарь строит список лет назад. --}}
                <x-form.form-date name="birth_date" label="Дата рождения"
                                  :value="$user->birth_date?->format('Y-m-d')"
                                  :max="now()->format('Y-m-d')"/>

                <div class="input-button">
                    <x-form.form-button :block="true">Сохранить</x-form.form-button>
                </div>
            </form>
        </div>

        {{-- id нужен для перехода с карточки «Пароль» в кабинете: ссылка ведёт
             на /cabinet/setting#password, и страница открывается сразу на этом блоке --}}
        <div class="cab-card" id="password">
            <h2 class="cab-card__title">Смена пароля</h2>

            <p class="cab-note">
                Текущий пароль нужен, чтобы сменить его мог только владелец учётной записи.
            </p>

            <form class="cab-form" method="POST" action="{{ route('cabinet.password') }}">
                @csrf
                @method('PUT')

                <x-form.form-input name="current_password" type="password" label="Текущий пароль"
                                   autocomplete="current-password" :required="true"/>

                <x-form.form-input name="password" type="password" label="Новый пароль"
                                   autocomplete="new-password" :required="true"/>

                <x-form.form-input name="password_confirmation" type="password" label="Повторите новый пароль"
                                   autocomplete="new-password" :required="true"/>

                <div class="input-button">
                    <x-form.form-button :block="true">Изменить пароль</x-form.form-button>
                </div>
            </form>
        </div>

    </x-cabinet.shell>
@endsection
