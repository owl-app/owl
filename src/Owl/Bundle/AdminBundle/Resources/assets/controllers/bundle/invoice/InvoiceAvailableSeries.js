import { Controller } from '@hotwired/stimulus';

export default class extends Controller {

    static targets = ['serieRadio', 'valueNumber', 'previewNumber'];

    static outlets = ['modal'];

    connect() {
        this.serieRadioTargets.forEach(checkbox => {
            checkbox.addEventListener('change', this.handleChangeFormat);
        });
    }

    disconnect() {
        this.serieRadioTargets.forEach(checkbox => {
            checkbox.removeEventListener('change', this.handleChangeFormat);
        });
    }

    handleChangeFormat = (event) => {
        this.previewNumberTarget.innerHTML = event.target.dataset.nextValue;
        this.valueNumberTarget.value = event.target.dataset.nextCounter;
    };
}
