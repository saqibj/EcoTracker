// Path: EcoPower-Tracker/includes/class-ecopower-tracker-csv-upload.php
// File: class-ecopower-tracker-csv-upload.php

<?php

if (!defined('ABSPATH')) {
    exit; // Exit if accessed directly
}

class EcoPower_Tracker_CSV_Upload {

    public function __construct() {
        // Hook to handle CSV upload
        add_action('admin_post_ecopower_tracker_upload_csv', array($this, 'handle_csv_upload'));
    }

    // Function to handle CSV upload
    public function handle_csv_upload() {
        // Check nonce for security
        if (!isset($_POST['ecopower_tracker_nonce']) || !wp_verify_nonce($_POST['ecopower_tracker_nonce'], 'ecopower_tracker_upload_csv')) {
            wp_die(__('Security check failed', 'ecopower-tracker'));
        }

        // Check if file is uploaded
        if (isset($_FILES['ecopower_tracker_csv']) && !empty($_FILES['ecopower_tracker_csv']['tmp_name'])) {
            $file = $_FILES['ecopower_tracker_csv'];

            // Check file type
            $filetype = wp_check_filetype($file['name']);
            if ($filetype['ext'] !== 'csv') {
                wp_die(__('Please upload a valid CSV file', 'ecopower-tracker'));
            }

            // Move the file to the uploads directory
            $upload_dir = wp_upload_dir();
            $destination = $upload_dir['basedir'] . '/ecopower_tracker_csv/' . basename($file['name']);

            if (!is_dir($upload_dir['basedir'] . '/ecopower_tracker_csv/')) {
                mkdir($upload_dir['basedir'] . '/ecopower_tracker_csv/', 0755, true);
            }

            if (move_uploaded_file($file['tmp_name'], $destination)) {
                // Redirect to CSV processing with the file path
                wp_redirect(admin_url('admin.php?page=ecopower-tracker&csv_uploaded=' . urlencode($destination)));
                exit;
            } else {
                wp_die(__('Failed to upload the file', 'ecopower-tracker'));
            }
        } else {
            wp_die(__('No file uploaded', 'ecopower-tracker'));
        }
    }
}

// Initialize the CSV upload functionalities
new EcoPower_Tracker_CSV_Upload();

?>