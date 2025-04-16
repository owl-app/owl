import { Controller } from '@hotwired/stimulus';
import { getComponent } from '@symfony/ux-live-component';

export default class extends Controller {

    component = null;

    static targets = ['form', 'issueDate', 'serie', 'sequenceNumber', 'fullNumber'];

    static outlets = ['modal'];

    async initialize() {
        this.component = await getComponent(this.element.querySelector('[data-live-name-value="owl_admin:invoice:form"]'));

        this.issueDateTarget.addEventListener('change', this.handleChangeIssueDate);
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
}
