import { Controller } from '@hotwired/stimulus';
import { getComponent } from '@symfony/ux-live-component';

import redirect from '../../../utils/redirect';
import flattenObject from '../../../utils/flatten-object';
import { showLoader, hideLoader } from '../../../scripts/loader';
import { debounce } from '../../../utils/debounce';

export default class extends Controller {

    static values = { asyncEvents: Array};

    static targets = ['content', 'live', 'form', 'action', 'loading'];

    async initialize() {
        this.formLiveComponent = await getComponent(this.liveTarget);
    }

    save({ params }) {
        const formData = new FormData(this.formTarget);

        this.showLoading();

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
                const error = { cause: { errors: (await response.json()).errors }};
                throw new Error('Error validation', error);
            }

            this.afterSave(response);
        }).catch(async ({ cause: { errors }}) => {
            await this.synchronizeLiveComponent(errors);

            this.hideLoading();
        });
    }

    serializeForm(formData) {
        return new URLSearchParams(formData).toString();
    }

    showLoading() {
        showLoader(this.contentTarget, { class: { loader: 'content' }});

        this.actionTargets.forEach((action) => {
            if (action.tagName === 'BUTTON') {
                action.setAttribute('disabled', true);
            } else {
                action.classList.add('disabled');
            }
        });
    }

    hideLoading() {
        hideLoader(this.contentTarget);

        this.actionTargets.forEach((action) => {
            if (action.tagName === 'BUTTON') {
                action.removeAttribute('disabled');
            } else {
                action.classList.remove('disabled');
            }
        });
    }

    async afterSave(response) {
        const resource = await response.json();

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
            this.hideLoading();

            if (response.headers.has('x-owl-location')) {
                redirect(response.headers.get('x-owl-location'));
            }
        }, 300)();
    }

    async synchronizeLiveComponent(data) {
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
    }
}
