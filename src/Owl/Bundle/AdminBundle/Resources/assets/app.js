import '@hotwired/turbo';
import { startStimulusApp } from '@symfony/stimulus-bridge';
import LiveController from '@symfony/ux-live-component';
import '@symfony/ux-live-component/styles/live.css';

// symfony
import ModalFormController from './controllers/symfony/ModalFormController';
//modal
import ModalController from './controllers/common/modal/ModalController';
import StaticModalController from './controllers/common/modal/StaticModalController';
import ShowModalController from './controllers/common/modal/ShowModalController';
//form
import FormController from './controllers/common/form/FormController';
//uploader
import UploaderController from './controllers/common/uploader/UploaderController';
//grid
import GridController from './controllers/common/grid/GridController';
import GridBulkController from './controllers/common/grid/BulkController';

export function startApp() {
    const appSymfonyStimulus = startStimulusApp(require.context(
        '@symfony/stimulus-bridge/lazy-controller-loader!./controllers',
        true,
        /\.[jt]sx?$/
    ));
    
    appSymfonyStimulus.register('live', LiveController);
    appSymfonyStimulus.register('symfony-modal-form', ModalFormController);
    appSymfonyStimulus.register('modal', ModalController);
    appSymfonyStimulus.register('modal-static', StaticModalController);
    appSymfonyStimulus.register('show-modal', ShowModalController);
    appSymfonyStimulus.register('form', FormController);
    appSymfonyStimulus.register('uploader', UploaderController);
    appSymfonyStimulus.register('grid', GridController);
    appSymfonyStimulus.register('grid-bulk', GridBulkController);
    
    appSymfonyStimulus.debug = process.env.NODE_ENV !== 'production';
    
    window.appIsStarted = true;
}


if (!window.appIsStarted) {
    startApp();
}
