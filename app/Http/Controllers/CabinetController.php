<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use App\Http\Requests\Cabinet\PasswordUpdateRequest;
use App\Http\Requests\Cabinet\ProfileUpdateRequest;
use Illuminate\Contracts\View\View;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;

/**
 * Личный кабинет: просмотр своих данных, их изменение и смена пароля.
 *
 * Доступ — только авторизованным (middleware auth на маршрутах).
 * Пока это весь кабинет: дневник давления и прочие разделы появятся позже.
 */
class CabinetController extends Controller
{
    public function index(Request $request): View
    {
        return view('cabinet.index', [
            'user' => $request->user(),
        ]);
    }

    public function edit(Request $request): View
    {
        return view('cabinet.edit', [
            'user' => $request->user(),
        ]);
    }

    public function update(ProfileUpdateRequest $request): RedirectResponse
    {
        $request->user()->update($request->validated());

        flash()->info('Данные сохранены.');

        return redirect()->route('cabinet');
    }

    public function updatePassword(PasswordUpdateRequest $request): RedirectResponse
    {
        $password = $request->validated('password');

        $request->user()->update(['password' => $password]);

        /*
         | Остальные сессии этого пользователя закрываем: если пароль меняют
         | из-за подозрения на чужой доступ, смена должна выкинуть чужого.
         | Текущая сессия остаётся активной.
         */
        auth()->logoutOtherDevices($password);

        flash()->info('Пароль изменён.');

        return redirect()->route('cabinet.edit');
    }
}
