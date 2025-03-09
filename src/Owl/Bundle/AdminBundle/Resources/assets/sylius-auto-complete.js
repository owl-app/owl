/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

import 'fomantic-ui/dist/components/dropdown';
import $ from 'jquery';

$.fn.extend({
  autoComplete() {
    this.each((idx, el) => {
      const element = $(el);
      const criteriaName = element.data('criteria-name');
      const choiceName = element.data('choice-name');
      const choiceValue = element.data('choice-value');
      const autocompleteValue = element.find('input.autocomplete').val();
      const loadForEditUrl = element.data('load-edit-url');
      const menuElement = element.find('div.menu');
      const extraFieldsElement = element.find('.extra-fields-autocomplete');
      const eventName = element.data('event');
      const extraData = element.data('extra-data').split(',');
      const extraFields = (() => {
        let fields = [];

        extraFieldsElement.children().map((key, child) => {
          fields.push({
            'id': $(child).attr('id'),
            'name': $(child).data('name')
          });
        });

        return fields;
      })();
      const appendExtraDataAttr = (element, item) => {
        if(extraFields.length > 0) {
          extraFields.map((data) => {
            element.attr('data-'+data.name, item[data.name]);
          })
        }

        if(extraData.length > 0) {
          extraData.map((value) => {
            value = value.trim();
            element.attr('data-'+value, item[value]);
          })
        }

        return element;
      }

      element.dropdown({
        delay: {
          search: 250,
        },
        minCharacters: 1,
        forceSelection: false,
        apiSettings: {
          dataType: 'JSON',
          cache: false,
          beforeSend(settings) {
            /* eslint-disable-next-line no-param-reassign */
            settings.data[criteriaName] = settings.urlData.query;

            return settings;
          },
          onResponse(response) {
            menuElement.empty();
            response.map((item) => {
              menuElement.append(
                appendExtraDataAttr(
                  $(`<div class="item" data-value="${item[choiceValue]}">${item[choiceName]}</div>`),
                  item
                )
              );
            });
          },
        },
        onChange(value, text, $choice) {
          let data = {};

          if (typeof value !== undefined && value !== '') {
            if(extraFields.length > 0) {
              extraFields.map((data) => {
                $('#'+data.id).val($choice.data(data.name));
              })
            }
  
            if(extraData.length > 0) {
              extraData.map((value) => {
                value = value.trim();
                data[value] = $choice.data(value);
              })
            }
  
            if(eventName !== '') {
              $(document).trigger('change-autocomplete-'+eventName, [value, data]);
            }
          }
        }
      });

      if (autocompleteValue.split(',').filter(String).length > 0 && loadForEditUrl!== "") {
        menuElement.api({
          on: 'now',
          method: 'GET',
          url: loadForEditUrl,
          beforeSend(settings) {
            /* eslint-disable-next-line no-param-reassign */
            settings.data[choiceValue] = autocompleteValue.split(',').filter(String);

            return settings;
          },
          onSuccess(response) {
            response.forEach((item) => {
              menuElement.append((
                $(`<div class="item" data-value="${item[choiceValue]}">${item[choiceName]}</div>`)
              ));
            });

            element.dropdown('refresh');
            element.dropdown('set selected', element.find('input.autocomplete').val().split(',').filter(String));
          },
        });
      }else{
        menuElement.append((
          $(`<div class="item" data-value="${autocompleteValue}">${autocompleteValue}</div>`)
        ));

        element.dropdown('refresh');
        element.dropdown('set selected', autocompleteValue);
      }
    });
  },
});
