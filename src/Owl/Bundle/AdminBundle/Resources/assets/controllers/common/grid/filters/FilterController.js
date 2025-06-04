import { Controller } from '@hotwired/stimulus';
import { getComponent } from '@symfony/ux-live-component';
import { updateURLSearchParam, updateSingleQueryParam } from '../../../../utils/url';

export default class extends Controller {

    component = null;

    async initialize() {
        this.component = await getComponent(this.element);
    }

    connect() {
        window.addEventListener('filter:changed', this.handleFilterChanged);
    }

    disconnect() {
        window.removeEventListener('filter:changed', this.handleFilterChanged);
    }

    async updateAll(event) {
        event.preventDefault();
        event.stopPropagation();

        await this.component.action('updateAll');

        this.updateURLSearchParam();
    }

    async updateFilter(event) {
        event.preventDefault();
        event.stopPropagation();
        const field = event.params.field;

        await this.component.action('updateFilter', { field });

        this.updateURLSearchParam(field);
    }

    handleFilterChanged = async (event) => {
        const { field, value } = event.detail;

        await this.component.action('updateFilter', { field, value });

        this.updateURLSearchParam(field);
    };

    updateURLSearchParam(field = null) {
        const criteria = field ? this.component.valueStore.get('criteria')[field] : this.component.valueStore.get('criteria');

        Turbo.visit(updateSingleQueryParam(field, criteria, 'criteria'), {
            action: 'replace',
            history: true
        });
    }
}
