import { Controller } from '@hotwired/stimulus';
import { showLoader } from '../../../scripts/loader';
import { debounce } from '../../../utils/debounce';

export default class extends Controller {

    static targets = ['content', 'table'];

    connect() {
        
        document.addEventListener('turbo:render', this.turboRender);
        document.addEventListener('turbo:before-render', this.beforeRender);
        document.addEventListener('turbo:before-fetch-request', this.beforeFetchRequest);
    }

    disconnect() {
        document.removeEventListener('turbo:render', this.turboRender);
        document.removeEventListener('turbo:before-render', this.beforeRender);
        document.removeEventListener('turbo:before-fetch-request', this.beforeFetchRequest);
    }

    turboRender = (event) => {
        if (event.target.hasAttribute('data-turbo-preview')) {
            showLoader(this.contentTarget, { class: { loader: 'content' }});
            this.tableTarget.classList.add('table-placeholder');
        } else {
            debounce(async () => {
                this.tableTarget.querySelectorAll('tbody').forEach(tbody => {
                    tbody.classList.add('show');
                });
            }, 100)();
        }
    };

    beforeRender = (event) => {
        event.detail.newBody.querySelectorAll('[data-grid-target="table"]').forEach(table => {
            table.querySelectorAll('tbody').forEach(tbody => {
                tbody.classList.add('fade');
            });
        });
    };

    beforeFetchRequest = () => {
        showLoader(this.contentTarget, { class: { loader: 'content' }});
    };
}
