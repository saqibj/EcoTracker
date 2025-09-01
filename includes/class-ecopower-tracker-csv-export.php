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

require_once plugin_dir_path(__FILE__) . 'class-ecopower-tracker-error-handler.php';

/**
 * Class EcoPower_Tracker_CSV_Export
 */
class EcoPower_Tracker_CSV_Export {

    /**
     * Maximum rows to export at once
     *
     * @var int
     */
    private $max_export_rows = 10000;

    /**
     * Export batch size for memory management
     *
     * @var int
     */
    private $batch_size = 500;

    /**
     * Constructor
     */
    public function __construct() {
        add_action('admin_post_ecopower_tracker_export_csv', array($this, 'export_csv'));
        
        // Allow filtering of export limits
        $this->max_export_rows = apply_filters('ecopower_tracker_max_export_rows', $this->max_export_rows);
        $this->batch_size = apply_filters('ecopower_tracker_export_batch_size', $this->batch_size);
    }

    /**
     * Export project data to CSV
     *
     * @return void
     */
    public function export_csv() {
        try {
            // Check system resources before export
            $system_status = EcoPower_Tracker_Error_Handler::check_system_resources();
            if (!$system_status['memory_ok'] || !$system_status['time_ok']) {
                EcoPower_Tracker_Error_Handler::log_error(
                    'System resources insufficient for CSV export',
                    EcoPower_Tracker_Error_Handler::ERROR_TYPE_SYSTEM,
                    EcoPower_Tracker_Error_Handler::SEVERITY_HIGH,
                    $system_status
                );
            }

            // Check capabilities
            if (!current_user_can('manage_options')) {
                EcoPower_Tracker_Error_Handler::log_error(
                    'Unauthorized CSV export attempt',
                    EcoPower_Tracker_Error_Handler::ERROR_TYPE_SECURITY,
                    EcoPower_Tracker_Error_Handler::SEVERITY_HIGH,
                    array('user_id' => get_current_user_id())
                );
                wp_die(__('Unauthorized access', 'ecopower-tracker'));
            }

            // Verify nonce
            if (!isset($_POST['ecopower_tracker_nonce']) || 
                !wp_verify_nonce($_POST['ecopower_tracker_nonce'], 'ecopower_tracker_export_csv')) {
                EcoPower_Tracker_Error_Handler::log_error(
                    'Nonce verification failed for CSV export',
                    EcoPower_Tracker_Error_Handler::ERROR_TYPE_SECURITY,
                    EcoPower_Tracker_Error_Handler::SEVERITY_HIGH
                );
                wp_die(__('Security check failed', 'ecopower-tracker'));
            }

            // Get project count for memory management
            global $wpdb;
            $table_name = $wpdb->prefix . 'ecopower_tracker_projects';
            $total_projects = $wpdb->get_var("SELECT COUNT(*) FROM $table_name");

            if ($total_projects === null) {
                throw new \Exception(__('Failed to count projects for export', 'ecopower-tracker'));
            }

            if ($total_projects == 0) {
                throw new \Exception(__('No projects found to export', 'ecopower-tracker'));
            }

            if ($total_projects > $this->max_export_rows) {
                throw new \Exception(sprintf(
                    /* translators: 1: Number of projects to export, 2: Maximum allowed projects */
                    __('Too many projects to export (%1$d). Maximum allowed: %2$d. Please contact administrator.', 'ecopower-tracker'),
                    $total_projects,
                    $this->max_export_rows
                ));
            }

            // Increase execution time for large exports
            if ($total_projects > 1000) {
                set_time_limit(300); // 5 minutes
            }

            // Set headers for CSV download with security headers
            $filename = sanitize_file_name('ecopower_tracker_projects_' . date('Y-m-d_H-i-s') . '.csv');
            
            // Clear any previous output
            if (ob_get_level()) {
                ob_clean();
            }

            header('Content-Type: text/csv; charset=utf-8');
            header('Content-Disposition: attachment; filename=' . $filename);
            header('Cache-Control: no-cache, no-store, must-revalidate');
            header('Pragma: no-cache');
            header('Expires: 0');
            header('X-Content-Type-Options: nosniff');

            // Create output stream with error handling
            $output = fopen('php://output', 'w');
            if ($output === false) {
                throw new \Exception(__('Failed to create output stream', 'ecopower-tracker'));
            }

            // Add UTF-8 BOM for Excel compatibility
            fprintf($output, chr(0xEF).chr(0xBB).chr(0xBF));

            // Write CSV header with proper escaping
            $headers = array(
                __('Project Number', 'ecopower-tracker'),
                __('Project Company', 'ecopower-tracker'),
                __('Project Name', 'ecopower-tracker'),
                __('Project Location', 'ecopower-tracker'),
                __('Type of Plant', 'ecopower-tracker'),
                __('Project CUF (%)', 'ecopower-tracker'),
                __('Generation Capacity (KW)', 'ecopower-tracker'),
                __('Date of Activation', 'ecopower-tracker')
            );

            if (fputcsv($output, $headers) === false) {
                throw new \Exception(__('Failed to write CSV headers', 'ecopower-tracker'));
            }

            // Export data in batches for memory efficiency
            $exported_count = 0;
            $offset = 0;

            while ($offset < $total_projects) {
                $projects = $wpdb->get_results($wpdb->prepare(
                    "SELECT * FROM $table_name ORDER BY id ASC LIMIT %d OFFSET %d",
                    $this->batch_size,
                    $offset
                ), ARRAY_A);

                if ($wpdb->last_error) {
                    throw new \Exception(__('Database error during export: ', 'ecopower-tracker') . $wpdb->last_error);
                }

                if (!$projects) {
                    break;
                }

                foreach ($projects as $project) {
                    try {
                        // Sanitize and prepare data for export
                        $export_data = $this->prepare_export_data($project);
                        
                        if (fputcsv($output, $export_data) === false) {
                            throw new \Exception(__('Failed to write project data', 'ecopower-tracker'));
                        }
                        
                        $exported_count++;

                    } catch (\Exception $row_error) {
                        EcoPower_Tracker_Error_Handler::log_error(
                            'Error exporting project row: ' . $row_error->getMessage(),
                            EcoPower_Tracker_Error_Handler::ERROR_TYPE_VALIDATION,
                            EcoPower_Tracker_Error_Handler::SEVERITY_MEDIUM,
                            array('project_id' => $project['id'] ?? 'unknown')
                        );
                        
                        // Continue with other rows, but log the error
                        continue;
                    }
                }

                $offset += $this->batch_size;

                // Check memory usage and break if getting too high
                if (memory_get_usage(true) > (ini_get('memory_limit') ? $this->convert_memory_limit() * 0.9 : 128 * 1024 * 1024)) {
                    EcoPower_Tracker_Error_Handler::log_error(
                        'Memory limit approaching during CSV export',
                        EcoPower_Tracker_Error_Handler::ERROR_TYPE_SYSTEM,
                        EcoPower_Tracker_Error_Handler::SEVERITY_HIGH,
                        array('exported_count' => $exported_count, 'memory_usage' => memory_get_usage(true))
                    );
                    break;
                }

                // Flush output buffer to prevent memory buildup
                if (ob_get_level()) {
                    ob_flush();
                }
                flush();
            }

            fclose($output);

            // Log successful export
            EcoPower_Tracker_Error_Handler::log_error(
                'CSV export completed successfully',
                EcoPower_Tracker_Error_Handler::ERROR_TYPE_SYSTEM,
                EcoPower_Tracker_Error_Handler::SEVERITY_LOW,
                array(
                    'exported_count' => $exported_count,
                    'total_projects' => $total_projects,
                    'filename' => $filename
                )
            );

            exit;

        } catch (\Exception $e) {
            EcoPower_Tracker_Error_Handler::log_error(
                $e->getMessage(),
                EcoPower_Tracker_Error_Handler::ERROR_TYPE_FILE,
                EcoPower_Tracker_Error_Handler::SEVERITY_CRITICAL,
                array(
                    'total_projects' => isset($total_projects) ? $total_projects : 0,
                    'exported_count' => isset($exported_count) ? $exported_count : 0
                )
            );

            // Clean output buffer before showing error
            if (ob_get_level()) {
                ob_clean();
            }

            $user_message = EcoPower_Tracker_Error_Handler::format_user_error(
                $e->getMessage(),
                null,
                array(
                    __('Try exporting fewer records if you have a large dataset', 'ecopower-tracker'),
                    __('Check that you have sufficient server resources', 'ecopower-tracker'),
                    __('Contact administrator if the problem persists', 'ecopower-tracker')
                )
            );

            wp_die(
                esc_html($user_message),
                esc_html__('Export Error', 'ecopower-tracker'),
                array('response' => 500)
            );
        }
    }

    /**
     * Prepare project data for export
     *
     * @param array $project Project data from database
     * @return array Sanitized export data
     */
    private function prepare_export_data($project) {
        // Define the expected fields in export order
        $export_fields = array(
            'project_number',
            'project_company',
            'project_name',
            'project_location',
            'type_of_plant',
            'project_cuf',
            'generation_capacity',
            'date_of_activation'
        );

        $export_data = array();

        foreach ($export_fields as $field) {
            $value = isset($project[$field]) ? $project[$field] : '';
            $export_data[] = $this->sanitize_export_field($value, $field);
        }

        return $export_data;
    }

    /**
     * Sanitize export field with type-specific handling
     *
     * @param mixed $field Field value to sanitize
     * @param string $field_name Field name for type-specific handling
     * @return string Sanitized field value
     */
    private function sanitize_export_field($field, $field_name = '') {
        // Handle null/empty values
        if ($field === null || $field === '') {
            return '';
        }

        // Type-specific sanitization
        switch ($field_name) {
            case 'project_cuf':
            case 'generation_capacity':
                // Ensure numeric fields are properly formatted
                return is_numeric($field) ? number_format((float)$field, 2, '.', '') : '0.00';

            case 'date_of_activation':
                // Validate and format dates
                $date = date_create($field);
                return $date !== false ? date_format($date, 'Y-m-d') : '';

            case 'type_of_plant':
                // Ensure plant type is lowercase and clean
                return strtolower(sanitize_text_field($field));

            default:
                // Standard text field sanitization
                $field = str_replace(array("\r", "\n", "\t"), ' ', $field);
                $field = wp_strip_all_tags($field);
                $field = sanitize_text_field($field);
                
                // Remove potential CSV injection characters
                if (in_array(substr($field, 0, 1), array('=', '+', '-', '@'))) {
                    $field = "'" . $field; // Prefix with single quote to prevent injection
                }
                
                return $field;
        }
    }

    /**
     * Convert memory limit string to bytes
     *
     * @return int Memory limit in bytes
     */
    private function convert_memory_limit() {
        $memory_limit = ini_get('memory_limit');
        if ($memory_limit === '-1') {
            return PHP_INT_MAX;
        }
        
        $unit = strtolower(substr($memory_limit, -1));
        $value = intval($memory_limit);
        
        switch ($unit) {
            case 'g':
                $value *= 1024;
            case 'm':
                $value *= 1024;
            case 'k':
                $value *= 1024;
        }
        
        return $value;
    }
}

// Initialize the CSV export functionalities
new EcoPower_Tracker_CSV_Export();

?>