import { Controller } from '@hotwired/stimulus';

export default class extends Controller {
    static outlets = ['modal'];

    open(params) {
        this.modalOutlet.open(params);
    }
}
