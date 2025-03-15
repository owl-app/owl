import { Controller } from '@hotwired/stimulus';
import { Modal } from 'bootstrap';

import { showLoader, hideLoader } from '../../../scripts/loader';

export default class extends Controller {

    static targets = ['dialog', 'content', 'error'];

    modal = null;

    connect() {
        this.element.addEventListener('hidden.bs.modal', () => {
            this.removeContent();
        });
    }

    open({ params: { url, size = 'lg'} }) {
        this.modal = new Modal(this.element, {
            backdrop: 'static',
            keyboard: true,
            focus: true
        });

        this.loadingShow(size);
        
        this.modal.show();

        setTimeout(() => {
            fetch(url, {
                headers: {
                    'X-Requested-With': 'XMLHttpRequest'
                }
            }).then(response => {
                this.loadingHide();

                if (!response.ok) {
                    throw new Error(response);
                }

                return response.text();
            }).then(html => {
                this.dialogTarget.insertAdjacentHTML('afterbegin', html);
            }).catch(() => {
                this.showError();
            });
        }, 300);
    }

    close() {
        if (this.modal) {
            this.modal.hide();
        }
    }

    loadingShow(size) {
        const loaderOptions = {
            class: { 
                spinner: 'text-light'
            }, 
            width: '5rem',
            height: '5rem' 
        };

        if (size && size !== 'default') {
            this.size = size;
            this.element.classList.add(`modal-${size}`);
        }

        showLoader(this.dialogTarget, loaderOptions);
    }

    loadingHide() {
        hideLoader(this.dialogTarget);
    }

    removeContent() {
        this.element.classList.remove(`modal-${this.size}`);

        if (this.hasContentTarget) {
            this.contentTarget.remove();
        }

        if (this.hasErrorTarget) {
            this.errorTarget.classList.add('d-none');
        }
    }

    showError() {
        this.errorTarget.classList.remove('d-none');
    }
}
