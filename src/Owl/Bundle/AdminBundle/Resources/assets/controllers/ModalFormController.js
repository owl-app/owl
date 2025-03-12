/*
 * This file is part of the Sylius package.
 *
 * (c) Sylius Sp. z o.o.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

import { getComponent } from '@symfony/ux-live-component';

import { Controller } from '@hotwired/stimulus';
import { Modal } from 'bootstrap';

export default class extends Controller {
    modal = null;

    async initialize() {
        window.addEventListener('owl_admin:modal:close', () => this.modal.hide());

        this.component = await getComponent(this.element);

        window.addEventListener('owl_admin:modal:opened', () => {
            this.modal = Modal.getOrCreateInstance(this.element.querySelector('.modal-form'));
            
            this.modal.show();

            this.element.querySelector('.modal-form').addEventListener('hidden.bs.modal', () => {
                this.component.action('toggle', { resourceId: null });
            });
        });

        window.addEventListener('owl_admin:modal:open', (event) => {
            this.component.action('toggle', { resourceId: event.detail.resourceId });
        });
    }
}
