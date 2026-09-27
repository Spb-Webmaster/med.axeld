<?php

declare(strict_types=1);

namespace App\MoonShine\Resources\User\Pages;

use App\MoonShine\Resources\User\UserResource;
use Illuminate\Validation\Rule;
use Illuminate\Validation\Rules\Password as PasswordRule;
use MoonShine\Contracts\Core\TypeCasts\DataWrapperContract;
use MoonShine\Contracts\UI\ComponentContract;
use MoonShine\Contracts\UI\FieldContract;
use MoonShine\Laravel\Pages\Crud\FormPage;
use MoonShine\UI\Components\Collapse;
use MoonShine\UI\Fields\Date;
use MoonShine\UI\Fields\Password;
use MoonShine\UI\Fields\PasswordRepeat;
use MoonShine\UI\Fields\Switcher;
use MoonShine\UI\Fields\Text;

/**
 * Карточка пользователя сайта.
 *
 * Регистрации на сайте нет — запись заводится здесь, и здесь же администратор
 * задаёт первый пароль, чтобы передать его человеку. В открытом виде пароль
 * не хранится: после сохранения он виден только тому, кто его ввёл.
 * Забыл — администратор выдаёт новый.
 *
 * @extends FormPage<UserResource, \App\Models\User>
 */
final class UserFormPage extends FormPage
{
    /**
     * @return list<ComponentContract|FieldContract>
     */
    protected function fields(): iterable
    {
        $isEdit = $this->getResource()->getItemID() !== null;

        return [
            Text::make('ФИО', 'name')->required(),

            Text::make('Email', 'email')
                ->required()
                ->hint('Он же логин для входа в личный кабинет'),

            Text::make('Телефон', 'phone')
                ->hint('Сохраняется одними цифрами, разделители убираются автоматически'),

            Date::make('Дата рождения', 'birth_date')->format('d.m.Y'),

            Switcher::make('Доступ разрешён', 'published')
                ->default(true)
                ->hint('Снятая отметка закрывает вход в кабинет, но сохраняет запись'),

            Collapse::make($isEdit ? 'Смена пароля' : 'Пароль', [
                Password::make($isEdit ? 'Новый пароль' : 'Пароль', 'password')
                    ->customAttributes(['autocomplete' => 'new-password'])
                    ->eye(),

                PasswordRepeat::make('Повторите пароль', 'password_confirmation')
                    ->customAttributes(['autocomplete' => 'new-password'])
                    ->eye(),
            ]),
        ];
    }

    /**
     * @return array<string, array<int, mixed>>
     */
    protected function rules(DataWrapperContract $item): array
    {
        $isEdit = $item->getKey() !== null;

        return [
            'name'  => ['required', 'string', 'max:255'],
            'email' => [
                'required', 'email', 'max:255',
                Rule::unique('users', 'email')->ignoreModel($item->getOriginal()),
            ],
            'phone'      => ['nullable', 'string', 'max:20'],
            'birth_date' => ['nullable', 'date', 'before_or_equal:today'],

            // При создании пароль обязателен, при правке — только если его меняют
            'password' => [$isEdit ? 'nullable' : 'required', PasswordRule::default(), 'confirmed'],
        ];
    }
}
