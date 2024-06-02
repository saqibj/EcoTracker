<?php
if (!defined('ABSPATH')) {
    exit; // Exit if accessed directly
}

// Function to export project data as CSV
function ecopower_tracker_export_csv() {
    if (isset($_POST['ecopower_tracker_export_csv'])) {
        global $wpdb;
        $table_name = $wpdb->prefix . 'ecopower_projects';

        $filename = 'ecopower_projects_' . date('Ymd') . '.csv';
        header('Content-Type: text/csv');
        header('Content-Disposition: attachment;filename=' . $filename);

        $output = fopen('php://output', 'w');

        fputcsv($output, array('Project Company', 'Project Name', 'Project Location', 'Type of Plant', 'Project CUF', 'Generation Capacity (in KWs)', 'Date of Activation'));

        $projects = $wpdb->get_results("SELECT * FROM $table_name");

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
add_action('admin_post_ecopower_tracker_export_csv', 'ecopower_tracker_export_csv');

// Function to import project data from CSV
function ecopower_tracker_import_csv() {
    if (isset($_POST['ecopower_tracker_import_csv']) && !empty($_FILES['ecopower_tracker_csv']['tmp_name'])) {
        global $wpdb;
        $table_name = $wpdb->prefix . 'ecopower_projects';

        $csv_file = fopen($_FILES['ecopower_tracker_csv']['tmp_name'], 'r');

        fgetcsv($csv_file); // Skip the header row

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
add_action('admin_post_ecopower_tracker_import_csv', 'ecopower_tracker_import_csv');
?>