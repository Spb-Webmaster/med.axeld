<?php

declare(strict_types=1);

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Http\Requests\Auth\LoginRequest;
use Illuminate\Contracts\View\View;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;

/**
 * Вход и выход пользователя сайта.
 *
 * Регистрации нет: учётные записи заводит администратор в MoonShine
 * (App\MoonShine\Resources\User\UserResource).
 */
class LoginController extends Controller
{
    public function create(): View
    {
        return view('auth.login');
    }

    public function store(LoginRequest $request): RedirectResponse
    {
        /*
         | Условия входа собирает LoginRequest::credentials() — туда же добавлен
         | published, поэтому отключённая администратором запись не пускает.
         |
         | Причину отказа не уточняем: по тексту ошибки не должно быть видно,
         | существует ли такой пользователь и не отключён ли он.
         */
        if (! auth()->attempt($request->credentials(), $request->boolean('remember'))) {
            return back()
                ->withErrors(['email' => 'Неверный email или пароль.'])
                ->onlyInput('email');
        }

        // Смена идентификатора сессии после входа — защита от session fixation
        $request->session()->regenerate();

        return redirect()->intended(route('cabinet'));
    }

    public function destroy(Request $request): RedirectResponse
    {
        auth()->logout();

        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()->route('home');
    }
}
