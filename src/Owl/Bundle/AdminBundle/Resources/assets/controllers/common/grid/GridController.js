import { Controller } from '@hotwired/stimulus';

import { showLoader } from '../../../scripts/loader';
import { debounce } from '../../../utils/debounce';

export default class extends Controller {

    static targets = ['content', 'filters', 'table'];

    connect() {
        document.addEventListener('turbo:render', this.turboRender);
        document.addEventListener('turbo:before-fetch-request', this.turboBeforeFetchRequest);
    }

    disconnect() {
        document.removeEventListener('turbo:render', this.turboRender);
        document.removeEventListener('turbo:before-fetch-request', this.turboBeforeFetchRequest);
    }

    turboRender = () => {
        if (this.hasTableTarget) {
            this.addClassCells(this.tableTarget.querySelectorAll('td'), 'faded');

            debounce(() => {
                this.addClassCells(this.tableTarget.querySelectorAll('td'), 'fade-in');
            }, 200)();
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
        if (this.hasFiltersTarget) {
            this.filtersTarget.querySelector('.accordion-button').setAttribute('disabled', true);
        }
    }
}
