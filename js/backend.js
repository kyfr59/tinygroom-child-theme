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
    waiting.show();

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
        waiting.hide();
        checked.fadeIn(800);
        $(this).blur();
      }
    );
  });

});