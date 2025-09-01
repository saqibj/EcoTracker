<?php
/**
 * Shortcode functionality
 *
 * Path: EcoPower-Tracker/includes/class-ecopower-tracker-shortcodes.php
 * File: class-ecopower-tracker-shortcodes.php
 *
 * @package EcoPowerTracker
 * @since 2.0.1
 */

namespace EcoPowerTracker;

use function add_shortcode;
use function wp_cache_get;
use function wp_cache_set;
use function __;
use function shortcode_atts;
use function absint;

if (!defined('ABSPATH')) {
    exit;
}

/**
 * Class EcoPower_Tracker_Shortcodes
 */
class EcoPower_Tracker_Shortcodes {

    /**
     * Constructor
     */
    public function __construct() {
        $this->register_shortcodes();
    }

    /**
     * Register all shortcodes
     *
     * @return void
     */
    public function register_shortcodes() {
        $shortcodes = array(
            'ecopower_tracker_total_power' => 'display_total_power',
            'ecopower_tracker_total_co2' => 'display_total_co2',
            'ecopower_tracker_project_power' => 'display_project_power',
            'ecopower_tracker_project_co2' => 'display_project_co2',
            'ecopower_tracker_project_capacity' => 'display_project_capacity',
            'ecopower_tracker_company_power' => 'display_company_power',
            'ecopower_tracker_company_co2' => 'display_company_co2',
            'ecopower_tracker_company_capacity' => 'display_company_capacity',
            'ecopower_tracker_location_power' => 'display_location_power',
            'ecopower_tracker_location_co2' => 'display_location_co2',
            'ecopower_tracker_location_capacity' => 'display_location_capacity',
            'ecopower_tracker_type_power' => 'display_type_power',
            'ecopower_tracker_type_co2' => 'display_type_co2',
            'ecopower_tracker_type_capacity' => 'display_type_capacity'
        );

        foreach ($shortcodes as $tag => $function) {
            add_shortcode($tag, array($this, $function));
        }
    }

    /**
     * Display total power generation
     *
     * @param array $atts Shortcode attributes
     * @return string
     */
    public function display_total_power($atts) {
        $cache_key = $this->get_versioned_cache_key('ecopower_tracker_total_power');
        $power = wp_cache_get($cache_key);
        
        if (false === $power) {
            global $wpdb;
            $table_name = $wpdb->prefix . 'ecopower_tracker_projects';
            
            $power = $wpdb->get_var("
                SELECT SUM(generation_capacity * project_cuf / 100)
                FROM $table_name
            ");
            
            // Cache for 1 hour, with fallback to 0 if null
            $power = $power !== null ? floatval($power) : 0;
            wp_cache_set($cache_key, $power, '', 3600);
        }
        
        return $this->format_output(
            'total-power',
            __('Total Power Generation', 'ecopower-tracker'),
            $power,
            'KWh'
        );
    }

    /**
     * Display total CO2 offset
     *
     * @param array $atts Shortcode attributes
     * @return string
     */
    public function display_total_co2($atts) {
        $cache_key = $this->get_versioned_cache_key('ecopower_tracker_total_co2');
        $co2 = wp_cache_get($cache_key);
        
        if (false === $co2) {
            global $wpdb;
            $table_name = $wpdb->prefix . 'ecopower_tracker_projects';
            
            $power = $wpdb->get_var("
                SELECT SUM(generation_capacity * project_cuf / 100)
                FROM $table_name
            ");
            
            $power = $power !== null ? floatval($power) : 0;
            $co2 = $this->calculate_co2_offset($power);
            wp_cache_set($cache_key, $co2, '', 3600); // Cache for 1 hour
        }
        
        return $this->format_output(
            'total-co2',
            __('Total CO2 Offset', 'ecopower-tracker'),
            $co2,
            'tons'
        );
    }

    /**
     * Display project power generation
     *
     * @param array $atts Shortcode attributes
     * @return string
     */
    public function display_project_power($atts) {
        $atts = shortcode_atts(array(
            'project_id' => 0
        ), $atts, 'ecopower_tracker_project_power');

        $project_id = absint($atts['project_id']);
        if (!$project_id) {
            return $this->error_message(__('Invalid project ID', 'ecopower-tracker'));
        }

        $cache_key = $this->get_versioned_cache_key('ecopower_tracker_project_power_' . $project_id);
        $power = wp_cache_get($cache_key);
        
        if (false === $power) {
            global $wpdb;
            $table_name = $wpdb->prefix . 'ecopower_tracker_projects';

            $power = $wpdb->get_var($wpdb->prepare("
                SELECT (generation_capacity * project_cuf / 100)
                FROM $table_name
                WHERE id = %d
            ", $project_id));

            if (null === $power) {
                // Cache the null result to avoid repeated DB queries
                wp_cache_set($cache_key, 'NOT_FOUND', '', 1800); // Cache for 30 minutes
                return $this->error_message(__('Project not found', 'ecopower-tracker'));
            }
            
            $power = floatval($power);
            wp_cache_set($cache_key, $power, '', 3600); // Cache for 1 hour
        } elseif ('NOT_FOUND' === $power) {
            // Handle cached not found result
            return $this->error_message(__('Project not found', 'ecopower-tracker'));
        }

        return $this->format_output(
            'project-power',
            /* translators: %d: Project ID */
            sprintf(__('Project Power Generation (#%d)', 'ecopower-tracker'), $project_id),
            $power,
            'KWh'
        );
    }

    /**
     * Display project CO2 offset
     *
     * @param array $atts Shortcode attributes
     * @return string
     */
    public function display_project_co2($atts) {
        $atts = shortcode_atts(array(
            'project_id' => 0
        ), $atts, 'ecopower_tracker_project_co2');

        $project_id = absint($atts['project_id']);
        if (!$project_id) {
            return $this->error_message(__('Invalid project ID', 'ecopower-tracker'));
        }

        $cache_key = $this->get_versioned_cache_key('ecopower_tracker_project_co2_' . $project_id);
        $co2 = wp_cache_get($cache_key);
        
        if (false === $co2) {
            global $wpdb;
            $table_name = $wpdb->prefix . 'ecopower_tracker_projects';

            $power = $wpdb->get_var($wpdb->prepare("
                SELECT (generation_capacity * project_cuf / 100)
                FROM $table_name
                WHERE id = %d
            ", $project_id));

            if (null === $power) {
                // Cache the null result to avoid repeated DB queries
                wp_cache_set($cache_key, 'NOT_FOUND', '', 1800); // Cache for 30 minutes
                return $this->error_message(__('Project not found', 'ecopower-tracker'));
            }

            $power = floatval($power);
            $co2 = $this->calculate_co2_offset($power);
            wp_cache_set($cache_key, $co2, '', 3600); // Cache for 1 hour
        } elseif ('NOT_FOUND' === $co2) {
            // Handle cached not found result
            return $this->error_message(__('Project not found', 'ecopower-tracker'));
        }

        return $this->format_output(
            'project-co2',
            /* translators: %d: Project ID */
            sprintf(__('Project CO2 Offset (#%d)', 'ecopower-tracker'), $project_id),
            $co2,
            'tons'
        );
    }

    /**
     * Display project capacity
     *
     * @param array $atts Shortcode attributes
     * @return string
     */
    public function display_project_capacity($atts) {
        $atts = shortcode_atts(array(
            'project_id' => 0
        ), $atts, 'ecopower_tracker_project_capacity');

        $project_id = absint($atts['project_id']);
        if (!$project_id) {
            return $this->error_message(__('Invalid project ID', 'ecopower-tracker'));
        }

        $cache_key = $this->get_versioned_cache_key('ecopower_tracker_project_capacity_' . $project_id);
        $capacity = wp_cache_get($cache_key);
        
        if (false === $capacity) {
            global $wpdb;
            $table_name = $wpdb->prefix . 'ecopower_tracker_projects';

            $capacity = $wpdb->get_var($wpdb->prepare("
                SELECT generation_capacity
                FROM $table_name
                WHERE id = %d
            ", $project_id));

            if (null === $capacity) {
                // Cache the null result to avoid repeated DB queries
                wp_cache_set($cache_key, 'NOT_FOUND', '', 1800); // Cache for 30 minutes
                return $this->error_message(__('Project not found', 'ecopower-tracker'));
            }
            
            $capacity = floatval($capacity);
            wp_cache_set($cache_key, $capacity, '', 3600); // Cache for 1 hour
        } elseif ('NOT_FOUND' === $capacity) {
            // Handle cached not found result
            return $this->error_message(__('Project not found', 'ecopower-tracker'));
        }

        return $this->format_output(
            'project-capacity',
            /* translators: %d: Project ID */
            sprintf(__('Project Capacity (#%d)', 'ecopower-tracker'), $project_id),
            $capacity,
            'KWs'
        );
    }

    /**
     * Display company power generation
     *
     * @param array $atts Shortcode attributes
     * @return string
     */
    public function display_company_power($atts) {
        $atts = shortcode_atts(array(
            'company' => ''
        ), $atts, 'ecopower_tracker_company_power');

        $company = sanitize_text_field($atts['company']);
        if (empty($company)) {
            return $this->error_message(__('Company name is required', 'ecopower-tracker'));
        }

        $cache_key = $this->get_versioned_cache_key('ecopower_tracker_company_power_' . md5($company));
        $power = wp_cache_get($cache_key);
        
        if (false === $power) {
            global $wpdb;
            $table_name = $wpdb->prefix . 'ecopower_tracker_projects';
            
            $power = $wpdb->get_var($wpdb->prepare("
                SELECT SUM(generation_capacity * project_cuf / 100)
                FROM $table_name
                WHERE project_company = %s
            ", $company));
            
            wp_cache_set($cache_key, $power, '', 3600);
        }

        return $this->format_output(
            'company-power',
            /* translators: %s: Company name */
            sprintf(__('Power Generation - Company: %s', 'ecopower-tracker'), $company),
            $power,
            'KWh'
        );
    }

    /**
     * Display company CO2 offset
     *
     * @param array $atts Shortcode attributes
     * @return string
     */
    public function display_company_co2($atts) {
        $atts = shortcode_atts(array(
            'company' => ''
        ), $atts, 'ecopower_tracker_company_co2');

        $company = sanitize_text_field($atts['company']);
        if (empty($company)) {
            return $this->error_message(__('Company name is required', 'ecopower-tracker'));
        }

        $cache_key = $this->get_versioned_cache_key('ecopower_tracker_company_co2_' . md5($company));
        $co2 = wp_cache_get($cache_key);
        
        if (false === $co2) {
            global $wpdb;
            $table_name = $wpdb->prefix . 'ecopower_tracker_projects';
            
            $power = $wpdb->get_var($wpdb->prepare("
                SELECT SUM(generation_capacity * project_cuf / 100)
                FROM $table_name
                WHERE project_company = %s
            ", $company));
            
            $co2 = $this->calculate_co2_offset($power);
            wp_cache_set($cache_key, $co2, '', 3600);
        }

        return $this->format_output(
            'company-co2',
            /* translators: %s: Company name */
            sprintf(__('CO2 Offset - Company: %s', 'ecopower-tracker'), $company),
            $co2,
            'tons'
        );
    }

    /**
     * Display company capacity
     *
     * @param array $atts Shortcode attributes
     * @return string
     */
    public function display_company_capacity($atts) {
        $atts = shortcode_atts(array(
            'company' => ''
        ), $atts, 'ecopower_tracker_company_capacity');

        $company = sanitize_text_field($atts['company']);
        if (empty($company)) {
            return $this->error_message(__('Company name is required', 'ecopower-tracker'));
        }

        $cache_key = $this->get_versioned_cache_key('ecopower_tracker_company_capacity_' . md5($company));
        $capacity = wp_cache_get($cache_key);
        
        if (false === $capacity) {
            global $wpdb;
            $table_name = $wpdb->prefix . 'ecopower_tracker_projects';
            
            $capacity = $wpdb->get_var($wpdb->prepare("
                SELECT SUM(generation_capacity)
                FROM $table_name
                WHERE project_company = %s
            ", $company));
            
            wp_cache_set($cache_key, $capacity, '', 3600);
        }

        return $this->format_output(
            'company-capacity',
            /* translators: %s: Company name */
            sprintf(__('Total Capacity - Company: %s', 'ecopower-tracker'), $company),
            $capacity,
            'KWs'
        );
    }

    /**
     * Display location power generation
     *
     * @param array $atts Shortcode attributes
     * @return string
     */
    public function display_location_power($atts) {
        $atts = shortcode_atts(array(
            'location' => ''
        ), $atts, 'ecopower_tracker_location_power');

        $location = sanitize_text_field($atts['location']);
        if (empty($location)) {
            return $this->error_message(__('Location is required', 'ecopower-tracker'));
        }

        $cache_key = $this->get_versioned_cache_key('ecopower_tracker_location_power_' . md5($location));
        $power = wp_cache_get($cache_key);
        
        if (false === $power) {
            global $wpdb;
            $table_name = $wpdb->prefix . 'ecopower_tracker_projects';
            
            $power = $wpdb->get_var($wpdb->prepare("
                SELECT SUM(generation_capacity * project_cuf / 100)
                FROM $table_name
                WHERE project_location = %s
            ", $location));
            
            wp_cache_set($cache_key, $power, '', 3600);
        }

        return $this->format_output(
            'location-power',
            /* translators: %s: Location name */
            sprintf(__('Power Generation - Location: %s', 'ecopower-tracker'), $location),
            $power,
            'KWh'
        );
    }

    /**
     * Display location CO2 offset
     *
     * @param array $atts Shortcode attributes
     * @return string
     */
    public function display_location_co2($atts) {
        $atts = shortcode_atts(array(
            'location' => ''
        ), $atts, 'ecopower_tracker_location_co2');

        $location = sanitize_text_field($atts['location']);
        if (empty($location)) {
            return $this->error_message(__('Location is required', 'ecopower-tracker'));
        }

        $cache_key = $this->get_versioned_cache_key('ecopower_tracker_location_co2_' . md5($location));
        $co2 = wp_cache_get($cache_key);
        
        if (false === $co2) {
            global $wpdb;
            $table_name = $wpdb->prefix . 'ecopower_tracker_projects';
            
            $power = $wpdb->get_var($wpdb->prepare("
                SELECT SUM(generation_capacity * project_cuf / 100)
                FROM $table_name
                WHERE project_location = %s
            ", $location));
            
            $co2 = $this->calculate_co2_offset($power);
            wp_cache_set($cache_key, $co2, '', 3600);
        }

        return $this->format_output(
            'location-co2',
            /* translators: %s: Location name */
            sprintf(__('CO2 Offset - Location: %s', 'ecopower-tracker'), $location),
            $co2,
            'tons'
        );
    }

    /**
     * Display location capacity
     *
     * @param array $atts Shortcode attributes
     * @return string
     */
    public function display_location_capacity($atts) {
        $atts = shortcode_atts(array(
            'location' => ''
        ), $atts, 'ecopower_tracker_location_capacity');

        $location = sanitize_text_field($atts['location']);
        if (empty($location)) {
            return $this->error_message(__('Location is required', 'ecopower-tracker'));
        }

        $cache_key = $this->get_versioned_cache_key('ecopower_tracker_location_capacity_' . md5($location));
        $capacity = wp_cache_get($cache_key);
        
        if (false === $capacity) {
            global $wpdb;
            $table_name = $wpdb->prefix . 'ecopower_tracker_projects';
            
            $capacity = $wpdb->get_var($wpdb->prepare("
                SELECT SUM(generation_capacity)
                FROM $table_name
                WHERE project_location = %s
            ", $location));
            
            wp_cache_set($cache_key, $capacity, '', 3600);
        }

        return $this->format_output(
            'location-capacity',
            /* translators: %s: Location name */
            sprintf(__('Total Capacity - Location: %s', 'ecopower-tracker'), $location),
            $capacity,
            'KWs'
        );
    }

    /**
     * Display type power generation
     *
     * @param array $atts Shortcode attributes
     * @return string
     */
    public function display_type_power($atts) {
        $atts = shortcode_atts(array(
            'type' => ''
        ), $atts, 'ecopower_tracker_type_power');

        $type = sanitize_text_field($atts['type']);
        if (empty($type)) {
            return $this->error_message(__('Plant type is required', 'ecopower-tracker'));
        }

        $cache_key = $this->get_versioned_cache_key('ecopower_tracker_type_power_' . md5($type));
        $power = wp_cache_get($cache_key);
        
        if (false === $power) {
            global $wpdb;
            $table_name = $wpdb->prefix . 'ecopower_tracker_projects';
            
            $power = $wpdb->get_var($wpdb->prepare("
                SELECT SUM(generation_capacity * project_cuf / 100)
                FROM $table_name
                WHERE type_of_plant = %s
            ", $type));
            
            wp_cache_set($cache_key, $power, '', 3600);
        }

        return $this->format_output(
            'type-power',
            /* translators: %s: Plant type */
            sprintf(__('Power Generation - Type: %s', 'ecopower-tracker'), $type),
            $power,
            'KWh'
        );
    }

    /**
     * Display type CO2 offset
     *
     * @param array $atts Shortcode attributes
     * @return string
     */
    public function display_type_co2($atts) {
        $atts = shortcode_atts(array(
            'type' => ''
        ), $atts, 'ecopower_tracker_type_co2');

        $type = sanitize_text_field($atts['type']);
        if (empty($type)) {
            return $this->error_message(__('Plant type is required', 'ecopower-tracker'));
        }

        $cache_key = $this->get_versioned_cache_key('ecopower_tracker_type_co2_' . md5($type));
        $co2 = wp_cache_get($cache_key);
        
        if (false === $co2) {
            global $wpdb;
            $table_name = $wpdb->prefix . 'ecopower_tracker_projects';
            
            $power = $wpdb->get_var($wpdb->prepare("
                SELECT SUM(generation_capacity * project_cuf / 100)
                FROM $table_name
                WHERE type_of_plant = %s
            ", $type));
            
            $co2 = $this->calculate_co2_offset($power);
            wp_cache_set($cache_key, $co2, '', 3600);
        }

        return $this->format_output(
            'type-co2',
            /* translators: %s: Plant type */
            sprintf(__('CO2 Offset - Type: %s', 'ecopower-tracker'), $type),
            $co2,
            'tons'
        );
    }

    /**
     * Display type capacity
     *
     * @param array $atts Shortcode attributes
     * @return string
     */
    public function display_type_capacity($atts) {
        $atts = shortcode_atts(array(
            'type' => ''
        ), $atts, 'ecopower_tracker_type_capacity');

        $type = sanitize_text_field($atts['type']);
        if (empty($type)) {
            return $this->error_message(__('Plant type is required', 'ecopower-tracker'));
        }

        $cache_key = $this->get_versioned_cache_key('ecopower_tracker_type_capacity_' . md5($type));
        $capacity = wp_cache_get($cache_key);
        
        if (false === $capacity) {
            global $wpdb;
            $table_name = $wpdb->prefix . 'ecopower_tracker_projects';
            
            $capacity = $wpdb->get_var($wpdb->prepare("
                SELECT SUM(generation_capacity)
                FROM $table_name
                WHERE type_of_plant = %s
            ", $type));
            
            wp_cache_set($cache_key, $capacity, '', 3600);
        }

        return $this->format_output(
            'type-capacity',
            /* translators: %s: Plant type */
            sprintf(__('Total Capacity - Type: %s', 'ecopower-tracker'), $type),
            $capacity,
            'KWs'
        );
    }

    /**
     * Calculate CO2 offset from power generated
     *
     * @param float $power Power generated in KWh
     * @return float CO2 offset in tons
     */
    private function calculate_co2_offset($power) {
        // Example conversion factor: 1 KWh = 0.001 tons of CO2 offset
        $conversion_factor = apply_filters('ecopower_tracker_co2_conversion_factor', 0.001);
        return floatval($power) * $conversion_factor;
    }

    /**
     * Format output HTML
     *
     * @param string $class CSS class suffix
     * @param string $label Label text
     * @param mixed  $value Value to display
     * @param string $unit Unit of measurement
     * @return string Formatted HTML
     */
    private function format_output($class, $label, $value, $unit) {
        $value = is_numeric($value) ? number_format($value, 2) : 0;
        
        return sprintf(
            '<div class="ecopower-tracker-%s" role="region" aria-labelledby="ecopower-label-%s">
                <span class="label" id="ecopower-label-%s">%s:</span>
                <span class="value" aria-describedby="ecopower-label-%s">%s</span>
                <span class="unit" aria-label="%s">%s</span>
            </div>',
            esc_attr($class),
            esc_attr($class),
            esc_attr($class),
            esc_html($label),
            esc_attr($class),
            esc_html($value),
            esc_attr(sprintf(__('Unit: %s', 'ecopower-tracker'), $unit)),
            esc_html($unit)
        );
    }

    /**
     * Format error message
     *
     * @param string $message Error message
     * @return string Formatted error HTML
     */
    private function error_message($message) {
        return sprintf(
            '<div class="ecopower-tracker-error" role="alert" aria-live="polite">%s</div>',
            esc_html($message)
        );
    }

    /**
     * Clear all cached shortcode data
     * Should be called when projects are added, updated, or deleted
     *
     * @param int $project_id Optional project ID to clear specific project cache
     * @return void
     */
    public function clear_cache($project_id = null) {
        // Get current cache version for versioned keys
        $version = get_option('ecopower_tracker_cache_version', 1);
        
        // Clear total caches (versioned)
        wp_cache_delete('ecopower_tracker_total_power_v' . $version);
        wp_cache_delete('ecopower_tracker_total_co2_v' . $version);
        
        if ($project_id) {
            // Clear specific project caches (versioned)
            wp_cache_delete('ecopower_tracker_project_power_' . $project_id . '_v' . $version);
            wp_cache_delete('ecopower_tracker_project_co2_' . $project_id . '_v' . $version);
            wp_cache_delete('ecopower_tracker_project_capacity_' . $project_id . '_v' . $version);
        }
        
        // Increment cache version to invalidate all versioned caches
        // This effectively clears all company, location, and type caches
        update_option('ecopower_tracker_cache_version', $version + 1);
        
        // Clear any WordPress transients we might be using
        delete_transient('ecopower_tracker_stats_summary');
        
        // Log cache clearing for debugging
        if (defined('WP_DEBUG') && WP_DEBUG) {
            error_log(sprintf(
                'EcoPower Tracker: Cache cleared. Project ID: %s, New version: %d',
                $project_id ? $project_id : 'all',
                $version + 1
            ));
        }
    }
    
    /**
     * Get cache key with version for cache invalidation
     *
     * @param string $key Base cache key
     * @return string Versioned cache key
     */
    private function get_versioned_cache_key($key) {
        $version = get_option('ecopower_tracker_cache_version', 1);
        return $key . '_v' . $version;
    }
    
    /**
     * Warm up frequently accessed cache entries
     * Should be called after cache clearing or on a scheduled basis
     *
     * @return void
     */
    public function warm_cache() {
        // Warm up total statistics (most frequently accessed)
        $this->display_total_power(array());
        $this->display_total_co2(array());
        
        // Get a few recent projects to warm their cache
        global $wpdb;
        $table_name = $wpdb->prefix . 'ecopower_tracker_projects';
        
        $recent_projects = $wpdb->get_results(
            "SELECT id FROM {$table_name} ORDER BY created_at DESC LIMIT 5",
            ARRAY_A
        );
        
        foreach ($recent_projects as $project) {
            $project_id = $project['id'];
            
            // Warm up project-specific caches
            $this->display_project_power(array('project_id' => $project_id));
            $this->display_project_co2(array('project_id' => $project_id));
            $this->display_project_capacity(array('project_id' => $project_id));
        }
        
        // Log cache warming for debugging
        if (defined('WP_DEBUG') && WP_DEBUG) {
            error_log(sprintf(
                'EcoPower Tracker: Cache warmed for %d recent projects',
                count($recent_projects)
            ));
        }
    }
    
    /**
     * Get cache statistics for debugging
     *
     * @return array Cache statistics
     */
    public function get_cache_stats() {
        $version = get_option('ecopower_tracker_cache_version', 1);
        $stats = array(
            'cache_version' => $version,
            'cached_keys' => array(),
            'hit_rate' => 0,
        );
        
        // Check common cache keys
        $common_keys = array(
            'ecopower_tracker_total_power_v' . $version,
            'ecopower_tracker_total_co2_v' . $version,
        );
        
        foreach ($common_keys as $key) {
            $cached_value = wp_cache_get($key);
            $stats['cached_keys'][$key] = ($cached_value !== false);
        }
        
        return $stats;
    }
}

// Initialize the shortcode functionalities
function ecopower_tracker_shortcodes_init() {
    global $ecopower_tracker_shortcodes;
    $ecopower_tracker_shortcodes = new EcoPower_Tracker_Shortcodes();
    return $ecopower_tracker_shortcodes;
}
add_action('init', 'EcoPowerTracker\\ecopower_tracker_shortcodes_init');