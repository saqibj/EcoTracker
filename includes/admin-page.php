<?php
if ( ! defined( 'ABSPATH' ) ) {
    exit; // Exit if accessed directly.
}

function ecopower_tracker_dashboard_content() {
    include plugin_dir_path( __FILE__ ) . 'dashboard.php';
}

function ecopower_tracker_all_projects_page() {
    include plugin_dir_path( __FILE__ ) . 'all-projects.php';
}

function ecopower_tracker_add_new_project_page() {
    include plugin_dir_path( __FILE__ ) . 'add-new-project.php';
}

function ecopower_tracker_display_import_export() {
    include plugin_dir_path( __FILE__ ) . 'import-export.php';
}

function ecopower_tracker_settings_page() {
    include plugin_dir_path( __FILE__ ) . 'settings.php';
}

function ecopower_tracker_reporting_intervals_page() {
    include plugin_dir_path( __FILE__ ) . 'reporting-intervals.php';
}

function ecopower_tracker_about_page() {
    include plugin_dir_path( __FILE__ ) . 'about.php';
}
?>