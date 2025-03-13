import { Controller } from '@hotwired/stimulus';
import { Modal } from 'bootstrap';

export default class extends Controller {

    connect() {
        this.element.addEventListener('hidden.bs.modal', () => {
            this.clearContent();
        });
    }

    open({ params: { url } }) {
        const modal = new Modal(this.element);

        this.element
            .querySelector('.modal-dialog')
            .insertAdjacentHTML(
                'beforeend', 
                '<div class="loading"><div class="spinner-border text-light"></div></div>'
            );
        
        modal.show();

        setTimeout(() => {
            fetch(url)
                .then(response => {
                    this.clearContent();

                    if (!response.ok) {
                        throw new Error(response);
                    }
                    return response.text();
                })
                .then(html => {
                    this.element.querySelector('.modal-dialog').innerHTML = html;
                })
                .catch(() => {
                    this.element
                        .querySelector('.modal-dialog')
                        .innerHTML = '<div class="alert alert-danger">An error occurred while loading the form</div>';
                });
        }, 300);
    }

    clearContent() {
        this.element.querySelector('.modal-dialog').innerHTML = '';
    }
}
