import { Controller } from '@hotwired/stimulus';
import { getComponent } from '@symfony/ux-live-component';
import { updateURLSearchParam, updateSingleQueryParam } from '../../../../utils/url';

export default class extends Controller {

    component = null;

    async initialize() {
        this.component = await getComponent(this.element);
    }

    async updateAll(event) {
        event.preventDefault();
        event.stopPropagation();

        await this.component.action('updateAll');

        Turbo.visit(updateURLSearchParam('criteria', this.component.valueStore.get('criteria')), {
            action: 'replace',
            history: true
        });
    }

    async updateFilter(event) {
        event.preventDefault();
        event.stopPropagation();
        const field = event.params.field;

        await this.component.action('updateFilter', { field });

        Turbo.visit(updateSingleQueryParam(field, this.component.valueStore.get('criteria')[field], 'criteria'), {
            action: 'replace',
            history: true
        });
    }
}
