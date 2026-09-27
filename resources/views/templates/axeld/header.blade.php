{{-- Шапка сайта. Пока в ней только логотип и вход в личный кабинет —
     навигация появится вместе с разделами сайта.
     Стили — resources/css/templates/axeld/header.scss --}}
<header class="site-header">
    <div class="site-header__inner">
        {{-- Знак — картинкой, название — шрифтом.
             У картинки пустой alt и aria-hidden: название стоит рядом текстом,
             иначе скринридер прочитал бы его дважды.
             width/height по исходнику 146x146 — чтобы шапка не дёргалась при загрузке. --}}
        <a class="site-header__logo" href="{{ route('home') }}">
            <img class="site-header__mark" src="{{ asset('images/med_2.png') }}"
                 alt="" aria-hidden="true" width="146" height="146">

            <span class="site-header__name">
                <span>Дневник</span>
                <span>давления</span>
            </span>
        </a>

        <x-cabinet.enter/>
    </div>
</header>
