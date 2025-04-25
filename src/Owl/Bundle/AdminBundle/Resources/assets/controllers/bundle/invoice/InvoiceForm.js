import { Controller } from '@hotwired/stimulus';
import { getComponent } from '@symfony/ux-live-component';

import { debounce } from '../../../utils/debounce';

export default class extends Controller {

    component = null;

    debouncedLienItemChanged = null;

    static targets = ['form', 'issueDate', 'serie', 'sequenceNumber', 'fullNumber'];

    static outlets = ['modal'];

    async initialize() {
        this.component = await getComponent(this.element.querySelector('[data-live-name-value="owl_admin:invoice:form"]'));

        this.issueDateTarget.addEventListener('change', this.handleChangeIssueDate);

        this.debouncedLienItemChanged = debounce((action, key, value) => this.component.action(action, { key, value}), 500);
    }

    async openModalSeries({ params }) {
        const { url } = params;
        const modalParams = {
            params: {
                url: `${url}?date=${this.issueDateTarget.value}`
            }
        };

        if (this.hasModalOutlet) {
            await this.modalOutlet.open(modalParams);
        }
    }

    handleChangeIssueDate = (event) => {
        const { value } = event.target;

        this.component.action('dateIssueChanged', { oldDate: value });
    };

    quantityChanged(event) {
        const { key } = event.params;

        this.debouncedLienItemChanged('quantityChanged', key, event.target.value);
    }

    unitPriceChanged(event) {
        const { key } = event.params;

        this.debouncedLienItemChanged('unitPriceChanged', key, event.target.value);
    }

    subtotalChanged(event) {
        const { key } = event.params;

        this.component.action('subtotalChanged', { key, value: event.target.value });
    }


}
