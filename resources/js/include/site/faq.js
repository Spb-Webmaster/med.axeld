import { slideDown } from '../methods/slideDown';
import { slideUp } from '../methods/slideUp';

export function faqAccordion() {
    const details = document.querySelectorAll('.faq-list details');
    if (!details.length) return;

    // Скрываем закрытые элементы через JS (убираем зависимость от CSS display:none)
    details.forEach(detail => {
        const content = detail.querySelector('p');
        if (!detail.hasAttribute('open')) {
            content.style.display = 'none';
        }
    });

    details.forEach(detail => {
        const summary = detail.querySelector('summary');
        const content = detail.querySelector('p');

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
