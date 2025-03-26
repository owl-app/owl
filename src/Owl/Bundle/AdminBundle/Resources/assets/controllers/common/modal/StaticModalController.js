import { Controller } from '@hotwired/stimulus';
import { Modal } from 'bootstrap';

import { showLoader, hideLoader } from '../../../scripts/loader';
import { debounce } from '../../../utils/debounce';
import redirect from '../../../utils/redirect';

export default class extends Controller {

    static targets = ['dialog', 'content', 'error'];

    modal = null;

    connect() {
        this.element.addEventListener('hidden.bs.modal', this.eventHiddenModal.bind(this));
    }

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

    close() {
        if (this.modal) {
            this.modal.hide();
        }
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
                    hideLoader(document.body);
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

    setSize(size) {
        if (size && size !== 'default') {
            this.size = size;
            this.element.classList.add(`modal-${size}`);
        }
    }

    showLoading() {
        const loaderOptions = {
            class: {
                loader: 'modal',
                spinner: 'text-light'
            },
            width: '5rem',
            height: '5rem'
        };

        this.element.classList.remove('show');

        showLoader(document.body, loaderOptions);
    }

    showContent() {
        this.contentTarget.classList.add('show');
    }

    showError() {
        this.errorTarget.classList.remove('d-none');
    }

    eventHiddenModal() {
        this.element.classList.remove(`modal-${this.size}`);
    }
}
