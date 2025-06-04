import { Controller } from '@hotwired/stimulus';

export default class extends Controller {

    isShow = false;

    static targets = ['filter', 'button'];

    static values = {
        formLiveName: String
    };

    connect() {
        document.addEventListener('click', this.clickOuterPopup);
    }

    disconnect() {
        document.removeEventListener('click', this.clickOuterPopup);
    }

    navigate(event) {
        event.preventDefault();
        event.stopPropagation();
        const { field, value } = event.params;
    
        window.dispatchEvent(new CustomEvent('filter:changed', { detail: { field, value } }));
    }

    togglePopup() {
        if (this.isShow) {
            this.filterTarget.classList.remove('d-flex');
            this.filterTarget.classList.add('d-none');

            this.isShow = false;
        } else {
            this.filterTarget.classList.add('d-flex');
            this.filterTarget.classList.remove('d-none');

            this.isShow = true;
        }
    }

    clickOuterPopup = (e) => {
        if (!this.filterTarget.contains(e.target) && !this.buttonTarget.contains(e.target)) {
            if (this.isShow) {
                this.filterTarget.classList.remove('d-flex');
                this.filterTarget.classList.add('d-none');
    
                this.isShow = false;
            }
        }
    };
}
