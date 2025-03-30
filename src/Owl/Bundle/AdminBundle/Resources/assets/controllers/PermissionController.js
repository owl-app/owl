import { Controller } from '@hotwired/stimulus';

export default class extends Controller {

    static targets = ['form', 'checkbox'];

    static values = { assignUrl: String, revokeUrl: String, messages: Object };

    connect() {
        this.checkboxTarget.addEventListener('change', this.handleChangeCheckbox);
    }

    disconnect() {
        this.checkboxTarget.removeEventListener('change', this.handleChangeCheckbox);
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

    handleChangeCheckbox = async (event) => {
        if (event.target.checked) {
            await this.sendRequest(this.assignUrlValue, 'POST', this.messagesValue.assign);
        } else {
            await this.sendRequest(this.revokeUrlValue, 'DELETE', this.messagesValue.revoke);
        }
    };

    sendRequest(url, method, message) {
        const formData = new FormData(this.element);

        this.checkboxTarget.setAttribute('disabled', true);

        fetch(url, {
            method,
            body: this.serializeForm(formData),
            headers: {
                'X-Requested-With': 'XMLHttpRequest',
                'Content-Type': 'application/x-www-form-urlencoded'
            }
        }).then(() => {
            this.dispatch('changed', { detail: { message: message.success, type: 'success' } });
        }).catch(async ({ cause: { errors } }) => {
            this.dispatch('changed', { detail: { message: message.error, type: 'error' } });
        }).finally(() => {
            this.checkboxTarget.removeAttribute('disabled');
        });
    }

    serializeForm(formData) {
        return new URLSearchParams(formData).toString();
    }
}
