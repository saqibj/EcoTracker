<?php
if (!defined('ABSPATH')) {
    exit; // Exit if accessed directly
}

// CSV Handler class for EcoPower Tracker
class EcoPower_CSV_Handler {

    // Export projects to CSV
    public static function export_csv() {
        if (isset($_POST['ecopower_tracker_export_csv'])) {
            global $wpdb;
            $table_name = $wpdb->prefix . 'ecopower_projects';

            $filename = 'ecopower_projects_' . date('Ymd') . '.csv';
            header('Content-Type: text/csv');
            header('Content-Disposition: attachment;filename=' . $filename);

            $output = fopen('php://output', 'w');

            // CSV Column headers
            fputcsv($output, array('Project Company', 'Project Name', 'Project Location', 'Type of Plant', 'Project CUF', 'Generation Capacity (in KWs)', 'Date of Activation'));

            // Fetch project data
            $projects = $wpdb->get_results("SELECT * FROM $table_name");

            // Output each row
            foreach ($projects as $project) {
                fputcsv($output, array(
                    $project->project_company,
                    $project->project_name,
                    $project->project_location,
                    $project->plant_type,
                    $project->project_cuf,
                    $project->generation_capacity,
                    $project->activation_date
                ));
            }

            fclose($output);
            exit;
        }
    }

    // Import projects from CSV
    public static function import_csv() {
        if (isset($_POST['ecopower_tracker_import_csv']) && !empty($_FILES['ecopower_tracker_csv']['tmp_name'])) {
            global $wpdb;
            $table_name = $wpdb->prefix . 'ecopower_projects';

            $csv_file = fopen($_FILES['ecopower_tracker_csv']['tmp_name'], 'r');

            // Skip the header row
            fgetcsv($csv_file);

            while ($row = fgetcsv($csv_file)) {
                $wpdb->insert($table_name, array(
                    'project_company' => sanitize_text_field($row[0]),
                    'project_name' => sanitize_text_field($row[1]),
                    'project_location' => sanitize_text_field($row[2]),
                    'plant_type' => sanitize_text_field($row[3]),
                    'project_cuf' => floatval($row[4]),
                    'generation_capacity' => floatval($row[5]),
                    'activation_date' => sanitize_text_field($row[6])
                ));
            }

            fclose($csv_file);
            wp_redirect(admin_url('admin.php?page=ecopower-tracker'));
            exit;
        }
    }
}

// Add actions for CSV export/import
add_action('admin_post_ecopower_tracker_export_csv', array('EcoPower_CSV_Handler', 'export_csv'));
add_action('admin_post_ecopower_tracker_import_csv', array('EcoPower_CSV_Handler', 'import_csv'));

?>