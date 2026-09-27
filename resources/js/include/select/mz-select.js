/**
 * MZ-Select — стилизованный выпадающий список поверх нативного <select>.
 *
 * Разметка: resources/views/components/form/select.blade.php (x-form.select)
 * Стили:    resources/css/components/form/mz-select.scss
 *
 * Нативный <select> остаётся в DOM и лишь визуально скрывается: форму отправляет
 * он, серверная валидация и old() работают как обычно. Поверх него скрипт рисует
 * кнопку и список, а выбор пункта пишет значение обратно в <select> и шлёт
 * событие change — сторонние обработчики продолжают работать.
 *
 * Ищется по атрибуту data-mz-select. Для разметки, вставленной после загрузки
 * страницы (формы FancyBox и AJAX), вызывается mzSelectInit(container) —
 * это уже делает asyncExecution().
 *
 * Перенесён из проекта axeld, логика не менялась.
 */

const SELECTOR    = '[data-mz-select]';
const OPEN_CLASS  = 'is-open';
const DROP_UP_CLASS  = 'drop-up';
const SELECTED_CLASS = 'is-selected';
const ACTIVE_CLASS   = 'is-active';
const DISABLED_CLASS = 'is-disabled';

function initMzSelect(root) {
    const select = root.querySelector('select');
    if (!select || root.dataset.mzSelectReady === 'true') return;
    root.dataset.mzSelectReady = 'true';

    if (select.disabled) root.classList.add(DISABLED_CLASS);

    const trigger = document.createElement('button');
    trigger.type = 'button';
    trigger.className = 'mz-select__trigger';
    trigger.setAttribute('aria-haspopup', 'listbox');
    trigger.setAttribute('aria-expanded', 'false');

    const dropdown = document.createElement('ul');
    dropdown.className = 'mz-select__dropdown';
    dropdown.setAttribute('role', 'listbox');

    root.appendChild(trigger);
    root.appendChild(dropdown);

    buildOptions(select, dropdown, trigger);
    updateTrigger(select, trigger);

    trigger.addEventListener('click', function () {
        if (select.disabled) return;
        const willOpen = !root.classList.contains(OPEN_CLASS);
        closeAllSelects(root);
        setOpen(root, trigger, dropdown, willOpen);
    });

    trigger.addEventListener('keydown', function (event) {
        if (select.disabled) return;

        if (event.key === 'Escape') {
            setOpen(root, trigger, dropdown, false);
            return;
        }

        if (event.key === 'Enter' || event.key === ' ') {
            event.preventDefault();
            if (root.classList.contains(OPEN_CLASS)) {
                const active = dropdown.querySelector('.' + ACTIVE_CLASS + ':not(.' + DISABLED_CLASS + ')');
                if (active) {
                    selectOption(select, Number(active.dataset.index));
                    setOpen(root, trigger, dropdown, false);
                }
                return;
            }
            setOpen(root, trigger, dropdown, true);
            return;
        }

        if (event.key === 'ArrowDown' || event.key === 'ArrowUp') {
            event.preventDefault();
            setOpen(root, trigger, dropdown, true);
            moveActive(dropdown, event.key === 'ArrowDown' ? 1 : -1);
        }
    });

    select.addEventListener('change', function () {
        syncSelectedState(select, dropdown);
        updateTrigger(select, trigger);
    });
}

function buildOptions(select, dropdown, trigger) {
    dropdown.innerHTML = '';
    Array.from(select.options).forEach(function (option, index) {
        const item = document.createElement('li');
        item.className = 'mz-select__option';
        item.textContent = option.textContent;
        item.dataset.value = option.value;
        item.dataset.index = String(index);
        item.setAttribute('role', 'option');

        if (option.disabled) {
            item.classList.add(DISABLED_CLASS);
            item.setAttribute('aria-disabled', 'true');
        }

        if (option.selected) {
            item.classList.add(SELECTED_CLASS, ACTIVE_CLASS);
            item.setAttribute('aria-selected', 'true');
        }

        item.addEventListener('click', function () {
            if (option.disabled) return;
            selectOption(select, index);
            closeAllSelects();
            trigger.focus();
        });

        dropdown.appendChild(item);
    });
}

function updateTrigger(select, trigger) {
    const selectedOption = select.options[select.selectedIndex];
    trigger.textContent = selectedOption ? selectedOption.textContent : '';
    const root = trigger.closest('[data-mz-select]');
    if (root) root.classList.toggle('has-value', !selectedOption?.disabled && select.selectedIndex > 0);
}

function syncSelectedState(select, dropdown) {
    Array.from(dropdown.children).forEach(function (item) {
        const isSelected = Number(item.dataset.index) === select.selectedIndex;
        item.classList.toggle(SELECTED_CLASS, isSelected);
        item.classList.toggle(ACTIVE_CLASS, isSelected);
        item.setAttribute('aria-selected', String(isSelected));
    });
}

function setOpen(root, trigger, dropdown, isOpen) {
    if (isOpen) positionDropdown(root, trigger, dropdown);
    root.classList.toggle(OPEN_CLASS, isOpen);
    trigger.setAttribute('aria-expanded', String(isOpen));
}

// Определяет, хватает ли места снизу, и раскрывает список вверх, если нет
function positionDropdown(root, trigger, dropdown) {
    const triggerRect = trigger.getBoundingClientRect();
    const dropdownHeight = dropdown.offsetHeight || dropdown.scrollHeight || 240;
    const spaceBelow = window.innerHeight - triggerRect.bottom;
    const spaceAbove = triggerRect.top;

    const shouldDropUp = spaceBelow < dropdownHeight && spaceAbove > spaceBelow;
    root.classList.toggle(DROP_UP_CLASS, shouldDropUp);
}

function selectOption(select, index) {
    select.selectedIndex = index;
    select.dispatchEvent(new Event('change', { bubbles: true }));
}

function closeAllSelects(exceptRoot) {
    document.querySelectorAll(SELECTOR + '.' + OPEN_CLASS).forEach(function (root) {
        if (root === exceptRoot) return;
        const trigger = root.querySelector('.mz-select__trigger');
        root.classList.remove(OPEN_CLASS, DROP_UP_CLASS);
        if (trigger) trigger.setAttribute('aria-expanded', 'false');
    });
}

function moveActive(dropdown, direction) {
    const options = Array.from(dropdown.querySelectorAll('.mz-select__option:not(.is-disabled)'));
    if (!options.length) return;

    const currentIndex = options.findIndex(function (item) {
        return item.classList.contains(ACTIVE_CLASS);
    });

    const nextIndex = currentIndex < 0
        ? 0
        : (currentIndex + direction + options.length) % options.length;

    options.forEach(function (item) { item.classList.remove(ACTIVE_CLASS); });
    options[nextIndex].classList.add(ACTIVE_CLASS);
    options[nextIndex].scrollIntoView({ block: 'nearest' });
}

// Инициализация для статических элементов на странице
export function mzSelect() {
    document.addEventListener('click', function (event) {
        if (!event.target.closest(SELECTOR)) closeAllSelects();
    });

    if (document.readyState === 'loading') {
        document.addEventListener('DOMContentLoaded', function () {
            document.querySelectorAll(SELECTOR).forEach(initMzSelect);
        });
    } else {
        document.querySelectorAll(SELECTOR).forEach(initMzSelect);
    }
}

// Инициализация для динамически вставленного контента (FancyBox и т.п.)
export function mzSelectInit(container) {
    (container || document).querySelectorAll(SELECTOR).forEach(initMzSelect);
}
