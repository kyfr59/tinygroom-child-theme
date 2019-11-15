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


