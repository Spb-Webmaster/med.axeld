export function removeErrors(parentEl = null) {
    const inputGroups = Array.from(document.querySelectorAll('.app_input_group'))


    if (inputGroups.length) {
        for (let inputGroup of inputGroups) {
            const input = inputGroup.querySelector('.app_input_name')
            if (input) {
                input.addEventListener('focus', resetInputError)
                input.addEventListener('input', resetInputError)
            }

        }
    }

    function resetInputError(e) {
        /** Доберемся до группы (app_input_group) **/
        const inputGroup = e.target.closest('.app_input_group')

        /** опустимся до input  **/
        const input = inputGroup.querySelector('.app_input_name')
        /** уберем у input красную рамку **/
        input.classList.remove('_error')
        /** опустимся до div с class app_input_error  **/
        const errorMessageDiv = inputGroup.querySelector('.app_input_error')
        /** очистим app_input_error  **/
        errorMessageDiv.textContent = '';
    }


    const selGroups = Array.from(document.querySelectorAll('.app_select_group'))

    for (let selGroup of selGroups) {
        const sel = selGroup.querySelector('.selected')
        sel.addEventListener('click', resetSelError)
    }


    function resetSelError(e) {
        /** Доберемся до группы (app_input_group) **/
        const selGroup = e.target.closest('.app_select_group')

        /** опустимся до selected  **/
        const sel = selGroup.querySelector('.selected')
        /** уберем у selected красную рамку **/
        sel.classList.remove('_error')
        /** опустимся до div с class app_input_error  **/
        const errorMessageDiv = selGroup.querySelector('.app_input_error')
        /** очистим app_input_error  **/
        errorMessageDiv.textContent = '';
    }

}
