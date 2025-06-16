<?php
// If uninstall not called from WordPress, exit
if (!defined('WP_UNINSTALL_PLUGIN')) {
    exit;
}

// Remove plugin options
$option_names = array(
    'ecopower_tracker_version',
    'ecopower_tracker_install_date',
    'ecopower_tracker_co2_factor',
    'ecopower_tracker_temp_data',
);
foreach ($option_names as $option) {
    delete_option($option);
}

// Drop custom database table
global $wpdb;
$table_name = $wpdb->prefix . 'ecopower_tracker_projects';
$wpdb->query("DROP TABLE IF EXISTS $table_name");

// Remove temp CSV files
define('ECOPOWER_TRACKER_PATH', plugin_dir_path(__FILE__));
$upload_dir = wp_upload_dir();
$temp_dir = $upload_dir['basedir'] . '/ecopower_tracker_csv';
if (is_dir($temp_dir)) {
    $files = glob($temp_dir . '/*');
    foreach ($files as $file) {
        if (is_file($file)) {
            @unlink($file);
        }
    }
    @rmdir($temp_dir);
} 