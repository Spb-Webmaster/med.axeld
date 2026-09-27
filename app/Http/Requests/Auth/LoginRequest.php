<?php

declare(strict_types=1);

namespace App\Http\Requests\Auth;

use Illuminate\Foundation\Http\FormRequest;

/**
 * Вход в личный кабинет по email.
 *
 * Пару сверяет LoginController через credentials(): в условия добавляется
 * published — отключённая администратором запись в кабинет не пускает.
 */
class LoginRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    protected function prepareForValidation(): void
    {
        $this->merge(['email' => trim((string) $this->input('email'))]);
    }

    /**
     * @return array<string, array<int, mixed>>
     */
    public function rules(): array
    {
        return [
            'email'    => ['required', 'string', 'email'],
            'password' => ['required', 'string'],
        ];
    }

    /**
     * Данные для auth()->attempt().
     *
     * @return array<string, mixed>
     */
    public function credentials(): array
    {
        return [
            'email'     => $this->validated('email'),
            'password'  => $this->validated('password'),
            'published' => true,
        ];
    }

    /**
     * @return array<string, string>
     */
    public function messages(): array
    {
        return [
            'email.required'    => 'Введите email.',
            'email.email'       => 'Введите корректный email.',
            'password.required' => 'Введите пароль.',
        ];
    }
}
