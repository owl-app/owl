import { Controller } from '@hotwired/stimulus';
import { getComponent } from '@symfony/ux-live-component';

import redirect from '../../../utils/redirect';
import flattenObject from '../../../utils/flatten-object';
import { showLoader, hideLoader } from '../../../scripts/loader';
import { debounce } from '../../../utils/debounce';

export default class extends Controller {

    static values = { asyncEvents: Array };

    static targets = ['content', 'live', 'form', 'action', 'loading'];

    async initialize() {
        if (this.hasLiveTarget) {
            this.formLiveComponent = await getComponent(this.liveTarget);
        }
    }

    save({ params }) {
        const formData = new FormData(this.formTarget);
        const { disableAllButtons = false } = params;

        this.showLoading(disableAllButtons);

        if (params.saveAction) {
            formData.append('save_action', params.saveAction);
        }

        fetch(params.url, {
            method: params.method ?? 'POST',
            body: this.serializeForm(formData),
            headers: {
                'X-Requested-With': 'XMLHttpRequest',
                'Content-Type': 'application/x-www-form-urlencoded'
            }
        }).then(async response => {
            if (!response.ok && response.status === 422) {
                const error = { cause: { errors: (await response.json()).errors } };
                throw new Error('Error validation', error);
            }

            this.afterSave(response, disableAllButtons);
        }).catch(async ({ cause: { errors } }) => {
            await this.synchronizeLiveComponent(errors);

            this.hideLoading(disableAllButtons);
        });
    }

    serializeForm(formData) {
        return new URLSearchParams(formData).toString();
    }

    showLoading(disableAllButtons) {
        showLoader(this.contentTarget, { class: { loader: 'content' } });

        this.getAllButtons(disableAllButtons).forEach((action) => {
            if (action.tagName === 'BUTTON') {
                action.setAttribute('disabled', true);
            } else {
                action.classList.add('disabled');
            }
        });
    }

    hideLoading(disableAllButtons) {
        hideLoader(this.contentTarget);

        this.getAllButtons(disableAllButtons).forEach((action) => {
            if (action.tagName === 'BUTTON') {
                action.removeAttribute('disabled');
            } else {
                action.classList.remove('disabled');
            }
        });
    }

    getAllButtons(disableAllButtons) {
        let buttons = [];

        if (disableAllButtons) {
            disableAllButtons.map((selector) => {
                buttons = [...buttons, ...document.querySelectorAll(selector)];
            });
        } else {
            buttons = this.actionTargets;
        }

        return buttons;
    }

    async afterSave(response, disableAllButtons) {
        let resource = await response.text();

        try {
            resource = JSON.parse(resource);
        } catch (e) { /* empty */ }

        if (this.asyncEventsValue.length) {
            const awaited = [];

            this.asyncEventsValue.map((event) => {
                awaited.push(
                    new Promise((resolve) => {
                        this.dispatch(event, { detail: { resolve, resource } });
                    })
                );
            });

            await Promise.all(awaited);
        }

        this.dispatch('saved');

        debounce(async () => {
            this.hideLoading(disableAllButtons);

            if (response.headers.has('x-owl-location')) {
                redirect(response.headers.get('x-owl-location'));
            }
        }, 300)();
    }

    async synchronizeLiveComponent(data) {
        if (this.hasLiveTarget) {
            let errors = flattenObject(data);

            const promises = [];

            Object.keys(errors).map((key) => {
                const name = `${this.formTarget.name}.${key}`;

                if (this.formLiveComponent.valueStore.has(name)) {
                    promises.push(this.formLiveComponent.set(name));
                }
            });

            await this.formLiveComponent.debouncedStartRequest();

            await Promise.all(promises);
        } else {
            let errors = flattenObject(data, '_');

            this.formTarget.querySelectorAll('.field').forEach(function (field) {
                const error = field.querySelector('.invalid-feedback');

                if (error) {
                    error.remove();
                }

                field.querySelector('.form-control')?.classList.remove('is-invalid');
            });

            Object.keys(errors).map((key) => {
                let field = document.getElementById(this.formTarget.name + '_' + key)?.closest('.field');

                if (field) {
                    field.querySelector('.form-control')?.classList.add('is-invalid');

                    let errorLabel = document.createElement('div');
                    errorLabel.className = 'invalid-feedback d-block';
                    errorLabel.textContent = errors[key];

                    field.appendChild(errorLabel);
                }
            });
        }

    }
}
