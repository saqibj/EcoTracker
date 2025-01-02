// Path: EcoPower-Tracker/includes/class-ecopower-tracker-csv-export.php
// File: class-ecopower-tracker-csv-export.php

<?php
/**
 * CSV Export functionality
 *
 * @package EcoPowerTracker
 * @since 2.0.1
 */

namespace EcoPowerTracker;

if (!defined('ABSPATH')) {
    exit;
}

/**
 * Class EcoPower_Tracker_CSV_Export
 */
class EcoPower_Tracker_CSV_Export {

    /**
     * Constructor
     */
    public function __construct() {
        add_action('admin_post_ecopower_tracker_export_csv', array($this, 'export_csv'));
    }

    /**
     * Export project data to CSV
     *
     * @return void
     */
    public function export_csv() {
        try {
            // Check capabilities
            if (!current_user_can('manage_options')) {
                wp_die(__('Unauthorized access', 'ecopower-tracker'));
            }

            // Verify nonce
            if (!isset($_POST['ecopower_tracker_nonce']) || 
                !wp_verify_nonce($_POST['ecopower_tracker_nonce'], 'ecopower_tracker_export_csv')) {
                wp_die(__('Security check failed', 'ecopower-tracker'));
            }

            // Set headers for CSV download
            $filename = 'ecopower_tracker_projects_' . date('Y-m-d') . '.csv';
            header('Content-Type: text/csv; charset=utf-8');
            header('Content-Disposition: attachment; filename=' . $filename);

            // Create output stream
            $output = fopen('php://output', 'w');
            if ($output === false) {
                throw new \Exception(__('Failed to create output stream', 'ecopower-tracker'));
            }

            // Add UTF-8 BOM for Excel compatibility
            fprintf($output, chr(0xEF).chr(0xBB).chr(0xBF));

            // Write CSV header
            fputcsv($output, array(
                __('Project Number', 'ecopower-tracker'),
                __('Project Company', 'ecopower-tracker'),
                __('Project Name', 'ecopower-tracker'),
                __('Project Location', 'ecopower-tracker'),
                __('Type of Plant', 'ecopower-tracker'),
                __('Project CUF', 'ecopower-tracker'),
                __('Generation Capacity (KWs)', 'ecopower-tracker'),
                __('Date of Activation', 'ecopower-tracker')
            ));

            // Get and write project data
            global $wpdb;
            $table_name = $wpdb->prefix . 'ecopower_tracker_projects';
            $projects = $wpdb->get_results("SELECT * FROM $table_name ORDER BY id ASC", ARRAY_A);

            if ($projects) {
                foreach ($projects as $project) {
                    // Sanitize data before export
                    $export_data = array_map(array($this, 'sanitize_export_field'), $project);
                    fputcsv($output, $export_data);
                }
            }

            fclose($output);
            exit;

        } catch (\Exception $e) {
            error_log('EcoPower Tracker CSV Export Error: ' . $e->getMessage());
            wp_die(
                esc_html__('An error occurred during export. Please try again.', 'ecopower-tracker'),
                esc_html__('Export Error', 'ecopower-tracker'),
                array('response' => 500)
            );
        }
    }

    /**
     * Sanitize export field
     *
     * @param mixed $field Field value to sanitize
     * @return string Sanitized field value
     */
    private function sanitize_export_field($field) {
        // Remove any potentially harmful characters
        $field = str_replace(array("\r", "\n"), ' ', $field);
        $field = wp_strip_all_tags($field);
        return sanitize_text_field($field);
    }
}

// Initialize the CSV export functionalities
new EcoPower_Tracker_CSV_Export();

?>