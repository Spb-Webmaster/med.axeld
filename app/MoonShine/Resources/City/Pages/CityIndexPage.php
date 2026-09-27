<?php

declare(strict_types=1);

namespace App\MoonShine\Resources\City\Pages;

use App\MoonShine\Resources\City\CityResource;
use MoonShine\Contracts\UI\FieldContract;
use MoonShine\Laravel\Pages\Crud\IndexPage;
use MoonShine\UI\Fields\ID;
use MoonShine\UI\Fields\Switcher;
use MoonShine\UI\Fields\Text;

/**
 * @extends IndexPage<CityResource>
 */
final class CityIndexPage extends IndexPage
{
    /**
     * @return list<FieldContract>
     */
    protected function fields(): iterable
    {
        return [
            ID::make(),
            Text::make('Город', 'title')->unescape()->updateOnPreview(),
            Text::make('Телефон', 'phone'),
            Text::make('Email', 'email'),
            Text::make('Адрес', 'address'),
            Switcher::make('Опубликовано', 'published')->updateOnPreview(),
            Text::make('Сортировка', 'sorting')->updateOnPreview(),
        ];
    }
}
