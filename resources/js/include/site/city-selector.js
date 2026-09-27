import {axiosLaravel} from '../axios/axiosLaravel';

export function citySelector() {

    const toggle   = document.querySelector('.js-city-toggle');
    const list     = document.querySelector('.js-city-list');
    const nameEl   = document.querySelector('.js-city-name');
    const phoneEl  = document.querySelector('.js-city-phone');

    if (!toggle || !list) return;

    const items = Array.from(document.querySelectorAll('.js-city-item'));

    /** Открыть / закрыть список **/
    toggle.addEventListener('click', () => {
        const isOpen = list.classList.contains('active');
        list.classList.toggle('active', !isOpen);
        toggle.setAttribute('aria-expanded', String(!isOpen));
    });

    /** Закрыть при клике вне компонента **/
    document.addEventListener('click', (e) => {
        if (!e.target.closest('.header-city-wrap')) {
            list.classList.remove('active');
            toggle.setAttribute('aria-expanded', 'false');
        }
    }, true);

    /** Выбор города **/
    items.forEach((item) => {
        item.addEventListener('click', async () => {
            const title = item.dataset.title ?? '';
            const phone = item.dataset.phone ?? '';

            const result = await axiosLaravel({ title, phone }, '/set-city');

            if (!result || result.error) return;

            if (result.city_title && nameEl) {
                nameEl.textContent = result.city_title;
            }

            if (phoneEl) {
                if (result.city_phone) {
                    phoneEl.textContent = result.city_phone_formatted ?? result.city_phone;
                    phoneEl.href = 'tel:' + result.city_phone;
                } else {
                    phoneEl.textContent = '';
                    phoneEl.href = '#';
                }
            }

            items.forEach(i => i.classList.remove('is-active'));
            item.classList.add('is-active');

            list.classList.remove('active');
            toggle.setAttribute('aria-expanded', 'false');
        });
    });
}
