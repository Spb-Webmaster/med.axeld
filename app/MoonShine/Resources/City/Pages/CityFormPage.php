<?php

declare(strict_types=1);

namespace App\MoonShine\Resources\City\Pages;

use App\Models\City;
use App\MoonShine\Resources\City\CityResource;
use MoonShine\Contracts\Core\TypeCasts\DataWrapperContract;
use MoonShine\Contracts\UI\ComponentContract;
use MoonShine\Contracts\UI\FieldContract;
use MoonShine\Contracts\UI\FormBuilderContract;
use MoonShine\Laravel\Fields\Slug;
use MoonShine\Laravel\Pages\Crud\FormPage;
use MoonShine\Support\ListOf;
use MoonShine\TinyMce\Fields\TinyMce;
use MoonShine\UI\Components\Layout\Box;
use MoonShine\UI\Components\Layout\Column;
use MoonShine\UI\Components\Layout\Grid;
use MoonShine\UI\Components\Tabs;
use MoonShine\UI\Components\Tabs\Tab;
use MoonShine\UI\Fields\ID;
use MoonShine\UI\Fields\Number;
use MoonShine\UI\Fields\Switcher;
use MoonShine\UI\Fields\Text;
use Throwable;

/**
 * @extends FormPage<CityResource, City>
 */
final class CityFormPage extends FormPage
{
    /**
     * @return list<ComponentContract|FieldContract>
     */
    protected function fields(): iterable
    {
        return [
            Box::make([
                Tabs::make([
                    Tab::make('Основное', [
                        ID::make('ID'),
                        Grid::make([
                            Column::make([
                                Box::make([
                                    Text::make('Название', 'title')->required()->unescape(),
                                    Slug::make('Slug', 'slug')->from('title')->unique()->locked(),
                                ]),
                                Text::make('Подзаголовок', 'subtitle')->unescape(),
                            ])->columnSpan(9),

                            Column::make([
                                Box::make([
                                    Switcher::make('Опубликовано', 'published')->default(1),
                                    Number::make('Сортировка', 'sorting')->default(1),
                                ]),
                            ])->columnSpan(3),
                        ]),
                    ])->icon('map-pin'),

                    Tab::make('Контакты', [
                        Grid::make([
                            Column::make([
                                Box::make('Телефоны', [
                                    Text::make('Телефон', 'phone'),
                                    Text::make('Телефон 2', 'phone2'),
                                    Text::make('Телефон 3', 'phone3'),
                                    Text::make('Телефон 4', 'phone4'),
                                ]),
                            ])->columnSpan(6),

                            Column::make([
                                Box::make('Email', [
                                    Text::make('Email', 'email'),
                                    Text::make('Email 2', 'email2'),
                                    Text::make('Email 3', 'email3'),
                                ]),
                            ])->columnSpan(6),

                            Column::make([
                                Box::make('Адрес и координаты', [
                                    Text::make('Адрес', 'address'),
                                    Text::make('Координаты', 'coordinates'),
                                ]),
                            ])->columnSpan(12),
                        ]),
                    ])->icon('phone'),

                    Tab::make('Описание', [
                        TinyMce::make('Описание', 'desc'),
                    ])->icon('document-text'),
                ]),
            ]),
        ];
    }

    protected function buttons(): ListOf
    {
        return parent::buttons();
    }

    protected function formButtons(): ListOf
    {
        return parent::formButtons();
    }

    protected function rules(DataWrapperContract $item): array
    {
        return [];
    }

    protected function modifyFormComponent(FormBuilderContract $component): FormBuilderContract
    {
        return $component;
    }

    /**
     * @return list<ComponentContract>
     * @throws Throwable
     */
    protected function topLayer(): array
    {
        return [...parent::topLayer()];
    }

    /**
     * @return list<ComponentContract>
     * @throws Throwable
     */
    protected function mainLayer(): array
    {
        return [...parent::mainLayer()];
    }

    /**
     * @return list<ComponentContract>
     * @throws Throwable
     */
    protected function bottomLayer(): array
    {
        return [...parent::bottomLayer()];
    }
}
