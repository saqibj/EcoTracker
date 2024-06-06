<?php
if ( ! defined( 'ABSPATH' ) ) {
    exit; // Exit if accessed directly.
}

// Function to load the text domain for translation
function ecopower_tracker_load_textdomain() {
    load_plugin_textdomain( 'ecopower-tracker', false, basename( dirname( __FILE__ ) ) . '/languages' ); 
}

add_action( 'plugins_loaded', 'ecopower_tracker_load_textdomain' );
?>