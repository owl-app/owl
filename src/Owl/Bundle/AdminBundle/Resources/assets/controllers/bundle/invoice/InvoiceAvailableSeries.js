import { Controller } from '@hotwired/stimulus';
import { getComponent } from '@symfony/ux-live-component';

export default class extends Controller {

    component = null;

    static targets = [
        'form'
    ];

    static outlets = ['modal', 'invoice-form'];

    async initialize() {
        this.component = await getComponent(this.element.querySelector('[data-live-name-value="owl_admin:invoice:numbering:form"]'));

        const { sequenceNumberTarget, serieTarget, issueDateTarget } = this.invoiceFormOutlet;

        this.component.set(`${this.formTarget.name}.number`, sequenceNumberTarget.value);
        this.component.set(`${this.formTarget.name}.serie`, serieTarget.value);
        this.component.set('issueDate', issueDateTarget.value);

        this.component.render();
    }

    async confirm() {
        const { response } = await this.component.action('confirm');

        if (response.status === 200) {
            this.modalOutlet.close();
        }
    }
}
