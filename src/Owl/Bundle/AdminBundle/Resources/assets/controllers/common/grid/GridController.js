import * as bootstrap from 'bootstrap';
import { Controller } from '@hotwired/stimulus';

import { showLoader } from '../../../scripts/loader';
import { debounce } from '../../../utils/debounce';

export default class extends Controller {

    static targets = ['content', 'filters', 'table'];

    connect() {
        document.addEventListener('turbo:before-cache', this.turboBeforeCache);
        document.addEventListener('turbo:render', this.turboRender);
        document.addEventListener('turbo:before-render', this.turboBeforeRender);
        document.addEventListener('turbo:before-fetch-request', this.turboBeforeFetchRequest);
    }

    disconnect() {
        document.removeEventListener('turbo:before-cache', this.turboBeforeCache);
        document.removeEventListener('turbo:render', this.turboRender);
        document.removeEventListener('turbo:before-render', this.turboBeforeRender);
        document.removeEventListener('turbo:before-fetch-request', this.turboBeforeFetchRequest);
    }

    turboBeforeCache = () => {
        const fiiltersCollapse = new bootstrap.Collapse(this.filtersTarget.querySelector('.accordion-collapse'), { toggle: false });
        fiiltersCollapse.hide();
    };

    turboRender = (event) => {
        if (event.target.hasAttribute('data-turbo-preview')) {
            this.disabledFilters();

            if (this.hasTableTarget) {
                showLoader(this.tableTarget);
                this.tableTarget.classList.add('table-placeholder');
            }
        } else {
            if (this.hasTableTarget) {
                debounce(async () => {
                    this.tableTarget.querySelectorAll('tbody').forEach(tbody => {
                        tbody.classList.add('show');
                    });
                }, 100)();
            }
        }
    };

    turboBeforeRender = (event) => {
        event.detail.newBody.querySelectorAll('[data-grid-target="table"]').forEach(table => {
            table.querySelectorAll('tbody').forEach(tbody => {
                tbody.classList.add('fade');
            });
        });
    };

    turboBeforeFetchRequest = () => {
        this.disabledFilters();

        if (this.hasTableTarget) {
            showLoader(this.tableTarget, { class: { loader: 'content' }});
        }
    };

    disabledFilters() {
        this.filtersTarget.querySelector('.accordion-button').setAttribute('disabled', true);
    }
}
