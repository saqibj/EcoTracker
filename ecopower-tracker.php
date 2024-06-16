// Path: EcoPower-Tracker/ecopower-tracker.php
// File: ecopower-tracker.php

<?php
/**
 * Plugin Name: EcoPower Tracker
 * Description: A plugin to manage and display data for renewable energy projects.
 * Version: 2.0.1
 * Author: Saqib Jawaid
 * License: GPL v3
 */

// Ensure no direct access
if (!defined('ABSPATH')) {
    exit;
}

// Start output buffering to handle any potential issues with output.
ob_start();

// Include necessary files
require_once plugin_dir_path(__FILE__) . 'includes/class-ecopower-tracker-admin.php';
require_once plugin_dir_path(__FILE__) . 'includes/class-ecopower-tracker-csv-upload.php';
require_once plugin_dir_path(__FILE__) . 'includes/class-ecopower-tracker-csv-process.php';
require_once plugin_dir_path(__FILE__) . 'includes/class-ecopower-tracker-csv-export.php';
require_once plugin_dir_path(__FILE__) . 'includes/class-ecopower-tracker-dashboard.php';
require_once plugin_dir_path(__FILE__) . 'includes/class-ecopower-tracker-shortcodes.php';
require_once plugin_dir_path(__FILE__) . 'includes/class-ecopower-tracker-utils.php';
require_once plugin_dir_path(__FILE__) . 'includes/ecopower-tracker-functions.php';

// Main plugin class
class EcoPowerTracker {

    public function __construct() {
        // Hook to add menu items
        add_action('admin_menu', array($this, 'add_admin_menus'));
    }

    // Function to add admin menus
    public function add_admin_menus() {
        // Main menu
        add_menu_page(
            'EcoPower Tracker',                // Page title
            'EcoPower Tracker',                // Menu title
            'manage_options',                  // Capability
            'ecopower-tracker',                // Menu slug
            array($this, 'display_dashboard'), // Callback function
            plugin_dir_url(__FILE__) . 'assets/img/EcoTracker-Wht.svg', // Icon URL
            26                                  // Position
        );

        // Submenu: Dashboard
        add_submenu_page(
            'ecopower-tracker',               // Parent slug
            'Dashboard',                      // Page title
            'Dashboard',                      // Menu title
            'manage_options',                 // Capability
            'ecopower-tracker',               // Menu slug
            array($this, 'display_dashboard') // Callback function
        );

        // Submenu: About
        add_submenu_page(
            'ecopower-tracker',               // Parent slug
            'About EcoPower Tracker',         // Page title
            'About',                          // Menu title
            'manage_options',                 // Capability
            'ecopower-tracker-about',         // Menu slug
            array($this, 'display_about')     // Callback function
        );
    }

    // Function to display the dashboard page
    public function display_dashboard() {
        include plugin_dir_path(__FILE__) . 'templates/admin/dashboard.php';
    }

    // Function to display the "About" page
    public function display_about() {
        include plugin_dir_path(__FILE__) . 'templates/admin/about.php';
    }
}

// Initialize the plugin
new EcoPowerTracker();

// Register activation and deactivation hooks
register_activation_hook(__FILE__, 'ecopower_tracker_activate');
register_deactivation_hook(__FILE__, 'ecopower_tracker_deactivate');

// Activation callback
function ecopower_tracker_activate() {
    // Code to execute upon activation
}

// Deactivation callback
function ecopower_tracker_deactivate() {
    // Code to execute upon deactivation
}

// Cleanup on uninstall
if (defined('WP_UNINSTALL_PLUGIN')) {
    // Code to execute upon uninstall
    // For instance, deleting options or custom tables
}

?>