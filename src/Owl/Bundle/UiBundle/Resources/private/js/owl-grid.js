import $ from 'jquery';
import redirect from './owl-redirect';

class GridComponent {
  constructor(wrapper) {
    if (!wrapper) return;

    this.wrapper = wrapper;
    this.table = this.wrapper.getElementsByClassName('sylius-grid-table-wrapper')[0]
    this.bulkActions = this.wrapper.querySelectorAll('[data-bulk-action]');
  }

  addLoading() {
    if (typeof(this.table) != 'undefined' && this.table != null) {
      this.table.className += ' loading';

      this.bulkActions.forEach((action) => {
        action.className += ' disabled';
      });
    }

  }

  removeLoading() {
    if (typeof(this.table) != 'undefined' && this.table != null) {
      this.table.classList.remove('loading')

      this.bulkActions.forEach((action) => {
        action.classList.remove('disabled');
      });
    }
  }

  delete(url, data) {
    var self = this;

    $.ajax({
      type: 'DELETE',
      url: url,
      data: data,
      success: function(data, textStatus, xhr){
        setTimeout(function(){
          self.removeLoading();
          redirect(xhr.getResponseHeader('x-owl-location'));
        }.bind(self), 400);
        
      },
      error: function (xhr) {
        self.removeLoading();
      }
    });
  }
}

export default GridComponent;
