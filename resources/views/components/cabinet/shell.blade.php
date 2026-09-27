{{-- Оболочка страниц личного кабинета: слева внутреннее меню, справа содержимое.

     Формы внутри остаются узкими — длинную строку ввода читать неудобно,
     за ширину полей отвечает .cab-card.

     Пример:
     <x-cabinet.shell title="Личный кабинет" lead="Здравствуйте, Иван!">
         ... карточки .cab-card ...
     </x-cabinet.shell> --}}
@props([
    'title' => '',
    'lead' => '',
])

<div class="cab-page">
    <div class="cab-page__inner">
        <x-cabinet.nav/>

        <section class="cab-content">
            <div class="cab-content__sub">
                @if ($title)
                    <h1 class="cab-content__title">{{ $title }}</h1>
                @endif

                @if ($lead)
                    <p class="cab-content__lead">{{ $lead }}</p>
                @endif
            </div>

            {{ $slot }}
        </section>
    </div>
</div>
