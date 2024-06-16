// Path: EcoPower-Tracker/includes/class-ecopower-tracker-csv-process.php
// File: class-ecopower-tracker-csv-process.php

<?php

if (!defined('ABSPATH')) {
    exit; // Exit if accessed directly
}

class EcoPower_Tracker_CSV_Process {

    public function __construct() {
        // Hook to process CSV file after upload
        add_action('admin_post_ecopower_tracker_process_csv', array($this, 'process_csv_file'));
    }

    // Function to process the uploaded CSV file
    public function process_csv_file() {
        // Check nonce for security
        if (!isset($_POST['ecopower_tracker_nonce']) || !wp_verify_nonce($_POST['ecopower_tracker_nonce'], 'ecopower_tracker_process_csv')) {
            wp_die(__('Security check failed', 'ecopower-tracker'));
        }

        // Get the CSV file path
        if (isset($_GET['csv_uploaded'])) {
            $file_path = urldecode($_GET['csv_uploaded']);

            // Check if file exists
            if (!file_exists($file_path)) {
                wp_die(__('CSV file not found', 'ecopower-tracker'));
            }

            // Open the CSV file for reading
            if (($handle = fopen($file_path, 'r')) !== false) {
                // Skip the header row
                fgetcsv($handle);

                // Process each row in the CSV
                while (($data = fgetcsv($handle, 1000, ',')) !== false) {
                    // Prepare data for insertion
                    $project_data = array(
                        'project_number' => sanitize_text_field($data[0]),
                        'project_company' => sanitize_text_field($data[1]),
                        'project_name' => sanitize_text_field($data[2]),
                        'project_location' => sanitize_text_field($data[3]),
                        'type_of_plant' => sanitize_text_field($data[4]),
                        'project_cuf' => sanitize_text_field($data[5]),
                        'generation_capacity' => floatval($data[6]),
                        'date_of_activation' => date('Y-m-d', strtotime($data[7]))
                    );

                    // Insert or update the project data in the database
                    $this->insert_or_update_project($project_data);
                }
                fclose($handle);

                // Redirect to dashboard with success message
                wp_redirect(admin_url('admin.php?page=ecopower-tracker&csv_processed=success'));
                exit;
            } else {
                wp_die(__('Failed to open the CSV file', 'ecopower-tracker'));
            }
        } else {
            wp_die(__('No CSV file specified', 'ecopower-tracker'));
        }
    }

    // Function to insert or update project data
    private function insert_or_update_project($project_data) {
        global $wpdb;

        // Check if the project already exists
        $existing_project = $wpdb->get_row(
            $wpdb->prepare(
                "SELECT * FROM {$wpdb->prefix}ecopower_tracker_projects WHERE project_number = %s",
                $project_data['project_number']
            )
        );

        if ($existing_project) {
            // Update existing project
            $wpdb->update(
                "{$wpdb->prefix}ecopower_tracker_projects",
                $project_data,
                array('project_number' => $project_data['project_number'])
            );
        } else {
            // Insert new project
            $wpdb->insert("{$wpdb->prefix}ecopower_tracker_projects", $project_data);
        }
    }
}

// Initialize the CSV processing functionalities
new EcoPower_Tracker_CSV_Process();

?>