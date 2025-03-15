import { Controller } from '@hotwired/stimulus';
import { Modal } from 'bootstrap';

export default class extends Controller {

    static targets = ['dialog', 'content', 'loading'];

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
                this.addError();
            });
        }, 300);
    }

    close() {
        if (this.modal) {
            this.modal.hide();
        }
    }

    loadingShow(size) {
        if (size && size !== 'default') {
            this.size = size;
            this.element.classList.add(`modal-${size}`);
        }

        this.loadingTarget.style = 'display: block';
    }

    loadingHide() {
        this.loadingTarget.style = 'display: none';
    }

    removeContent() {
        this.element.classList.remove(`modal-${this.size}`);
        this.contentTarget.remove();
    }

    addError() {
        this.dialogTarget.insertAdjacentHTML(
            'afterbegin',
            `<div data-modal-target="content" class="modal-content">
                <div class="alert alert-danger m-3">
                    An error occurred while loading content
                </div>
            </div>`
        );
    }
}
