<?php

declare(strict_types=1);

namespace App\Http\Requests\Cabinet;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

/** Изменение своих настроек в личном кабинете */
class ProfileUpdateRequest extends FormRequest
{
    public function authorize(): bool
    {
        return auth()->check();
    }

    protected function prepareForValidation(): void
    {
        // Телефон хранится одними цифрами: в поле стоит маска, разделители из неё не нужны
        if ($this->filled('phone')) {
            $this->merge(['phone' => preg_replace('/\D/', '', (string) $this->input('phone'))]);
        }
    }

    /**
     * @return array<string, array<int, mixed>>
     */
    public function rules(): array
    {
        return [
            'name' => ['required', 'string', 'min:2', 'max:255'],

            // Свой же email не считаем занятым — иначе форму нельзя сохранить без его смены
            'email' => [
                'required', 'string', 'email', 'max:255',
                Rule::unique('users', 'email')->ignore($this->user()->id),
            ],

            'phone' => ['nullable', 'digits_between:10,15'],

            /*
             | Верхняя граница даты рождения — сегодня. Нижнего возрастного порога нет:
             | дневник давления ведут и за ребёнка.
             */
            'birth_date' => ['nullable', 'date', 'before_or_equal:today', 'after:1900-01-01'],
        ];
    }

    /**
     * @return array<string, string>
     */
    public function messages(): array
    {
        return [
            'name.required'        => 'Введите ФИО.',
            'name.min'             => 'ФИО не короче двух символов.',
            'email.required'       => 'Введите email.',
            'email.email'          => 'Введите корректный email.',
            'email.unique'         => 'Такой email уже занят.',
            'phone.digits_between' => 'Проверьте номер телефона.',

            'birth_date.date'            => 'Укажите дату рождения.',
            'birth_date.before_or_equal' => 'Дата рождения не может быть в будущем.',
            'birth_date.after'           => 'Проверьте дату рождения.',
        ];
    }
}
