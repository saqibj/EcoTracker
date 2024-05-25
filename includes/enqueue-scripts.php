<?php
if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

add_action( 'admin_enqueue_scripts', 'ecopower_tracker_enqueue_admin_scripts' );
function ecopower_tracker_enqueue_admin_scripts() {
    wp_enqueue_style( 'ecopower-tracker-admin-style', ECOPOWER_TRACKER_URL . 'css/admin-style.css', array(), ECOPOWER_TRACKER_VERSION );
    wp_enqueue_script( 'ecopower-tracker-admin-script', ECOPOWER_TRACKER_URL . 'js/admin-script.js', array( 'jquery' ), ECOPOWER_TRACKER_VERSION, true );
}
?>
