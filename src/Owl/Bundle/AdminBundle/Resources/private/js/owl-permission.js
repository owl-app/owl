import $ from 'jquery';

$.fn.extend({
  changePermission() {
    $('.ui.checkbox-permission').checkbox({
      onChange(elem) {
        let $checkbox = $(this).parent('.checkbox'),
            $form = $(this).parents('form'),
            action,
            $listPermissions = $('#list-permissions');

        if($form.attr('name') == 'availables_roles-'+$form.data('route')) {
          $form.parents('.content').append(
            '<div class="ui active inverted dimmer">'+
              '<div class="ui medium loader"></div>'+
            '</div>'
          );
        } else {
          $checkbox.checkbox('set disabled');
        }

        if($checkbox.checkbox('is checked')) {
          action = createPermission(
            createUrl($listPermissions.data('add-url'), $form), 
            getData($listPermissions.data('add-with-form-data'), $form)
          );
        } else {
          action = deletePermission(
            createUrl($listPermissions.data('remove-url'), $form),
            getData($listPermissions.data('remove-with-form-data'), $form, true)
          );
        }


        action.done((data) => {
          if(data.permissions !== undefined) {
            $('form', $listPermissions).each(function (key, item) {
              let route = $(this).data('route'),
                  checkbox = $(this).find('.checkbox');

              if($.inArray( route, data.permissions.inherited)  !== -1) {
                if($.inArray( route, data.permissions.direct)  === -1) {
                  checkbox.checkbox('set checked');
                  checkbox.checkbox('set disabled');
                  
                }else {
                  checkbox.checkbox('set checked');
                }
              } else if($.inArray( route, data.permissions.direct)  === -1) {
                $(item).find('input[type=hidden]').removeAttr('disabled')
                checkbox.checkbox('set enabled');
                checkbox.checkbox('set unchecked');
              } else {
                checkbox.checkbox('set enabled');
              }
            });
            setTimeout(function() {
              $form.parents('.content').find('.dimmer').remove();
            }, 200)
          } else {
            $checkbox.checkbox('set enabled');
          }
        });

        action.fail(function (jqXHR, textStatus) {
          if($checkbox.checkbox('is checked')) {
            $checkbox.checkbox('set unchecked')
          } else {
            $checkbox.checkbox('set checked')
          }
        });
      }
    });

    const createPermission = (url, data) => {
      return $.ajax({
        type: 'POST',
        url: url,
        data: data
      });
    };

    const deletePermission = (url, data) => {
      return $.ajax({
        type: 'POST',
        url: url,
        data: data
      });
    };

    const createUrl = (url, $form) => {
      if(url.includes('permission_name')) {
        url = url.replace('permission_name', $form.find('input[name="name"]').val())
      }

      return url;
    }

    const getData = (withFormData, $form, isDelete) => {
      let formData;

      if(withFormData) {
        formData = $form.serializeArray();
      } else {
        formData = [
          { name : '_csrf_token', value: $form.find('input[name="_csrf_token"]').val() }
        ];
      }

      if(isDelete) {
        formData.push({ name: '_method', value: 'DELETE' });
      }

      return formData;
    }
  }
});
