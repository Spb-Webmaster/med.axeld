<?php

declare(strict_types=1);

namespace App\MoonShine\Resources\News;

use App\Models\News;
use App\MoonShine\Resources\News\Pages\NewsFormPage;
use App\MoonShine\Resources\News\Pages\NewsIndexPage;
use MoonShine\Laravel\Resources\ModelResource;
use MoonShine\MenuManager\Attributes\Group;
use MoonShine\MenuManager\Attributes\Order;
use MoonShine\Support\Attributes\Icon;

/**
 * @extends ModelResource<News, NewsIndexPage, NewsFormPage>
 */
#[Icon('document')]
#[Group('Контент', 'document-text')]
#[Order(13)]
class NewsResource extends ModelResource
{
    protected string $model = News::class;

    protected string $column = 'title';
    protected string $sortColumn = 'created_at';
    protected bool $simplePaginate = true;

    public function getTitle(): string
    {
        return 'Новости';
    }

    protected function pages(): array
    {
        return [
            NewsIndexPage::class,
            NewsFormPage::class,
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
