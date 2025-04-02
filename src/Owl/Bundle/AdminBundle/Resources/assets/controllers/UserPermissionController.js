import { Controller } from '@hotwired/stimulus';

import { debounce } from '../utils/debounce';

export default class extends Controller {

    static targets = ['form'];

    changed({ detail: { response } }) {
        const { permissions = {} } = response;

        this.formTargets.map((form) => {
            let route = form.querySelector('.input-route-name').value,
                checkbox = form.querySelector('input[type="checkbox"]');

            if (Object.keys(permissions).length > 0) {
                if (Array.isArray(permissions?.inherited) && permissions?.inherited.indexOf(route) !== -1) {
                    if (Array.isArray(permissions?.direct) && permissions?.direct.indexOf(route) === -1) {
                        checkbox.checked = true;
                        debounce(() => checkbox.disabled = true, 100)();
                    } else {
                        form.querySelectorAll('input[type=hidden]').forEach((input) => input.removeAttribute('disabled'));
                        checkbox.checked = true;
                    }
                } else if (Array.isArray(permissions?.direct) && permissions?.direct.indexOf(route) === -1) {
                    form.querySelectorAll('input[type=hidden]').forEach((input) => input.removeAttribute('disabled'));

                    checkbox.removeAttribute('disabled');
                    checkbox.checked = false;
                } else {
                    checkbox.removeAttribute('disabled');
                }
            }
        });
    }
}
