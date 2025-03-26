import { Controller } from '@hotwired/stimulus';

export default class extends Controller {

    countChecked = 0;

    static targets = ['checkbox', 'checkAll', 'action'];

    static outlets = ['modal-static'];

    connect() {
        this.checkboxTargets.forEach(checkbox => {
            checkbox.addEventListener('change', this.handleChangeCheckbox);
        });

        this.checkAllTarget.addEventListener('change', this.handleCheckAll);

        document.addEventListener('turbo:before-cache', this.cleanup);
    }

    disconnect() {
        this.checkboxTargets.forEach(checkbox => {
            checkbox.removeEventListener('change', this.handleChangeCheckbox);
        });

        this.checkAllTarget.removeEventListener('change', this.handleCheckAll);

        document.removeEventListener('turbo:before-cache', this.cleanup);
    }

    confirmAction(event) {

        if (event.params.request.url === undefined) {
            throw new Error('The request.url parameter is required.');
        }

        const data = Object.assign(event.params.request?.data ?? {}, {
            ids: this.checkboxTargets
                .filter(checkbox => checkbox.checked)
                .map(checkbox => checkbox.value)
        });

        event.params.request.data = data;

        this.modalStaticOutlet.open(event);
    }

    changeVisbililityActions() {
        this.actionTargets.forEach(action => {
            action.disabled = this.countChecked > 0 ? false : true;
        });
    }

    handleChangeCheckbox = (event) => {
        if (event.target.checked) {
            this.countChecked++;
        } else {
            this.countChecked--;
        }

        switch (this.countChecked) {
        case this.checkboxTargets.length:
            this.checkAllTarget.indeterminate = false;
            this.checkAllTarget.checked = true;
            break;
        case 0:
            this.checkAllTarget.indeterminate = false;
            this.checkAllTarget.checkedd = false;
            break;
        default:
            this.checkAllTarget.indeterminate = true;
            this.checkAllTarget.checked = false;
            break;
        }

        this.changeVisbililityActions();
    };

    handleCheckAll = () => {
        const checked = this.countChecked > 0;

        this.checkAllTarget.checked = !checked;
        this.checkboxTargets.forEach(checkbox => {
            checkbox.checked = !checked;
        });

        if (!checked) {
            this.countChecked = this.checkboxTargets.length;
        } else {
            this.countChecked = 0;
        }

        this.changeVisbililityActions();
    };

    cleanup = () => {
        this.countChecked = 0;
        this.checkAllTarget.checked = false;
        this.checkboxTargets.forEach(checkbox => {
            checkbox.checked = false;
        });

        this.changeVisbililityActions();
    };
}
