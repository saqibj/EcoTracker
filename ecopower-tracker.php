<?php
/*
Plugin Name: EcoPower Tracker
Description: A plugin to track power generation and carbon offset for wind and solar plants.
Version: 1.0
Author: Saqib Jawaid
License: GPL 3
Text Domain: ecopower-tracker
*/

if (!defined('ABSPATH')) {
    exit; // Exit if accessed directly
}

// Define plugin paths
define('ECOPOWER_TRACKER_PATH', plugin_dir_path(__FILE__));
define('ECOPOWER_TRACKER_URL', plugin_dir_url(__FILE__));

// Include necessary files
include_once ECOPOWER_TRACKER_PATH . 'includes/admin-page.php';
include_once ECOPOWER_TRACKER_PATH . 'includes/shortcodes.php';
include_once ECOPOWER_TRACKER_PATH . 'includes/data-management.php';
include_once ECOPOWER_TRACKER_PATH . 'includes/csv-handler.php';

// Register activation hook
register_activation_hook(__FILE__, 'ecopower_tracker_activate');
function ecopower_tracker_activate() {
    // Create custom database table
    global $wpdb;
    $table_name = $wpdb->prefix . 'ecopower_projects';
    $charset_collate = $wpdb->get_charset_collate();

    $sql = "CREATE TABLE $table_name (
        id mediumint(9) NOT NULL AUTO_INCREMENT,
        project_company varchar(255) NOT NULL,
        project_name varchar(255) NOT NULL,
        project_location varchar(255) NOT NULL,
        plant_type varchar(50) NOT NULL,
        project_cuf float NOT NULL,
        generation_capacity float NOT NULL,
        activation_date date NOT NULL,
        PRIMARY KEY  (id)
    ) $charset_collate;";

    require_once(ABSPATH . 'wp-admin/includes/upgrade.php');
    dbDelta($sql);
}

// Register deactivation hook
register_deactivation_hook(__FILE__, 'ecopower_tracker_deactivate');
function ecopower_tracker_deactivate() {
    // Clean up tasks, if any
}

// Enqueue admin scripts and styles
add_action('admin_enqueue_scripts', 'ecopower_tracker_admin_assets');
function ecopower_tracker_admin_assets() {
    wp_enqueue_style('ecopower-tracker-admin', ECOPOWER_TRACKER_URL . 'css/admin.css');
    wp_enqueue_script('ecopower-tracker-admin', ECOPOWER_TRACKER_URL . 'js/admin.js', array('jquery'), null, true);
}

// Register admin menu
add_action('admin_menu', 'ecopower_tracker_admin_menu');
function ecopower_tracker_admin_menu() {
    add_menu_page(
        __('EcoPower Tracker', 'ecopower-tracker'),
        __('EcoPower Tracker', 'ecopower-tracker'),
        'manage_options',
        'ecopower-tracker',
        'ecopower_tracker_admin_page'
    );
}
?>