/**
 * Выгрузка дневника в Excel: показываем поля того периода, который выбран.
 *
 * Разметка: resources/views/components/calendar/export.blade.php
 * Проверка: App\Http\Requests\Cabinet\BloodPressureExportRequest
 */

export function bpExport() {
    const root = document.getElementById('bpExport');
    if (!root) return;

    const mode = root.querySelector('#bpExportMode');
    const blocks = [...root.querySelectorAll('[data-export-fields]')];
    if (!mode || !blocks.length) return;

    function apply() {
        blocks.forEach(block => {
            const active = block.dataset.exportFields === mode.value;
            block.hidden = !active;

            /*
             | Скрытые поля именно отключаем, а не только прячем: в режимах
             | «год» и «месяц» есть по полю year, и без disabled на сервер
             | ушли бы оба значения сразу.
             */
            block.querySelectorAll('input, select').forEach(field => {
                field.disabled = !active;
            });
        });
    }

    mode.addEventListener('change', apply);

    /*
     | Сброс формы. Обработчик срабатывает до того, как браузер вернёт значения
     | полей, поэтому дожидаемся следующего тика.
     |
     | Дальше рассылаем change вручную: нативный reset такого события не шлёт,
     | и стилизованный список остался бы с прежней надписью на кнопке, хотя
     | в самом <select> уже другое значение.
     */
    const form = root.querySelector('form');

    form.addEventListener('reset', () => {
        setTimeout(() => {
            form.querySelectorAll('select').forEach(field => {
                field.dispatchEvent(new Event('change', { bubbles: true }));
            });
            apply();
        }, 0);
    });

    apply();
}
