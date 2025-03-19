import { Controller } from '@hotwired/stimulus';
import { Modal } from 'bootstrap';

import { showLoader, hideLoader } from '../../../scripts/loader';
import { debounce } from '../../../utils/debounce';

export default class extends Controller {

    static targets = ['dialog', 'content', 'error'];

    modal = null;

    connect() {
        this.element.addEventListener('hidden.bs.modal', this.removeContent.bind(this));
    }

    open({ params: { url, size = 'lg' } }) {
        this.modal = new Modal(this.element, {
            backdrop: 'static',
            keyboard: true
        });

        this.loadingShow(size);

        this.modal.show();

        debounce(this.loadContent.bind(this), 200)(url);
    }

    close() {
        if (this.modal) {
            this.modal.hide();
        }
    }

    loadContent(url) {
        fetch(url, {
            headers: {
                'X-Requested-With': 'XMLHttpRequest'
            }
        }).then(response => {
            if (!response.ok) {
                throw new Error(response);
            }

            return response.text();
        }).then(html => {
            this.dialogTarget.insertAdjacentHTML('afterbegin', html);

            debounce(this.afterLoadContent.bind(this), 100)();
        }).catch(() => {
            this.showError();
        });
    }

    afterLoadContent() {
        this.loadingHide();
        this.showContent();
    }

    loadingShow(size) {
        const loaderOptions = {
            class: {
                loader: 'modal',
                spinner: 'text-light'
            },
            width: '5rem',
            height: '5rem'
        };

        if (size && size !== 'default') {
            this.size = size;
            this.element.classList.add(`modal-${size}`);
        }

        showLoader(document.body, loaderOptions);
    }

    loadingHide() {
        hideLoader(document.body);
    }

    showContent() {
        this.contentTarget.classList.add('show');
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
