{{-- Поле даты с календарём.

     Видимое поле readonly и показывает дату как дд.мм.гггг, на сервер уходит
     скрытое поле с тем же name в формате Y-m-d — проверка на сервере
     («date_format:Y-m-d») остаётся без изменений.

     Оживляет resources/js/include/datepicker/calendar.js,
     стили — resources/css/components/form/calendar.scss.

     Границы: min/max — даты Y-m-d; min-field/max-field — имя другого поля формы,
     чьё значение становится границей (период «от — до»).

     Пример: <x-form.date-picker name="birth_date" :max="now()->format('Y-m-d')"/> --}}
@props([
    'name',
    'id' => null,
    'label' => null,
    'value' => null,
    'placeholder' => 'дд.мм.гггг',
    'min' => null,
    'max' => null,
    'minField' => null,
    'maxField' => null,
    'required' => false,
])

@php
    $date = null;

    if (filled($value)) {
        try {
            $date = \Illuminate\Support\Carbon::parse($value);
        } catch (\Throwable) {
            // Мусор в значении не должен ронять страницу — показываем поле пустым
            $date = null;
        }
    }

    $iso   = $date?->format('Y-m-d') ?? '';
    $human = $date?->format('d.m.Y') ?? '';
    $id    = $id ?: 'date-' . \Illuminate\Support\Str::random(6);
@endphp

<div {{ $attributes->merge(['class' => 'date-field']) }}>
    @if($label)
        <label class="date-field__label" for="{{ $id }}">{{ $label }}</label>
    @endif

    {{-- Обёртка намеренно span, а не label: клик по дню внутри label браузер
         повторяет на самом поле, из-за чего календарь закрывался и открывался снова --}}
    <span class="date-wrap" data-date-picker
          @if($min) data-min="{{ $min }}" @endif
          @if($max) data-max="{{ $max }}" @endif
          @if($minField) data-min-field="{{ $minField }}" @endif
          @if($maxField) data-max-field="{{ $maxField }}" @endif>

        <svg class="date-ico" width="16" height="16" viewBox="0 0 16 16" fill="none" stroke="currentColor"
             stroke-width="1.4" stroke-linecap="round" aria-hidden="true">
            <rect x="1.5" y="2.5" width="13" height="12" rx="2"/>
            <path d="M5 1.5v2M11 1.5v2M1.5 6.5h13"/>
        </svg>

        <input class="date-input" type="text" id="{{ $id }}" value="{{ $human }}"
               placeholder="{{ $placeholder }}" readonly data-date-display>

        <input type="hidden" name="{{ $name }}" value="{{ $iso }}" data-date-value
               @if($required) data-required @endif>

        <span class="cal-pop" data-calendar>
            <span class="cal-header">
                <button type="button" class="cal-nav" data-cal-prev aria-label="Предыдущий месяц">‹</button>
                <span class="cal-title">
                    <span class="cal-month-lbl" data-cal-label></span>
                    {{-- Год выбирается напрямую; список лет строит calendar.js по границам поля --}}
                    <select class="cal-year" data-cal-year aria-label="Год"></select>
                </span>
                <button type="button" class="cal-nav" data-cal-next aria-label="Следующий месяц">›</button>
            </span>

            <span class="cal-grid" data-cal-grid></span>
        </span>
    </span>

    {{ $slot }}
</div>
