import $ from "jquery";

const renderContent = (data, isBulk) => {
  setTimeout(() => {
    $("form.is-modal-ajax").find(".loader").remove();
    $("#ajax-modal").html(data);
    $("form.is-modal-ajax").ajaxForm({modal: true});
    $(".owl-tabular-show .item").tab();
    $(".ui.modal.active").css({position: "fixed"});

    if (isBulk) {
      let form = $(".modal.active form");

      $("input.bulk-select-checkbox:checked").each((index, element) => {
        $(`<input type="hidden" name="ids[]" value="${element.value}">`).appendTo(form);
      });
    }

    $("#ajax-modal").modal("refresh");

    if($("#ajax-modal .ui.dropdown").length) {
      $('#ajax-modal .ui.dropdown').dropdown();
    }

    if($('#ajax-modal').find('.tinymce').length) {
      initTinyMCE();
    }

    $("#ajax-modal .actions .buttons > button[type=submit]").click(function (e) {
      e.preventDefault();
  
      let targetForm = $(this).data('target');

      $("#ajax-modal .actions .buttons > button").each(function () {
        $(this).addClass("disabled");
      });

      if (typeof tinyMCE !== "undefined") {
        tinyMCE.triggerSave();
      }

      if(targetForm != undefined) {
        $(".modal.active form[name="+targetForm+']').submit();
      }else{
        $(".modal.active form").submit();
      }

      if ($("form.is-modal-generate-document").length > 0) {
        $("form.is-modal-generate-document").addClass("loading");

        setTimeout(function () {
          $("#ajax-modal").modal("hide");
        }, 400);
      }
    });
  }, 300);
};

$.fn.extend({
  ajaxModal() {
    let data = $(this).data(),
      isBulk = false;

    if (typeof $(this).attr("data-bulk-action-modal") !== "undefined") {
      isBulk = true;
    }

    $(this).click(() => {
      $("#ajax-modal").html("");
      $("#ajax-modal").append(`<div class="ui massive text loader active loader-ajax-modal">Ładowanie</div>`);
      $("#ajax-modal").modal({
        duration: 300, 
        transition: "scale", 
        closable: false,
        onHidden: function () {
          $(".ui.modal.hidden").removeAttr("style");
        },
        onHide: function () {
          if (typeof tinyMCE !== "undefined") {
            tinyMCE.editors = [];
          }
        }
      }).modal("show");

      $.ajax({
        type: "GET",
        url: data["url"],
        success: function (data) {
          renderContent(data, isBulk);
        },
        error: function (xhr) {
          setTimeout(function () {
            $("#ajax-modal").modal("hide");
          }, 400);
        }
      });
    });
  }
});
