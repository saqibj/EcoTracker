// Path: EcoPower-Tracker/includes/class-ecopower-tracker-csv-process.php
// File: class-ecopower-tracker-csv-process.php

<?php
/**
 * CSV Processing functionality
 *
 * @package EcoPowerTracker
 * @since 2.0.1
 */

namespace EcoPowerTracker;

if (!defined('ABSPATH')) {
    exit;
}

/**
 * Class EcoPower_Tracker_CSV_Process
 */
class EcoPower_Tracker_CSV_Process {

    /**
     * Required CSV columns
     *
     * @var array
     */
    private $required_columns = array(
        'project_number',
        'project_company',
        'project_name',
        'project_location',
        'type_of_plant',
        'project_cuf',
        'generation_capacity',
        'date_of_activation'
    );

    /**
     * Constructor
     */
    public function __construct() {
        add_action('admin_post_ecopower_tracker_process_csv', array($this, 'process_csv_file'));
    }

    /**
     * Process the uploaded CSV file
     *
     * @return void
     */
    public function process_csv_file() {
        try {
            // Check capabilities
            if (!current_user_can('manage_options')) {
                wp_die(__('Unauthorized access', 'ecopower-tracker'));
            }

            // Verify nonce
            if (!isset($_POST['ecopower_tracker_nonce']) || 
                !wp_verify_nonce($_POST['ecopower_tracker_nonce'], 'ecopower_tracker_process_csv')) {
                wp_die(__('Security check failed', 'ecopower-tracker'));
            }

            // Get CSV file path from user meta
            $user_id = get_current_user_id();
            $file_path = get_user_meta($user_id, 'ecopower_tracker_csv_file', true);
            if (empty($file_path)) {
                throw new \Exception(__('No CSV file found for processing', 'ecopower-tracker'));
            }

            // Validate file exists
            if (!file_exists($file_path)) {
                delete_user_meta($user_id, 'ecopower_tracker_csv_file');
                throw new \Exception(__('CSV file not found', 'ecopower-tracker'));
            }

            // Process CSV file
            $this->import_csv_data($file_path);

            // Clean up
            unlink($file_path);
            delete_user_meta($user_id, 'ecopower_tracker_csv_file');

            // Redirect with success message
            wp_safe_redirect(add_query_arg(
                array(
                    'page' => 'ecopower-tracker',
                    'message' => 'import_success'
                ),
                admin_url('admin.php')
            ));
            exit;

        } catch (\Exception $e) {
            error_log('EcoPower Tracker CSV Processing Error: ' . $e->getMessage());
            wp_die(
                esc_html($e->getMessage()),
                esc_html__('Processing Error', 'ecopower-tracker'),
                array('response' => 500, 'back_link' => true)
            );
        }
    }

    /**
     * Import CSV data
     *
     * @param string $file_path Path to CSV file
     * @return void
     * @throws \Exception If import fails
     */
    private function import_csv_data($file_path) {
        global $wpdb;
        $table_name = $wpdb->prefix . 'ecopower_tracker_projects';

        // Start transaction
        $wpdb->query('START TRANSACTION');

        try {
            $handle = fopen($file_path, 'r');
            if ($handle === false) {
                throw new \Exception(__('Failed to open CSV file', 'ecopower-tracker'));
            }

            // Validate header row
            $header = fgetcsv($handle);
            $this->validate_csv_headers($header);

            // Process rows
            $row_count = 0;
            while (($data = fgetcsv($handle)) !== false) {
                $row_count++;

                // Skip empty rows
                if (empty(array_filter($data))) {
                    continue;
                }

                // Prepare and validate data
                $project_data = $this->prepare_project_data($data, $header);
                
                // Insert or update project
                $this->insert_or_update_project($project_data);
            }

            fclose($handle);
            $wpdb->query('COMMIT');

        } catch (\Exception $e) {
            $wpdb->query('ROLLBACK');
            throw $e;
        }
    }

    /**
     * Validate CSV headers
     *
     * @param array $headers CSV header row
     * @return void
     * @throws \Exception If headers are invalid
     */
    private function validate_csv_headers($headers) {
        if (!$headers || !is_array($headers)) {
            throw new \Exception(__('Invalid CSV headers', 'ecopower-tracker'));
        }

        // Sanitize headers
        $headers = array_map('sanitize_text_field', $headers);

        // Check for required columns
        $missing_columns = array_diff($this->required_columns, $headers);
        if (!empty($missing_columns)) {
            /* translators: %s: List of missing columns */
            throw new \Exception(sprintf(
                __('Missing required columns: %s', 'ecopower-tracker'),
                implode(', ', $missing_columns)
            ));
        }

        // Check for malicious column names
        $malicious_patterns = array('/\.\./', '/\./', '<', '>', '%', 'script');
        foreach ($headers as $header) {
            if (preg_match('/(' . implode('|', $malicious_patterns) . ')/i', $header)) {
                throw new \Exception(__('Invalid column name detected', 'ecopower-tracker'));
            }
        }
    }

    /**
     * Prepare project data from CSV row
     *
     * @param array $data    Row data
     * @param array $headers CSV headers
     * @return array Prepared project data
     */
    private function prepare_project_data($data, $headers) {
        $project_data = array_combine($headers, $data);
        
        // Sanitize and validate each field
        return array(
            'project_number' => sanitize_text_field($project_data['project_number']),
            'project_company' => sanitize_text_field($project_data['project_company']),
            'project_name' => sanitize_text_field($project_data['project_name']),
            'project_location' => sanitize_text_field($project_data['project_location']),
            'type_of_plant' => sanitize_text_field($project_data['type_of_plant']),
            'project_cuf' => floatval($project_data['project_cuf']),
            'generation_capacity' => floatval($project_data['generation_capacity']),
            'date_of_activation' => sanitize_text_field($project_data['date_of_activation'])
        );
    }

    /**
     * Insert or update project in database
     *
     * @param array $project_data Project data
     * @return void
     * @throws \Exception If database operation fails
     */
    private function insert_or_update_project($project_data) {
        global $wpdb;
        $table_name = $wpdb->prefix . 'ecopower_tracker_projects';

        // Check if project exists
        $existing = $wpdb->get_var($wpdb->prepare(
            "SELECT id FROM $table_name WHERE project_number = %s",
            $project_data['project_number']
        ));

        if ($existing) {
            // Update existing project
            $result = $wpdb->update(
                $table_name,
                $project_data,
                array('project_number' => $project_data['project_number'])
            );
        } else {
            // Insert new project
            $result = $wpdb->insert($table_name, $project_data);
        }

        if ($result === false) {
            throw new \Exception($wpdb->last_error);
        }
    }
}

// Initialize the CSV processing functionalities
new EcoPower_Tracker_CSV_Process();

?>