<?php
if ( ! defined( 'ABSPATH' ) ) {
    exit; // Exit if accessed directly.
}

function ecopower_tracker_register_settings() {
    register_setting( 'ecopower_tracker_options', 'ecopower_projects', 'ecopower_tracker_sanitize' );
}
add_action( 'admin_init', 'ecopower_tracker_register_settings' );
?>
