import '@hotwired/turbo';
import { startStimulusApp } from '@symfony/stimulus-bridge';
import LiveController from '@symfony/ux-live-component';
import '@symfony/ux-live-component/styles/live.css';

// modal
import ModalController from './controllers/common/modal/ModalController';
import StaticModalController from './controllers/common/modal/StaticModalController';
import ShowModalController from './controllers/common/modal/ShowModalController';
// toast
import ToastController from './controllers/common/toast/ToastController';
// form
import FormController from './controllers/common/form/FormController';
// uploader
import UploaderController from './controllers/common/uploader/UploaderController';
// grid
import GridController from './controllers/common/grid/GridController';
import GridBulkController from './controllers/common/grid/BulkController';
// permission
import PermissionController from './controllers/common/permission/PermissionController';

// bundle
import DetailsNotificationController from './controllers/bundle/notification/DetailsNotificationController';

import UserPermissionController from './controllers/bundle/user/UserPermissionController';
import ListNotificationController from './controllers/bundle/notification/ListNotificationController';

export function startApp() {
    const appSymfonyStimulus = startStimulusApp(require.context(
        '@symfony/stimulus-bridge/lazy-controller-loader!./controllers',
        true,
        /\.[jt]sx?$/
    ));
    
    appSymfonyStimulus.register('live', LiveController);
    appSymfonyStimulus.register('modal', ModalController);
    appSymfonyStimulus.register('modal-static', StaticModalController);
    appSymfonyStimulus.register('show-modal', ShowModalController);
    appSymfonyStimulus.register('form', FormController);
    appSymfonyStimulus.register('uploader', UploaderController);
    appSymfonyStimulus.register('grid', GridController);
    appSymfonyStimulus.register('grid-bulk', GridBulkController);
    appSymfonyStimulus.register('toast', ToastController);
    appSymfonyStimulus.register('permission', PermissionController);
    appSymfonyStimulus.register('user-permission', UserPermissionController);
    appSymfonyStimulus.register('details-notification', DetailsNotificationController);
    appSymfonyStimulus.register('list-notification', ListNotificationController);
    
    
    appSymfonyStimulus.debug = process.env.NODE_ENV !== 'production';
    
    window.appIsStarted = true;
}


if (!window.appIsStarted) {
    startApp();
}
