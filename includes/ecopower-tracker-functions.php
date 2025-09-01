// Path: EcoPower-Tracker/includes/ecopower-tracker-functions.php
// File: ecopower-tracker-functions.php

<?php
/**
 * Core functions for EcoPower Tracker
 *
 * @package EcoPowerTracker
 * @since 2.0.1
 */

namespace EcoPowerTracker;

if (!defined('ABSPATH')) {
    exit;
}

/**
 * Create plugin database tables
 *
 * @return bool True on success, false on failure
 */
function ecopower_tracker_create_tables() {
    global $wpdb;
    $charset_collate = $wpdb->get_charset_collate();

    $table_name = $wpdb->prefix . 'ecopower_tracker_projects';
    
    $sql = "CREATE TABLE IF NOT EXISTS $table_name (
        id bigint(20) unsigned NOT NULL AUTO_INCREMENT,
        project_number varchar(50) NOT NULL,
        project_company varchar(255) NOT NULL,
        project_name varchar(255) NOT NULL,
        project_location varchar(255) NOT NULL,
        type_of_plant varchar(50) NOT NULL,
        project_cuf decimal(5,2) NOT NULL DEFAULT '0.00',
        generation_capacity decimal(10,2) NOT NULL DEFAULT '0.00',
        date_of_activation date NOT NULL,
        created_at datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
        updated_at datetime NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
        PRIMARY KEY  (id),
        UNIQUE KEY project_number (project_number),
        KEY type_of_plant (type_of_plant),
        KEY project_company (project_company),
        KEY project_location (project_location),
        KEY project_company_location (project_company, project_location),
        KEY type_activation (type_of_plant, date_of_activation)
    ) $charset_collate;";

    require_once(ABSPATH . 'wp-admin/includes/upgrade.php');
    $result = dbDelta($sql);
    
    // Log table creation result
    if (empty($result)) {
        error_log('EcoPower Tracker: Database table creation may have failed');
        return false;
    }
    
    return true;
}

/**
 * Enqueue frontend assets with fallback support
 *
 * @return void
 */
function ecopower_tracker_enqueue_frontend_assets() {
    // Only load assets when needed
    if (!is_ecopower_tracker_content()) {
        return;
    }

    $suffix = ecopower_tracker_get_asset_suffix();

    // Enqueue CSS with fallback
    ecopower_tracker_enqueue_style_with_fallback(
        'ecopower-tracker-frontend',
        "assets/css/ecopower-tracker-frontend{$suffix}.css",
        'assets/css/ecopower-tracker-frontend.css'
    );

    // Enqueue JS with fallback
    ecopower_tracker_enqueue_script_with_fallback(
        'ecopower-tracker-frontend',
        "assets/js/ecopower-tracker-frontend{$suffix}.js",
        'assets/js/ecopower-tracker-frontend.js',
        array('jquery')
    );

    wp_localize_script(
        'ecopower-tracker-frontend',
        'ecoPowerTrackerData',
        array(
            'ajaxUrl' => admin_url('admin-ajax.php'),
            'nonce' => wp_create_nonce('ecopower_tracker_frontend'),
            'locale' => get_locale(),
            'autoRefresh' => apply_filters('ecopower_tracker_auto_refresh', true),
            'refreshInterval' => apply_filters('ecopower_tracker_refresh_interval', 300000), // 5 minutes
            'i18n' => array(
                'error' => __('Error loading data', 'ecopower-tracker'),
                'loading' => __('Loading...', 'ecopower-tracker')
            )
        )
    );
}

/**
 * Get asset suffix for minified files
 *
 * @return string
 */
function ecopower_tracker_get_asset_suffix() {
    return (defined('SCRIPT_DEBUG') && SCRIPT_DEBUG) ? '' : '.min';
}

/**
 * Enqueue stylesheet with fallback to non-minified version
 *
 * @param string $handle
 * @param string $src_minified
 * @param string $src_fallback
 * @param array $deps
 * @param string $media
 */
function ecopower_tracker_enqueue_style_with_fallback($handle, $src_minified, $src_fallback, $deps = array(), $media = 'all') {
    $minified_path = ECOPOWER_TRACKER_PATH . $src_minified;
    $src = file_exists($minified_path) ? $src_minified : $src_fallback;
    
    wp_enqueue_style(
        $handle,
        ECOPOWER_TRACKER_URL . $src,
        $deps,
        ECOPOWER_TRACKER_VERSION,
        $media
    );
}

/**
 * Enqueue script with fallback to non-minified version
 *
 * @param string $handle
 * @param string $src_minified
 * @param string $src_fallback
 * @param array $deps
 * @param bool $in_footer
 */
function ecopower_tracker_enqueue_script_with_fallback($handle, $src_minified, $src_fallback, $deps = array(), $in_footer = true) {
    $minified_path = ECOPOWER_TRACKER_PATH . $src_minified;
    $src = file_exists($minified_path) ? $src_minified : $src_fallback;
    
    wp_enqueue_script(
        $handle,
        ECOPOWER_TRACKER_URL . $src,
        $deps,
        ECOPOWER_TRACKER_VERSION,
        $in_footer
    );
}

/**
 * Check if current content contains plugin shortcodes or blocks
 *
 * @return boolean
 */
function is_ecopower_tracker_content() {
    global $post;

    if (!is_singular() || empty($post)) {
        return false;
    }

    // List of shortcodes to check for
    $shortcodes = array(
        'ecopower_tracker_total_power',
        'ecopower_tracker_total_co2',
        'ecopower_tracker_project_power',
        // ... add other shortcodes
    );

    // Check for shortcodes
    foreach ($shortcodes as $shortcode) {
        if (has_shortcode($post->post_content, $shortcode)) {
            return true;
        }
    }

    // Check for Gutenberg blocks
    if (has_blocks($post->post_content)) {
        foreach (parse_blocks($post->post_content) as $block) {
            if (strpos($block['blockName'], 'ecopower-tracker/') === 0) {
                return true;
            }
        }
    }

    return false;
}

/**
 * Display admin notices
 *
 * @return void
 */
function ecopower_tracker_admin_notices() {
    if (!current_user_can('manage_options')) {
        return;
    }

    // Check PHP version
    if (version_compare(PHP_VERSION, '7.4', '<')) {
        $message = sprintf(
            /* translators: %s: PHP version */
            __('EcoPower Tracker requires PHP version 7.4 or higher. You are running version %s.', 'ecopower-tracker'),
            PHP_VERSION
        );
        echo '<div class="notice notice-error"><p>' . esc_html($message) . '</p></div>';
    }

    // Check WordPress version
    if (version_compare($GLOBALS['wp_version'], '5.0', '<')) {
        $message = sprintf(
            /* translators: %s: WordPress version */
            __('EcoPower Tracker requires WordPress version 5.0 or higher. You are running version %s.', 'ecopower-tracker'),
            $GLOBALS['wp_version']
        );
        echo '<div class="notice notice-error"><p>' . esc_html($message) . '</p></div>';
    }

    // Display update notices
    if (get_transient('ecopower_tracker_updated')) {
        echo '<div class="notice notice-success is-dismissible"><p>' . 
             esc_html__('EcoPower Tracker has been updated successfully.', 'ecopower-tracker') . 
             '</p></div>';
        delete_transient('ecopower_tracker_updated');
    }
}

/**
 * Clean up old temporary files
 *
 * @return void
 */
function ecopower_tracker_cleanup_temp_files() {
    $upload_dir = wp_upload_dir();
    $temp_dir = $upload_dir['basedir'] . '/ecopower_tracker_csv';

    if (!is_dir($temp_dir)) {
        return;
    }

    // Delete files older than 24 hours
    $files = glob($temp_dir . '/*');
    $now = time();

    foreach ($files as $file) {
        if (is_file($file)) {
            if ($now - filemtime($file) >= 24 * 3600) {
                @unlink($file);
            }
        }
    }
}

// Register hooks
add_action('wp_enqueue_scripts', 'EcoPowerTracker\\ecopower_tracker_enqueue_frontend_assets');
add_action('admin_notices', 'EcoPowerTracker\\ecopower_tracker_admin_notices');
add_action('ecopower_tracker_daily_cleanup', 'EcoPowerTracker\\ecopower_tracker_cleanup_temp_files');

// Schedule cleanup task
if (!wp_next_scheduled('ecopower_tracker_daily_cleanup')) {
    wp_schedule_event(time(), 'daily', 'ecopower_tracker_daily_cleanup');
}

// Schedule cache warming (every 6 hours)
if (!wp_next_scheduled('ecopower_tracker_cache_warm')) {
    wp_schedule_event(time(), 'sixhourly', 'ecopower_tracker_cache_warm');
}

/**
 * Warm up shortcode cache
 *
 * @return void
 */
function ecopower_tracker_warm_cache() {
    global $ecopower_tracker_shortcodes;
    
    if ($ecopower_tracker_shortcodes && method_exists($ecopower_tracker_shortcodes, 'warm_cache')) {
        $ecopower_tracker_shortcodes->warm_cache();
    }
}

// Hook cache warming to scheduled event
add_action('ecopower_tracker_cache_warm', 'EcoPowerTracker\\ecopower_tracker_warm_cache');

// Add custom cron interval for cache warming
add_filter('cron_schedules', function($schedules) {
    if (!isset($schedules['sixhourly'])) {
        $schedules['sixhourly'] = array(
            'interval' => 6 * HOUR_IN_SECONDS,
            'display' => __('Every 6 Hours', 'ecopower-tracker')
        );
    }
    return $schedules;
});