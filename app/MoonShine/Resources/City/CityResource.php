<?php

declare(strict_types=1);

namespace App\MoonShine\Resources\City;

use App\Models\City;
use App\MoonShine\Resources\City\Pages\CityFormPage;
use App\MoonShine\Resources\City\Pages\CityIndexPage;
use MoonShine\Laravel\Resources\ModelResource;
use MoonShine\MenuManager\Attributes\Group;
use MoonShine\MenuManager\Attributes\Order;
use MoonShine\Support\Attributes\Icon;

/**
 * @extends ModelResource<City, CityIndexPage, CityFormPage>
 */
#[Icon('map-pin')]
#[Group('Контент', 'document-text')]
#[Order(13)]
class CityResource extends ModelResource
{
    protected string $model = City::class;

    protected string $column = 'title';
    protected bool $simplePaginate = true;
    protected string $sortColumn = 'sorting';

    public function getTitle(): string
    {
        return 'Города';
    }

    protected function pages(): array
    {
        return [
            CityIndexPage::class,
            CityFormPage::class,
        ];
    }

    protected function search(): array
    {
        return [
            'id',
            'title',
            'slug',
        ];
    }
}
