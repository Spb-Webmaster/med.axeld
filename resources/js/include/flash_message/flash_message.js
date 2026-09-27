export function flash_message() {
    const flashMessage = document.querySelector('.app_flach_message')
    const flashClose = document.querySelector('.app_f_message_close')
    if (flashMessage && flashClose) {
        flashClose.addEventListener('click', function () {
            flashMessage.remove()
        })
    }
}

