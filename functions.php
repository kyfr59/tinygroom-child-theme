<?php
/**
 * Tinygroom functions and definitions.
 */

// Add CSS & JS to frontend
add_action('wp_enqueue_scripts', 'theme_enqueue_frontend_styles');
function theme_enqueue_front_styles()
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
}


// Add a button to download the letter/cerfa from the admin
add_action('gform_entry_detail_sidebar_after', 'tinygroom_add_send_letter_button_in_admin', 10, 2);
function tinygroom_add_send_letter_button_in_admin($form, $lead)
{
  if ( $form['id'] == 1 ) {
    $link = '<a download href="../admin937597_php/letter.php?id='.$lead['id'].'" class="button">Télécharger la lettre</a>';
    $file = "letter";
    $text = "la lettre";
  } elseif ( $form['id'] == 18 ) {
    $file = "cerfa_ecj";
    $text = "le CERFA";
  }
  echo '<div class="detail-view-print"><a download href="../admin937597_php/'.$file.'.php?id='.$lead['id'].'" class="button">Télécharger '.$text.'</a></div>';
}


add_filter( 'gform_entry_list_columns', 'set_columns', 10, 2 );
function set_columns( $table_columns, $form_id ){

//array_splice( $table_columns, 1, 0, ['field_id-88884' => 'Statut'] );


    return $table_columns;
}