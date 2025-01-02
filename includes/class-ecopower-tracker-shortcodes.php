// Path: EcoPower-Tracker/includes/class-ecopower-tracker-shortcodes.php
// File: class-ecopower-tracker-shortcodes.php

<?php
/**
 * Shortcode functionality
 *
 * @package EcoPowerTracker
 * @since 2.0.1
 */

namespace EcoPowerTracker;

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
        global $wpdb;
        $table_name = $wpdb->prefix . 'ecopower_tracker_projects';

        $power = $wpdb->get_var("
            SELECT SUM(generation_capacity * project_cuf / 100)
            FROM $table_name
        ");

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
        global $wpdb;
        $table_name = $wpdb->prefix . 'ecopower_tracker_projects';

        $power = $wpdb->get_var("
            SELECT SUM(generation_capacity * project_cuf / 100)
            FROM $table_name
        ");

        $co2 = $this->calculate_co2_offset($power);

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

        global $wpdb;
        $table_name = $wpdb->prefix . 'ecopower_tracker_projects';

        $power = $wpdb->get_var($wpdb->prepare("
            SELECT (generation_capacity * project_cuf / 100)
            FROM $table_name
            WHERE id = %d
        ", $project_id));

        if (null === $power) {
            return $this->error_message(__('Project not found', 'ecopower-tracker'));
        }

        return $this->format_output(
            'project-power',
            sprintf(__('Project Power Generation (#%d)', 'ecopower-tracker'), $project_id),
            $power,
            'KWh'
        );
    }

    // ... Similar improvements for other shortcode methods ...

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
            '<div class="ecopower-tracker-%s">
                <span class="label">%s:</span>
                <span class="value">%s</span>
                <span class="unit">%s</span>
            </div>',
            esc_attr($class),
            esc_html($label),
            esc_html($value),
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
            '<div class="ecopower-tracker-error">%s</div>',
            esc_html($message)
        );
    }
}