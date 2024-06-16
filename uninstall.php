// Path: EcoPower-Tracker/uninstall.php
// File: uninstall.php

<?php

if (!defined('WP_UNINSTALL_PLUGIN')) {
    exit; // Exit if accessed directly
}

// Function to handle the plugin's uninstallation
function ecopower_tracker_uninstall() {
    global $wpdb;

    // Delete the database table
    $table_name = $wpdb->prefix . 'ecopower_tracker_projects';
    $wpdb->query("DROP TABLE IF EXISTS $table_name");

    // Clean up options or settings
    delete_option('ecopower_tracker_version');
}

// Run the uninstall function
ecopower_tracker_uninstall();

?>