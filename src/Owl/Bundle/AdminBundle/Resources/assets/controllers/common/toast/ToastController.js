import { Controller } from '@hotwired/stimulus';
import { Toast } from 'bootstrap';

import { debounce } from '../../../utils/debounce';

export default class extends Controller {

    toasts = [];

    static values = { headersTxt: Object, icons: Object };

    colors = {
        success: 'success',
        error: 'danger'
    };

    show({ detail: { message, type } }) {
        const uniqueId = `message-${this.generateUniqueToastId()}`;

        this.appendToast(
            uniqueId,
            type,
            message
        );

        this.toasts[uniqueId] = Toast.getOrCreateInstance(this.element.querySelector(`#${uniqueId}`));

        this.toasts[uniqueId].show();

        debounce(this.hide, 5000)(uniqueId);
    }

    appendToast(uniqueId, type,  message) {
        const toast = `
            <div
                id="${uniqueId}"
                class="toast mt-3 fade owl owl-toast alert alert-dismissible alert-${this.colors[type]}"
                role="alert"
            >
                <div class="alert-icon">
                    ${this.iconsValue[type]}
                </div>
                <div>
                    <h4 class="alert-heading">
                        ${this.headersTxtValue[type]}
                    </h4>
                    <div class="alert-description text-secondary">
                        ${message}
                    </div>
                </div>
                <button type="button" class="btn-close" data-bs-dismiss="toast" aria-label="Close"></button>
            </div>
        `;

        this.element.insertAdjacentHTML('afterbegin', toast);
    }

    generateUniqueToastId() {
        return Date.now().toString(36) + Math.random().toString(36).substr(2, 5);
    }

    hide = (uniqueId) => {
        if (this.toasts[uniqueId].isShown()) {
            this.toasts[uniqueId].hide();
        }

        this.element.querySelector(`#${uniqueId}`).remove();

        delete this.toasts[uniqueId];
    };
}
