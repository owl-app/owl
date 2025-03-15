import { Controller } from '@hotwired/stimulus';
import redirect from '../../../utils/redirect';
import flattenObject from '../../../utils/flatten-object';
import { getComponent } from '@symfony/ux-live-component';

export default class extends Controller {

    static targets = ['content', 'live', 'form', 'action', 'loading'];

    async initialize() {
        this.liveFormComponent = await getComponent(this.liveTarget);
    }

    save({ params }) {
        this.showLoading();
        this.loadingTarget.classList.add('d-flex');

        this.actionTargets.forEach((action) => {
            action.setAttribute('disabled', true);
        });

        const formData = new FormData(this.formTarget);

        if (params.saveAction) {
            formData.append('save_action', params.saveAction);
        }

        fetch(params.url, {
            method: 'POST',
            body: formData,
            headers: {
                'X-Requested-With': 'XMLHttpRequest'
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

    showLoading() {
        this.loadingTarget.classList.add('d-flex');

        this.actionTargets.forEach((action) => {
            action.setAttribute('disabled', true);
        });
    }

    hideLoading() {
        this.loadingTarget.classList.remove('d-flex');

        this.actionTargets.forEach((action) => {
            action.removeAttribute('disabled');
        });
    }

    afterSave(response) {
        setTimeout(() => {
            this.hideLoading();

            this.dispatch('save');

            if (response.headers.has('x-owl-location')) {
                redirect(response.headers.get('x-owl-location'));
            }
        }, 300);
    }

    async synchronizeLiveComponent(data) {
        let errors = flattenObject(data);
    
        Object.keys(errors).map((key) => {
            const name = `${this.formTarget.name}.${key}`;

            if (this.liveFormComponent.valueStore.has(name)) {
                this.liveFormComponent.set(name);
            }
        });

        await this.liveFormComponent.debouncedStartRequest();
    }
}
