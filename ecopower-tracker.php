<?php
/*
Plugin Name: EcoPower Tracker
Description: A plugin to manage and display renewable energy project data.
Version: 2.1f
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
include_once plugin_dir_path( __FILE__ ) . 'includes/widgets.php';
include_once plugin_dir_path( __FILE__ ) . 'includes/localization.php';
include_once plugin_dir_path( __FILE__ ) . 'includes/reporting-intervals.php';
include_once plugin_dir_path( __FILE__ ) . 'includes/about.php';
include_once plugin_dir_path( __FILE__ ) . 'includes/shortcodes.php';

// Activation and deactivation hooks
register_activation_hook( __FILE__, 'ecopower_tracker_activate' );
register_deactivation_hook( __FILE__, 'ecopower_tracker_deactivate' );

function ecopower_tracker_activate() {
    // Code to run on activation
    global $wpdb;
    $table_name = $wpdb->prefix . 'ecopower_projects';
    $charset_collate = $wpdb->get_charset_collate();
    $sql = "CREATE TABLE IF NOT EXISTS $table_name (
        id mediumint(9) NOT NULL AUTO_INCREMENT,
        project_company varchar(255) NOT NULL,
        project_name varchar(255) NOT NULL,
        project_location varchar(255) NOT NULL,
        type_of_plant varchar(255) NOT NULL,
        project_cuf float NOT NULL,
        generation_capacity int NOT NULL,
        date_of_activation date NOT NULL,
        PRIMARY KEY  (id)
    ) $charset_collate;";
    require_once( ABSPATH . 'wp-admin/includes/upgrade.php' );
    dbDelta( $sql );
}

function ecopower_tracker_deactivate() {
    // Code to run on deactivation
}

// Add admin menu
function ecopower_tracker_add_admin_menu() {
    add_menu_page(
        __( 'EcoPower Tracker', 'ecopower-tracker' ),
        __( 'EcoPower Tracker', 'ecopower-tracker' ),
        'manage_options',
        'ecopower-tracker',
        'ecopower_tracker_dashboard_content',
        plugin_dir_url( __FILE__ ) . 'img/EcoTracker-Wht.svg', // Menu icon
        6
    );

    // Submenu pages
    add_submenu_page(
        'ecopower-tracker',
        __( 'Dashboard', 'ecopower-tracker' ),
        __( 'Dashboard', 'ecopower-tracker' ),
        'manage_options',
        'ecopower-tracker',
        'ecopower_tracker_dashboard_content'
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
        __( 'Import/Export', 'ecopower-tracker' ),
        __( 'Import/Export', 'ecopower-tracker' ),
        'manage_options',
        'ecopower-tracker-import-export',
        'ecopower_tracker_display_import_export'
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
add_action( 'admin_menu', 'ecopower_tracker_add_admin_menu' );

// Register shortcodes
add_action( 'init', 'ecopower_tracker_register_shortcodes' );

function ecopower_tracker_register_shortcodes() {
    add_shortcode( 'ecopower_tracker_total_power', 'ecopower_tracker_total_power_shortcode' );
    add_shortcode( 'ecopower_tracker_total_co2', 'ecopower_tracker_total_co2_shortcode' );
    add_shortcode( 'ecopower_tracker_projects', 'ecopower_tracker_projects_shortcode' );
    add_shortcode( 'ecopower_tracker_total_power_number', 'ecopower_tracker_total_power_number_shortcode' );
    add_shortcode( 'ecopower_tracker_total_co2_number', 'ecopower_tracker_total_co2_number_shortcode' );
    add_shortcode( 'ecopower_tracker_project', 'ecopower_tracker_project_shortcode' );
    add_shortcode( 'ecopower_tracker_company', 'ecopower_tracker_company_shortcode' );
    add_shortcode( 'ecopower_tracker_location', 'ecopower_tracker_location_shortcode' );
    add_shortcode( 'ecopower_tracker_type', 'ecopower_tracker_type_shortcode' );
    add_shortcode( 'ecopower_tracker_total_capacity', 'ecopower_tracker_total_capacity_shortcode' );
    add_shortcode( 'ecopower_tracker_project_capacity', 'ecopower_tracker_project_capacity_shortcode' );
    add_shortcode( 'ecopower_tracker_company_capacity', 'ecopower_tracker_company_capacity_shortcode' );
    add_shortcode( 'ecopower_tracker_location_capacity', 'ecopower_tracker_location_capacity_shortcode' );
    add_shortcode( 'ecopower_tracker_type_capacity', 'ecopower_tracker_type_capacity_shortcode' );
}

?>