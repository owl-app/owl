
import { Controller } from '@hotwired/stimulus';

import { debounce } from '../../../utils/debounce';
import { objectToFormData } from '../../../utils/format';

export default class extends Controller {

    static targets = ['action'];

    static outlets = ['modal'];

    accept({ params }) {
        const { request, method, notificationId, message } = params;

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
                    const detailEvent = {
                        detail: {
                            message: message.success,
                            type: 'success'
                        }
                    };

                    this.modalOutlet.hideLoading();

                    window.dispatchEvent(new CustomEvent('owl_admin.toast.show', detailEvent));
                }, 100)();
            }
        }).catch(() => {
            const detailEvent = {
                detail: {
                    message: message.error,
                    type: 'error'
                }
            };

            this.modalOutlet.close();
            this.modalOutlet.hideLoading();

            window.dispatchEvent(new CustomEvent('owl_admin.toast.show', detailEvent));
        });
    }
}
