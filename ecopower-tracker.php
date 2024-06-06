<?php
/*
Plugin Name: EcoPower Tracker
Description: A plugin to manage and display renewable energy project data.
Version: 2.1d
Author: Saqib Jawaid
License: GPL 3 or later
*/

// Exit if accessed directly
if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

// Include necessary files
include_once plugin_dir_path( __FILE__ ) . 'includes/csv-import.php';
include_once plugin_dir_path( __FILE__ ) . 'includes/csv-export.php';
include_once plugin_dir_path( __FILE__ ) . 'includes/csv-process.php';
include_once plugin_dir_path( __FILE__ ) . 'includes/admin-page.php';
include_once plugin_dir_path( __FILE__ ) . 'includes/data-management.php';
include_once plugin_dir_path( __FILE__ ) . 'includes/dashboard.php';
include_once plugin_dir_path( __FILE__ ) . 'includes/widgets.php';
include_once plugin_dir_path( __FILE__ ) . 'includes/localization.php';
include_once plugin_dir_path( __FILE__ ) . 'includes/reporting-intervals.php';
include_once plugin_dir_path( __FILE__ ) . 'includes/about.php';

// Activation and deactivation hooks
register_activation_hook( __FILE__, 'ecopower_tracker_activate' );
register_deactivation_hook( __FILE__, 'ecopower_tracker_deactivate' );

function ecopower_tracker_activate() {
    // Code to run on activation
    ecopower_tracker_create_db();
}

function ecopower_tracker_deactivate() {
    // Code to run on deactivation
}

// Initialize the plugin
function ecopower_tracker_init() {
    // Code to initialize the plugin
}

add_action( 'plugins_loaded', 'ecopower_tracker_init' );

// Function to create the database
function ecopower_tracker_create_db() {
    global $wpdb;
    $table_name = $wpdb->prefix . 'ecopower_projects';
    $charset_collate = $wpdb->get_charset_collate();

    $sql = "CREATE TABLE $table_name (
        id mediumint(9) NOT NULL AUTO_INCREMENT,
        project_company varchar(255) NOT NULL,
        project_name varchar(255) NOT NULL,
        project_location varchar(255) NOT NULL,
        type_of_plant varchar(255) NOT NULL,
        project_cuf float NOT NULL,
        generation_capacity float NOT NULL,
        date_of_activation date NOT NULL,
        PRIMARY KEY (id)
    ) $charset_collate;";

    require_once( ABSPATH . 'wp-admin/includes/upgrade.php' );
    dbDelta( $sql );
}

// Add admin menu
function ecopower_tracker_admin_menu() {
    add_menu_page(
        __( 'EcoPower Tracker', 'ecopower-tracker' ),
        __( 'EcoPower Tracker', 'ecopower-tracker' ),
        'manage_options',
        'ecopower-tracker',
        'ecopower_tracker_dashboard',
        'dashicons-chart-line',  // Use a built-in dashicon
        6
    );

    add_submenu_page(
        'ecopower-tracker',
        __( 'Dashboard', 'ecopower-tracker' ),
        __( 'Dashboard', 'ecopower-tracker' ),
        'manage_options',
        'ecopower-tracker-dashboard',
        'ecopower_tracker_dashboard'
    );

    add_submenu_page(
        'ecopower-tracker',
        __( 'All Projects', 'ecopower-tracker' ),
        __( 'All Projects', 'ecopower-tracker' ),
        'manage_options',
        'ecopower-tracker-all-projects',
        'ecopower_tracker_all_projects_page'
    );

    add_submenu_page(
        'ecopower-tracker',
        __( 'Add New Project', 'ecopower-tracker' ),
        __( 'Add New Project', 'ecopower-tracker' ),
        'manage_options',
        'ecopower-tracker-add-new-project',
        'ecopower_tracker_add_new_project_page'
    );

    add_submenu_page(
        'ecopower-tracker',
        __( 'Import/Export Projects', 'ecopower-tracker' ),
        __( 'Import/Export', 'ecopower-tracker' ),
        'manage_options',
        'ecopower-tracker-import-export',
        'ecopower_tracker_display_import_export_page'
    );

    add_submenu_page(
        'ecopower-tracker',
        __( 'Settings', 'ecopower-tracker' ),
        __( 'Settings', 'ecopower-tracker' ),
        'manage_options',
        'ecopower-tracker-settings',
        'ecopower_tracker_settings_page'
    );

    add_submenu_page(
        'ecopower-tracker',
        __( 'Reporting Intervals', 'ecopower-tracker' ),
        __( 'Reporting Intervals', 'ecopower-tracker' ),
        'manage_options',
        'ecopower-tracker-reporting-intervals',
        'ecopower_tracker_reporting_intervals_page'
    );

    add_submenu_page(
        'ecopower-tracker',
        __( 'About', 'ecopower-tracker' ),
        __( 'About', 'ecopower-tracker' ),
        'manage_options',
        'ecopower-tracker-about',
        'ecopower_tracker_about_page'
    );
}

add_action( 'admin_menu', 'ecopower_tracker_admin_menu' );

// Functions for the submenu pages
function ecopower_tracker_dashboard() {
    include plugin_dir_path( __FILE__ ) . 'includes/dashboard.php';
}

function ecopower_tracker_all_projects_page() {
    include plugin_dir_path( __FILE__ ) . 'includes/admin-page.php';
}

function ecopower_tracker_add_new_project_page() {
    include plugin_dir_path( __FILE__ ) . 'includes/admin-page.php';
}

function ecopower_tracker_display_import_export_page() {
    include plugin_dir_path( __FILE__ ) . 'includes/csv-import.php';
}

function ecopower_tracker_settings_page() {
    echo '<h1>Settings</h1>';
}

function ecopower_tracker_reporting_intervals_page() {
    include plugin_dir_path( __FILE__ ) . 'includes/reporting-intervals.php';
}

function ecopower_tracker_about_page() {
    include plugin_dir_path( __FILE__ ) . 'includes/about.php';
}
?>