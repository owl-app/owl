import { Controller } from '@hotwired/stimulus';

export default class extends Controller {

    static targets = ['changeNumber', 'issueDate'];

    static outlets = ['modal'];

    openModalSeries({ params }) {
        const { url } = params;
        const modalParams = {
            params: {
                url: `${url}?date=${this.issueDateTarget.value}`
            }
        };

        if (this.hasModalOutlet) {
            this.modalOutlet.open(modalParams);
        }
    }
}
