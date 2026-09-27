<?php

declare(strict_types=1);

namespace App\MoonShine\Pages\Pages;

use App\Models\Setting;
use Illuminate\Http\Request;
use MoonShine\Crud\JsonResponse;
use MoonShine\Laravel\Pages\Page;
use MoonShine\Support\Attributes\AsyncMethod;
use MoonShine\Support\Enums\ToastType;
use MoonShine\TinyMce\Fields\TinyMce;
use MoonShine\UI\Components\Collapse;
use MoonShine\UI\Components\FormBuilder;
use MoonShine\UI\Components\Layout\Box;
use MoonShine\UI\Components\Layout\Column;
use MoonShine\UI\Components\Layout\Divider;
use MoonShine\UI\Components\Layout\Grid;
use MoonShine\UI\Components\Tabs;
use MoonShine\UI\Components\Tabs\Tab;
use MoonShine\UI\Fields\ID;
use MoonShine\UI\Fields\Json;
use App\Enums\Pages\PageTemplate;
use App\Enums\Resources\TeaserTemplate;
use MoonShine\UI\Fields\Select;
use MoonShine\UI\Fields\Switcher;
use MoonShine\UI\Fields\Text;
use MoonShine\UI\Fields\Textarea;

class NewsPage extends Page
{
    public function getTitle(): string
    {
        return 'Новости';
    }

    private function getSetting(): Setting
    {
        return Setting::getGroup('poleznoe_novosti');
    }

    #[AsyncMethod]
    public function store(Request $request): JsonResponse
    {
        $setting = $this->getSetting();
        $setting->data = $request->except(['_token', '_method']);
        $setting->save();

        return JsonResponse::make()->toast('Сохранено', ToastType::SUCCESS);
    }

    private function form(): FormBuilder
    {
        return FormBuilder::make()
            ->asyncMethod('store')
            ->fill($this->getSetting()->data ?? [])
            ->fields([
                Box::make([
                    Tabs::make([
                        Tab::make('Основное', [
                            ID::make('ID'),
                            Grid::make([
                                Column::make([
                                    Box::make([
                                        Text::make('Заголовок', 'title')->required()->unescape(),
                                        Text::make('Для меню', 'menu_title')->unescape(),
                                        Textarea::make('Краткое описание', 'short_desc')->unescape(),
                                    ]),
                                ])->columnSpan(9),

                                Column::make([
                                    Box::make([
                                        Divider::make('Статус публикации'),
                                        Switcher::make('Опубликовано', 'published')->default(1),
                                        Divider::make('Шаблон вывода'),
                                        Select::make('Шаблон', 'page_template')
                                            ->options(PageTemplate::toOptions())
                                            ->default(PageTemplate::Default->value),
                                        Divider::make('Шаблон внутренних страниц'),
                                        Select::make('Анонс', 'section_template')
                                            ->options(TeaserTemplate::toOptions())
                                            ->default(TeaserTemplate::Default->value),
                                    ]),
                                ])->columnSpan(3),
                            ]),
                        ])->icon('document-text'),

                        Tab::make('Медиа', [
                            Grid::make([
                                Column::make([
                                    Divider::make('НЕ пишите заголовок h1 в описании'),
                                    TinyMce::make('Описание', 'desc'),
                                ])->columnSpan(12),
                            ]),
                        ])->icon('photo'),

                        Tab::make('SEO', [
                            Text::make('Мета-заголовок', 'metatitle')->unescape(),
                            Text::make('Мета-описание', 'description')->unescape(),
                            Text::make('Ключевые слова', 'keywords')->unescape(),
                            Textarea::make('Скрипт', 'script')->unescape(),
                        ])->icon('magnifying-glass'),

                        Tab::make('Дополнительно', [
                            Column::make([
                                Collapse::make('Вопрос/Ответ', [
                                    Json::make('', 'faq')->fields([
                                        Text::make('Заголовок', 'title'),
                                        Json::make('Опции', 'options')->fields([
                                            Textarea::make('Вопрос', 'question'),
                                            TinyMce::make('Ответ', 'answer'),
                                        ])->vertical()->creatable(limit: 50)->removable(),
                                    ])->vertical()->creatable(limit: 1)->removable(),
                                ]),
                            ]),
                        ])->icon('adjustments-horizontal'),
                    ]),
                ]),
            ])
            ->submit('Сохранить', ['class' => 'btn-primary']);
    }

    protected function components(): iterable
    {
        yield $this->form();
    }
}
