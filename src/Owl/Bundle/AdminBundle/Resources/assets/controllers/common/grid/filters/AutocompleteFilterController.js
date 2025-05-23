import { Controller } from '@hotwired/stimulus';
import { getComponent } from '@symfony/ux-live-component';
import { updateSingleQueryParam } from '../../../../utils/url';

export default class extends Controller {
    tomSelect = null;

    static values = {
        field: String,
        formLiveName: String
    };

    async initialize() {
        this.component = await getComponent(document.querySelector(`[data-live-name-value="${this.formLiveNameValue}"]`));
    }

    connect() {
        this.element.addEventListener('autocomplete:pre-connect', this._onPreConnect);
    }

    disconnect() {
        this.element.removeEventListener('autocomplete:pre-connect', this._onPreConnect);
    }

    _onPreConnect = (event) => {
        event.detail.options.onChange = async (value) => {
            await this.component.action('updateFilter', { field: this.fieldValue });

            Turbo.visit(updateSingleQueryParam(this.fieldValue, value, 'criteria'), {
                action: 'replace',
                history: true
            });
        };
    };
}