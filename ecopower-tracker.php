<?php
/**
 * Plugin Name: EcoPower Tracker
 * Description: A plugin to track and manage data for renewable energy projects.
 * Version: 1.0.1
 * Author: Saqib Jawaid
 * License: GPLv3 or later
 */

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

// Define plugin constants
define( 'ECOPOWER_TRACKER_VERSION', '1.0.1' );
define( 'ECOPOWER_TRACKER_DIR', plugin_dir_path( __FILE__ ) );
define( 'ECOPOWER_TRACKER_URL', plugin_dir_url( __FILE__ ) );

// Include necessary files
require_once ECOPOWER_TRACKER_DIR . 'includes/enqueue-scripts.php';
require_once ECOPOWER_TRACKER_DIR . 'includes/admin-page.php';
require_once ECOPOWER_TRACKER_DIR . 'includes/project-functions.php';
require_once ECOPOWER_TRACKER_DIR . 'includes/csv-handler.php';

// Activation hook
register_activation_hook( __FILE__, 'ecopower_tracker_activate' );
function ecopower_tracker_activate() {
    ecopower_tracker_create_table();
}

// Deactivation hook
register_deactivation_hook( __FILE__, 'ecopower_tracker_deactivate' );
function ecopower_tracker_deactivate() {
    // Actions to perform on deactivation
}

// Admin menu hook
add_action( 'admin_menu', 'ecopower_tracker_admin_menu' );
function ecopower_tracker_admin_menu() {
    add_menu_page(
        'EcoPower Tracker',
        'EcoPower Tracker',
        'manage_options',
        'ecopower-tracker',
        'ecopower_tracker_project_data_page',
        'dashicons-chart-area',
        6
    );
}

// Handle delete project action
add_action( 'admin_init', 'ecopower_tracker_handle_delete_project' );
function ecopower_tracker_handle_delete_project() {
    if ( isset( $_GET['action'] ) && $_GET['action'] == 'delete' && isset( $_GET['project_id'] ) ) {
        ecopower_tracker_delete_project( intval( $_GET['project_id'] ) );
        wp_redirect( admin_url( 'admin.php?page=ecopower-tracker' ) );
        exit;
    }
}
?>
