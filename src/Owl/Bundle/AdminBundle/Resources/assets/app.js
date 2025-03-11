import $ from 'jquery';

import './sylius-bulk-action-require-confirmation';
import './owl-bulk-action';
import './sylius-form-collection';
import './sylius-require-confirmation';
import './sylius-toggle';
import './sylius-check-all';

import { startStimulusApp } from '@symfony/stimulus-bridge';
import LiveController from '@symfony/ux-live-component';
import '@symfony/ux-live-component/styles/live.css';

// Registers Stimulus controllers from controllers.json and in the controllers/ directory
export const app = startStimulusApp(require.context(
  '@symfony/stimulus-bridge/lazy-controller-loader!./controllers',
  true,
  /\.[jt]sx?$/
));

app.register('live', LiveController);

app.debug = process.env.NODE_ENV !== 'production';

document.addEventListener("turbo:load", () => {
  // $('#sidebar').addClass('visible');
  // $('#sidebar').sidebar('attach events', '#sidebar-toggle', 'toggle');
  // $('#sidebar').sidebar('setting', {
  //   dimPage: false,
  //   closable: false,
  // });

  // $('.ui.checkbox').checkbox();
  // $('.ui.accordion').accordion();
  // $('.ui.menu .dropdown').dropdown({ action: 'hide' });
  // $('.ui.inline.dropdown').dropdown();
  // $('.link.ui.dropdown').dropdown({ action: 'hide' });
  // $('.button.ui.dropdown').dropdown({ action: 'hide' });
  // $('.ui.fluid.search.selection.ui.dropdown').dropdown();
  // $('.ui.tabular.menu .item, .owl-tabular-form .menu .item, .owl-tabular-show .item').tab();
  // $('.ui.card .dimmable.image, .ui.cards .card .dimmable.image').dimmer({ on: 'hover' });
  // $('.ui.rating').rating('disable');

  // $('form.loadable button[type=submit]').on('click', (event) => {
  //   let $form = $(event.currentTarget).closest('form');

  //   if(!$form.hasClass('is-ajax')) {
  //     $(event.currentTarget).closest('form').addClass('loading');
  //   }
  // });
  // $('.loadable.button').on('click', (event) => {
  //   $(event.currentTarget).addClass('loading');
  // });
  // $('.message .close').on('click', (event) => {
  //   $(event.currentTarget).closest('.message').transition('fade');
  // });

  // $('[data-requires-confirmation]').requireConfirmation();
  // $('[data-bulk-action-requires-confirmation]').bulkActionRequireConfirmation();
  // $('[data-bulk-action-default]').bulkAction();
  // $('[data-toggles]').toggleElement();
  // $('[data-js-bulk-checkboxes]').checkAll();

  // $('.special.cards .image').dimmer({
  //   on: 'hover',
  // });

  // $('[data-form-type="collection"]').CollectionForm();

  // $('[data-js-disable]').on('click', (e) => {
  //   const $current = $(e.currentTarget);
  //   $(document).find($current.attr('data-js-disable')).addClass('disabled');
  // });
});
