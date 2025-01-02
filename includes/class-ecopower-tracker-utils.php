// Path: EcoPower-Tracker/includes/class-ecopower-tracker-utils.php
// File: class-ecopower-tracker-utils.php

<?php
/**
 * Utility functions for EcoPower Tracker
 *
 * @package EcoPowerTracker
 * @since 2.0.1
 */

namespace EcoPowerTracker;

if (!defined('ABSPATH')) {
    exit;
}

/**
 * Class EcoPower_Tracker_Utils
 */
class EcoPower_Tracker_Utils {

    /**
     * Validate date format (YYYY-MM-DD)
     *
     * @param string $date Date string to validate
     * @return boolean
     */
    public static function validate_date($date) {
        if (empty($date)) {
            return false;
        }

        $format = 'Y-m-d';
        $d = \DateTime::createFromFormat($format, $date);
        return $d && $d->format($format) === $date;
    }

    /**
     * Validate and sanitize numeric data
     *
     * @param mixed $number Number to validate
     * @param float $min Minimum allowed value
     * @param float $max Maximum allowed value
     * @return float
     */
    public static function validate_numeric($number, $min = 0, $max = PHP_FLOAT_MAX) {
        $number = filter_var($number, FILTER_SANITIZE_NUMBER_FLOAT, FILTER_FLAG_ALLOW_FRACTION);
        $number = floatval($number);
        
        return max(min($number, $max), $min);
    }

    /**
     * Format numbers with thousand delimiters
     *
     * @param float  $number Number to format
     * @param int    $decimals Number of decimal places
     * @param string $decimal_separator Decimal separator
     * @param string $thousands_separator Thousands separator
     * @return string
     */
    public static function format_number($number, $decimals = 0, $decimal_separator = '.', $thousands_separator = ',') {
        return number_format(
            floatval($number),
            $decimals,
            $decimal_separator,
            $thousands_separator
        );
    }

    /**
     * Format project details for display
     *
     * @param object $project Project data object
     * @return array Formatted project details
     */
    public static function format_project_details($project) {
        if (!is_object($project)) {
            return array();
        }

        return array(
            'Project Number' => esc_html($project->project_number),
            'Project Company' => esc_html($project->project_company),
            'Project Name' => esc_html($project->project_name),
            'Project Location' => esc_html($project->project_location),
            'Type of Plant' => esc_html($project->type_of_plant),
            'Project CUF' => self::format_number($project->project_cuf, 2) . '%',
            'Generation Capacity' => self::format_number($project->generation_capacity) . ' KWs',
            'Date of Activation' => esc_html(date_i18n(
                get_option('date_format'),
                strtotime($project->date_of_activation)
            ))
        );
    }

    /**
     * Sanitize CSV data
     *
     * @param array $data Data to sanitize
     * @return array Sanitized data
     */
    public static function sanitize_csv_data($data) {
        if (!is_array($data)) {
            return array();
        }

        $sanitized = array();
        foreach ($data as $key => $value) {
            $key = sanitize_key($key);
            
            // Handle different types of data
            if (strpos($key, 'date') !== false) {
                $value = self::sanitize_date($value);
            } elseif (strpos($key, 'capacity') !== false || strpos($key, 'cuf') !== false) {
                $value = self::validate_numeric($value);
            } else {
                $value = sanitize_text_field($value);
            }
            
            $sanitized[$key] = $value;
        }

        return $sanitized;
    }

    /**
     * Sanitize date value
     *
     * @param string $date Date string
     * @return string Sanitized date (Y-m-d format)
     */
    public static function sanitize_date($date) {
        if (empty($date)) {
            return '';
        }

        $timestamp = strtotime($date);
        return $timestamp ? date('Y-m-d', $timestamp) : '';
    }

    /**
     * Calculate CO2 offset
     *
     * @param float $power_generated Power generated in KWh
     * @return float CO2 offset in tons
     */
    public static function calculate_co2_offset($power_generated) {
        $conversion_factor = apply_filters(
            'ecopower_tracker_co2_conversion_factor',
            0.001 // Default: 1 KWh = 0.001 tons of CO2 offset
        );
        
        return self::validate_numeric($power_generated) * $conversion_factor;
    }

    /**
     * Get allowed plant types
     *
     * @return array Array of allowed plant types
     */
    public static function get_plant_types() {
        return apply_filters('ecopower_tracker_plant_types', array(
            'solar' => __('Solar', 'ecopower-tracker'),
            'wind' => __('Wind', 'ecopower-tracker'),
            'hydro' => __('Hydro', 'ecopower-tracker'),
            'biomass' => __('Biomass', 'ecopower-tracker'),
            'geothermal' => __('Geothermal', 'ecopower-tracker')
        ));
    }

    /**
     * Validate file upload
     *
     * @param array $file Uploaded file information
     * @return boolean|string True if valid, error message if invalid
     */
    public static function validate_file_upload($file) {
        if (!isset($file['error']) || is_array($file['error'])) {
            return __('Invalid file upload', 'ecopower-tracker');
        }

        switch ($file['error']) {
            case UPLOAD_ERR_OK:
                break;
            case UPLOAD_ERR_INI_SIZE:
            case UPLOAD_ERR_FORM_SIZE:
                return __('File size exceeds limit', 'ecopower-tracker');
            case UPLOAD_ERR_NO_FILE:
                return __('No file uploaded', 'ecopower-tracker');
            default:
                return __('Unknown upload error', 'ecopower-tracker');
        }

        $allowed_types = array('text/csv', 'application/csv');
        if (!in_array($file['type'], $allowed_types)) {
            return __('Invalid file type', 'ecopower-tracker');
        }

        return true;
    }
}

?>