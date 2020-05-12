<?php
/**
 * Tinygroom functions and definitions.
 */

// Add CSS & JS to frontend
add_action('wp_enqueue_scripts', 'theme_enqueue_frontend_styles');
function theme_enqueue_frontend_styles()
{
    wp_enqueue_style('parent-style', get_template_directory_uri() . '/style.css');
    wp_enqueue_style('tinygroom', get_stylesheet_uri());
    wp_enqueue_script('tinygroom', get_stylesheet_directory_uri() . '/js/frontend.js', array( 'jquery' ), false, true);
}


// Add JS to backend
add_action('admin_enqueue_scripts', 'theme_enqueue_backend_styles');
function theme_enqueue_backend_styles()
{
    wp_enqueue_script('tinygroom', get_stylesheet_directory_uri() . '/js/backend.js', array( 'jquery' ), false, true);

    // pass Ajax Url to script.js
    wp_localize_script('tinygroom', 'ajaxurl', admin_url( 'admin-ajax.php' ) );
}


// Replace the status value by a combo and change the status via Ajax (see js/backend.js)
add_filter( 'gform_get_field_value', 'enable_select_status_on_form_entries', 10, 3 );
function enable_select_status_on_form_entries( $value, $entry, $field ){

  if (!isset($_GET['lid']) && $_GET['page'] == 'gf_entries' && $field['type'] == 'select' && $field['adminLabel'] == 'Statut') {

    // Build and display the combobox
    $form     =  GFFormsModel::get_form_meta( $entry['form_id'] );
    $fields   = $form['fields'];
    $key      = array_search('Statut', array_column($fields, 'adminLabel'));
    $choices  = $fields[$key]['choices'];

    $selected[$value] = 'selected="selected"';
    $select = '<select name="status" data-form_id="'.$entry['form_id'].'" data-entry_id="'.$entry['id'].'" data-field_id="'.$field['id'].'" >';
    foreach($choices as $choice) {
      $select .= '<option '.@$selected[$choice['value']].' value="'.$choice['value'].'">'.$choice['text'].'</option>';
    }
    $select .= '</select>';
    $select .= '<img class="waiting-status" src="'.get_stylesheet_directory_uri().'/img/waiting.gif" style="position: absolute; padding-left: 2px; padding-top: 1px; display:none;" />';
    $select .= '<i class="fa fa-check fa-lg" style="size:2px; position: absolute; padding-left: 4px; padding-top: 7px; color:green; opacity:.7; display:none;"></i>';

    echo $select;
    return;
  }

  return $value;
}


// Manage the Ajax call to change the status (see enable_select_status_on_form_entries() and js/backend.js)
add_action( 'wp_ajax_change_form_entry_status', 'change_form_entry_status' );
function change_form_entry_status() {

  // Retrive values pass throught Ajax
  $form_id  = $_POST['form_id'];
  $entry_id = $_POST['entry_id'];
  $field_id = $_POST['field_id'];
  $value    = $_POST['value'];

  // Get needed data to update the value in database
  global $wpdb;
  $form           = GFFormsModel::get_form_meta($form_id);
  $entry          = GFFormsModel::get_lead($entry_id);
  $field          = GFFormsModel::get_field($form, $field_id);
  $entry_meta_table_name = GFFormsModel::get_entry_meta_table_name();
  $sql                   = $wpdb->prepare("SELECT id FROM {$entry_meta_table_name} WHERE entry_id=%d AND meta_key = %s", $entry_id, $field_id);
  $entry_meta_id         = $wpdb->get_var($sql);

  // Update the value in database
  GFFormsModel::update_entry_field_value( $form, $entry, $field, $entry_meta_id, $field_id, $value);
}


//  Set most older year to 1885 in select boxes
add_filter( 'gform_date_min_year', 'set_min_year' );
function set_min_year( $min_year ) {
    return 1885;
}

// Set the gender field required
add_filter( 'gform_field_validation', 'tinygroom_set_gender_field_required', 10, 4);
function tinygroom_set_gender_field_required ( $result, $value, $form, $field ) {

  if ( $field->type == 'name' ) {

    $prefix = rgar( $value, $field->id . '.2' );
    $first  = rgar( $value, $field->id . '.3' );
    $middle = rgar( $value, $field->id . '.4' );
    $last   = rgar( $value, $field->id . '.6' );
    $suffix = rgar( $value, $field->id . '.8' );

    if ( empty( $prefix ) && ! $field->get_input_property( '2', 'isHidden' )
      || empty( $first ) && ! $field->get_input_property( '3', 'isHidden' )
      || empty( $middle ) && ! $field->get_input_property( '4', 'isHidden' )
      || empty( $last ) && ! $field->get_input_property( '6', 'isHidden' )
      || empty( $suffix ) && ! $field->get_input_property( '8', 'isHidden' )
    ) {
      $result['is_valid'] = false;
      $result['message']  = empty( $field->errorMessage ) ? __( 'Ce champ est obligatoire, merci de le remplir.', 'gravityforms' ) : $field->errorMessage;
    } else {
      $result['is_valid'] = true;
      $result['message']  = '';
    }
  }

  return $result;
}


/**
 * Add the search order widget to the dashboard.
 */
add_action( 'wp_dashboard_setup', 'tinygroom_add_dashboard_widgets' );
function tinygroom_add_dashboard_widgets() {
  wp_add_dashboard_widget(
    'tinygroom_search_order_widget',
    'Rechercher une commande',
    'tinygroom_display_search_order_widget'
    );
}

function tinygroom_display_search_order_widget() {
  echo '<form action="#" method="post" id="tinygroom-form-order">';
  echo "<input type='text' name='id' />";
  echo "<input type='submit'>";
  echo '</form>';
  echo '<div id="tinygroom-search-order-response" style="margin:10px;"></div>';
}

// Manage the Ajax call to change the status (see enable_select_status_on_form_entries() and js/backend.js)
add_action( 'wp_ajax_tinygroom_search_order', 'tinygroom_search_order' );
function tinygroom_search_order() {

  // Retrive values pass throught Ajax
  if (!$entry_id = $_POST['entry_id']) die("Aucune commande trouvée");

  // Check for entry in database
  $entry = GFFormsModel::get_lead($entry_id);

  if (isset($entry['id']) && is_numeric($entry['id'])) {
    die($admin_url = admin_url( 'admin.php?page=gf_entries&view=entry&id=' . $entry['form_id'] . '&lid='.$entry['id']));
  } else {
    die("Aucune commande trouvée");
  }
}
