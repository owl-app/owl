import { Controller } from '@hotwired/stimulus';

import flattenObject from '../utils/flatten-object';

export default class extends Controller {

    static targets = ['form', 'checkbox'];

    static values = { assignUrl: String, revokeUrl: String, defaultMessageError: String };

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
            await this.sendRequest(this.assignUrlValue, 'POST');
        } else {
            await this.sendRequest(this.revokeUrlValue, 'DELETE');
        }
    };

    sendRequest(url, method) {
        const formData = new FormData(this.element);

        this.checkboxTarget.setAttribute('disabled', true);

        fetch(url, {
            method,
            body: this.serializeForm(formData),
            headers: {
                'X-Requested-With': 'XMLHttpRequest',
                'Content-Type': 'application/x-www-form-urlencoded'
            }
        }).then(async (response) => {
            if (!response.ok && response.status === 422) {
                const error = { cause: { errors: (await response.json()).errors } };
                throw new Error('Error validation', error);
            } else if (!response.ok) {
                throw new Error();
            }

            const responseData = await response.json();

            this.dispatch('changed', { detail: { message: responseData.message, type: 'success' } });
        }).catch(async ({ cause }) => {
            let errors = [];

            if (cause?.errors !== undefined) {
                errors = flattenObject(cause?.errors);
            }

            if (Object.keys(errors).length === 0) {
                errors = { default: this.defaultMessageErrorValue };
            }

            this.dispatch('changed', { detail: { message: Object.values(errors).join('<br />'), type: 'error' } });
        }).finally(() => {
            this.checkboxTarget.removeAttribute('disabled');
        });
    }

    serializeForm(formData) {
        return new URLSearchParams(formData).toString();
    }
}
