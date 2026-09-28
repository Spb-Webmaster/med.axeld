{{-- Выгрузка дневника в Excel.

     Три способа задать период: произвольный диапазон, целый год, отдельный месяц.
     Нужные поля показывает resources/js/include/site/bp-export.js,
     проверяет их App\Http\Requests\Cabinet\BloodPressureExportRequest.

     Форма уходит обычным GET — браузер сам скачивает файл, страница не перезагружается. --}}
@php
    $minYear = (int) config('site.bp_calendar.min_year', 2026);
    $today   = now();

    // Годы от начала ведения дневника до текущего: будущее выгружать нечего
    $years = collect(range($minYear, max($minYear, (int) $today->format('Y'))))
        ->mapWithKeys(fn (int $year) => [$year => $year])
        ->all();

    $months = [
        1 => 'Январь', 2 => 'Февраль', 3 => 'Март', 4 => 'Апрель',
        5 => 'Май', 6 => 'Июнь', 7 => 'Июль', 8 => 'Август',
        9 => 'Сентябрь', 10 => 'Октябрь', 11 => 'Ноябрь', 12 => 'Декабрь',
    ];
@endphp

<div class="cab-card bp-export" id="bpExport">
    <h2 class="cab-card__title">Выгрузка в Excel</h2>

    <p class="cab-note">
        Выберите период — файл со списком замеров скачается сразу.
    </p>

    <form class="bp-export__form" method="GET" action="{{ route('cabinet.pressure.export') }}">
        <div class="bp-export__row">
            <x-form.select name="mode" label="Период" id="bpExportMode"
                           :options="['range' => 'Диапазон дат', 'year' => 'Год', 'month' => 'Месяц']"
                           :selected="old('mode', 'range')"/>
        </div>

        {{-- Блоки полей переключает скрипт: показан ровно один, остальные скрыты
             и обесточены через disabled, чтобы их значения не уходили на сервер --}}
        <div class="bp-export__row" data-export-fields="range">
            <x-form.form-date name="from" label="С какого числа"
                              :value="old('from', $today->copy()->startOfMonth()->format('Y-m-d'))"
                              :max="$today->format('Y-m-d')"/>

            <x-form.form-date name="to" label="По какое число"
                              :value="old('to', $today->format('Y-m-d'))"
                              :max="$today->format('Y-m-d')"/>
        </div>

        <div class="bp-export__row" data-export-fields="year" hidden>
            <x-form.select name="year" label="Год" :options="$years"
                           :selected="old('year', $today->format('Y'))"/>
        </div>

        <div class="bp-export__row" data-export-fields="month" hidden>
            <x-form.select name="month" label="Месяц" :options="$months"
                           :selected="old('month', $today->format('n'))"/>

            <x-form.select name="year" label="Год" :options="$years"
                           :selected="old('year', $today->format('Y'))"/>
        </div>

        <div class="input-button">
            <x-form.form-button type="submit">Скачать файл</x-form.form-button>
        </div>
    </form>
</div>
