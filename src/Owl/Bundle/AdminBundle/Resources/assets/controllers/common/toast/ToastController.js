import { Controller } from '@hotwired/stimulus';
import { debounce } from '../../../utils/debounce';

export default class extends Controller {

    static targets = ['header', 'title', 'body'];

    show({ detail: { message, type } }) {
        this.bodyTarget.innerHTML = message;

        this.element.classList.add('show');

        switch (type) {
        case 'success':
            this.headerTarget.classList.add('bg-success');
            break;
        case 'error':
            this.headerTarget.classList.add('bg-danger');
            break;
        }

        debounce(this.hide, 3000)();
    }

    hide = () => {
        this.element.classList.remove('show');
    }
}
