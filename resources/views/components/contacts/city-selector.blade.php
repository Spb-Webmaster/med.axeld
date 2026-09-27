<div class="header-topbar-right">

    @if($currentPhone)
        <a href="tel:{{ $currentPhone }}" class="header-topbar-phone js-city-phone">{{ format_phone($currentPhone) }}</a>
    @endif

    @if($cities->isNotEmpty())
        <div class="header-city-wrap">
            <button type="button" class="header-city js-city-toggle" aria-expanded="false" aria-haspopup="listbox">
                <span class="js-city-name">{{ $currentTitle }}</span>
                <img src="data:image/svg+xml;base64,PHN2ZyB3aWR0aD0iMTIiIGhlaWdodD0iNiIgdmlld0JveD0iMCAwIDEyIDYiIGZpbGw9Im5vbmUiIHhtbG5zPSJodHRwOi8vd3d3LnczLm9yZy8yMDAwL3N2ZyI+CjxwYXRoIGQ9Ik0xLjMzMzAxIDAuNjY2MDE2TDUuOTk5NjcgNC42NjYwMkwxMC42NjYzIDAuNjY2MDE2IiBzdHJva2U9IiM2Rjc4OTMiIHN0cm9rZS13aWR0aD0iMS4yIiBzdHJva2UtbGluZWNhcD0icm91bmQiIHN0cm9rZS1saW5lam9pbj0icm91bmQiLz4KPC9zdmc+Cg==" alt="">
            </button>

            <ul class="header-city-list js-city-list" role="listbox" aria-label="Выбор города">
                @foreach($cities as $city)
                    <li class="header-city-list__item js-city-item {{ $city->title === $currentTitle ? 'is-active' : '' }}"
                        data-title="{{ $city->title }}"
                        data-phone="{{ $city->phone }}"
                        role="option">
                        <span class="header-city-list__name">{{ $city->title }}</span>
                        @if($city->phone)
                            <span class="header-city-list__phone">{{ format_phone($city->phone) }}</span>
                        @endif
                    </li>
                @endforeach
            </ul>
        </div>
    @endif

</div>
