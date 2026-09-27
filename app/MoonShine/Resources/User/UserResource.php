<?php

declare(strict_types=1);

namespace App\MoonShine\Resources\User;

use App\Models\User;
use App\MoonShine\Resources\User\Pages\UserFormPage;
use App\MoonShine\Resources\User\Pages\UserIndexPage;
use MoonShine\Laravel\Resources\ModelResource;
use MoonShine\MenuManager\Attributes\Group;
use MoonShine\MenuManager\Attributes\Order;
use MoonShine\Support\Attributes\Icon;
use MoonShine\Support\Enums\SortDirection;
use MoonShine\UI\Fields\Switcher;
use MoonShine\UI\Fields\Text;

/**
 * Пользователи сайта — те, кто входит в личный кабинет.
 *
 * Это не администраторы панели: у MoonShine своя таблица moonshine_users
 * и свой ресурс. Регистрации на сайте нет, учётные записи заводятся здесь.
 *
 * @extends ModelResource<User, UserIndexPage, UserFormPage>
 */
#[Icon('user-group')]
#[Group('Пользователи', 'users')]
#[Order(5)]
class UserResource extends ModelResource
{
    protected string $model = User::class;

    protected string $column = 'name';

    protected string $sortColumn = 'created_at';

    // Свежие записи сверху
    protected SortDirection $sortDirection = SortDirection::DESC;

    protected int $itemsPerPage = 50;

    public function getTitle(): string
    {
        return 'Пользователи сайта';
    }

    protected function pages(): array
    {
        return [
            UserIndexPage::class,
            UserFormPage::class,
        ];
    }

    protected function search(): array
    {
        return ['id', 'name', 'email', 'phone'];
    }

    protected function filters(): iterable
    {
        return [
            Text::make('ФИО', 'name'),
            Text::make('Email', 'email'),
            Text::make('Телефон', 'phone'),
            Switcher::make('Доступ разрешён', 'published'),
        ];
    }
}
