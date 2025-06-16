// Path: EcoPower-Tracker/includes/class-ecopower-tracker-csv-upload.php
// File: class-ecopower-tracker-csv-upload.php

<?php
/**
 * CSV Upload functionality
 *
 * @package EcoPowerTracker
 * @since 2.0.1
 */

namespace EcoPowerTracker;

if (!defined('ABSPATH')) {
    exit;
}

/**
 * Class EcoPower_Tracker_CSV_Upload
 */
class EcoPower_Tracker_CSV_Upload {

    /**
     * Allowed mime types for upload
     *
     * @var array
     */
    private $allowed_mime_types = array(
        'text/csv',
        'text/plain',
        'application/csv',
        'application/excel',
        'application/vnd.ms-excel',
        'application/vnd.msexcel',
    );

    /**
     * Constructor
     */
    public function __construct() {
        add_action('admin_post_ecopower_tracker_upload_csv', array($this, 'handle_csv_upload'));
    }

    /**
     * Handle CSV file upload
     *
     * @return void
     */
    public function handle_csv_upload() {
        try {
            // Check capabilities
            if (!current_user_can('manage_options')) {
                wp_die(__('Unauthorized access', 'ecopower-tracker'));
            }

            // Verify nonce
            if (!isset($_POST['ecopower_tracker_nonce']) || 
                !wp_verify_nonce($_POST['ecopower_tracker_nonce'], 'ecopower_tracker_upload_csv')) {
                wp_die(__('Security check failed', 'ecopower-tracker'));
            }

            // Check file upload
            if (!isset($_FILES['ecopower_tracker_csv']) || 
                empty($_FILES['ecopower_tracker_csv']['tmp_name'])) {
                throw new \Exception(__('No file uploaded', 'ecopower-tracker'));
            }

            $file = $_FILES['ecopower_tracker_csv'];

            // Validate file type
            if (!$this->validate_file_type($file)) {
                throw new \Exception(__('Invalid file type. Please upload a CSV file.', 'ecopower-tracker'));
            }

            // Create upload directory if it doesn't exist
            $upload_dir = $this->get_upload_directory();
            if (!file_exists($upload_dir)) {
                wp_mkdir_p($upload_dir);
            }

            // Generate unique filename
            $filename = uniqid('ecopower_', true) . '.csv';
            $destination = $upload_dir . '/' . $filename;

            // Move uploaded file
            if (!move_uploaded_file($file['tmp_name'], $destination)) {
                throw new \Exception(__('Failed to move uploaded file', 'ecopower-tracker'));
            }

            // Set user meta variable for processing
            $user_id = get_current_user_id();
            update_user_meta($user_id, 'ecopower_tracker_csv_file', $destination);

            // Redirect to processing page
            wp_safe_redirect(add_query_arg(
                array(
                    'page' => 'ecopower-tracker',
                    'action' => 'process_csv',
                    '_wpnonce' => wp_create_nonce('ecopower_tracker_process_csv')
                ),
                admin_url('admin.php')
            ));
            exit;

        } catch (\Exception $e) {
            error_log('EcoPower Tracker CSV Upload Error: ' . $e->getMessage());
            wp_die(
                esc_html($e->getMessage()),
                esc_html__('Upload Error', 'ecopower-tracker'),
                array('response' => 500, 'back_link' => true)
            );
        }
    }

    /**
     * Validate uploaded file type
     *
     * @param array $file Uploaded file information
     * @return boolean
     */
    private function validate_file_type($file) {
        // Check file extension
        $ext = strtolower(pathinfo($file['name'], PATHINFO_EXTENSION));
        if ($ext !== 'csv') {
            return false;
        }

        // Check mime type
        $mime_type = mime_content_type($file['tmp_name']);
        return in_array($mime_type, $this->allowed_mime_types, true);
    }

    /**
     * Get upload directory path
     *
     * @return string
     */
    private function get_upload_directory() {
        $upload_dir = wp_upload_dir();
        return $upload_dir['basedir'] . '/ecopower_tracker_csv';
    }
}

// Initialize the CSV upload functionalities
new EcoPower_Tracker_CSV_Upload();

?>