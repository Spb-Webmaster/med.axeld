<?php

declare(strict_types=1);

namespace App\MoonShine\Resources\User\Pages;

use App\MoonShine\Resources\User\UserResource;
use MoonShine\Contracts\UI\FieldContract;
use MoonShine\Laravel\Pages\Crud\IndexPage;
use MoonShine\UI\Fields\Date;
use MoonShine\UI\Fields\ID;
use MoonShine\UI\Fields\Switcher;
use MoonShine\UI\Fields\Text;

/**
 * @extends IndexPage<UserResource>
 */
final class UserIndexPage extends IndexPage
{
    /**
     * @return list<FieldContract>
     */
    protected function fields(): iterable
    {
        return [
            ID::make(),
            Text::make('ФИО', 'name'),
            Text::make('Email', 'email'),
            Text::make('Телефон', 'phone'),
            Switcher::make('Доступ разрешён', 'published')->updateOnPreview(),
            Date::make('Заведён', 'created_at')->format('d.m.Y'),
        ];
    }
}
