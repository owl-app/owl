
import { getComponent } from '@symfony/ux-live-component';
import { Controller } from '@hotwired/stimulus';

import { debounce } from '../../../utils/debounce';
import { objectToFormData } from '../../../utils/format';

export default class extends Controller {

    static targets = ['action'];

    static outlets = ['modal'];

    accept({ params }) {
        const { request, method, notificationId } = params;

        if (request.url === undefined) {
            throw new Error('The request.url parameter is required.');
        }

        this.modalOutlet.showLoading();

        fetch(request.url, {
            method: method,
            body: objectToFormData(request.data),
            headers: {
                'X-Requested-With': 'XMLHttpRequest'
            }
        }).then(async response => {
            if (response.ok) {
                this.modalOutlet.close();

                this.dispatch('read', { detail: { notificationId } });

                debounce(() => {
                    this.modalOutlet.hideLoading();
                }, 100)();
            }
        }).catch(() => {
            this.modalOutlet.hide();
        });
    }
}
