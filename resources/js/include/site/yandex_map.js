export function yandex_map_object(key){
    setTimeout(function () {

const yMap = document.querySelector('#JFormFieldMap');
const loader = document.querySelector('#loader_wrapper');
const wLoader = document.querySelector('.wrapper_loader');

        if(yMap !== null) {

            var elem = document.createElement('script');
            elem.type = 'text/javascript';
            elem.src = '//api-maps.yandex.ru/2.1/?apikey='+ key +'&package.standard&lang=ru_RU&onload=getYaMap';
            document.getElementsByTagName('body')[0].appendChild(elem);

            if(loader !== null) {
                loader.style.visibility = 'hidden';
            }
            if(wLoader !== null) {
                wLoader.style.display = 'none';
            }
        }

    }, 2000);


}
