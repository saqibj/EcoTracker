<?php
// Include necessary files
include_once ECOPOWER_TRACKER_DIR . 'admin/edit-project.php';

// Create admin menu
function ecopower_tracker_admin_menu() {
    add_menu_page('EcoPower Tracker', 'EcoPower Tracker', 'manage_options', 'ecopower-tracker', 'ecopower_tracker_dashboard', 'dashicons-chart-area');
    add_submenu_page('ecopower-tracker', 'Manage Data', 'Manage Data', 'manage_options', 'ecopower-tracker-manage', 'ecopower_tracker_manage_data');
    add_submenu_page('ecopower-tracker', 'Import Data', 'Import Data', 'manage_options', 'ecopower-tracker-import', 'ecopower_tracker_import_data');
    add_submenu_page('ecopower-tracker', 'Export Data', 'Export Data', 'manage_options', 'ecopower-tracker-export', 'ecopower_tracker_export_data');
}
add_action('admin_menu', 'ecopower_tracker_admin_menu');

// Dashboard page
function ecopower_tracker_dashboard() {
    include_once ECOPOWER_TRACKER_DIR . 'templates/dashboard.php';
}

// Manage Data page
function ecopower_tracker_manage_data() {
    include_once ECOPOWER_TRACKER_DIR . 'admin/manage-data.php';
}

// Import Data page
function ecopower_tracker_import_data() {
    include_once ECOPOWER_TRACKER_DIR . 'admin/import.php';
}

// Export Data page
function ecopower_tracker_export_data() {
    include_once ECOPOWER_TRACKER_DIR . 'admin/export.php';
}

// Handle form submission for adding new project
function ecopower_tracker_handle_form_submission() {
    if (isset($_POST['ecopower_tracker_submit'])) {
        global $wpdb;
        $table_name = $wpdb->prefix . 'ecopower_projects';
        $data = [
            'project_company' => sanitize_text_field($_POST['project_company']),
            'project_name' => sanitize_text_field($_POST['project_name']),
            'project_location' => sanitize_text_field($_POST['project_location']),
            'type_of_plant' => sanitize_text_field($_POST['type_of_plant']),
            'cuf' => floatval($_POST['cuf']),
            'generation_capacity' => floatval($_POST['generation_capacity']),
            'date_of_activation' => sanitize_text_field($_POST['date_of_activation'])
        ];
        $wpdb->insert($table_name, $data);
    }
    wp_redirect(admin_url('admin.php?page=ecopower-tracker-manage'));
    exit;
}
add_action('admin_post_ecopower_tracker_add_project', 'ecopower_tracker_handle_form_submission');

// Handle form submission for editing a project
function ecopower_tracker_handle_edit_form_submission() {
    if (isset($_POST['ecopower_tracker_edit_submit'])) {
        global $wpdb;
        $table_name = $wpdb->prefix . 'ecopower_projects';
        $project_number = intval($_POST['project_number']);
        $data = [
            'project_company' => sanitize_text_field($_POST['project_company']),
            'project_name' => sanitize_text_field($_POST['project_name']),
            'project_location' => sanitize_text_field($_POST['project_location']),
            'type_of_plant' => sanitize_text_field($_POST['type_of_plant']),
            'cuf' => floatval($_POST['cuf']),
            'generation_capacity' => floatval($_POST['generation_capacity']),
            'date_of_activation' => sanitize_text_field($_POST['date_of_activation'])
        ];
        $wpdb->update($table_name, $data, ['project_number' => $project_number]);
    }
    wp_redirect(admin_url('admin.php?page=ecopower-tracker-manage'));
    exit;
}
add_action('admin_post_ecopower_tracker_edit_project', 'ecopower_tracker_handle_edit_form_submission');

// Handle project deletion
function ecopower_tracker_handle_delete_project() {
    if (isset($_GET['project_number'])) {
        global $wpdb;
        $table_name = $wpdb->prefix . 'ecopower_projects';
        $project_number = intval($_GET['project_number']);
        $wpdb->delete($table_name, ['project_number' => $project_number]);
    }
    wp_redirect(admin_url('admin.php?page=ecopower-tracker-manage'));
    exit;
}
add_action('admin_post_ecopower_tracker_delete_project', 'ecopower_tracker_handle_delete_project');

// Handle CSV import
function ecopower_tracker_handle_csv_import() {
    if (isset($_POST['ecopower_tracker_import_submit']) && !empty($_FILES['csv_file']['tmp_name'])) {
        $file = $_FILES['csv_file']['tmp_name'];
        $handle = fopen($file, 'r');
        if ($handle) {
            global $wpdb;
            $table_name = $wpdb->prefix . 'ecopower_projects';
            
            // Read the header row
            $header = fgetcsv($handle, 1000, ',');
            // Map the header to expected column names
            $header_map = [
                'Project Company' => 'project_company',
                'Project Name' => 'project_name',
                'Project Location' => 'project_location',
                'Type of Plant' => 'type_of_plant',
                'Project CUF' => 'cuf',
                'Generation Capacity (KW)' => 'generation_capacity',
                'Date of Activation' => 'date_of_activation'
            ];
            
            // Read the data rows
            while (($data = fgetcsv($handle, 1000, ',')) !== FALSE) {
                $mapped_data = [];
                foreach ($header as $index => $column) {
                    if ($column == 'Date of Activation') {
                        // Convert date format from DD/MM/YYYY to YYYY-MM-DD
                        $date = DateTime::createFromFormat('d/m/Y', $data[$index]);
                        $mapped_data[$header_map[$column]] = $date ? $date->format('Y-m-d') : null;
                    } else {
                        $mapped_data[$header_map[$column]] = sanitize_text_field($data[$index]);
                    }
                }
                $wpdb->insert($table_name, $mapped_data);
            }
            fclose($handle);
        }
    }
    wp_redirect(admin_url('admin.php?page=ecopower-tracker-manage'));
    exit;
}
add_action('admin_post_ecopower_tracker_import_csv', 'ecopower_tracker_handle_csv_import');

// Handle CSV export
function ecopower_tracker_handle_csv_export() {
    if (isset($_POST['ecopower_tracker_export_submit'])) {
        global $wpdb;
        $table_name = $wpdb->prefix . 'ecopower_projects';
        $projects = $wpdb->get_results("SELECT * FROM $table_name", ARRAY_A);

        header('Content-Type: text/csv');
        header('Content-Disposition: attachment;filename=ecopower_projects.csv');
        $output = fopen('php://output', 'w');
        fputcsv($output, ['Project#', 'Project Company', 'Project Name', 'Project Location', 'Type of Plant', 'CUF', 'Generation Capacity', 'Date of Activation']);
        foreach ($projects as $project) {
            fputcsv($output, [
                $project['project_company'],
                $project['project_name'],
                $project['project_location'],
                $project['type_of_plant'],
                $project['cuf'],
                $project['generation_capacity'],
                $project['date_of_activation']
            ]);
        }
        fclose($output);
        exit;
    }
}
add_action('admin_post_ecopower_tracker_export_csv', 'ecopower_tracker_handle_csv_export');
?>