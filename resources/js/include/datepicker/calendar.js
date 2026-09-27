/**
 * Кастомный календарь для полей даты (компонент x-form.date-picker).
 *
 * Перенесён из проекта polise24. Без привязки к id: на странице может быть
 * сколько угодно полей даты, для динамически вставленных вызывается повторно.

 * Стили — resources/css/components/form/calendar.scss.
 *
 * Три режима: дни → месяцы → годы. Клик по заголовку поднимает на уровень выше,
 * поэтому до далёкого года (например, 1975) добираться не пролистыванием
 * месяцев, а двумя кликами и выбором из сетки.
 *
 * Видимое поле показывает дату как dd.mm.yyyy, в скрытом хранится Y-m-d.
 */

const MONTHS = [
    'Январь', 'Февраль', 'Март', 'Апрель', 'Май', 'Июнь',
    'Июль', 'Август', 'Сентябрь', 'Октябрь', 'Ноябрь', 'Декабрь',
];

const MONTHS_SHORT = [
    'Янв', 'Фев', 'Мар', 'Апр', 'Май', 'Июн',
    'Июл', 'Авг', 'Сен', 'Окт', 'Ноя', 'Дек',
];

const DAYS = ['Пн', 'Вт', 'Ср', 'Чт', 'Пт', 'Сб', 'Вс'];

/** Сколько лет показываем на одной странице выбора года */
const YEARS_PER_PAGE = 12;

const pad = (value) => String(value).padStart(2, '0');

const toIso = (date) => `${date.getFullYear()}-${pad(date.getMonth() + 1)}-${pad(date.getDate())}`;

const toHuman = (date) => `${pad(date.getDate())}.${pad(date.getMonth() + 1)}.${date.getFullYear()}`;

/** Дата из «Y-m-d»; всё остальное считаем пустым значением. */
function parseIso(value) {
    const match = /^(\d{4})-(\d{2})-(\d{2})$/.exec(String(value ?? '').trim());

    if (!match) {
        return null;
    }

    const date = new Date(+match[1], +match[2] - 1, +match[3]);

    return Number.isNaN(date.getTime()) ? null : date;
}

function setup(wrap) {
    const display = wrap.querySelector('[data-date-display]');
    const hidden = wrap.querySelector('[data-date-value]');
    const popup = wrap.querySelector('[data-calendar]');
    const grid = wrap.querySelector('[data-cal-grid]');
    const label = wrap.querySelector('[data-cal-label]');
    const prev = wrap.querySelector('[data-cal-prev]');
    const next = wrap.querySelector('[data-cal-next]');
    /** необязательный: селект года в шапке, если он есть в разметке */
    const yearSelect = wrap.querySelector('[data-cal-year]');

    if (!display || !hidden || !popup || !grid || !label) {
        return;
    }

    /** границы читаем каждый раз: их может двигать соседнее поле периода */
    const min = () => parseIso(wrap.dataset.min);
    const max = () => parseIso(wrap.dataset.max);

    let selected = parseIso(hidden.value);
    let viewing = selected ? new Date(selected) : new Date();
    let view = 'days';

    const isDisabled = (date) => {
        const from = min();
        const to = max();

        return Boolean((from && date < from) || (to && date > to));
    };

    /** Месяц целиком вне диапазона — выбирать нечего */
    const isMonthDisabled = (year, month) => {
        const first = new Date(year, month, 1);
        const last = new Date(year, month + 1, 0);
        const from = min();
        const to = max();

        return Boolean((to && first > to) || (from && last < from));
    };

    const isYearDisabled = (year) => {
        const first = new Date(year, 0, 1);
        const last = new Date(year, 11, 31);
        const from = min();
        const to = max();

        return Boolean((to && first > to) || (from && last < from));
    };

    const yearPageStart = (year) => year - (((year % YEARS_PER_PAGE) + YEARS_PER_PAGE) % YEARS_PER_PAGE);

    /**
     * Диапазон лет для селекта — по границам поля: обе заданы → между ними;
     * только max (дата рождения) → 100 лет назад; только min (дата поездки) →
     * 10 лет вперёд; без границ → и то и другое от текущего года.
     * Просматриваемый год всегда в списке, чтобы селект не показывал пустоту.
     */
    function yearRange() {
        const from = min();
        const to = max();
        const current = new Date().getFullYear();
        let start;
        let end;

        if (from && to) {
            start = from.getFullYear();
            end = to.getFullYear();
        } else if (to) {
            start = to.getFullYear() - 100;
            end = to.getFullYear();
        } else if (from) {
            start = from.getFullYear();
            end = from.getFullYear() + 10;
        } else {
            start = current - 100;
            end = current + 10;
        }

        const year = viewing.getFullYear();

        return [Math.min(start, year), Math.max(end, year)];
    }

    /** Список лет пересобираем только когда сдвинулись границы */
    let yearOptionsKey = '';

    function renderYearSelect() {
        if (!yearSelect) {
            return;
        }

        const [start, end] = yearRange();
        const key = `${start}-${end}`;

        if (key !== yearOptionsKey) {
            yearOptionsKey = key;

            let html = '';

            for (let year = start; year <= end; year++) {
                html += `<option value="${year}">${year}</option>`;
            }

            yearSelect.innerHTML = html;
        }

        yearSelect.value = String(viewing.getFullYear());
        /** в сетке лет селект дублирует её — прячем */
        yearSelect.hidden = view === 'years';
    }

    /** После смены года месяц может целиком выпасть из диапазона — сдвигаем к ближайшему */
    function clampViewingMonth() {
        const year = viewing.getFullYear();
        const month = viewing.getMonth();

        if (!isMonthDisabled(year, month)) {
            return;
        }

        const from = min();
        const to = max();

        if (to && new Date(year, month, 1) > to) {
            viewing = new Date(year, to.getMonth(), 1);
        } else if (from) {
            viewing = new Date(year, from.getMonth(), 1);
        }
    }

    function renderDays() {
        const year = viewing.getFullYear();
        const month = viewing.getMonth();

        /** год показывает селект, если он есть — в подписи его не дублируем */
        label.textContent = yearSelect ? MONTHS[month] : `${MONTHS[month]} ${year}`;
        grid.className = 'cal-grid';

        let html = DAYS.map((day) => `<span class="cal-dow">${day}</span>`).join('');

        /** В JS неделя начинается с воскресенья, а в календаре — с понедельника */
        let offset = new Date(year, month, 1).getDay();
        offset = offset === 0 ? 6 : offset - 1;

        for (let i = 0; i < offset; i++) {
            html += '<span class="cal-day empty"></span>';
        }

        const total = new Date(year, month + 1, 0).getDate();
        const today = new Date();

        for (let day = 1; day <= total; day++) {
            const date = new Date(year, month, day);
            const isSelected = selected && date.toDateString() === selected.toDateString();
            const isToday = date.toDateString() === today.toDateString();

            const classes = [
                'cal-day',
                isSelected ? 'selected' : '',
                isToday && !isSelected ? 'today' : '',
                isDisabled(date) ? 'disabled' : '',
            ].filter(Boolean).join(' ');

            html += `<span class="${classes}" data-day="${day}">${day}</span>`;
        }

        grid.innerHTML = html;
    }

    function renderMonths() {
        const year = viewing.getFullYear();

        label.textContent = yearSelect ? 'Месяц' : String(year);
        grid.className = 'cal-grid cal-grid--months';

        grid.innerHTML = MONTHS_SHORT.map((name, index) => {
            const classes = [
                'cal-cell',
                selected && selected.getFullYear() === year && selected.getMonth() === index ? 'selected' : '',
                isMonthDisabled(year, index) ? 'disabled' : '',
            ].filter(Boolean).join(' ');

            return `<span class="${classes}" data-month="${index}">${name}</span>`;
        }).join('');
    }

    function renderYears() {
        const start = yearPageStart(viewing.getFullYear());
        const end = start + YEARS_PER_PAGE - 1;

        label.textContent = `${start} — ${end}`;
        grid.className = 'cal-grid cal-grid--months';

        let html = '';

        for (let year = start; year <= end; year++) {
            const classes = [
                'cal-cell',
                selected && selected.getFullYear() === year ? 'selected' : '',
                isYearDisabled(year) ? 'disabled' : '',
            ].filter(Boolean).join(' ');

            html += `<span class="${classes}" data-year="${year}">${year}</span>`;
        }

        grid.innerHTML = html;
    }

    function render() {
        if (view === 'years') {
            renderYears();
        } else if (view === 'months') {
            renderMonths();
        } else {
            renderDays();
        }

        updateNav();
        renderYearSelect();
    }

    /** Не даём листать туда, где все даты вне диапазона */
    function updateNav() {
        const year = viewing.getFullYear();
        const from = min();
        const to = max();

        let forwardBlocked = false;
        let backBlocked = false;

        if (view === 'days') {
            forwardBlocked = Boolean(to && new Date(year, viewing.getMonth() + 1, 1) > to);
            backBlocked = Boolean(from && new Date(year, viewing.getMonth(), 0) < from);
        } else if (view === 'months') {
            forwardBlocked = Boolean(to && new Date(year + 1, 0, 1) > to);
            backBlocked = Boolean(from && new Date(year - 1, 11, 31) < from);
        } else {
            const start = yearPageStart(year);

            forwardBlocked = Boolean(to && new Date(start + YEARS_PER_PAGE, 0, 1) > to);
            backBlocked = Boolean(from && new Date(start - 1, 11, 31) < from);
        }

        if (next) {
            next.disabled = forwardBlocked;
            next.classList.toggle('is-disabled', forwardBlocked);
        }

        if (prev) {
            prev.disabled = backBlocked;
            prev.classList.toggle('is-disabled', backBlocked);
        }
    }

    function shift(direction) {
        if (view === 'days') {
            viewing.setMonth(viewing.getMonth() + direction);
        } else if (view === 'months') {
            viewing.setFullYear(viewing.getFullYear() + direction);
        } else {
            viewing.setFullYear(viewing.getFullYear() + direction * YEARS_PER_PAGE);
        }

        render();
    }

    function close() {
        popup.classList.remove('open');
        display.classList.remove('open');
    }

    function open() {
        /** одновременно открыт только один календарь */
        document.querySelectorAll('[data-calendar].open').forEach((other) => {
            if (other !== popup) {
                other.classList.remove('open');
                other.closest('[data-date-picker]')?.querySelector('[data-date-display]')?.classList.remove('open');
            }
        });

        viewing = selected ? new Date(selected) : new Date();

        const from = min();
        const to = max();

        /** если сегодняшний месяц уже вне диапазона — открываем ближайший доступный */
        if (!selected && to && viewing > to) {
            viewing = new Date(to);
        }

        if (!selected && from && viewing < from) {
            viewing = new Date(from);
        }

        view = 'days';
        render();

        popup.classList.add('open');
        display.classList.add('open');
    }

    display.addEventListener('click', (event) => {
        event.stopPropagation();

        if (popup.classList.contains('open')) {
            close();

            return;
        }

        open();
    });

    /** Заголовок поднимает на уровень выше: месяц → год → страница лет */
    label.addEventListener('click', (event) => {
        event.stopPropagation();

        view = view === 'days' ? 'months' : 'years';
        render();
    });

    prev?.addEventListener('click', (event) => {
        event.stopPropagation();
        shift(-1);
    });

    next?.addEventListener('click', (event) => {
        event.stopPropagation();
        shift(1);
    });

    /** Селект года: прыгаем сразу в выбранный год, оставаясь в текущем режиме */
    yearSelect?.addEventListener('change', (event) => {
        /** это не поле формы — обработчики формы (сброс результата и т.п.) его не касаются */
        event.stopPropagation();

        /** день сбрасываем на 1-е, иначе 31-е при смене года уедет в соседний месяц */
        viewing = new Date(+yearSelect.value, viewing.getMonth(), 1);

        if (view === 'years') {
            view = 'months';
        }

        clampViewingMonth();
        render();
    });

    grid.addEventListener('click', (event) => {
        const cell = event.target.closest('.cal-day, .cal-cell');

        if (!cell || cell.classList.contains('empty') || cell.classList.contains('disabled')) {
            return;
        }

        event.stopPropagation();

        /** выбор года → показываем месяцы этого года */
        if (cell.dataset.year !== undefined) {
            viewing.setFullYear(+cell.dataset.year);
            view = 'months';
            render();

            return;
        }

        /** выбор месяца → показываем его дни */
        if (cell.dataset.month !== undefined) {
            viewing.setDate(1);
            viewing.setMonth(+cell.dataset.month);
            view = 'days';
            render();

            return;
        }

        selected = new Date(viewing.getFullYear(), viewing.getMonth(), +cell.dataset.day);

        hidden.value = toIso(selected);
        display.value = toHuman(selected);

        /** чтобы валидация и другие обработчики увидели новое значение */
        hidden.dispatchEvent(new Event('input', { bubbles: true }));
        hidden.dispatchEvent(new Event('change', { bubbles: true }));

        close();
    });

    popup.addEventListener('click', (event) => event.stopPropagation());

    /**
     * Граница пришла от соседнего поля (период «от — до»): подхватываем её
     * значение и, если выбранная дата в него больше не укладывается, чистим
     * поле — период не должен получаться вывернутым.
     */
    function follow(bound, name) {
        if (!name) {
            return;
        }

        const scope = wrap.closest('.app_form_data') || wrap.closest('form') || document;
        const source = scope.querySelector(`[name="${name}"]`);

        if (!source) {
            return;
        }

        const apply = () => {
            if (source.value) {
                wrap.dataset[bound] = source.value;
            } else {
                delete wrap.dataset[bound];
            }

            /** значение читаем из самого поля: его могли проставить и извне */
            selected = parseIso(hidden.value);

            if (selected && isDisabled(selected)) {
                selected = null;
                hidden.value = '';
                display.value = '';
                hidden.dispatchEvent(new Event('change', { bubbles: true }));
            }

            if (popup.classList.contains('open')) {
                render();
            }
        };

        source.addEventListener('change', apply);
        apply();
    }

    follow('min', wrap.dataset.minField);
    follow('max', wrap.dataset.maxField);

    wrap.dataset.calendarReady = '1';
}

export function calendar() {
    document.querySelectorAll('[data-date-picker]').forEach((wrap) => {
        if (!wrap.dataset.calendarReady) {
            setup(wrap);
        }
    });

    if (!document.body.dataset.calendarOutsideReady) {
        /** клик вне календаря закрывает его */
        document.addEventListener('click', () => {
            document.querySelectorAll('[data-calendar].open').forEach((popup) => {
                popup.classList.remove('open');
                popup.closest('[data-date-picker]')?.querySelector('[data-date-display]')?.classList.remove('open');
            });
        });

        document.body.dataset.calendarOutsideReady = '1';
    }
}
