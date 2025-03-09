import 'fomantic-ui/dist/components/modal';
import $ from 'jquery';

$.fn.extend({
  bulkAction() {
    this.each((idx, el) => {
      $(el).on('click', (evt) => {
        evt.preventDefault();
        const actionButton = $(evt.currentTarget),
              form = actionButton.closest('form');

        $('input.bulk-select-checkbox:checked').each((index, element) => {
          $(`<input type="hidden" name="ids[]" value="${element.value}">`).appendTo(form);
        });

        form.submit();
      });
    });
  },
});
