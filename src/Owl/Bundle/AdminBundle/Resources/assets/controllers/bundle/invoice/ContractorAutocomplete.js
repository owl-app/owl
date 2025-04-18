// assets/controllers/custom-autocomplete_controller.js
import { Controller } from '@hotwired/stimulus';

export default class extends Controller {
    tomSelect = null;

    initialize() {
        this._onConnect = this._onConnect.bind(this);
    }

    connect() {
        this.element.addEventListener('autocomplete:connect', this._onConnect);
    }

    disconnect() {
        this.element.removeEventListener('autocomplete:connect', this._onConnect);
    }

    _onConnect(event) {
        this.tomSelect = event.detail.tomSelect;
    }

    addOption({ detail: { resource = null } }) {
        this.tomSelect.addOption({
            value: resource.id,
            text: resource.companyName
        });

        this.tomSelect.setValue([resource.id]);
    }
}