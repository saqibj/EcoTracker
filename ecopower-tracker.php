<?php
/**
 * Plugin Name: EcoPower Tracker
 * Plugin URI: https://github.com/saqibj/EcoTracker
 * Description: Track and display renewable energy project data including power generation and CO2 offset calculations.
 * Version: 2.0.2
 * Requires at least: 5.0
 * Requires PHP: 7.4
 * Author: Saqib Jawaid
 * Author URI: https://github.com/saqibj
 * License: GPL v2 or later
 * License URI: https://www.gnu.org/licenses/gpl-2.0.html
 * Text Domain: ecopower-tracker
 * Domain Path: /languages
 *
 * @package EcoPowerTracker
 */

namespace EcoPowerTracker;

if (!defined('ABSPATH')) {
    exit; // Exit if accessed directly
}

// Define plugin constants
define('ECOPOWER_TRACKER_VERSION', '2.0.2');
define('ECOPOWER_TRACKER_PATH', plugin_dir_path(__FILE__));
define('ECOPOWER_TRACKER_URL', plugin_dir_url(__FILE__));

// Start output buffering to handle any potential issues with output
ob_start();

// Setup autoloader
spl_autoload_register(function ($class) {
    // Check if the class is from our namespace
    if (strpos($class, 'EcoPowerTracker\\') !== 0) {
        return;
    }

    $class_path = str_replace('EcoPowerTracker\\', '', $class);
    $class_path = str_replace('_', '-', strtolower($class_path));
    $file = ECOPOWER_TRACKER_PATH . 'includes/class-' . $class_path . '.php';

    if (file_exists($file)) {
        require_once $file;
    }
});

// Include function files
$required_files = [
    'includes/ecopower-tracker-functions.php',
];

foreach ($required_files as $file) {
    $file_path = ECOPOWER_TRACKER_PATH . $file;
    if (!file_exists($file_path)) {
        wp_die(sprintf(__('Required file missing: %s', 'ecopower-tracker'), $file));
    }
    require_once $file_path;
}

/**
 * Main plugin class
 */
class EcoPowerTracker {
    /**
     * Constructor
     */
    public function __construct() {
        // Hook to add menu items
        add_action('admin_menu', array($this, 'add_admin_menus'));
    }

    /**
     * Add admin menus
     */
    public function add_admin_menus() {
        // Main menu
        add_menu_page(
            __('EcoPower Tracker', 'ecopower-tracker'),
            __('EcoPower Tracker', 'ecopower-tracker'),
            'manage_options',
            'ecopower-tracker',
            array($this, 'display_dashboard'),
            ECOPOWER_TRACKER_URL . 'assets/img/EcoTracker-Wht.svg',
            26
        );

        // Submenu: Dashboard
        add_submenu_page(
            'ecopower-tracker',
            __('Dashboard', 'ecopower-tracker'),
            __('Dashboard', 'ecopower-tracker'),
            'manage_options',
            'ecopower-tracker',
            array($this, 'display_dashboard')
        );

        // Submenu: About
        add_submenu_page(
            'ecopower-tracker',
            __('About EcoPower Tracker', 'ecopower-tracker'),
            __('About', 'ecopower-tracker'),
            'manage_options',
            'ecopower-tracker-about',
            array($this, 'display_about')
        );
    }

    /**
     * Display the dashboard page
     */
    public function display_dashboard() {
        if (!current_user_can('manage_options')) {
            wp_die(__('You do not have sufficient permissions to access this page.', 'ecopower-tracker'));
        }

        // Verify nonce if processing form data
        if (!empty($_POST)) {
            check_admin_referer('ecopower_tracker_dashboard_nonce');
        }

        include ECOPOWER_TRACKER_PATH . 'templates/admin/dashboard.php';
    }

    /**
     * Display the about page
     */
    public function display_about() {
        if (!current_user_can('manage_options')) {
            wp_die(__('You do not have sufficient permissions to access this page.', 'ecopower-tracker'));
        }

        include ECOPOWER_TRACKER_PATH . 'templates/admin/about.php';
    }
}

// Initialize the plugin
new EcoPowerTracker();

/**
 * Handle plugin activation
 */
function ecopower_tracker_activate() {
    // Create or update the database table structure
    ecopower_tracker_create_tables();

    // Set default options
    update_option('ecopower_tracker_version', ECOPOWER_TRACKER_VERSION);
    
    // Log activation
    error_log(sprintf('EcoPower Tracker activated (v%s)', ECOPOWER_TRACKER_VERSION));
}

/**
 * Handle plugin deactivation
 */
function ecopower_tracker_deactivate() {
    // Clean up temporary data
    delete_option('ecopower_tracker_temp_data');
    
    // Log deactivation
    error_log('EcoPower Tracker deactivated');
}

// Register activation and deactivation hooks
register_activation_hook(__FILE__, __NAMESPACE__ . '\\ecopower_tracker_activate');
register_deactivation_hook(__FILE__, __NAMESPACE__ . '\\ecopower_tracker_deactivate');

// Cleanup on uninstall
if (defined('WP_UNINSTALL_PLUGIN')) {
    require_once ECOPOWER_TRACKER_PATH . 'uninstall.php';
}

// Handle output buffer
if (ob_get_level() > 0) {
    ob_end_flush();
}