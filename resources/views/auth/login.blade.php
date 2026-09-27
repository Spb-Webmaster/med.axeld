@extends('layouts.layout')
<x-seo.meta title="Вход в личный кабинет" description="Вход в личный кабинет"/>

{{-- Вход. Контроллер — App\Http\Controllers\Auth\LoginController,
     проверка — App\Http\Requests\Auth\LoginRequest.
     Стили — resources/css/pages/auth.scss.

     Ссылок «Регистрация» и «Забыли пароль?» нет намеренно: учётные записи
     заводит администратор в MoonShine, он же выдаёт и меняет пароль. --}}
@section('content')
    <div class="auth-page">
        <div class="auth-card">
            <h1 class="auth-card__title">Вход в личный кабинет</h1>
            <p class="auth-card__lead">Войдите, чтобы видеть и менять свои данные</p>

            <form method="POST" action="{{ route('login.store') }}">
                @csrf

                <x-form.form-input name="email" type="email" label="Email" autocomplete="username"
                                   value="{{ old('email') }}" :required="true" :autofocus="true"/>

                <x-form.form-input name="password" type="password" label="Пароль"
                                   autocomplete="current-password" :required="true"/>

                <label class="auth-remember">
                    <input type="checkbox" name="remember" value="1" @checked(old('remember'))>
                    <span>Запомнить меня</span>
                </label>

                <div class="input-button">
                    <x-form.form-button :block="true">Войти</x-form.form-button>
                </div>

                <p class="auth-note">
                    Нет доступа? Учётные записи заводит администратор — обратитесь к нему за логином и паролем.
                </p>
            </form>
        </div>
    </div>
@endsection
