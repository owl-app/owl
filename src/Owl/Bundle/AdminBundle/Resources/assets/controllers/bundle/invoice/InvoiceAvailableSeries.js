import { Controller } from '@hotwired/stimulus';

export default class extends Controller {

    selectedForamt = null;

    fullNumber = null;

    static targets = ['serieRadio', 'valueNumber', 'previewNumber'];

    static outlets = ['modal', 'invoice-form'];

    connect() {
        this.serieRadioTargets.forEach(checkbox => {
            checkbox.addEventListener('change', this.handleChangeFormat);
        });
        this.valueNumberTarget.addEventListener('change', this.handleChangeNumber);

        this.initializeSelectedRadio();
    }

    disconnect() {
        this.serieRadioTargets.forEach(checkbox => {
            checkbox.removeEventListener('change', this.handleChangeFormat);
        });
    }

    initializeSelectedRadio() {
        this.serieRadioTargets.forEach((radio) => {
            const { numberTarget, serieTarget } = this.invoiceFormOutlet;

            if (radio.dataset.formatId === serieTarget.value) {
                radio.checked = true;
                
                this.setNumber(numberTarget.value);
                this.setFullNumber(numberTarget.value, radio.dataset.format);
                this.setSelectedFormat(radio.dataset);
            }
        });
    }

    confirm() {
        const { numberTarget, fullNumberTarget, serieTarget, previewNumberTarget } = this.invoiceFormOutlet;
        const event = new Event('change', { bubbles: true });

        numberTarget.value = this.valueNumberTarget.value;
        fullNumberTarget.value = previewNumberTarget.innerHTML = this.fullNumber;
        serieTarget.value = this.selectedForamt.id;

        fullNumberTarget.dispatchEvent(event);
        numberTarget.dispatchEvent(event);
        serieTarget.dispatchEvent(event);

        this.modalOutlet.close();
    }

    setNumber(number) {
        this.valueNumberTarget.value = number;
    }

    setFullNumber(number, format) {
        const { issueDateTarget } = this.invoiceFormOutlet;
        const date = new Date(issueDateTarget.value);

        const search = ['__YYYY__', '__MM__', '__NUMBER__'];
        const replace = [date.getFullYear(), String(date.getMonth() + 1).padStart(2, '0'), number];

        let result = format;

        search.forEach((token, index) => {
            const regex = new RegExp(token, 'g');
            result = result.replace(regex, replace[index]);
        });

        this.fullNumber = this.previewNumberTarget.innerHTML = result;
    }

    setNextNumber(fullNumber, nextNumber) {
        this.fullNumber = this.previewNumberTarget.innerHTML = fullNumber;
        this.valueNumberTarget.value = nextNumber;
    }

    setSelectedFormat(data) {
        this.selectedForamt = {
            id: data.formatId,
            nextValue: data.nextValue,
            nextCounter: data.nextCounter,
            format: data.format
        };
    }

    handleChangeFormat = (event) => {
        this.setSelectedFormat(event.target.dataset);
        this.setNextNumber(event.target.dataset.nextValue, event.target.dataset.nextCounter);
    };

    handleChangeNumber = (event) => {
        this.setFullNumber(event.target.value, this.selectedForamt.format);
    };
}
