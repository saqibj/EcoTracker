<?php
if ( ! defined( 'ABSPATH' ) ) {
    exit; // Exit if accessed directly.
}

function ecopower_tracker_load_textdomain() {
    load_plugin_textdomain( 'ecopower-tracker', false, dirname( plugin_basename( __FILE__ ) ) . '/languages' );
}
add_action( 'plugins_loaded', 'ecopower_tracker_load_textdomain' );

?>