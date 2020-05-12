jQuery(document).ready(function ($) {

  /**
   * Manage the change of the "statut" combobox
   * Call an Ajax query to change the form entry status
   * @see functions.php::enable_select_status_on_form_entries()
   */
  $('select[name="status"]').change(function () {

    // Get icons and show the waiter
    var waiting = $(this).next('img');
    var checked = $(this).nextAll('i.fa');
    checked.css('display', 'none');
    waiting.css('display', 'inline-block');

    // Get needed values from the select tag
    var form_id   = $(this).data('form_id');
    var entry_id  = $(this).data('entry_id');
    var field_id  = $(this).data('field_id');
    var value     = $(this).find('option:selected').val();

    // Call Ajax request
    jQuery.post(
      ajaxurl,
      {
          'action': 'change_form_entry_status',
          'form_id': form_id,
          'entry_id': entry_id,
          'field_id': field_id,
          'value': value
      },
      function(response){
        // Show the checked icon
        waiting.css('display', 'none');
        checked.fadeIn().css("display","inline-block");
        $(this).blur();
      }
    );
  });


  /**
   * Manage the search of an order (entry) from dashboard
   * Call an Ajax query to change the form entry status
   * @see functions.php::wp_ajax_tinygroom_search_order()
   */
  $('#tinygroom-form-order').submit(function () {
    var form = $(this);
    var entry_id = form.find('input[type="text"]').val();

      // Call Ajax request
      jQuery.post(
        ajaxurl,
        {
            'action': 'tinygroom_search_order',
            'entry_id': entry_id,
        },
        function(response){

          var pattern = /^((http|https):\/\/)/;
          if(pattern.test(response)) {
            window.location.replace(response);
          } else {
            $('#tinygroom-search-order-response').html(response);
          }
        }
      );
    return false;
  });
});