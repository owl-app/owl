import { Controller } from '@hotwired/stimulus';

export default class extends Controller {

    onOpenModal(event) {
        const groupItems = Array.from(document.querySelectorAll('input[data-check-all-group="index"]'));
        const checked = [];

        groupItems.forEach((item) => {
            if (item.checked) {
                checked.push(item.value);
            }
        });

        event.detail.request.data.ids = checked;
    }

}
