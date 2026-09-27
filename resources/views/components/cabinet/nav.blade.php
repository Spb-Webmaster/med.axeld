{{-- Внутреннее меню личного кабинета.

     Активный пункт подсвечивается фоном (цветных полос-акцентов у края в проекте не используем).
     Определяется по имени текущего маршрута: cabinet — профиль, cabinet.edit — настройки.

     «Дневник давления» — тот же календарь, что на главной, но открыт сразу
     на своих замерах (App\Http\Controllers\Cabinet\BloodPressureController). --}}

<nav class="cab-nav" aria-label="Разделы личного кабинета">
    <a class="cab-nav__item @if (request()->routeIs('cabinet')) is-active @endif"
       href="{{ route('cabinet') }}">
        <svg class="cab-nav__ico" width="18" height="18" viewBox="0 0 24 24" fill="none"
             stroke="currentColor" stroke-width="1.6" stroke-linecap="round" stroke-linejoin="round"
             aria-hidden="true">
            <path d="M20 21v-2a4 4 0 0 0-4-4H8a4 4 0 0 0-4 4v2"/>
            <circle cx="12" cy="7" r="4"/>
        </svg>
        <span>Профиль</span>
    </a>

    <a class="cab-nav__item @if (request()->routeIs('cabinet.edit')) is-active @endif"
       href="{{ route('cabinet.edit') }}">
        <svg class="cab-nav__ico" width="18" height="18" viewBox="0 0 24 24" fill="none"
             stroke="currentColor" stroke-width="1.6" stroke-linecap="round" stroke-linejoin="round"
             aria-hidden="true">
            <path d="M12 20h9"/>
            <path d="M16.5 3.5a2.1 2.1 0 0 1 3 3L7 19l-4 1 1-4Z"/>
        </svg>
        <span>Настройки</span>
    </a>

    <a class="cab-nav__item @if (request()->routeIs('cabinet.pressure')) is-active @endif"
       href="{{ route('cabinet.pressure') }}">
        <svg class="cab-nav__ico" width="18" height="18" viewBox="0 0 24 24" fill="none"
             stroke="currentColor" stroke-width="1.6" stroke-linecap="round" stroke-linejoin="round"
             aria-hidden="true">
            <path d="M3 12h4l3 8 4-16 3 8h4"/>
        </svg>
        <span>Дневник давления</span>
    </a>

    <form class="cab-nav__logout" method="POST" action="{{ route('logout') }}">
        @csrf
        <button type="submit" class="cab-nav__item cab-nav__item--exit">
            <svg class="cab-nav__ico" width="18" height="18" viewBox="0 0 24 24" fill="none"
                 stroke="currentColor" stroke-width="1.6" stroke-linecap="round" stroke-linejoin="round"
                 aria-hidden="true">
                <path d="M9 21H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h4"/>
                <path d="M16 17l5-5-5-5M21 12H9"/>
            </svg>
            <span>Выйти</span>
        </button>
    </form>
</nav>
