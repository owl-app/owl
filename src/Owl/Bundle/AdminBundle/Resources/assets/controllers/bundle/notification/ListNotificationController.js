
import { Controller } from '@hotwired/stimulus';

export default class extends Controller {

    count = 0;

    static targets = ['empty', 'count', 'list', 'notification'];

    initialize() {
        this.count = this.notificationTargets.length;
    }

    remove({ detail: { notificationId } }) {
        this.count = this.count - 1;

        if (this.count === 0) {
            this.emptyTarget.classList.remove('d-none');
            this.listTarget.classList.add('d-none');
            this.countTarget.classList.add('d-none');
        } else {
            this.countTarget.innerText = this.count;
        }

        const notification = this.notificationTargets.find(notification =>  parseInt(notification.dataset.notificationId) === notificationId);

        if (notification) {
            notification.remove();
        }
    }
}
