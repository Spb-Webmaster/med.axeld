{{-- Вход в кабинет в шапке сайта.

     Гостю показываем ссылку «Войти», авторизованному — имя со ссылкой в кабинет
     и кнопку выхода. Выход именно кнопкой в форме: маршрут logout принимает POST
     и защищён CSRF-токеном, обычная ссылка сюда не годится. --}}

@auth
    <div class="header-user">
        <a class="header-user__link" href="{{ route('cabinet') }}">
            <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor"
                 stroke-width="1.6" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">
                <path d="M20 21v-2a4 4 0 0 0-4-4H8a4 4 0 0 0-4 4v2"/>
                <circle cx="12" cy="7" r="4"/>
            </svg>
            <span>{{ auth()->user()->name }}</span>
        </a>

        <form class="header-user__logout" method="POST" action="{{ route('logout') }}">
            @csrf
            <button type="submit" class="btn-round btn-round--close" title="Выйти" aria-label="Выйти">
                <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor"
                     stroke-width="1.6" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">
                    <path d="M9 21H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h4"/>
                    <path d="M16 17l5-5-5-5M21 12H9"/>
                </svg>
            </button>
        </form>
    </div>
@else
    <a href="{{ route('login') }}" class="btn btn_outline btn_sm">
        <span>Войти</span>
        <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor"
             stroke-width="1.6" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">
            <path d="M15 3h4a2 2 0 0 1 2 2v14a2 2 0 0 1-2 2h-4"/>
            <path d="M10 17l5-5-5-5M15 12H3"/>
        </svg>
    </a>
@endauth
