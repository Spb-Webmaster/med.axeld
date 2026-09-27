import {axiosLaravel} from '../axios/axiosLaravel.js';
import {scrf} from '../csrf.js';

export function scrollCabinetMessages() {
    document.querySelectorAll('.cabinet-messages-container').forEach(el => {
        el.scrollTop = el.scrollHeight;
    });
}

export function cabinetMessageDeleteInit() {

    document.addEventListener('DOMContentLoaded', scrollCabinetMessages);

    document.addEventListener('click', function (e) {
        const btn = e.target.closest('.cabinet-msg-delete');
        if (!btn) return;

        e.stopPropagation();

        const messageId = btn.dataset.msgId;
        const url       = btn.dataset.url;
        const token     = scrf();

        const formData = new FormData();
        formData.append('_token', token);
        formData.append('message_id', messageId);

        axiosLaravel(formData, url)
            .then((response) => {
                if (response.success) {
                    const item = document.querySelector(`.cabinet-msg-item[data-msg-id="${messageId}"]`);
                    if (item) item.remove();
                }
            });
    });

}
