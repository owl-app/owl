import { Controller } from '@hotwired/stimulus';
import { getComponent } from '@symfony/ux-live-component';
import updateSingleQueryParam from '../../../../utils/url';

export default class extends Controller {

    component = null;

    async initialize() {
        this.component = await getComponent(this.element);
    }

    async change(event) {
        event.preventDefault();
        event.stopPropagation();
        const field = event.params.field;

        await this.component.action('update', { field: field });

        Turbo.visit(updateSingleQueryParam(field, this.component.valueStore.get('criteria')[field], 'criteria'), {
            action: 'replace',
            history: true
        });
    }
}
