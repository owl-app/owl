import { Controller } from '@hotwired/stimulus';

export default class extends Controller {
    static outlets = ['modal', 'modal-static'];

    open(event) {
        event.preventDefault();

        const dispatchedEvent = this.dispatch('show:modal', { detail: event.params });

        if (Object.keys(dispatchedEvent.detail).length !== 0 ) {
            Object.assign(event.params, dispatchedEvent.detail);
        }

        if (this.hasModalOutlet) {
            this.modalOutlet.open(event);
        } else if (this.hasModalStaticOutlet) {
            this.modalStaticOutlet.open(event);
        }
    }
}
