// import Swiper bundle with all modules installed
import Swiper from 'swiper/bundle';

export function swiper() {

    if (document.querySelector('.block3-swiper')) {
        new Swiper('.block3-swiper', {
            loop: true,
            navigation: {
                nextEl: '.block3-swiper .swiper-button-next',
                prevEl: '.block3-swiper .swiper-button-prev',
            },
        });
    }

    if (document.querySelector('.schedule-swiper')) {
        new Swiper('.schedule-swiper', {
            loop: true,
            slidesPerView: 'auto',
         /*   spaceBetween: 20,*/
            pagination: false,
        });
    }
}
