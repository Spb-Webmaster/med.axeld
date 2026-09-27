import 'trix/dist/trix.css';

import Trix from "trix"
export function trix() {
    document.addEventListener("trix-before-initialize", () => {
        // Убираем кнопку вставки файлов
        delete Trix.config.toolbar.getDefaultHTML

        // Или полностью переопределяем HTML
        Trix.config.toolbar.getDefaultHTML = () => `
        <div class="trix-button-row">
            <span class="trix-button-group">
                <button data-trix-attribute="bold">Жирный</button>
                <button data-trix-attribute="italic">Наклонный</button>
               <!-- <button data-trix-attribute="bullet">Список</button>-->
            </span>
        </div>
    `
    })
}
