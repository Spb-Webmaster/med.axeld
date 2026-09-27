<?php

declare(strict_types=1);

namespace App\MoonShine\Layouts;




use App\MoonShine\Pages\Pages\ContactPage;
use App\MoonShine\Pages\Pages\HomePage;
use App\MoonShine\Pages\Pages\NewsPage;
use App\MoonShine\Resources\City\CityResource;
use App\MoonShine\Resources\MoonShineUser\MoonShineUserResource;
use App\MoonShine\Resources\News\NewsResource;
use App\MoonShine\Resources\User\UserResource;
use MoonShine\AssetManager\Js;
use MoonShine\Laravel\Layouts\AppLayout;
use MoonShine\ColorManager\Palettes\PurplePalette;
use MoonShine\ColorManager\ColorManager;
use MoonShine\Contracts\ColorManager\ColorManagerContract;
use MoonShine\Contracts\ColorManager\PaletteContract;
use MoonShine\MenuManager\MenuDivider;
use MoonShine\MenuManager\MenuGroup;
use MoonShine\MenuManager\MenuItem;
use YuriZoom\MoonShineMediaManager\Pages\MediaManagerPage;


final class AxeldLayout extends AppLayout
{
    /**
     * @var null|class-string<PaletteContract>
     */
    protected ?string $palette = PurplePalette::class;

    protected function assets(): array
    {
        return [
            ...parent::assets(),
            new Js('/js/admin/tab-persist.js'),
        ];
    }

    protected function menu(): array
    {
        return [
            MenuGroup::make('Пользователи', [
                MenuItem::make(MoonShineUserResource::class, 'Админ', 'user'),
                MenuDivider::make(),
                // Пользователи сайта — те, кто входит в личный кабинет (таблица users).
                // Это не администраторы панели: у MoonShine своя таблица moonshine_users.
                MenuItem::make(UserResource::class, 'Пользователи сайта', 'user-group'),
            ]),


            MenuGroup::make(static fn() => __('Страницы'), [
                MenuItem::make(HomePage::class, 'Главная', 'home'),
                MenuItem::make(ContactPage::class, 'Контакты', 'phone'),
                MenuDivider::make(),
                MenuItem::make(NewsPage::class, 'Новости', 'document-text'),
            ]),


            MenuGroup::make(static fn() => __('Новости'), [
               MenuItem::make(NewsResource::class, 'Страницы', 'folder-plus'),
           ]),
/*            MenuGroup::make(static fn() => __('Страницы'), [
                MenuItem::make(HomePage::class, 'Главная страница', 'building-library'),
                MenuItem::make(PageResource::class, 'Страницы', 'check'),

            ]),*/


            MenuGroup::make(static fn() => __('Настройки'), [
                MenuItem::make(CityResource::class, 'Города', 'building-office-2'),
             /* MenuItem::make(SettingPage::class, 'Константы', 'adjustments-vertical'),*/
                MenuItem::make(MediaManagerPage::class, 'Media', 'film'),
/*                MenuItem::make(TaxationResource::class, 'Налоги', 'currency-dollar'),
                MenuGroup::make(static fn() => __('Продавцы'), [
                    MenuItem::make(LegalEntityResource::class, 'Юр.Лица'),
                    MenuItem::make(IndividualEntrepreneurResource::class, 'ИП'),
                    MenuItem::make(SelfEmployedResource::class, 'Самозанятые'),*/

                ]),




        ];
    }

    /**
     * @param ColorManager $colorManager
     */
    protected function colors(ColorManagerContract $colorManager): void
    {
        parent::colors($colorManager);

        // $colorManager->primary('#00000');
    }

    protected function getFooterCopyright(): string
    {
        return \sprintf(
            <<<'HTML'
                &copy; %d Портал
                <a href="/"
                    class="font-semibold text-primary"
                    target="_blank"
                >
                    GeneralRe
                </a>
                HTML,
            now()->year,
        );
    }

    protected function getFooterMenu(): array
    {
        return [
            config('app.url') => 'WebSite',
        ];
    }
}
