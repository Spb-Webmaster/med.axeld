<?php

declare(strict_types=1);

namespace App\View\Components\Contacts;

use App\Models\City;
use Closure;
use Illuminate\Contracts\View\View;
use Illuminate\View\Component;

class CitySelector extends Component
{
    public string $currentTitle = '';
    public string $currentPhone = '';
    /** @var \Illuminate\Database\Eloquent\Collection<int, City> */
    public $cities;

    public function __construct()
    {
        $this->cities = City::where('published', 1)
            ->orderBy('sorting')
            ->get();

        $first = $this->cities->first();

        $this->currentTitle = session('city_title', $first?->title ?? '');
        $this->currentPhone = session('city_phone', $first?->phone ?? '');
    }

    public function render(): View|Closure|string
    {
        return view('components.contacts.city-selector');
    }
}
