// Path: EcoPower-Tracker/includes/class-ecopower-tracker-csv-export.php
// File: class-ecopower-tracker-csv-export.php

<?php

if (!defined('ABSPATH')) {
    exit; // Exit if accessed directly
}

class EcoPower_Tracker_CSV_Export {

    public function __construct() {
        // Hook to handle CSV export
        add_action('admin_post_ecopower_tracker_export_csv', array($this, 'export_csv'));
    }

    // Function to export project data to CSV
    public function export_csv() {
        // Check nonce for security
        if (!isset($_POST['ecopower_tracker_nonce']) || !wp_verify_nonce($_POST['ecopower_tracker_nonce'], 'ecopower_tracker_export_csv')) {
            wp_die(__('Security check failed', 'ecopower-tracker'));
        }

        // Set CSV headers
        header('Content-Type: text/csv');
        header('Content-Disposition: attachment;filename=ecopower_tracker_projects.csv');

        // Open output stream
        $output = fopen('php://output', 'w');

        // Add CSV header row
        fputcsv($output, array('Project Number', 'Project Company', 'Project Name', 'Project Location', 'Type of Plant', 'Project CUF', 'Generation Capacity (KWs)', 'Date of Activation'));

        // Retrieve project data from database
        global $wpdb;
        $projects = $wpdb->get_results("SELECT * FROM {$wpdb->prefix}ecopower_tracker_projects", ARRAY_A);

        // Write project data to CSV
        foreach ($projects as $project) {
            fputcsv($output, $project);
        }

        // Close output stream
        fclose($output);
        exit;
    }
}

// Initialize the CSV export functionalities
new EcoPower_Tracker_CSV_Export();

?>