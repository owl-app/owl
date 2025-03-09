import $ from 'jquery';
import Dropzone from "dropzone";
import redirect from 'owl/ui/owl-redirect';

let redirectUrl = '';

const extractParamsUrl = function([beg, end], str, data) {
  const matcher = new RegExp(`${beg}(.*?)${end}`,'gm'),
        normalise = (str) => str.slice(beg.length,end.length*-1),
        params = str.match(matcher).map(normalise);

  params.map((item) => {
    str = str.replace(beg+item, data[item]);
  })

  return str;
}

const uploaderInit = function(current, quantity) {
  return function () {
    this.on("complete", function (file) {
      if (this.getUploadingFiles().length === 0 && this.getQueuedFiles().length > 0) {
        this.processQueue();
      } else {
        if(current === quantity) {
          setTimeout(function(){
            redirect(redirectUrl);
          }, 800);
        }
      }
    })
  }
}

const flattenObject = function (obj, parent, res = {}) {
    for(let key in obj){
      if(typeof obj[key] == 'object'){
        let propName = parent ? parent + '_' + key : key;
        flattenObject(obj[key], propName, res);
      } else {
          res[parent] = obj[key];
      }
    }
  
    return res;
}

const renderErrors = function(data, $form, formName) {
    let errors = flattenObject(data.errors)

    $form.removeClass('error');
    $form.find('.sylius-validation-error').remove();

    $('.field').each(function() {
      $(this).removeClass('error');
    })

    Object.keys(errors).map(function(key, value) {
      let field = $('#'+formName+'_'+key).closest('.field');
      field.addClass('error');
      field.append(
        '<div class="ui red pointing label sylius-validation-error">'+
          errors[key]+
        '</div>'
      )
    });

    $form.removeClass('loading');
}

$.fn.extend({
  ajaxForm(params) {
    let initSettings = {
          modal : false
        },
        settings = {...initSettings, ...params},
        dropzones = [],
        quantityUploaders = this.find('.uploader').length,
        currentUploader = 1;

    $('.uploader', this).each(function () {
        let data = $(this).data(),
            options = {
              url: data.url,
              autoProcessQueue: false,
              parallelUploads: 1,
              maxFiles: 100,
              init: uploaderInit(currentUploader, quantityUploaders),
              dictDefaultMessage: "Proszę wybrać pliki"
            }

        dropzones.push(new Dropzone('#'+$(this).attr('id'), options));

        currentUploader++;
    });

    this.on('submit', function(event) {
      event.preventDefault();

      var btn = $(":focus"),
          $this = $(this),
          formName = $this.attr('name'),
          formAction = $this.attr('action'),
          formMethod = $this.find('input[name=_method]').val() ?? $this.attr('method')

      if(btn.hasClass('save-action')) {
        $this.addClass('loading');

        $.ajax({
          type: formMethod,
          url: formAction,
          data: $this.serialize()+ '&save_action=' + btn.val(),
          dataType: 'json',
          success: function(data, textStatus, xhr){
            let quantityToUpload = 0;

            redirectUrl = xhr.getResponseHeader('x-owl-location')
      
            if (xhr.status >= 200 && xhr.status < 300) {
              if(redirectUrl !== null) {
                if(dropzones.length > 0) {
                  $('.ui.loading.form').addClass('uploading');

                  dropzones.forEach((item) => {
                    item.options.url = extractParamsUrl(['param_','/'], item.options.url, data);
                    quantityToUpload += item.getQueuedFiles().length;

                    if(item.getQueuedFiles().length > 0) {
                      let tab = $(item.element).parents('.tab').data('tab');
                      $('.owl-tabular-form .menu .item').tab('change tab', tab);
                    }

                    item.processQueue();
                  })
                }

                if(quantityToUpload === 0) {
                  $("#ajax-modal").modal("hide");
                  redirect(redirectUrl);
                }
                
                return;
              }
            }
          },
          error: function (xhr) {
            if(xhr.status === 422 ) {
              if(settings.modal) {
                $('#ajax-modal .actions .buttons > button').each(function() {
                  $(this).removeClass('disabled');
                });
              }
              renderErrors(xhr.responseJSON, $this, formName)
            } else {
              return new Error(xhr);
            }
          }
        });
      }
    });
  }
});

