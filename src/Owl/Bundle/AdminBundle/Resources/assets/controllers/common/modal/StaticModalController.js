import { Modal } from 'bootstrap';

import { debounce } from '../../../utils/debounce';
import redirect from '../../../utils/redirect';

import { BaseModal } from './BaseModal';

export default class extends BaseModal {

    static targets = ['dialog', 'content', 'error'];

    open({ params }) {
        this.configuration = params.configuration ?? {};
        this.request = params.request;
        this.modal = new Modal(this.element, {
            backdrop: 'static',
            keyboard: true
        });

        this.setSize(this.configuration?.size);

        this.modal.show();
    }

    run() {
        this.showLoading();

        fetch(this.request.url, {
            method: 'POST',
            body: this.objectToFormData(this.request.data),
            headers: {
                'X-Requested-With': 'XMLHttpRequest'
            }
        }).then(async response => {
            if (response.ok) {
                this.close();

                debounce(() => {
                    this.hideLoading();
                }, 100)();

                debounce(async () => {
                    if (response.headers.has('x-owl-location')) {
                        redirect(response.headers.get('x-owl-location'));
                    }
                }, 200)();
            }
        }).catch(() => {
            this.hide();
        });
    }

    objectToFormData(obj, formData = new FormData(), parentKey = '') {
        for (const key in obj) {
            if (Object.prototype.hasOwnProperty.call(obj, key)) {
                const fullKey = parentKey ? `${parentKey}[${key}]` : key;
                const value = obj[key];
    
                if (value instanceof File) {
                    formData.append(fullKey, value);
                } else if (Array.isArray(value)) {
                    value.forEach((val, index) => {
                        formData.append(`${fullKey}[${index}]`, val);
                    });
                } else if (typeof value === 'object' && value !== null) {
                    this.objectToFormData(value, formData, fullKey);
                } else {
                    formData.append(fullKey, value);
                }
            }
        }
        return formData;
    }

    eventHiddenModal = () => {
        this.element.classList.remove(`modal-${this.size}`);
    };
}
