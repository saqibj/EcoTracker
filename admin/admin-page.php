<?php
// Create admin menu
function ecopower_tracker_admin_menu() {
    add_menu_page('EcoPower Tracker', 'EcoPower Tracker', 'manage_options', 'ecopower-tracker', 'ecopower_tracker_dashboard', 'dashicons-chart-area');
    add_submenu_page('ecopower-tracker', 'Manage Data', 'Manage Data', 'manage_options', 'ecopower-tracker-manage', 'ecopower_tracker_manage_data');
    add_submenu_page('ecopower-tracker', 'Import Data', 'Import Data', 'manage_options', 'ecopower-tracker-import', 'ecopower_tracker_import_data');
    add_submenu_page('ecopower-tracker', 'Export Data', 'Export Data', 'manage_options', 'ecopower_tracker_export_data');
}
add_action('admin_menu', 'ecopower_tracker_admin_menu');

// Dashboard page
function ecopower_tracker_dashboard() {
    include_once ECOPOWER_TRACKER_DIR . 'templates/dashboard.php';
}

// Manage Data page
function ecopower_tracker_manage_data() {
    include_once ECOPOWER_TRACKER_DIR . 'admin/manage-data.php';
}

// Import Data page
function ecopower_tracker_import_data() {
    include_once ECOPOWER_TRACKER_DIR . 'admin/import.php';
}

// Export Data page
function ecopower_tracker_export_data() {
    include_once ECOPOWER_TRACKER_DIR . 'admin/export.php';
}
?>
