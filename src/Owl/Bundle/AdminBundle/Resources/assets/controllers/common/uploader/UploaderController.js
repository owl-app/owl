import { Controller } from '@hotwired/stimulus';
import Dropzone from 'dropzone';
import { debounce } from '../../../utils/debounce';

export default class extends Controller {

    static values = { url: String, replace: String };

    async initialize() {
        this.dropzone = new Dropzone(
            this.element.querySelector('.dropzone'),
            {
                url: this.urlValue,
                autoProcessQueue: false
            }
        );
    }

    afterSaveFormEvent({ detail: { resolve, resource = null } }) {
        let replaces = this.replaceValue.split(',');

        replaces.map((replace) => {
            const toReplace = replace.charAt(0).toUpperCase() + replace.slice(1);
            this.dropzone.options.url = this.urlValue.replace(`replaceParam${toReplace}`, resource[replace]);
        });

        this.dropzone.on('queuecomplete', () => {
            debounce(resolve, 500)();
        });

        if (this.dropzone.getQueuedFiles().length > 0) {
            this.upload();
        } else {
            resolve();
        }
    }

    upload() {
        if (!this.dropzone.options.autoProcessQueue) {
            this.dropzone.on('complete', () => {
                if (this.dropzone.getUploadingFiles().length === 0 && this.dropzone.getQueuedFiles().length > 0) {
                    this.dropzone.processQueue();
                }
            });
        }

        this.dropzone.processQueue();
    }
}
