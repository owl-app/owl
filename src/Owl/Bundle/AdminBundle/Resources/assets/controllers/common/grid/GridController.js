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

        this.addClassCells(this.tableTarget.querySelectorAll('td'), 'faded');
        this.removeClassCells(this.tableTarget.querySelectorAll('td'), 'fade-in');
    };

    turboRender = async (event) => {
        if (event.target.hasAttribute('data-turbo-preview')) {
            if (this.hasTableTarget) {
                this.disabledFilters();
                showLoader(this.tableTarget);
            }
        } else {
            if (this.hasTableTarget) {
                await debounce(() => {
                    this.addClassCells(this.tableTarget.querySelectorAll('td'), 'fade-in');
                }, 100)();
            }
        }
    };

    turboBeforeRender = (event) => {
        if (!event.target.hasAttribute('data-turbo-preview')) {
            this.addClassCells(event.detail.newBody.querySelectorAll('.owl-grid td'), 'faded');
        }
    };

    turboBeforeFetchRequest = () => {
        this.disabledFilters();

        if (this.hasTableTarget) {
            this.removeClassCells(this.tableTarget.querySelectorAll('td'), 'fade-in');
            showLoader(this.tableTarget);
            this.addClassCells(this.tableTarget.querySelectorAll('td'), 'faded');
        }
    };

    addClassCells(cells, className) {
        return cells.forEach(cell => {
            cell.classList.add(className);
        });
    }

    removeClassCells(cells, className) {
        return cells.forEach(cell => {
            cell.classList.remove(className);
        });
    }

    disabledFilters() {
        this.filtersTarget.querySelector('.accordion-button').setAttribute('disabled', true);
    }
}
