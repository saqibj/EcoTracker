<?php
/*
Plugin Name: EcoPower Tracker
Description: A plugin to manage and display renewable energy project data.
Version: 2.1e
Author: Saqib Jawaid
License: GPL 3 or later
*/

// Exit if accessed directly
if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

// Start output buffering
ob_start();

// Include necessary files
include_once plugin_dir_path( __FILE__ ) . 'includes/admin-page.php';
include_once plugin_dir_path( __FILE__ ) . 'includes/csv-import.php';
include_once plugin_dir_path( __FILE__ ) . 'includes/csv-export.php';
include_once plugin_dir_path( __FILE__ ) . 'includes/data-management.php';
include_once plugin_dir_path( __FILE__ ) . 'includes/dashboard.php';
include_once plugin_dir_path( __FILE__ ) . 'includes/shortcodes.php';
include_once plugin_dir_path( __FILE__ ) . 'includes/widgets.php';
include_once plugin_dir_path( __FILE__ ) . 'includes/localization.php';
include_once plugin_dir_path( __FILE__ ) . 'includes/reporting-intervals.php';

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
        PRIMARY KEY  (id)
    ) $charset_collate;";

    require_once( ABSPATH . 'wp-admin/includes/upgrade.php' );
    dbDelta( $sql );
}

// Add admin menus
function ecopower_tracker_admin_menu() {
    add_menu_page( 
        __( 'EcoPower Tracker', 'ecopower-tracker' ), 
        __( 'EcoPower Tracker', 'ecopower-tracker' ), 
        'manage_options', 
        'ecopower-tracker-dashboard', 
        'ecopower_tracker_dashboard_content', 
        plugin_dir_url( __FILE__ ) . 'img/EcoTracker-Wht.svg', 
        20 
    );

    add_submenu_page(
        'ecopower-tracker-dashboard',
        __( 'Dashboard', 'ecopower-tracker' ),
        __( 'Dashboard', 'ecopower-tracker' ),
        'manage_options',
        'ecopower-tracker-dashboard',
        'ecopower_tracker_dashboard_content'
    );

    add_submenu_page(
        'ecopower-tracker-dashboard',
        __( 'All Projects', 'ecopower-tracker' ),
        __( 'All Projects', 'ecopower-tracker' ),
        'manage_options',
        'ecopower-tracker-all-projects',
        'ecopower_tracker_all_projects'
    );

    add_submenu_page(
        'ecopower-tracker-dashboard',
        __( 'Add New Project', 'ecopower-tracker' ),
        __( 'Add New Project', 'ecopower-tracker' ),
        'manage_options',
        'ecopower-tracker-add-new-project',
        'ecopower_tracker_add_new_project'
    );

    add_submenu_page(
        'ecopower-tracker-dashboard',
        __( 'Import/Export', 'ecopower-tracker' ),
        __( 'Import/Export', 'ecopower-tracker' ),
        'manage_options',
        'ecopower-tracker-import-export',
        'ecopower_tracker_display_import_export'
    );

    add_submenu_page(
        'ecopower-tracker-dashboard',
        __( 'Settings', 'ecopower-tracker' ),
        __( 'Settings', 'ecopower-tracker' ),
        'manage_options',
        'ecopower-tracker-settings',
        'ecopower_tracker_settings_page'
    );

    add_submenu_page(
        'ecopower-tracker-dashboard',
        __( 'Reporting Intervals', 'ecopower-tracker' ),
        __( 'Reporting Intervals', 'ecopower-tracker' ),
        'manage_options',
        'ecopower-tracker-reporting-intervals',
        'ecopower_tracker_reporting_intervals'
    );

    add_submenu_page(
        'ecopower-tracker-dashboard',
        __( 'About', 'ecopower-tracker' ),
        __( 'About', 'ecopower-tracker' ),
        'manage_options',
        'ecopower-tracker-about',
        'ecopower_tracker_about_page'
    );
}
add_action( 'admin_menu', 'ecopower_tracker_admin_menu' );

// Initialize the plugin
function ecopower_tracker_init() {
    // Code to initialize the plugin
}
add_action( 'plugins_loaded', 'ecopower_tracker_init' );

// Placeholder for displaying the Import/Export page
function ecopower_tracker_display_import_export() {
    // Import/Export content goes here
    echo '<div class="wrap"><h1>' . __( 'Import/Export', 'ecopower-tracker' ) . '</h1>';
    echo '<p>' . __( 'Import and export project data using CSV files.', 'ecopower-tracker' ) . '</p></div>';
}

// Placeholder for displaying the Settings page
function ecopower_tracker_settings_page() {
    // Settings content goes here
    echo '<div class="wrap"><h1>' . __( 'Settings', 'ecopower-tracker' ) . '</h1>';
    echo '<p>' . __( 'Settings page content goes here.', 'ecopower-tracker' ) . '</p></div>';
}

// Placeholder for displaying the Reporting Intervals page
function ecopower_tracker_reporting_intervals() {
    // Reporting Intervals content goes here
    echo '<div class="wrap"><h1>' . __( 'Reporting Intervals', 'ecopower-tracker' ) . '</h1>';
    echo '<p>' . __( 'Configure the reporting intervals for power generation and CO2 offset.', 'ecopower-tracker' ) . '</p></div>';
}

// Placeholder for displaying the About page
function ecopower_tracker_about_page() {
    echo '<div class="wrap">';
    echo '<h1>' . __( 'About EcoPower Tracker', 'ecopower-tracker' ) . '</h1>';
    echo '<div style="text-align: center;"><img src="' . plugin_dir_url( __FILE__ ) . 'img/EcoTracker-Logo.webp" style="max-width: 20%;" alt="EcoPower Tracker Logo" /></div>';
    echo '<p>' . __( 'Version 2.1e', 'ecopower-tracker' ) . '</p>';
    echo '<p>' . __( 'EcoPower Tracker helps you manage and display renewable energy project data, including total power generation and CO2 offset.', 'ecopower-tracker' ) . '</p>';
    echo '<h2>' . __( 'Usage Manual', 'ecopower-tracker' ) . '</h2>';
    echo '<p>' . __( 'Use the shortcodes [ecopower_tracker_projects], [ecopower_tracker_total_power], [ecopower_tracker_total_co2], [ecopower_tracker_total_power_number], and [ecopower_tracker_total_co2_number] to display project information on your site.', 'ecopower-tracker' ) . '</p>';
    echo '<p>' . __( 'For support, visit our GitHub page: <a href="https://github.com/saqibj/EcoTracker/" target="_blank">EcoPower Tracker on GitHub</a>.', 'ecopower-tracker' ) . '</p>';
    echo '<p>' . __( 'To report issues or request features, open an issue on our GitHub Issues page: <a href="https://github.com/saqibj/EcoTracker/issues" target="_blank">GitHub Issues</a>.', 'ecopower-tracker' ) . '</p>';
    echo '</div>';
}

?>