/**
 * Всплывающее сообщение: закрытие крестиком и автоматическое скрытие.
 *
 * Разметка: resources/views/components/message/{message,message_error}.blade.php
 * Стили:    resources/css/message/flach_message.scss
 */

// Сколько висит сообщение. Ошибкам даём дольше — их читают внимательнее
const HIDE_AFTER = 5000;
const HIDE_AFTER_ALERT = 9000;

function hide(box) {
    if (box.classList.contains('is-hiding')) return;

    box.classList.add('is-hiding');

    // Ждём конец перехода, но не полагаемся на него: если анимации отключены
    // в системе, transitionend не придёт и сообщение осталось бы висеть
    const remove = () => box.remove();
    box.addEventListener('transitionend', remove, { once: true });
    setTimeout(remove, 400);
}

export function flash_message() {
    // Сообщений на странице может быть два сразу: обычное и блок ошибок
    const boxes = document.querySelectorAll('.app_flach_message');
    if (!boxes.length) return;

    boxes.forEach(box => {
        const closeBtn = box.querySelector('.app_f_message_close');
        if (closeBtn) closeBtn.addEventListener('click', () => hide(box));

        const delay = box.classList.contains('class__alert') ? HIDE_AFTER_ALERT : HIDE_AFTER;
        let timer = setTimeout(() => hide(box), delay);

        // Пока читают — не убираем; увели курсор, отсчёт начинается заново
        box.addEventListener('mouseenter', () => clearTimeout(timer));
        box.addEventListener('mouseleave', () => {
            timer = setTimeout(() => hide(box), delay);
        });
    });

    // Esc закрывает всё показанное разом
    document.addEventListener('keydown', e => {
        if (e.key === 'Escape') boxes.forEach(hide);
    });
}
