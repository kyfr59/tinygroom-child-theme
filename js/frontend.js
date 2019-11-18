jQuery(document).ready(function ($) {

  // Redirect to CMCIC payment platform (Monetico)
  formBanque = document.getElementById('cmcic_form');
  if(formBanque) {
      formBanque.submit();
  }

  $('td.field_id-84').die();

});