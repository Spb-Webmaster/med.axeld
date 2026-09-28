/**
 * Дневник давления — календарь.
 *
 * Разметка: resources/views/components/calendar/bp-calendar.blade.php
 * Стили:    resources/css/components/modules/bp-calendar.scss
 * Данные:   таблица blood_pressure_readings, отдаётся в
 *           <script type="application/json" id="bpCalendarData">
 *
 * Навигация:
 *   — год: кнопки ‹ ›, лента годов (клик, стрелки, колесо мыши), кнопка «Сегодня»;
 *   — месяц: клик по карточке открывает большую сетку, в ней « ‹ › » и стрелки клавиатуры.
 * Назад календарь не листается раньше data-min-year, вперёд — без ограничений:
 * горизонт лет расширяется по мере листания.
 *
 * Запись замера: в открытом месяце нажатие на день вызывает форму из трёх полей.
 * Будущие дни не отмечаются — сегодняшняя дата приходит с сервера (data-today),
 * та же проверка продублирована в BloodPressureRequest.
 */

import { axiosLaravel } from '../axios/axiosLaravel';

const MONTHS = ['Январь', 'Февраль', 'Март', 'Апрель', 'Май', 'Июнь',
    'Июль', 'Август', 'Сентябрь', 'Октябрь', 'Ноябрь', 'Декабрь'];

const MONTHS_GEN = ['января', 'февраля', 'марта', 'апреля', 'мая', 'июня',
    'июля', 'августа', 'сентября', 'октября', 'ноября', 'декабря'];

const WEEKDAYS = ['Пн', 'Вт', 'Ср', 'Чт', 'Пт', 'Сб', 'Вс'];

const BLUE = [29, 78, 216];    // #1d4ed8 — низкое давление
const WHITE = [255, 255, 255]; // норма
const RED = [185, 28, 28];     // #b91c1c — высокое давление

const pad = n => String(n).padStart(2, '0');
const keyFor = (y, m, d) => `${y}-${pad(m + 1)}-${pad(d)}`;
const daysInMonth = (y, m) => new Date(y, m + 1, 0).getDate();
const mondayIndex = (y, m) => (new Date(y, m, 1).getDay() + 6) % 7; // 0 = Пн
const formatDate = (y, m, d) => `${d} ${MONTHS_GEN[m]} ${y}`;
const formatKey = key => {
    const [y, m, d] = key.split('-').map(Number);
    return formatDate(y, m - 1, d);
};
const clamp01 = t => Math.min(1, Math.max(0, t));
const lerp = (a, b, t) => a.map((v, i) => Math.round(v + (b[i] - v) * t));

function el(tag, cls, text) {
    const node = document.createElement(tag);
    if (cls) node.className = cls;
    if (text != null) node.textContent = text;
    return node;
}

function relativeLuminance([r, g, b]) {
    const f = c => {
        c /= 255;
        return c <= 0.03928 ? c / 12.92 : Math.pow((c + 0.055) / 1.055, 2.4);
    };
    return 0.2126 * f(r) + 0.7152 * f(g) + 0.0722 * f(b);
}

/** Единственная функция расчёта цвета по верхнему давлению. */
function colorForSystolic(sys, scale) {
    let rgb;
    if (sys <= scale.low) rgb = BLUE;
    else if (sys < scale.mid) rgb = lerp(BLUE, WHITE, clamp01((sys - scale.low) / (scale.mid - scale.low)));
    else if (sys === scale.mid) rgb = WHITE;
    else if (sys < scale.high) rgb = lerp(WHITE, RED, clamp01((sys - scale.mid) / (scale.high - scale.mid)));
    else rgb = RED;

    return {
        bg: `rgb(${rgb.join(',')})`,
        fg: relativeLuminance(rgb) > 0.30 ? '#1f2328' : '#ffffff',
    };
}

export function bpCalendar() {
    const root = document.getElementById('bpCalendar');
    if (!root) return;

    /* ================== Данные и настройки ================== */
    const dataNode = document.getElementById('bpCalendarData');
    let rows = [];
    if (dataNode) {
        try {
            rows = JSON.parse(dataNode.textContent) || [];
        } catch (e) {
            rows = [];
        }
    }

    const DATA = new Map();
    rows.forEach(r => DATA.set(r.date, r));

    const SCALE = {
        low: Number(root.dataset.scaleLow) || 100,
        mid: Number(root.dataset.scaleMid) || 120,
        high: Number(root.dataset.scaleHigh) || 150,
    };

    const MIN_YEAR = Number(root.dataset.minYear) || 2026;
    const MIN_MONTH_INDEX = MIN_YEAR * 12;
    const YEARS_AHEAD = Math.max(1, Number(root.dataset.yearsAhead) || 4);

    const CAN_EDIT = root.dataset.canEdit === '1';
    const STORE_URL = root.dataset.storeUrl || '';
    const DELETE_URL = root.dataset.deleteUrl || '';

    const color = sys => colorForSystolic(sys, SCALE);

    /* ================== Узлы ================== */
    const yearLabel = root.querySelector('#bpYearLabel');
    const yearsStrip = root.querySelector('#bpYears');
    const prevYearBtn = root.querySelector('#bpPrevYear');
    const nextYearBtn = root.querySelector('#bpNextYear');
    const todayBtn = root.querySelector('#bpToday');
    const monthsRoot = root.querySelector('#bpMonths');
    const legendBar = root.querySelector('#bpLegendBar');

    const tooltip = document.getElementById('bpTooltip');
    const modal = document.getElementById('bpModal');
    const modalTitle = document.getElementById('bpModalTitle');
    const bigCalHead = document.getElementById('bpBigCalHead');
    const bigCalBody = document.getElementById('bpBigCalBody');
    const prevMonthBtn = document.getElementById('bpPrevMonth');
    const nextMonthBtn = document.getElementById('bpNextMonth');
    const prevYearModalBtn = document.getElementById('bpPrevYearModal');
    const nextYearModalBtn = document.getElementById('bpNextYearModal');
    const closeModalBtn = document.getElementById('bpCloseModal');
    const modalBackdrop = document.getElementById('bpModalBackdrop');

    // Форма ввода есть только у авторизованного пользователя
    const entry = document.getElementById('bpEntry');
    const entryForm = document.getElementById('bpEntryForm');
    const entryTitle = document.getElementById('bpEntryTitle');
    const entryDate = document.getElementById('bpEntryDate');
    const entrySystolic = document.getElementById('bpEntrySystolic');
    const entryDiastolic = document.getElementById('bpEntryDiastolic');
    const entryPulse = document.getElementById('bpEntryPulse');
    const entryError = document.getElementById('bpEntryError');
    const entrySave = document.getElementById('bpEntrySave');
    const entryDelete = document.getElementById('bpEntryDelete');
    const entryCancel = document.getElementById('bpEntryCancel');
    const entryBackdrop = document.getElementById('bpEntryBackdrop');

    const canEdit = CAN_EDIT && !!entry;

    /* ================== Состояние ================== */
    /*
     | Сегодняшний день берём у сервера: по нему решается, какие даты ещё нельзя
     | отмечать. Сбитые часы на машине пользователя не должны это менять.
     */
    const TODAY_KEY = root.dataset.today || '';
    const TODAY_YEAR = Number(TODAY_KEY.slice(0, 4)) || new Date().getFullYear();

    // Ключи вида ГГГГ-ММ-ДД сравниваются как строки, отдельный разбор не нужен
    const isFuture = key => TODAY_KEY !== '' && key > TODAY_KEY;

    const dataMaxYear = rows.reduce((max, r) => Math.max(max, Number(r.date.slice(0, 4)) || 0), MIN_YEAR);

    let horizonYear = Math.max(MIN_YEAR + 9, dataMaxYear, TODAY_YEAR + YEARS_AHEAD);
    let currentYear = Math.max(MIN_YEAR, TODAY_YEAR);
    let modalYear = currentYear;
    let modalMonth = TODAY_YEAR === currentYear ? Number(TODAY_KEY.slice(5, 7)) - 1 || 0 : 0;
    let chips = new Map();
    let lastOpener = null;
    let entryOpener = null;

    /* ================== Легенда ================== */
    function buildLegend() {
        if (!legendBar) return;
        const span = SCALE.high - SCALE.low;
        const stops = [];
        for (let s = SCALE.low; s <= SCALE.high; s += 2) {
            stops.push(`${color(s).bg} ${((s - SCALE.low) / span * 100).toFixed(1)}%`);
        }
        legendBar.style.background = `linear-gradient(90deg, ${stops.join(', ')})`;
    }

    /* ================== Лента годов ================== */
    function ensureHorizon(year) {
        if (year <= horizonYear) return false;
        horizonYear = year + YEARS_AHEAD;
        buildYearStrip();
        return true;
    }

    function buildYearStrip() {
        if (!yearsStrip) return;
        yearsStrip.innerHTML = '';
        chips = new Map();
        for (let y = MIN_YEAR; y <= horizonYear; y++) {
            const chip = el('button', 'btn-pill bp-calendar__year-chip', String(y));
            chip.type = 'button';
            chip.dataset.year = String(y);
            chip.setAttribute('aria-label', `Показать ${y} год`);
            if (y === TODAY_YEAR) chip.classList.add('is-current');
            yearsStrip.appendChild(chip);
            chips.set(y, chip);
        }
    }

    function syncYearStrip(smooth) {
        if (!yearsStrip) return;
        chips.forEach((chip, y) => {
            const active = y === currentYear;
            chip.classList.toggle('is-active', active);
            chip.setAttribute('aria-pressed', active ? 'true' : 'false');
            chip.tabIndex = active ? 0 : -1;
        });

        const active = chips.get(currentYear);
        if (!active) return;
        const left = active.offsetLeft - (yearsStrip.clientWidth - active.offsetWidth) / 2;
        yearsStrip.scrollTo({ left: Math.max(0, left), behavior: smooth ? 'smooth' : 'auto' });
    }

    function updateYearNav() {
        prevYearBtn.disabled = currentYear <= MIN_YEAR;
        if (prevYearBtn.disabled && document.activeElement === prevYearBtn) nextYearBtn.focus();

        if (todayBtn) {
            if (TODAY_YEAR < MIN_YEAR) {
                todayBtn.hidden = true;
            } else {
                todayBtn.disabled = currentYear === TODAY_YEAR;
            }
        }
    }

    function setYear(year, focusChip) {
        const next = Math.max(MIN_YEAR, year);
        ensureHorizon(next);
        if (next !== currentYear) {
            currentYear = next;
            renderYear();
        }
        syncYearStrip(true);
        updateYearNav();
        if (focusChip) {
            const chip = chips.get(currentYear);
            if (chip) chip.focus();
        }
    }

    /* ================== Годовой календарь ================== */
    function renderYear() {
        yearLabel.textContent = currentYear;
        monthsRoot.innerHTML = '';
        for (let m = 0; m < 12; m++) monthsRoot.appendChild(buildMonthCard(currentYear, m));
    }

    function buildMonthCard(y, m) {
        const card = el('article', 'bp-month');
        card.dataset.month = String(m);

        const title = el('button', 'bp-month__title', MONTHS[m]);
        title.type = 'button';
        title.setAttribute('aria-label', `Открыть ${MONTHS[m]} ${y}`);
        card.appendChild(title);

        const weekdays = el('div', 'bp-month__weekdays');
        WEEKDAYS.forEach(w => weekdays.appendChild(el('span', null, w)));
        card.appendChild(weekdays);

        const days = el('div', 'bp-month__days');
        const offset = mondayIndex(y, m);
        const total = daysInMonth(y, m);

        for (let i = 0; i < offset; i++) days.appendChild(el('span', 'bp-day is-blank'));

        for (let d = 1; d <= total; d++) {
            const key = keyFor(y, m, d);
            const rec = DATA.get(key);
            const cell = el('span', 'bp-day', d);
            if (rec) {
                const c = color(rec.sys);
                cell.classList.add('is-filled');
                cell.style.background = c.bg;
                cell.style.color = c.fg;
                cell.tabIndex = 0;
                cell.dataset.key = key;
                cell.setAttribute('aria-label',
                    `${formatDate(y, m, d)}: давление ${rec.sys}/${rec.dia} мм рт. ст., пульс ${rec.pulse} уд/мин`);
            } else {
                cell.classList.add('is-empty');
                cell.setAttribute('aria-hidden', 'true');
            }
            if (isFuture(key)) cell.classList.add('is-future');
            if (key === TODAY_KEY) cell.classList.add('is-today');
            days.appendChild(cell);
        }

        const trailing = (7 - (offset + total) % 7) % 7;
        for (let i = 0; i < trailing; i++) days.appendChild(el('span', 'bp-day is-blank'));
        card.appendChild(days);

        card.addEventListener('click', () => openModal(y, m, title));
        title.addEventListener('click', e => {
            e.stopPropagation();
            openModal(y, m, title);
        });
        days.addEventListener('keydown', e => {
            if ((e.key === 'Enter' || e.key === ' ') && e.target.classList.contains('is-filled')) {
                e.preventDefault();
                openModal(y, m, title);
            }
        });

        return card;
    }

    /* ================== Подсказка ================== */
    function showTooltip(target) {
        const rec = DATA.get(target.dataset.key);
        if (!rec || !tooltip) return;

        tooltip.innerHTML = '';
        tooltip.appendChild(el('strong', null, formatKey(rec.date)));
        tooltip.appendChild(document.createTextNode(`Давление: ${rec.sys}/${rec.dia} мм рт. ст.`));
        tooltip.appendChild(el('br'));
        tooltip.appendChild(document.createTextNode(`Пульс: ${rec.pulse} уд/мин`));

        const r = target.getBoundingClientRect();
        tooltip.classList.add('is-visible');

        const tw = tooltip.offsetWidth;
        const th = tooltip.offsetHeight;
        let left = r.left + r.width / 2 - tw / 2;
        left = Math.max(8, Math.min(left, window.innerWidth - tw - 8));
        let top = r.top - th - 8;
        if (top < 8) top = r.bottom + 8;

        tooltip.style.left = `${left}px`;
        tooltip.style.top = `${top}px`;
    }

    function hideTooltip() {
        if (tooltip) tooltip.classList.remove('is-visible');
    }

    /* ================== Модальное окно месяца ================== */
    function monthIndex(y, m) {
        return y * 12 + m;
    }

    function setModalDate(index) {
        const safe = Math.max(MIN_MONTH_INDEX, index);
        const y = Math.floor(safe / 12);
        ensureHorizon(y);
        modalYear = y;
        modalMonth = safe % 12;
        renderModal();
    }

    function shiftModalMonth(delta) {
        setModalDate(monthIndex(modalYear, modalMonth) + delta);
    }

    function shiftModalYear(delta) {
        setModalDate(monthIndex(modalYear + delta, modalMonth));
    }

    function updateModalNav() {
        prevMonthBtn.disabled = modalYear === MIN_YEAR && modalMonth === 0;
        prevYearModalBtn.disabled = modalYear <= MIN_YEAR;

        // Фокус не должен остаться на кнопке, которая только что стала недоступной
        if (prevMonthBtn.disabled && document.activeElement === prevMonthBtn) nextMonthBtn.focus();
        if (prevYearModalBtn.disabled && document.activeElement === prevYearModalBtn) nextYearModalBtn.focus();
    }

    function openModal(y, m, opener) {
        lastOpener = opener || document.activeElement;
        setModalDate(monthIndex(y, m));
        modal.hidden = false;
        document.body.style.overflow = 'hidden';
        hideTooltip();
        closeModalBtn.focus();
    }

    function closeModal() {
        closeEntry();
        modal.hidden = true;
        document.body.style.overflow = '';

        // Если при листании месяцев сменился год — синхронизируем годовой календарь
        if (modalYear !== currentYear) setYear(modalYear, false);

        const target = lastOpener && document.contains(lastOpener)
            ? lastOpener
            : monthsRoot.querySelector(`.bp-month[data-month="${modalMonth}"] .bp-month__title`);
        if (target) target.focus();
    }

    function renderModal() {
        modalTitle.textContent = `${MONTHS[modalMonth]} ${modalYear}`;
        updateModalNav();

        bigCalHead.innerHTML = '';
        WEEKDAYS.forEach(w => {
            const th = el('th', null, w);
            th.scope = 'col';
            bigCalHead.appendChild(th);
        });

        bigCalBody.innerHTML = '';
        const offset = mondayIndex(modalYear, modalMonth);
        const total = daysInMonth(modalYear, modalMonth);
        const rowsCount = Math.ceil((offset + total) / 7);

        for (let r = 0; r < rowsCount; r++) {
            const tr = el('tr');
            for (let c = 0; c < 7; c++) {
                const d = r * 7 + c - offset + 1;
                const td = el('td');
                if (d >= 1 && d <= total) td.appendChild(buildBigCell(modalYear, modalMonth, d));
                tr.appendChild(td);
            }
            bigCalBody.appendChild(tr);
        }
    }

    function buildBigCell(y, m, d) {
        const key = keyFor(y, m, d);
        const rec = DATA.get(key);
        const future = isFuture(key);
        const cell = el('div', 'bp-bigcal__cell');
        cell.dataset.key = key;
        cell.appendChild(el('div', 'bp-bigcal__num', d));

        if (rec) {
            const c = color(rec.sys);
            cell.classList.add('is-filled');
            cell.style.background = c.bg;
            cell.style.color = c.fg;
            cell.appendChild(el('div', 'bp-bigcal__bp', `${rec.sys}/${rec.dia}`));
            cell.appendChild(el('div', 'bp-bigcal__unit', 'мм рт. ст.'));
            /*
             | «уд/мин» отдельным узлом: на узких экранах эта приписка не влезает
             | в ячейку и её прячет CSS. Само значение при этом остаётся,
             | а в aria-label ниже подпись сохраняется целиком.
             */
            const pulse = el('div', 'bp-bigcal__pulse');
            pulse.appendChild(document.createTextNode(`Пульс: ${rec.pulse}`));
            pulse.appendChild(el('span', 'bp-bigcal__pulse-unit', ' уд/мин'));
            cell.appendChild(pulse);
        } else {
            cell.classList.add('is-empty');
            cell.appendChild(el('div', 'bp-bigcal__none', future ? '—' : 'Нет записи'));
        }

        if (future) cell.classList.add('is-future');
        if (key === TODAY_KEY) cell.classList.add('is-today');

        /*
         | Будущие дни не отмечаются: такая ячейка не кнопка и клавиатурой
         | не берётся, чтобы о запрете не приходилось узнавать из ошибки.
         */
        if (canEdit && !future) {
            cell.classList.add('is-editable');
            cell.tabIndex = 0;
            cell.setAttribute('role', 'button');
            cell.setAttribute('aria-label', rec
                ? `${formatDate(y, m, d)}: давление ${rec.sys}/${rec.dia} мм рт. ст., пульс ${rec.pulse} уд/мин. Изменить`
                : `${formatDate(y, m, d)}: нет записи. Добавить замер`);
        } else {
            cell.setAttribute('aria-label', rec
                ? `${formatDate(y, m, d)}: давление ${rec.sys}/${rec.dia} мм рт. ст., пульс ${rec.pulse} уд/мин`
                : `${formatDate(y, m, d)}: нет записи`);
        }

        return cell;
    }

    /* ================== Форма замера ================== */
    function showEntryError(message) {
        if (!entryError) return;
        entryError.textContent = message;
        entryError.hidden = false;
    }

    function hideEntryError() {
        if (!entryError) return;
        entryError.textContent = '';
        entryError.hidden = true;
    }

    function setEntryBusy(busy) {
        entrySave.disabled = busy;
        entryDelete.disabled = busy;
        entryCancel.disabled = busy;
        entryForm.classList.toggle('is-busy', busy);
    }

    function openEntry(key, opener) {
        if (!canEdit || isFuture(key)) return;

        const rec = DATA.get(key);
        entryOpener = opener || null;

        entryDate.value = key;
        entrySystolic.value = rec ? rec.sys : '';
        entryDiastolic.value = rec ? rec.dia : '';
        entryPulse.value = rec ? rec.pulse : '';
        entryTitle.textContent = formatKey(key);
        entryDelete.hidden = !rec;

        hideEntryError();
        setEntryBusy(false);
        entry.hidden = false;
        entrySystolic.focus();
        entrySystolic.select();
    }

    function closeEntry() {
        if (!canEdit || entry.hidden) return;
        entry.hidden = true;
        hideEntryError();

        // Возвращаем фокус на ту же ячейку — она пережила перерисовку под тем же ключом
        const key = entryDate.value;
        const back = (entryOpener && document.contains(entryOpener))
            ? entryOpener
            : bigCalBody.querySelector(`.bp-bigcal__cell[data-key="${key}"]`);
        if (back) back.focus();
        entryOpener = null;
    }

    /** Разбор ответа: 422 от Laravel приходит объектом errors, сеть — полем error */
    function errorFromResponse(res) {
        if (!res) return 'Не удалось сохранить замер. Попробуйте ещё раз.';

        if (res.errors) {
            const first = Object.values(res.errors)[0];
            return Array.isArray(first) ? first[0] : String(first);
        }

        if (res.error) return 'Не удалось связаться с сервером. Проверьте соединение.';

        return 'Не удалось сохранить замер. Попробуйте ещё раз.';
    }

    /** Перерисовка после записи: и большая сетка, и карточки года */
    function refreshAfterChange() {
        renderModal();
        renderYear();
    }

    if (canEdit) {
        entryForm.addEventListener('submit', async e => {
            e.preventDefault();
            hideEntryError();

            const key = entryDate.value;
            if (isFuture(key)) {
                showEntryError('Записать замер можно только за сегодня или за прошедший день.');
                return;
            }

            const payload = {
                date: key,
                systolic: entrySystolic.value,
                diastolic: entryDiastolic.value,
                pulse: entryPulse.value,
            };

            setEntryBusy(true);
            const res = await axiosLaravel(payload, STORE_URL);
            setEntryBusy(false);

            if (res && res.saved && res.reading) {
                DATA.set(res.reading.date, res.reading);
                closeEntry();
                refreshAfterChange();
                return;
            }

            showEntryError(errorFromResponse(res));
        });

        entryDelete.addEventListener('click', async () => {
            const key = entryDate.value;
            hideEntryError();
            setEntryBusy(true);

            // Хелпер умеет только POST, поэтому метод подменяем полем _method
            const res = await axiosLaravel({ _method: 'DELETE' }, `${DELETE_URL}/${key}`);
            setEntryBusy(false);

            if (res && typeof res.deleted !== 'undefined') {
                DATA.delete(key);
                closeEntry();
                refreshAfterChange();
                return;
            }

            showEntryError(errorFromResponse(res));
        });

        entryCancel.addEventListener('click', closeEntry);
        entryBackdrop.addEventListener('click', closeEntry);

        bigCalBody.addEventListener('click', e => {
            const cell = e.target.closest('.bp-bigcal__cell.is-editable');
            if (cell) openEntry(cell.dataset.key, cell);
        });

        bigCalBody.addEventListener('keydown', e => {
            if (e.key !== 'Enter' && e.key !== ' ') return;
            const cell = e.target.closest('.bp-bigcal__cell.is-editable');
            if (!cell) return;
            e.preventDefault();
            openEntry(cell.dataset.key, cell);
        });
    }

    /* ================== События ================== */
    prevYearBtn.addEventListener('click', () => setYear(currentYear - 1, false));
    nextYearBtn.addEventListener('click', () => setYear(currentYear + 1, false));

    if (todayBtn) {
        todayBtn.addEventListener('click', () => setYear(TODAY_YEAR, false));
    }

    if (yearsStrip) {
        yearsStrip.addEventListener('click', e => {
            const chip = e.target.closest('.bp-calendar__year-chip');
            if (chip) setYear(Number(chip.dataset.year), true);
        });

        yearsStrip.addEventListener('keydown', e => {
            const step = { ArrowRight: 1, ArrowLeft: -1 }[e.key];
            if (step) {
                e.preventDefault();
                setYear(currentYear + step, true);
                return;
            }
            if (e.key === 'Home') {
                e.preventDefault();
                setYear(MIN_YEAR, true);
            }
            if (e.key === 'End') {
                e.preventDefault();
                setYear(horizonYear, true);
            }
        });

        // Вертикальное колесо мыши прокручивает ленту по горизонтали
        yearsStrip.addEventListener('wheel', e => {
            if (!e.deltaY || yearsStrip.scrollWidth <= yearsStrip.clientWidth) return;
            e.preventDefault();
            yearsStrip.scrollLeft += e.deltaY;
        }, { passive: false });
    }

    monthsRoot.addEventListener('mouseover', e => {
        const t = e.target.closest('.bp-day.is-filled');
        if (t) showTooltip(t);
    });
    monthsRoot.addEventListener('mouseout', e => {
        if (e.target.closest('.bp-day.is-filled')) hideTooltip();
    });
    monthsRoot.addEventListener('focusin', e => {
        const t = e.target.closest('.bp-day.is-filled');
        if (t) showTooltip(t);
    });
    monthsRoot.addEventListener('focusout', e => {
        if (e.target.closest('.bp-day.is-filled')) hideTooltip();
    });
    window.addEventListener('scroll', hideTooltip, { passive: true });

    closeModalBtn.addEventListener('click', closeModal);
    modalBackdrop.addEventListener('click', closeModal);
    prevMonthBtn.addEventListener('click', () => shiftModalMonth(-1));
    nextMonthBtn.addEventListener('click', () => shiftModalMonth(1));
    prevYearModalBtn.addEventListener('click', () => shiftModalYear(-1));
    nextYearModalBtn.addEventListener('click', () => shiftModalYear(1));

    document.addEventListener('keydown', e => {
        // Пока открыта форма замера, клавиши принадлежат ей, а не листанию месяцев
        if (canEdit && !entry.hidden) {
            if (e.key === 'Escape') {
                e.preventDefault();
                closeEntry();
            }
            return;
        }

        if (modal.hidden) return;

        if (e.key === 'Escape') {
            e.preventDefault();
            closeModal();
            return;
        }

        if (e.target.matches('input, textarea')) return;

        if (e.key === 'ArrowLeft') {
            e.preventDefault();
            e.shiftKey ? shiftModalYear(-1) : shiftModalMonth(-1);
        } else if (e.key === 'ArrowRight') {
            e.preventDefault();
            e.shiftKey ? shiftModalYear(1) : shiftModalMonth(1);
        } else if (e.key === 'PageUp') {
            e.preventDefault();
            shiftModalYear(-1);
        } else if (e.key === 'PageDown') {
            e.preventDefault();
            shiftModalYear(1);
        } else if (e.key === 'Tab') {
            // Ловушка фокуса внутри диалога (отключённые кнопки пропускаем)
            const focusable = modal.querySelectorAll('button:not([disabled])');
            if (!focusable.length) return;
            const first = focusable[0];
            const last = focusable[focusable.length - 1];
            if (e.shiftKey && document.activeElement === first) {
                e.preventDefault();
                last.focus();
            } else if (!e.shiftKey && document.activeElement === last) {
                e.preventDefault();
                first.focus();
            }
        }
    });

    /* ================== Старт ================== */
    buildLegend();
    buildYearStrip();
    renderYear();
    syncYearStrip(false);
    updateYearNav();
}
