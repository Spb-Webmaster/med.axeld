export function close_flash() {

    const closeFModal = document.querySelector('.app_add_hm')
    if (!closeFModal) return;

    closeFModal.addEventListener('click', CloseModal)

    function CloseModal(e) {
        const parentEl = e.target.closest('.flashMassege');
        parentEl.remove();
    }


}
