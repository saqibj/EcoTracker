<?php
/*
Plugin Name: EcoPower Tracker
Description: A plugin to track power generation and CO2 offset for wind and solar plants.
Version: 1.0.1
Author: Saqib Jawaid
*/

if (!defined('ABSPATH')) {
    exit;
}

define('ECOPOWER_TRACKER_DIR', plugin_dir_path(__FILE__));

// Include required files
include_once ECOPOWER_TRACKER_DIR . 'includes/calculations.php';
include_once ECOPOWER_TRACKER_DIR . 'admin/admin-page.php';

// Create database table
function ecopower_tracker_create_db_table() {
    global $wpdb;
    $table_name = $wpdb->prefix . 'ecopower_projects';
    $charset_collate = $wpdb->get_charset_collate();

    $sql = "CREATE TABLE $table_name (
        project_number mediumint(9) NOT NULL AUTO_INCREMENT,
        project_company varchar(255) NOT NULL,
        project_name varchar(255) NOT NULL,
        project_location varchar(255) NOT NULL,
        type_of_plant varchar(50) NOT NULL,
        cuf float NOT NULL,
        generation_capacity float NOT NULL,
        date_of_activation date NOT NULL,
        PRIMARY KEY (project_number)
    ) $charset_collate;";

    require_once(ABSPATH . 'wp-admin/includes/upgrade.php');
    dbDelta($sql);
}
register_activation_hook(__FILE__, 'ecopower_tracker_create_db_table');
?>