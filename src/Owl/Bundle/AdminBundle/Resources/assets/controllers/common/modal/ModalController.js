import { Modal } from 'bootstrap';

import { debounce } from '../../../utils/debounce';
import { initializeTinyMce } from '../../../scripts/tinymce';

import { BaseModal } from './BaseModal';

export default class extends BaseModal {

    withRedirect = true;

    static targets = ['dialog', 'content', 'error'];

    open({ params: { url, size = 'lg', hasTinymce = false, withRedirect = true } }) {
        this.setSize(size);
        this.showLoading(size);

        this.withRedirect = withRedirect;

        this.modal = new Modal(this.element, {
            backdrop: 'static',
            keyboard: true
        });

        this.modal.show();

        debounce(this.loadContent.bind(this), 200)(url, hasTinymce);
    }

    loadContent(url, hasTinymce) {
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

            debounce(this.afterLoadContent.bind(this), 100)(hasTinymce);
        }).catch(() => {
            this.showError();

            this.hideLoading();
        });
    }

    afterLoadContent(hasTinymce) {
        this.hideLoading();
        this.showContent();

        if (hasTinymce) {
            initializeTinyMce();
        }
    }

    eventHiddenModal = () => {
        this.element.classList.remove(`modal-${this.size}`);

        if (this.hasContentTarget) {
            this.contentTarget.remove();
        }

        if (this.hasErrorTarget) {
            this.errorTarget.classList.add('d-none');
            this.errorTarget.classList.remove('show');
        }
    };
}
