// assets/controllers/custom-autocomplete_controller.js
import { Controller } from '@hotwired/stimulus';
import { getComponent } from '@symfony/ux-live-component';

export default class extends Controller {
    tomSelect = null;

    formInvoiceComponent = null;

    static values = {
        textBy: String,
        actionAfterChange: String
    };

    async initialize() {
        this.formInvoiceComponent = await getComponent(document.querySelector('[data-live-name-value="owl_admin:invoice:form"]'));

        this._onConnect = this._onConnect.bind(this);
    }

    connect() {
        this.element.addEventListener('autocomplete:connect', this._onConnect);
        this.element.addEventListener('autocomplete:pre-connect', this._onPreConnect);
    }

    disconnect() {
        this.element.removeEventListener('autocomplete:connect', this._onConnect);
        this.element.removeEventListener('autocomplete:pre-connect', this._onPreConnect);
    }

    _onConnect(event) {
        this.tomSelect = event.detail.tomSelect;
    }

    _onPreConnect = (event) => {
        event.detail.options.onChange = () => {
            this.formInvoiceComponent.action(this.actionAfterChangeValue);
        };
    };

    addOption({ detail: { resource = null } }) {
        this.tomSelect.addOption({
            value: resource.id,
            text: resource[this.textByValue]
        });

        this.tomSelect.setValue([resource.id]);
    }
}