import { Controller } from '@hotwired/stimulus';
import { Modal } from 'bootstrap';

export default class extends Controller {

    modalTarget = null;

    connect() {
        this.modalTarget = document.querySelector('.modal-form');
    }

    open({ params }) {
        const modal = new Modal(this.modalTarget);

        this.modalTarget
            .querySelector('.modal-dialog')
            .insertAdjacentHTML(
                'beforeend', 
                '<div class="loading"><div class="spinner-border text-light"></div></div>'
            );
        
        modal.show();

        this.modalTarget.addEventListener('hidden.bs.modal', () => {
            this.removeLoading();
        });

        setTimeout(() => {
            fetch(params.url)
                .then(response => {
                    this.removeLoading();

                    if (!response.ok) {
                        throw new Error(response);
                    }
                    return response.text();
                })
                .then(html => {
                    this.modalTarget.querySelector('.modal-dialog').innerHTML = html;
                })
                .catch(() => {
                    this.modalTarget
                        .querySelector('.modal-dialog')
                        .innerHTML = '<div class="alert alert-danger">An error occurred while loading the form</div>';
                });
        }, 300);
    }

    close() {
        // this.modalTarget.remove();
    }

    removeLoading() {
        this.modalTarget.querySelector('.modal-dialog').innerHTML = '';
    }
}
