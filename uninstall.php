<?php
if (!defined('WP_UNINSTALL_PLUGIN')) {
    exit; // Exit if accessed directly
}

// Delete custom database table on plugin uninstall
function ecopower_tracker_uninstall() {
    global $wpdb;
    $table_name = $wpdb->prefix . 'ecopower_projects';
    $wpdb->query("DROP TABLE IF EXISTS $table_name");
}

ecopower_tracker_uninstall();
?>