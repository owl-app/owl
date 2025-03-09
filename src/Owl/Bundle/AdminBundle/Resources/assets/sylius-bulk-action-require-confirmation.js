import $ from 'jquery';
import GridComponent from './owl-grid';

$.fn.extend({
  bulkActionRequireConfirmation() {
    this.each((idx, el) => {
      $(el).on('click', (evt) => {
        evt.preventDefault();

        const actionButton = $(evt.currentTarget);

        if (actionButton.is('a')) {
          $('#confirmation-button').attr('href', actionButton.attr('href'));
        }

        if (actionButton.is('button')) {
          $('#confirmation-button').on('click', (event) => {
            event.preventDefault();

            const form = actionButton.closest('form'),
                  grid = new GridComponent(document.querySelector('.sylius-grid-wrapper'));

            $('input.bulk-select-checkbox:checked').each((index, element) => {
              $(`<input type="hidden" name="ids[]" value="${element.value}">`).appendTo(form);
            });

            grid.delete(form.attr('action'), form.serialize());
          });
        }

        $('#confirmation-modal').modal('show');
      });
    });
  },
});
