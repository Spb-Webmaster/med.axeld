<?php

declare(strict_types=1);

namespace App\Providers;

use App\MoonShine\Pages\Pages\ContactPage;
use App\MoonShine\Pages\Pages\HomePage;
use App\MoonShine\Pages\Pages\NewsPage;
use Illuminate\Support\ServiceProvider;
use App\MoonShine\Resources\City\CityResource;
use MoonShine\Contracts\Core\DependencyInjection\CoreContract;
use MoonShine\Laravel\DependencyInjection\MoonShine;
use MoonShine\Laravel\DependencyInjection\MoonShineConfigurator;
use App\MoonShine\Resources\News\NewsResource;
use App\MoonShine\Resources\MoonShineUser\MoonShineUserResource;
use App\MoonShine\Resources\MoonShineUserRole\MoonShineUserRoleResource;
use App\MoonShine\Resources\User\UserResource;

class MoonShineServiceProvider extends ServiceProvider
{
    /**
     * @param  CoreContract<MoonShineConfigurator>  $core
     */
    public function boot(CoreContract $core): void
    {
        $core
            ->resources([
                CityResource::class,
                NewsResource::class,
                UserResource::class,
                MoonShineUserResource::class,
                MoonShineUserRoleResource::class,
            ])
            ->pages([
                ...$core->getConfig()->getPages(),
                HomePage::class,
                ContactPage::class,
                NewsPage::class,
            ])
        ;
    }
}
