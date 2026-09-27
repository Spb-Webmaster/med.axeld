import {asyncExecution} from "./async_execution";
/** получаем csrf **/
const metaElements = document.querySelectorAll('meta[name="csrf-token"]');
const csrf = metaElements.length > 0 ? metaElements[0].content : "";
/** получаем csrf **/


document.addEventListener('DOMContentLoaded', async () => {
    const containers = document.querySelectorAll('.axios__uploading_form');

    for (let container of containers) {
        const formTemplate = container.getAttribute('data-form');/** название шаблона для blade **/


        try {
            const template = { template: formTemplate, author: '@AxeldMaster' };



            const response = await fetch('/upload-form-async', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-Token': csrf
                },
                body: JSON.stringify(template),
            });
            if (!response.ok) {
                console.error(`Error: ${response.status}`);
            }
            // const data = await response.json();
            const data = await response.text(); // Важно использовать .text(), а не .json()

            //e.target.innerHTML = data;
            container.innerHTML = data;
             asyncExecution() // соберем эту форму

        } catch (err) {
            console.error('Ошибка AJAX:', err.message);
           // alert('Ошибка при получении данных');
        }
    }
});

















