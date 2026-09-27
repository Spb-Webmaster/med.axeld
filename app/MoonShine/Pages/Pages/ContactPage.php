<?php

declare(strict_types=1);

namespace App\MoonShine\Pages\Pages;

use App\Models\City;
use App\Models\Setting;
use Illuminate\Http\Request;
use MoonShine\Crud\JsonResponse;
use MoonShine\Laravel\Pages\Page;
use MoonShine\Support\Attributes\AsyncMethod;
use MoonShine\Support\Enums\ToastType;
use MoonShine\TinyMce\Fields\TinyMce;
use MoonShine\UI\Components\FormBuilder;
use MoonShine\UI\Components\Layout\Box;
use MoonShine\UI\Fields\Image;
use MoonShine\UI\Fields\Select;
use MoonShine\UI\Fields\Text;

class ContactPage extends Page
{
    public function getTitle(): string
    {
        return 'Контакты';
    }

    private function getSetting(): Setting
    {
        return Setting::getGroup('contact');
    }

    #[AsyncMethod]
    public function store(Request $request): JsonResponse
    {
        $setting = $this->getSetting();
        $setting->data = $request->except(['_token', '_method', 'cities']);
        $setting->save();

        $setting->cities()->sync($request->input('cities', []));

        return JsonResponse::make()->toast('Сохранено', ToastType::SUCCESS);
    }

    private function form(): FormBuilder
    {
        $setting = $this->getSetting();

        return FormBuilder::make()
            ->asyncMethod('store')
            ->fill([
                ...($setting->data ?? []),
                'cities' => $setting->cities()->pluck('cities.id')->toArray(),
            ])
            ->fields([
                Box::make('Главный баннер', [
                    Text::make('Заголовок', 'hero_title'),
                    Text::make('Подзаголовок', 'hero_subtitle'),
                    TinyMce::make('Описание', 'hero_description'),
                    Image::make('Изображение', 'hero_image')
                        ->disk('public')
                        ->dir('pages/contacts')
                        ->allowedExtensions(['jpg', 'png', 'jpeg', 'gif', 'svg', 'webp'])
                        ->removable(),
                ]),

                Box::make('Приветственный блок', [
                    TinyMce::make('Текст', 'welcome_text'),
                ]),

                Box::make('Города', [
                    Select::make('Города', 'cities')
                        ->multiple()
                        ->searchable()
                        ->options(City::query()->orderBy('title')->pluck('title', 'id')->toArray()),
                ]),
            ])
            ->submit('Сохранить', ['class' => 'btn-primary']);
    }

    protected function components(): iterable
    {
        yield $this->form();
    }
}
