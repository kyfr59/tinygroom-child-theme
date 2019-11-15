<?php
/**
 * Tinygroom functions and definitions.
 */

// Add CSS & JS
add_action('wp_enqueue_scripts', 'theme_enqueue_styles');
function theme_enqueue_styles()
{
    wp_enqueue_style('parent-style', get_template_directory_uri() . '/style.css');
    wp_enqueue_style('tinygroom', get_stylesheet_uri());
    wp_enqueue_script('tinygroom', get_stylesheet_directory_uri() . '/js/javascript.js', array( 'jquery' ), false, true);
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