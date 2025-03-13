import $ from 'jquery';
import '@hotwired/turbo';

import './sylius-bulk-action-require-confirmation';
import './owl-bulk-action';
import './sylius-form-collection';
import './sylius-require-confirmation';
import './sylius-toggle';
import './sylius-check-all';

import { startStimulusApp } from '@symfony/stimulus-bridge';
import { Application } from '@hotwired/stimulus';
import LiveController from '@symfony/ux-live-component';
import '@symfony/ux-live-component/styles/live.css';
import ModalFormController from './controllers/ModalFormController';
import ModalController from './controllers/ModalController';
import ButtonModalController from './controllers/ButtonModalController';

// Registers Stimulus controllers from controllers.json and in the controllers/ directory
export const appSymfonyStimulus = startStimulusApp(require.context(
    '@symfony/stimulus-bridge/lazy-controller-loader!./controllers',
    true,
    /\.[jt]sx?$/
));

appSymfonyStimulus.register('live', LiveController);
appSymfonyStimulus.register('modal-form', ModalFormController);

appSymfonyStimulus.debug = process.env.NODE_ENV !== 'production';

if (window.Stimulus) {
    window.Stimulus.stop();
}

const appStimulus = window.Stimulus = Application.start();

appStimulus.register('modal', ModalController);
appStimulus.register('button-modal', ButtonModalController);

appStimulus.debug = process.env.NODE_ENV !== 'production';
