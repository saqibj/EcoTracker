// Path: EcoPower-Tracker/includes/class-ecopower-tracker-utils.php
// File: class-ecopower-tracker-utils.php

<?php

if (!defined('ABSPATH')) {
    exit; // Exit if accessed directly
}

class EcoPower_Tracker_Utils {

    // Function to validate date format (YYYY-MM-DD)
    public static function validate_date($date) {
        $format = 'Y-m-d';
        $d = DateTime::createFromFormat($format, $date);
        return $d && $d->format($format) === $date;
    }

    // Function to sanitize and validate numeric data
    public static function validate_numeric($number) {
        return is_numeric($number) ? floatval($number) : 0;
    }

    // Function to format numbers with thousand delimiters
    public static function format_number($number) {
        return number_format($number, 0, '.', ',');
    }

    // Function to convert KWs to MWhs if ≥ 1000 KWs
    public static function convert_to_mwh($power) {
        if ($power >= 1000) {
            return ($power / 1000) . ' MWh';
        }
        return $power . ' KWs';
    }

    // Function to calculate CO2 offset from power generated
    public static function calculate_co2_offset($power) {
        // Example conversion factor: 1 KW = 0.001 tons of CO2 offset (adjust as needed)
        $conversion_factor = 0.001;
        return $power * $conversion_factor;
    }

    // Function to check for duplicates in the database based on project number
    public static function is_duplicate_project($project_number) {
        global $wpdb;
        $count = $wpdb->get_var($wpdb->prepare(
            "SELECT COUNT(*) FROM {$wpdb->prefix}ecopower_tracker_projects WHERE project_number = %s",
            $project_number
        ));
        return $count > 0;
    }

    // Function to get formatted project details by project ID
    public static function get_project_details($project_id) {
        global $wpdb;
        $project = $wpdb->get_row($wpdb->prepare(
            "SELECT * FROM {$wpdb->prefix}ecopower_tracker_projects WHERE id = %d",
            $project_id
        ));
        return $project ? self::format_project_details($project) : null;
    }

    // Function to format project details for display
    private static function format_project_details($project) {
        return array(
            'Project Number' => esc_html($project->project_number),
            'Project Company' => esc_html($project->project_company),
            'Project Name' => esc_html($project->project_name),
            'Project Location' => esc_html($project->project_location),
            'Type of Plant' => esc_html($project->type_of_plant),
            'Project CUF' => esc_html($project->project_cuf),
            'Generation Capacity' => self::format_number($project->generation_capacity),
            'Date of Activation' => esc_html(date('Y-m-d', strtotime($project->date_of_activation)))
        );
    }

    // Function to handle CSV data sanitization
    public static function sanitize_csv_data($data) {
        return array_map('sanitize_text_field', $data);
    }

    // Function to handle file upload and validate CSV file type
    public static function validate_csv_upload($file) {
        // Check file type
        $filetype = wp_check_filetype($file['name']);
        return $filetype['ext'] === 'csv';
    }
}

?>