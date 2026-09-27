import {slideToggle} from '../../methods/slideToggle';

export function mobileMenuComponent() {
//app_top_menu

    const appTopMenus = Array.from(document.querySelectorAll('.app_top_menu'))
    const bottomMenuContainer = document.querySelector('.app_bottom_menu')
    const appMobileMenu = document.querySelector('.app_mobile_menu')
    const appMobileContent = document.querySelector('.app_mobile_content')
    const appMobileClose = document.querySelector('.app_mobile_close')

    /** получаем все верхние меню на сайте  **/
    for (let menu of appTopMenus) {
        // Клонируем каждое меню, чтобы избежать удаления оригиналов
        let clonedMenu = menu.cloneNode(true);

        // Добавляем клонов в контейнер
        bottomMenuContainer.appendChild(clonedMenu);
    }
// Найдем все иконки стрелок '.arrow' на странице
    const arrows = document.querySelectorAll('.arrow');

// Перебираем каждую стрелку и назначаем ей обработчик события
    arrows.forEach((arrow) => {
        arrow.addEventListener('click', arrowMenu);
    });


    appMobileMenu.addEventListener('click', toggleMenu)
    appMobileClose.addEventListener('click', closeMenu)

    function toggleMenu(e) {

        const parentEl = e.target.closest('.app_mobile_menu');
        parentEl.classList.toggle('active');
        slideToggle(appMobileContent, 500);

    }

    function closeMenu() {

        appMobileMenu.classList.toggle('active');
        slideToggle(appMobileContent, 500);

    }

    function arrowMenu(e) {

        e.preventDefault()
        const parentEl = e.target.closest('.parent');
        e.target.classList.toggle('active');
        const subMenu = parentEl.querySelector('.submenu')
        slideToggle(subMenu, 500);

    }
    /** получим мобильное меню и откроем активные элементы **/
    bottomMenuContainer.querySelectorAll('.parent.active').forEach((activeItem) => {
        activeItem.querySelector('.submenu').style.display = 'block'
    })



}
