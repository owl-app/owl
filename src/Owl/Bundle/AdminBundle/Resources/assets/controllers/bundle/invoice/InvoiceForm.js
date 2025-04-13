import { Controller } from '@hotwired/stimulus';

export default class extends Controller {

    static targets = ['changeNumber', 'previewNumber', 'issueDate', 'serie', 'number', 'fullNumber'];

    static outlets = ['modal'];

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
}
