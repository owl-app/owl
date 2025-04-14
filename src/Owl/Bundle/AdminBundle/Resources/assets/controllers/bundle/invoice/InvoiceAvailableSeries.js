import { Controller } from '@hotwired/stimulus';

export default class extends Controller {

    selectedForamt = null;

    squenceFullNumber = null;

    static targets = [
        'serieRadio',
        'valueSequenceNumber',
        'previewNumberText',
        'previewNumberBox',
        'sequenceNumberBox',
        'valueFullNumber',
        'fullNumberBox'
    ];

    static outlets = ['modal', 'invoice-form'];

    connect() {
        this.serieRadioTargets.forEach(checkbox => {
            checkbox.addEventListener('change', this.handleChangeFormat);
        });
        this.valueSequenceNumberTarget.addEventListener('change', this.handleChangeSequenceNumber);
        this.valueFullNumberTarget.addEventListener('change', this.handleChangeFullNumber);

        this.initializeSelectedRadio();
    }

    disconnect() {
        this.serieRadioTargets.forEach(checkbox => {
            checkbox.removeEventListener('change', this.handleChangeFormat);
        });
    }

    initializeSelectedRadio() {
        this.serieRadioTargets.forEach((radio) => {
            const { sequenceNumberTarget, serieTarget, fullNumberTarget } = this.invoiceFormOutlet;

            if (radio.dataset.formatId === serieTarget.value) {
                radio.checked = true;
                
                this.setNumber(sequenceNumberTarget.value);
                this.initializeFullNumber(fullNumberTarget.value, radio.dataset.formatId);
                this.setSelectedFormat(radio.dataset);
                this.toggleBoxNumber(radio.dataset.formatId);
            }
        });
    }

    initializeFullNumber(number, formatId) {
        this.fullNumber = number;

        if(formatId !== '') {
            this.previewNumberTextTarget.innerHTML = number;
        } else {
            this.valueFullNumberTarget.value = number;
        }
    }

    confirm() {
        if (!this.validation()) return;
    
        const { sequenceNumberTarget, fullNumberTarget, serieTarget, previewNumberTarget } = this.invoiceFormOutlet;
        const event = new Event('change', { bubbles: true });

        sequenceNumberTarget.value = this.valueSequenceNumberTarget.value;
        fullNumberTarget.value = previewNumberTarget.innerHTML = this.fullNumber;
        serieTarget.value = this.selectedForamt.id;

        fullNumberTarget.dispatchEvent(event);
        sequenceNumberTarget.dispatchEvent(event);
        serieTarget.dispatchEvent(event);

        this.modalOutlet.close();
    }

    setNumber(number) {
        this.valueSequenceNumberTarget.value = number;
    }

    generateFullNumber(number, format) {
        const { issueDateTarget } = this.invoiceFormOutlet;
        const date = new Date(issueDateTarget.value);

        const search = ['__YYYY__', '__MM__', '__NUMBER__'];
        const replace = [date.getFullYear(), String(date.getMonth() + 1).padStart(2, '0'), number];

        let result = format;

        search.forEach((token, index) => {
            const regex = new RegExp(token, 'g');
            result = result.replace(regex, replace[index]);
        });

        return result;
    }

    setNextNumber(fullNumber, nextNumber) {
        this.fullNumber = this.previewNumberTextTarget.innerHTML = fullNumber;
        this.valueSequenceNumberTarget.value = nextNumber;
    }

    setSelectedFormat(data) {
        this.selectedForamt = {
            id: data.formatId,
            nextValue: data.nextValue,
            nextCounter: data.nextCounter,
            format: data.format
        };
    }

    toggleBoxNumber(formatId) {
        let show = this.previewNumberBoxTarget;
        let hide = this.fullNumberBoxTarget;

        if (formatId === '') {
            show = this.fullNumberBoxTarget;
            hide = this.previewNumberBoxTarget;
        }

        show.classList.add('d-block');
        show.classList.remove('d-none');
        hide.classList.add('d-none');
        hide.classList.remove('d-block');
    }

    validation() {
        let isValidSequenceNumber = this.valueSequenceNumberTarget.value.match(/^[0-9]+$/);
        let isValidFullNumber = true;

        this.valueSequenceNumberTarget.classList[isValidSequenceNumber ? 'remove' : 'add']('is-invalid');
        this.valueSequenceNumberTarget.nextElementSibling.classList[isValidSequenceNumber ? 'add' : 'remove']('d-none');

        if (this.selectedForamt.id === '') {
            isValidFullNumber = this.valueFullNumberTarget.value !== '';

            this.valueFullNumberTarget.classList[isValidFullNumber ? 'remove' : 'add']('is-invalid');
            this.valueFullNumberTarget.nextElementSibling.classList[isValidFullNumber ? 'add' : 'remove']('d-none');
        }

        return isValidSequenceNumber && isValidFullNumber;
    }

    resetValidation() {
        [this.valueSequenceNumberTarget, this.valueFullNumberTarget].forEach((input) => {
            input.classList.remove('is-invalid');
            input.nextElementSibling.classList.add('d-none');
        });
    }

    handleChangeFormat = (event) => {
        this.setSelectedFormat(event.target.dataset);
        this.setNextNumber(event.target.dataset.nextValue, event.target.dataset.nextCounter);

        this.toggleBoxNumber(event.target.dataset.formatId);

        this.resetValidation();
    };

    handleChangeSequenceNumber = (event) => {
        this.validation();

        if (this.selectedForamt.id !== '') {
            this.fullNumber = this.previewNumberTextTarget.innerHTML = this.generateFullNumber(
                event.target.value, this.selectedForamt.format
            );
        }
    };

    handleChangeFullNumber = (event) => {
        this.validation();

        this.fullNumber = event.target.value;
    };
}
