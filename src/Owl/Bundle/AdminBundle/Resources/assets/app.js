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
// filters
import FilterController from './controllers/common/grid/filters/FilterController';
import AutocompleteFilterController from './controllers/common/grid/filters/AutocompleteFilterController';
import PeriodFilterController from './controllers/common/grid/filters/PeriodFilterController';
// permission
import PermissionController from './controllers/common/permission/PermissionController';

// bundle
// notification
import DetailsNotificationController from './controllers/bundle/notification/DetailsNotificationController';
// permission
import UserPermissionController from './controllers/bundle/user/UserPermissionController';
import ListNotificationController from './controllers/bundle/notification/ListNotificationController';
// invoice
import InvoiceForm from './controllers/bundle/invoice/InvoiceForm';
import InvoiceAvailableSeries from './controllers/bundle/invoice/InvoiceAvailableSeries';
import AddableAutocomplete from './controllers/bundle/invoice/AddableAutocomplete';


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
    appSymfonyStimulus.register('grid-filter', FilterController);
    appSymfonyStimulus.register('grid-filter-autocomplete', AutocompleteFilterController);
    appSymfonyStimulus.register('grid-filter-period', PeriodFilterController);
    
    // bundle
    appSymfonyStimulus.register('user-permission', UserPermissionController);
    appSymfonyStimulus.register('details-notification', DetailsNotificationController);
    appSymfonyStimulus.register('list-notification', ListNotificationController);
    appSymfonyStimulus.register('invoice-form', InvoiceForm);
    appSymfonyStimulus.register('invoice-available-series', InvoiceAvailableSeries);
    appSymfonyStimulus.register('invoice-addable-autocomplete', AddableAutocomplete);

    appSymfonyStimulus.debug = process.env.NODE_ENV !== 'production';
    
    window.appIsStarted = true;
}


if (!window.appIsStarted) {
    startApp();
}
