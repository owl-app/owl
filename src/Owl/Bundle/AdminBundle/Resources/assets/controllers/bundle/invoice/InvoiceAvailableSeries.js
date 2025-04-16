import { Controller } from '@hotwired/stimulus';
import { getComponent } from '@symfony/ux-live-component';

export default class extends Controller {

    component = null;

    static targets = [
        'form',
        'serieRadio'
    ];

    static outlets = ['modal', 'invoice-form'];

    async initialize() {
        this.component = await getComponent(this.element.querySelector('[data-live-name-value="owl_admin:invoice:numbering:form"]'));

        const { formTarget: invoiceFormTarget, component: invoiceComponent } = this.invoiceFormOutlet;
        const serie = invoiceComponent.getData(`${invoiceFormTarget.name}.serie`);

        this.component.set(`${this.formTarget.name}.number`, invoiceComponent.getData(`${invoiceFormTarget.name}.sequenceNumber`));
        this.component.set(`${this.formTarget.name}.serie`, serie);
        this.component.set('issueDate', invoiceComponent.getData(`${invoiceFormTarget.name}.issueDate`));

        if (serie === '') {
            this.component.set(`${this.formTarget.name}.fullNumber`, invoiceComponent.getData(`${invoiceFormTarget.name}.fullNumber`));
        }

        await this.component.render();

        this.serieRadioTargets.forEach(checkbox => {
            checkbox.addEventListener('change', this.handleChangeSerie);
        });
    }

    disconnect() {
        this.serieRadioTargets.forEach(checkbox => {
            checkbox.removeEventListener('change', this.handleChangeSerie);
        });
    }

    async confirm() {
        const { response } = await this.component.action('confirm');

        if (response.status === 200) {
            this.modalOutlet.close();
        }
    }

    handleChangeSerie = (event) => {
        this.component.action('changeSerie', { serieValue: event.target.value });
    };
}
