import $ from 'jquery';
import { disableButtonsAjaxModal } from './owl-modal-ajax';

$.fn.extend({
  acceptNotification() {
    $(document).on('click', '.accept-notification', function(e){
      let buttonaData = $(this).data();
      
      $('#ajax-modal > .content').addClass('ui form loading');
      $('#ajax-modal .actions > button').each(function() {
        $(this).addClass('disabled');
      });

      $.ajax({
        type: 'POST',
        url: buttonaData.url,
        dataType: 'json',
        contentType: 'application/json',
        data: JSON.stringify({ "_csrf_token": buttonaData.csrfToken }),
        success: function(data, textStatus, xhr){
          var $quantityNotifications = $('.quantity-notifications');
          var quantity = $quantityNotifications.data('quantity')-1;

          $('.item.notification-'+data.id).remove();

          if(quantity == 0) {
            $('.label-quantity-notifications').removeClass('red label');
            $quantityNotifications.remove()
          }else {
            $quantityNotifications.html(quantity)
            $('.quantity-notifications').data('quantity', quantity);
          }

          setTimeout(function() {
            $('#ajax-modal').modal('hide')
          }, 400)
        },
        error: function (xhr) {

        }
      });
    })
  }
});
