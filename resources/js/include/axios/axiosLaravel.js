import {scrf} from "../csrf";
export async function axiosLaravel(resultArray, url) {

    /** Устанавливаем токен CSRF по умолчанию для всех запросов Axios **/
    const token = scrf();
        try {


            /** Отправляем POST-запрос с помощью Axios **/
            const response = await  axios.post(url,
                resultArray,
                { headers: {
                         'X-CSRF-TOKEN':token}
                });


            /** Работаем с ответом **/
            return response.data
        } catch (error) {

            if (error.response && error.response.status === 419) {
                // запрашиваем новый токен
                const resFreshToken = await axios.get('/refresh-csrf');
                const freshToken = resFreshToken.data.csrf_token;

                // повторяем запрос с новым токеном
                const response = await axios.post(url, resultArray, {
                    headers: {
                        'X-CSRF-TOKEN': freshToken
                    }
                });
                return response.data;
            }


            /** Обрабатываем возможную ошибку **/
            //console.log(error.message)
            /** Если ошибка связана с валидацией (код 422) **/
            if(error.response && error.response.status === 422) {
                return error.response.data; // Возвращаем объект с ошибками
            }
            /** Иначе возвращаем общую ошибку **/
            return { error: error.message };
        }


}
