import {axiosLaravel} from '../axios/axiosLaravel.js'
import {fieldErrors} from '../fancybox/form/fieldErrors.js'
import {removeErrors} from "../fancybox/form/removeErrors.js";
import {imask} from "../imask.js";

export function asyncExecution() {


    /** подключим маски **/
    imask()


    const appFormButtons = Array.from(document.querySelectorAll('.app_form_button'))

    console.log(appFormButtons.length)
   if(appFormButtons.length) {
       /** получим все возможные кнопки на всех формах **/
       for (let appFormButton of appFormButtons) {
           appFormButton.addEventListener('click', submitFormData)


           /** Добавляем обработку события Enter на поля ввода **/
           /** Выбираем input c фокусом **/
           const appInputs = Array.from(document.querySelectorAll('.app_form_data .app_input_name'));
           for (let input of appInputs) {
               input.addEventListener('keydown', function (event) {
                   if (event.key === 'Enter') { // Можно использовать event.keyCode === 13
                       event.preventDefault();  // Предотвращаем стандартное поведение браузера
                       /** Найдем ближайшую кнопку, принадлежащую этой же форме **/
                       const closestParentModal = input.closest('.app_form_modal');
                       const nearestButton = closestParentModal.querySelector('.app_form_button');

                       /** Эмулируем клик на ближайшей кнопке **/
                       submitFormData({target: nearestButton});
                   }
               });
           }


           function submitFormData(e) {


               //console.log(e.target);
               const parentEl = e.target.closest('.app_form_modal');
               /** parent **/
               let url = e.target.dataset.url /** url куда отправляем **/

               /** массив input` ов **/
               const appInputsName = Array.from(parentEl.querySelectorAll('.app_form_data .app_input_name'))

               /** массив активных select` ов **/
               const appSelected = Array.from(parentEl.querySelectorAll('.app_form_data .app_selected'))

               /** Массив для сохранения результатов **/
               const resultObject = {};

               if (appInputsName.length) {
                   /** Формируем объект с ключами и значениями **/
                   appInputsName.forEach(input => {
                       const {name, value} = input;
                       resultObject[name] = value;
                   });

               }

               if (appSelected.length) {
                   /** Формируем объект с ключами и значениями **/
                   appSelected.forEach(option => {
                       let name = option.dataset.select
                       resultObject[name] = option.textContent.trim();
                   });

               }

               /** добавим ссылку в общий request **/
               resultObject['url'] = url;

               /** Проверяем первый символ строки на равенство '/' **/
               if (!url.startsWith('/')) {
                   // Если '/' нет, добавляем его перед url
                   url = '/' + url;
               }


               /** Включаем компонент loader **/
               const loader = parentEl.querySelector('.app_loader');
               loader.classList.toggle('active');

               /** Получаем ссылку на компонент response **/
               const response = parentEl.querySelector('.app_form_response');

               /** Получаем ссылку на окно с inputs **/
               const modal = parentEl.querySelector('.app_modal');

               /** Выполняем запрос и ждем результата **/


               axiosLaravel(resultObject, url)
                   .then((result) => {

                       if (result.errors) {

                           /** отправим ошибки **/
                           fieldErrors(result.errors, parentEl)

                           /** снимем ошибки **/
                           removeErrors()

                           /** Выключаем компонент loader **/
                           loader.classList.toggle('active');
                       } else {
                           /** если нет ошибок **/
                           console.log(result.response)

                           /** Выключаем компонент loader **/
                           loader.classList.toggle('active');

                           /** Включаем ответ после выключения loader **/
                           response.classList.add('active');

                           /** Удаляем это окно после включения ответа и выключения loader **/
                           modal.remove();
                       }


                   })
                   .catch((error) => {

                       /** Выключаем компонент loader **/
                       loader.classList.toggle('active');

                   });

           }


       }
   }
}

