<?php

namespace App\View\Components\Menu;

use Closure;
use Illuminate\Contracts\View\View;
use Illuminate\View\Component;

class TopMenu extends Component
{
    /**
     * Create a new component instance.
     */
    public array $navItems;

    public function __construct()
    {
        $this->navItems = [
            ['label' => 'Главная',    'route' => 'home',       'pattern' => 'home'],
            ['label' => 'О нас',      'route' => null,         'pattern' => 'about*'],
            ['label' => 'Обучение',   'route' => null,         'pattern' => 'training*'],
            ['label' => 'Консалтинг', 'route' => null,         'pattern' => 'consulting*'],
            ['label' => 'Online',     'route' => null,         'pattern' => 'remote*'],
            ['label' => 'Расписание', 'route' => null,         'pattern' => 'schedule*'],
            ['label' => 'Полезное',   'route' => null,         'pattern' => 'resources*'],
            ['label' => 'Контакты',   'route' => null,         'pattern' => 'contacts*'],
        ];
    }

    /**
     * Get the view / contents that represent the component.
     */
    public function render(): View|Closure|string
    {
        return view('components.menu.top-menu');
    }
}
