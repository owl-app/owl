import { Controller } from '@hotwired/stimulus';

export default class extends Controller {
    static outlets = ['modal', 'modal-static'];

    open(event) {
        event.preventDefault();

        if (this.hasModalOutlet) {
            this.modalOutlet.open(event);
        } else if (this.hasModalStaticOutlet) {
            this.modalStaticOutlet.open(event);
        }
    }
}
