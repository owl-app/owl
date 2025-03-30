import { Controller } from '@hotwired/stimulus';

import { hideLoader, showLoader } from '../../../scripts/loader';
import { debounce } from '../../../utils/debounce';

export class BaseModal extends Controller {

    modal = null;

    connect() {
        this.element.addEventListener('hidden.bs.modal', this.eventHiddenModal);
    }

    disconnect() {
        this.element.removeEventListener('hidden.bs.modal', this.eventHiddenModal);
    }

    eventHiddenModal() {
        throw new Error('The abstract method must be implemented in the subclass');
    }

    close() {
        if (this.modal) {
            this.modal.hide();

            this.modal = null;
        }
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

    hideLoading() {
        hideLoader(document.body);
    }

    showContent() {
        this.contentTarget.classList.add('show');
    }

    showError() {
        this.errorTarget.classList.remove('d-none');

        debounce(() => {
            this.errorTarget.classList.add('show');
        }, 100)();
    }
}