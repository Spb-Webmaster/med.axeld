<?php

declare(strict_types=1);

namespace App\Http\Requests\Cabinet;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rules\Password;

/**
 * Смена пароля в кабинете.
 *
 * Текущий пароль спрашиваем: восстановления пароля по почте на сайте нет,
 * регистрации тоже — если к открытой сессии кто-то подсел, подтверждение
 * старым паролем не даёт молча забрать учётную запись.
 */
class PasswordUpdateRequest extends FormRequest
{
    public function authorize(): bool
    {
        return auth()->check();
    }

    /**
     * @return array<string, array<int, mixed>>
     */
    public function rules(): array
    {
        return [
            'current_password' => ['required', 'string', 'current_password'],
            'password'         => ['required', 'string', Password::default(), 'confirmed', 'different:current_password'],
        ];
    }

    /**
     * @return array<string, string>
     */
    public function messages(): array
    {
        return [
            'current_password.required'         => 'Введите текущий пароль.',
            'current_password.current_password' => 'Текущий пароль указан неверно.',
            'password.required'                 => 'Введите новый пароль.',
            'password.confirmed'                => 'Пароли не совпадают.',
            'password.different'                => 'Новый пароль должен отличаться от текущего.',
        ];
    }
}
