import { slideDown } from '../methods/slideDown';
import { slideUp } from '../methods/slideUp';

/*
 | Ответ лежит в .faq-list__answer (см. components/modules/faq.blade.php).
 | Запасной вариант с <p> оставлен для старой разметки, где обёртки не было.
 | Если ответа нет вовсе — пару просто пропускаем, раньше на этом месте
 | скрипт падал на обращении к null и ломал все аккордеоны на странице.
 */
const answerOf = detail => detail.querySelector('.faq-list__answer') || detail.querySelector('p');

export function faqAccordion() {
    const details = document.querySelectorAll('.faq-list details');
    if (!details.length) return;

    // Скрываем закрытые элементы через JS (убираем зависимость от CSS display:none)
    details.forEach(detail => {
        const content = answerOf(detail);
        if (content && !detail.hasAttribute('open')) {
            content.style.display = 'none';
        }
    });

    details.forEach(detail => {
        const summary = detail.querySelector('summary');
        const content = answerOf(detail);
        if (!summary || !content) return;

        summary.addEventListener('click', e => {
            e.preventDefault();

            if (detail.hasAttribute('open')) {
                slideUp(content, 300);
                setTimeout(() => detail.removeAttribute('open'), 300);
            } else {
                detail.setAttribute('open', '');
                slideDown(content, 300);
            }
        });
    });
}
