<?php
/**
 * Plugin Name: EcoPower Tracker
 * Plugin URI: https://github.com/saqibj/EcoTracker
 * Description: Track and display renewable energy project data including power generation and CO2 offset calculations.
 * Version: 2.2.0
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
define('ECOPOWER_TRACKER_VERSION', '2.2.0');
define('ECOPOWER_TRACKER_PATH', plugin_dir_path(__FILE__));
define('ECOPOWER_TRACKER_URL', plugin_dir_url(__FILE__));

// Start output buffering to handle any potential issues with output
ob_start();

class EcoPowerTrackerException extends \Exception {}

function ecopower_tracker_error_handler(
    $errno, $errstr, $errfile, $errline
) {
    error_log(sprintf(
        'EcoPower Tracker Error: %s in %s on line %d',
        $errstr,
        $errfile,
        $errline
    ));
    return true;
}

set_error_handler(__NAMESPACE__ . '\\ecopower_tracker_error_handler');

// Setup autoloader
spl_autoload_register(function ($class) {
    if (strpos($class, 'EcoPowerTracker\\') !== 0) {
        return;
    }
    $class_path = str_replace('EcoPowerTracker\\', '', $class);
    $class_path = str_replace(['_', '\\'], '-', $class_path);
    $file = ECOPOWER_TRACKER_PATH . 'includes/class-' . strtolower($class_path) . '.php';
    if (file_exists($file)) {
        require_once $file;
    } else {
        error_log(sprintf('EcoPower Tracker: Class file not found: %s', $file));
    }
});

// Include function files
$required_files = [
    'includes/ecopower-tracker-functions.php',
];

foreach ($required_files as $file) {
    $file_path = ECOPOWER_TRACKER_PATH . $file;
    if (!file_exists($file_path)) {
        /* translators: %s: File name */
        wp_die(sprintf(__('Required file missing: %s', 'ecopower-tracker'), $file));
    }
    require_once $file_path;
}

/**
 * Main plugin class
 */
class EcoPowerTracker {
    private static $instance = null;
    private $plugin_name;
    private $version;
    
    /**
     * Get singleton instance
     */
    public static function get_instance() {
        if (null === self::$instance) {
            self::$instance = new self();
        }
        return self::$instance;
    }

    /**
     * Constructor
     */
    private function __construct() {
        $this->plugin_name = 'ecopower-tracker';
        $this->version = ECOPOWER_TRACKER_VERSION;
        
        // Check WordPress version
        if (version_compare($GLOBALS['wp_version'], '5.0', '<')) {
            add_action('admin_notices', array($this, 'display_version_notice'));
            return;
        }

        // Check PHP version
        if (version_compare(PHP_VERSION, '7.4', '<')) {
            add_action('admin_notices', array($this, 'display_php_version_notice'));
            return;
        }

        $this->init_hooks();
    }

    /**
     * Initialize hooks
     */
    private function init_hooks() {
        add_action('admin_menu', array($this, 'add_admin_menus'));
        add_action('init', array($this, 'load_textdomain'));
        $this->define_public_hooks();
    }

    /**
     * Register all of the hooks related to the public-facing functionality
     * of the plugin.
     */
    private function define_public_hooks() {
        $plugin_public = new EcoPowerTracker_Public($this->get_plugin_name(), $this->get_version());

        add_action('wp_enqueue_scripts', array($plugin_public, 'enqueue_styles'));
        add_action('wp_enqueue_scripts', array($plugin_public, 'enqueue_scripts'));
        
        // Register shortcodes
        add_shortcode('ecopower_projects', array($plugin_public, 'render_projects_shortcode'));
        add_shortcode('ecopower_project', array($plugin_public, 'render_single_project_shortcode'));
        add_shortcode('ecopower_stats', array($plugin_public, 'render_stats_shortcode'));
        
        // AJAX handlers
        add_action('wp_ajax_ecopower_tracker_filter_projects', array($plugin_public, 'ajax_filter_projects'));
        add_action('wp_ajax_nopriv_ecopower_tracker_filter_projects', array($plugin_public, 'ajax_filter_projects'));
    }

    /**
     * Load plugin textdomain
     */
    public function load_textdomain() {
        load_plugin_textdomain(
            'ecopower-tracker',
            false,
            dirname(plugin_basename(__FILE__)) . '/languages'
        );
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

    /**
     * Display WordPress version notice
     */
    public function display_version_notice() {
        $message = sprintf(
            /* translators: %s: WordPress version */
            __('EcoPower Tracker requires WordPress version %s or higher.', 'ecopower-tracker'),
            '5.0'
        );
        echo '<div class="error"><p>' . esc_html($message) . '</p></div>';
    }

    /**
     * Display PHP version notice
     */
    public function display_php_version_notice() {
        $message = sprintf(
            /* translators: %s: PHP version */
            __('EcoPower Tracker requires PHP version %s or higher.', 'ecopower-tracker'),
            '7.4'
        );
        echo '<div class="error"><p>' . esc_html($message) . '</p></div>';
    }

    /**
     * The name of the plugin used to uniquely identify it within the context of
     * WordPress and to define internationalization functionality.
     */
    public function get_plugin_name() {
        return $this->plugin_name;
    }

    /**
     * Retrieve the version number of the plugin.
     */
    public function get_version() {
        return $this->version;
    }
}

// Replace "new EcoPowerTracker();" with:
function ecopower_tracker_init() {
    return EcoPowerTracker::get_instance();
}
add_action('plugins_loaded', __NAMESPACE__ . '\\ecopower_tracker_init');

/**
 * Handle plugin activation
 */
function ecopower_tracker_activate() {
    try {
        // Verify PHP version
        if (version_compare(PHP_VERSION, '7.4', '<')) {
            throw new EcoPowerTrackerException(
                sprintf('PHP version %s or higher is required', '7.4')
            );
        }

        // Verify WordPress version
        if (version_compare($GLOBALS['wp_version'], '5.0', '<')) {
            throw new EcoPowerTrackerException(
                sprintf('WordPress version %s or higher is required', '5.0')
            );
        }

        // Create or update the database table structure
        if (!function_exists('ecopower_tracker_create_tables')) {
            throw new EcoPowerTrackerException('Required function missing: ecopower_tracker_create_tables');
        }

        if (!ecopower_tracker_create_tables()) {
            throw new EcoPowerTrackerException('Failed to create database tables');
        }

        // Set default options
        $default_options = array(
            'ecopower_tracker_version' => ECOPOWER_TRACKER_VERSION,
            'ecopower_tracker_install_date' => current_time('mysql'),
            'ecopower_tracker_co2_factor' => 0.001, // Default CO2 conversion factor
        );

        foreach ($default_options as $option => $value) {
            update_option($option, $value);
        }
        
        // Log activation
        error_log(sprintf(
            'EcoPower Tracker activated (v%s) on PHP %s, WordPress %s',
            ECOPOWER_TRACKER_VERSION,
            PHP_VERSION,
            $GLOBALS['wp_version']
        ));
    } catch (EcoPowerTrackerException $e) {
        error_log('EcoPower Tracker activation failed: ' . $e->getMessage());
        throw $e; // Re-throw to prevent activation
    }
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