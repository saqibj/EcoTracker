// Path: EcoPower-Tracker/includes/ecopower-tracker-functions.php
// File: ecopower-tracker-functions.php

<?php

if (!defined('ABSPATH')) {
    exit; // Exit if accessed directly
}

// General functions for EcoPower Tracker

/**
 * Function to create the plugin's database table for storing project data.
 */
function ecopower_tracker_create_tables() {
    global $wpdb;

    $table_name = $wpdb->prefix . 'ecopower_tracker_projects';
    $charset_collate = $wpdb->get_charset_collate();

    $sql = "CREATE TABLE $table_name (
        id mediumint(9) NOT NULL AUTO_INCREMENT,
        project_number varchar(50) NOT NULL,
        project_company varchar(255) NOT NULL,
        project_name varchar(255) NOT NULL,
        project_location varchar(255) NOT NULL,
        type_of_plant varchar(50) NOT NULL,
        project_cuf float NOT NULL,
        generation_capacity float NOT NULL,
        date_of_activation date NOT NULL,
        PRIMARY KEY (id),
        UNIQUE KEY project_number (project_number)
    ) $charset_collate;";

    require_once(ABSPATH . 'wp-admin/includes/upgrade.php');
    dbDelta($sql);
}

/**
 * Function to add custom admin notices.
 */
function ecopower_tracker_admin_notices() {
    if (isset($_GET['csv_uploaded']) && $_GET['csv_uploaded']) {
        echo '<div class="notice notice-success is-dismissible">';
        echo '<p>' . __('CSV file uploaded successfully. Proceed to process the file.', 'ecopower-tracker') . '</p>';
        echo '</div>';
    }

    if (isset($_GET['csv_processed']) && $_GET['csv_processed'] === 'success') {
        echo '<div class="notice notice-success is-dismissible">';
        echo '<p>' . __('CSV file processed and data imported successfully.', 'ecopower-tracker') . '</p>';
        echo '</div>';
    }
}

/**
 * Function to enqueue plugin-specific frontend styles and scripts.
 */
function ecopower_tracker_enqueue_frontend_assets() {
    wp_enqueue_style(
        'ecopower-tracker-frontend-css',
        plugin_dir_url(__FILE__) . '../assets/css/ecopower-tracker-frontend.css',
        array(),
        '1.0.0',
        'all'
    );

    wp_enqueue_script(
        'ecopower-tracker-frontend-js',
        plugin_dir_url(__FILE__) . '../assets/js/ecopower-tracker-frontend.js',
        array('jquery'),
        '1.0.0',
        true
    );
}

// Hook to add admin notices
add_action('admin_notices', 'ecopower_tracker_admin_notices');

// Hook to enqueue frontend assets
add_action('wp_enqueue_scripts', 'ecopower_tracker_enqueue_frontend_assets');

?>