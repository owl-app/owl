import $ from 'jquery';

import 'dropzone/dist/dropzone.css';

import 'owl/ui/app';
import 'owl/ui/sylius-auto-complete';

import './owl-menu-search';

//owl
import './owl-permission';
import './owl-notification';

import './owl-form';
import './owl-modal-ajax';
import GridComponent from 'owl/ui/owl-grid';

$(window).off('beforeunload');

// import * as Turbo from '@hotwired/turbo';
 
var isLoadPage = false;

// Turbo.session.adapter.__proto__.visitRequestCompleted = function(visit) {
  
//   function isSuccessful(statusCode) {
//     return statusCode >= 200 && statusCode < 300;
//   }

//   if (visit.response) {
//     const { statusCode, responseHTML } = visit.response
//     const snapshot = Turbo.PageSnapshot.fromHTMLString(responseHTML);
//     visit.render(async () => {
//       if (visit.view.renderPromise) await visit.view.renderPromise
//       if (isSuccessful(statusCode) && responseHTML != null) {
//         await visit.view.renderPage(snapshot, false, visit.willRender)
//         visit.adapter.visitRendered(visit)
//         visit.complete()
//       } else {
//         await visit.view.renderError(snapshot)
//         visit.adapter.visitRendered(visit)
//         visit.fail()
//       }

//       visit.view.snapshotCache.put(new URL(visit.location.href), snapshot)
//     })
//   }
// }

document.addEventListener('turbo:before-cache',  (event) => {
  event.preventDefault();
  let checkboxAll = $('[data-js-bulk-checkboxes');

  if(checkboxAll.is(':checked')) {
    checkboxAll.trigger('click');
  }

  $('.modals').remove();
  $('body').removeClass('dimmable scrolling')
});

document.addEventListener('turbo:render',  (event) => {  
  if ($('.sylius-grid-wrapper table').length) {
    const grid = new GridComponent(document.querySelector('.sylius-grid-wrapper'));

    if(!isLoadPage) {
      grid.removeLoading();
    }else{
      grid.addLoading();
    }
  }
}) 

document.addEventListener('turbo:before-fetch-request',  (event) => {
  isLoadPage = true;

  if ($('.sylius-grid-wrapper table').length) {
    const grid = new GridComponent(document.querySelector('.sylius-grid-wrapper'));

    grid.addLoading();
  }
})

document.addEventListener('turbo:before-fetch-response', (event) => {
  isLoadPage= false; 

  if ($('.sylius-grid-wrapper table').length) {
    const grid = new GridComponent(document.querySelector('.sylius-grid-wrapper'));

    grid.removeLoading();
  }
})

document.addEventListener("turbo:load", () => {

  if($('body').find('.tinymce').length) {
    initTinyMCE();
  }

  // permission
  $('.ui.checkbox-permission').changePermission();

  $('.sylius-autocomplete').autoComplete();

  // $('div#attributeChoice > .ui.dropdown.search').productAttributes();

  $('table thead th.sortable').on('click', (event) => {
    window.location = $(event.currentTarget).find('a').attr('href');
  });

  $('form.is-ajax').ajaxForm();
  $('.ajax-modal-button').each((index, element) => {
    if ($._data($(element).get(0), 'events') == undefined) {
      $(element).ajaxModal();
    }
  })

  $('#actions a[data-form-collection="add"]').on('click', () => {
    setTimeout(() => {
      $('select[name^="sylius_promotion[actions]"][name$="[type]"]').last().change();
    }, 50);
  });
  $('#rules a[data-form-collection="add"]').on('click', (event) => {
    const name = $(event.target).closest('form').attr('name');

    setTimeout(() => {
      $(`select[name^="${name}[rules]"][name$="[type]"]`).last().change();
    }, 50);
  });

  $(document).on('collection-form-add', () => {
    $('.sylius-autocomplete').each((index, element) => {
      if ($._data($(element).get(0), 'events') == undefined) {
        $(element).autoComplete();
      }
    });
  });

  $(document).on('collection-form-update', () => {
    $('.sylius-autocomplete').each((index, element) => {
      if ($._data($(element).get(0), 'events') == undefined) {
        $(element).autoComplete();
      }
    });
  });

  $('#more-details').accordion({ exclusive: false });

  $('.owl-admin-menu').searchable('.owl-admin-menu-search-input');

  $('.accept-notification').acceptNotification();

  $('.sylius-filters .ui.dropdown').dropdown({
    fullTextSearch: true,
  });
});

window.$ = $;
window.jQuery = $;
