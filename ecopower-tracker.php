<?php
/**
 * Plugin Name: EcoPower Tracker
 * Description: A plugin to track power generation and carbon offset for wind and solar plants.
 * Version: 1.0
 * Author: Saqibn
 */

// Define constants
define('ECOPOWER_TRACKER_DIR', plugin_dir_path(__FILE__));

// Include necessary files
include_once ECOPOWER_TRACKER_DIR . 'admin/admin-page.php';
include_once ECOPOWER_TRACKER_DIR . 'includes/shortcodes.php';
include_once ECOPOWER_TRACKER_DIR . 'includes/display-functions.php';
include_once ECOPOWER_TRACKER_DIR . 'includes/calculations.php';

// Enqueue scripts and styles
function ecopower_tracker_enqueue_scripts() {
    wp_enqueue_style('ecopower-tracker-styles', plugins_url('css/styles.css', __FILE__));
    wp_enqueue_script('ecopower-tracker-scripts', plugins_url('js/scripts.js', __FILE__), array('jquery'), null, true);
}
add_action('wp_enqueue_scripts', 'ecopower_tracker_enqueue_scripts');

// Activation hook
function ecopower_tracker_activate() {
    // Code to run on plugin activation
}
register_activation_hook(__FILE__, 'ecopower_tracker_activate');

// Deactivation hook
function ecopower_tracker_deactivate() {
    // Code to run on plugin deactivation
}
register_deactivation_hook(__FILE__, 'ecopower_tracker_deactivate');
?>
