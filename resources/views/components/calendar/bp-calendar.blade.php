@props([
    'title'        => 'Дневник давления',
    'minYear'      => null,
    'yearsAhead'   => null,
    'scale'        => null,
    'measurements' => null,
])

@php
    use App\Models\BloodPressureReading;

    $minYear    = (int) ($minYear ?? config('site.bp_calendar.min_year', 2026));
    $yearsAhead = (int) ($yearsAhead ?? config('site.bp_calendar.years_ahead', 4));
    $scale      = $scale ?? config('site.bp_calendar.scale', ['low' => 100, 'mid' => 120, 'high' => 150]);
    $limits     = config('site.bp_calendar.limits');

    /*
     | Дневник на сайте один — владельца, и виден он всем, включая гостей.
     | Редактирование доступно только самому владельцу.
     */
    $measurements = $measurements ?? BloodPressureReading::forCalendar();
    $canEdit      = auth()->user()?->isDiaryOwner() ?? false;
    $isEmpty      = $measurements === [];
@endphp

<section class="bp-calendar"
         id="bpCalendar"
         data-min-year="{{ $minYear }}"
         data-years-ahead="{{ $yearsAhead }}"
         data-scale-low="{{ $scale['low'] }}"
         data-scale-mid="{{ $scale['mid'] }}"
         data-scale-high="{{ $scale['high'] }}"
         {{-- Сегодняшнюю дату берём с сервера: часы на машине пользователя
              могут быть сбиты, а запрет «не отмечать будущее» проверяется там же --}}
         data-today="{{ now()->format('Y-m-d') }}"
         data-can-edit="{{ $canEdit ? '1' : '0' }}"
         data-store-url="{{ route('cabinet.pressure.store') }}"
         data-delete-url="{{ url('cabinet/pressure') }}">
    <div class="container">

        @if($title)
            <h2 class="bp-calendar__heading">{{ $title }}</h2>
        @endif

        <header class="bp-calendar__head">

            {{-- Год: стрелки + крупная подпись --}}
            <div class="bp-calendar__yearnav">
                <button type="button" class="btn-round" id="bpPrevYear"
                        aria-label="Предыдущий год" title="Предыдущий год">
                    <svg viewBox="0 0 24 24" aria-hidden="true"><path d="M15 6l-6 6 6 6"/></svg>
                </button>

                <div class="bp-calendar__year" id="bpYearLabel" aria-live="polite">{{ $minYear }}</div>

                <button type="button" class="btn-round" id="bpNextYear"
                        aria-label="Следующий год" title="Следующий год">
                    <svg viewBox="0 0 24 24" aria-hidden="true"><path d="M9 6l6 6-6 6"/></svg>
                </button>
            </div>

            {{-- Лента годов: прокрутка мышью, колесом и стрелками --}}
            <div class="bp-calendar__years"
                 id="bpYears"
                 role="toolbar"
                 aria-orientation="horizontal"
                 aria-label="Выбор года"></div>

            <button type="button" class="btn-pill" id="bpToday">Сегодня</button>

            {{-- Гостю подсказка про запись не нужна: дневник он только читает --}}
            @if($canEdit)
                <p class="bp-calendar__hint">
                    Откройте месяц и нажмите на день, чтобы записать давление и пульс.
                    Отметить можно сегодняшний день и любой прошедший.
                </p>
            @elseif($isEmpty)
                <p class="bp-calendar__hint">Замеров пока нет.</p>
            @endif

            {{-- Легенда --}}
            <div class="bp-legend" aria-label="Легенда">
                <div class="bp-legend__item">
                    <span class="bp-legend__swatch"></span>
                    <span>Нет записи</span>
                </div>
                <div class="bp-legend__item">
                    <div class="bp-legend__scale">
                        <div class="bp-legend__bar" id="bpLegendBar"></div>
                        <div class="bp-legend__ticks">
                            <span>{{ $scale['low'] }}</span>
                            <span>{{ $scale['mid'] }}</span>
                            <span>{{ $scale['high'] }} мм рт. ст.</span>
                        </div>
                    </div>
                </div>
                <div class="bp-legend__note">Цвет отражает верхнее (систолическое) давление</div>
            </div>
        </header>

        {{-- Двенадцать карточек месяцев текущего года --}}
        <div class="bp-calendar__months" id="bpMonths"></div>
    </div>

    <script type="application/json" id="bpCalendarData">@json($measurements)</script>
</section>

{{-- Подсказка по дню --}}
<div class="bp-tooltip" id="bpTooltip" role="tooltip"></div>

{{-- Большая сетка месяца --}}
<div class="bp-modal" id="bpModal" hidden>
    <div class="bp-modal__backdrop" id="bpModalBackdrop"></div>
    <div class="bp-modal__dialog" role="dialog" aria-modal="true" aria-labelledby="bpModalTitle">
        <div class="bp-modal__header">
            <button type="button" class="btn-round" id="bpPrevYearModal"
                    aria-label="Предыдущий год" title="Предыдущий год (Shift + ←)">
                <svg viewBox="0 0 24 24" aria-hidden="true"><path d="M18 6l-6 6 6 6M11 6l-6 6 6 6"/></svg>
            </button>
            <button type="button" class="btn-round" id="bpPrevMonth"
                    aria-label="Предыдущий месяц" title="Предыдущий месяц (←)">
                <svg viewBox="0 0 24 24" aria-hidden="true"><path d="M15 6l-6 6 6 6"/></svg>
            </button>

            <h2 class="bp-modal__title" id="bpModalTitle" aria-live="polite"></h2>

            <button type="button" class="btn-round" id="bpNextMonth"
                    aria-label="Следующий месяц" title="Следующий месяц (→)">
                <svg viewBox="0 0 24 24" aria-hidden="true"><path d="M9 6l6 6-6 6"/></svg>
            </button>
            <button type="button" class="btn-round" id="bpNextYearModal"
                    aria-label="Следующий год" title="Следующий год (Shift + →)">
                <svg viewBox="0 0 24 24" aria-hidden="true"><path d="M6 6l6 6-6 6M13 6l6 6-6 6"/></svg>
            </button>
            <button type="button" class="btn-round btn-round--close" id="bpCloseModal"
                    aria-label="Закрыть" title="Закрыть (Esc)">
                <svg viewBox="0 0 24 24" aria-hidden="true"><path d="M6 6l12 12M18 6L6 18"/></svg>
            </button>
        </div>
        <div class="bp-modal__body">
            <table class="bp-bigcal">
                <thead>
                <tr id="bpBigCalHead"></tr>
                </thead>
                <tbody id="bpBigCalBody"></tbody>
            </table>
        </div>
    </div>
</div>

@if($canEdit)
    {{-- Ввод замера за день. Открывается поверх сетки месяца по нажатию на дату.
         Три поля, все целые; границы дублируются в проверке на сервере
         (App\Http\Requests\Cabinet\BloodPressureRequest). --}}
    <div class="bp-entry" id="bpEntry" hidden>
        <div class="bp-entry__backdrop" id="bpEntryBackdrop"></div>

        <form class="bp-entry__form" id="bpEntryForm" role="dialog" aria-modal="true"
              aria-labelledby="bpEntryTitle" novalidate>
            <h3 class="bp-entry__title" id="bpEntryTitle"></h3>

            <input type="hidden" name="date" id="bpEntryDate">

            <div class="bp-entry__fields">
                <label class="bp-entry__field">
                    <span>Верхнее</span>
                    <input type="number" inputmode="numeric" name="systolic" id="bpEntrySystolic"
                           min="{{ $limits['systolic']['min'] }}" max="{{ $limits['systolic']['max'] }}"
                           step="1" required>
                </label>

                <label class="bp-entry__field">
                    <span>Нижнее</span>
                    <input type="number" inputmode="numeric" name="diastolic" id="bpEntryDiastolic"
                           min="{{ $limits['diastolic']['min'] }}" max="{{ $limits['diastolic']['max'] }}"
                           step="1" required>
                </label>

                <label class="bp-entry__field">
                    <span>Пульс</span>
                    <input type="number" inputmode="numeric" name="pulse" id="bpEntryPulse"
                           min="{{ $limits['pulse']['min'] }}" max="{{ $limits['pulse']['max'] }}"
                           step="1" required>
                </label>
            </div>

            <p class="bp-entry__units">мм рт. ст., пульс — уд/мин</p>

            <div class="bp-entry__error" id="bpEntryError" role="alert" hidden></div>

            <div class="bp-entry__actions">
                <button type="submit" class="btn btn_sm" id="bpEntrySave">Сохранить</button>
                <button type="button" class="btn btn_white btn_sm" id="bpEntryCancel">Отмена</button>
                <button type="button" class="btn btn_danger btn_sm bp-entry__delete" id="bpEntryDelete" hidden>Удалить</button>
            </div>
        </form>
    </div>
@endif
