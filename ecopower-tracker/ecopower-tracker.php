<?php
/*
Plugin Name: EcoPower Tracker
Description: Calculate and display power generation and carbon offset for wind and solar plants.
Version: 1.0
Author: Your Name
*/

register_activation_hook(__FILE__, 'ecopower_tracker_install');

function ecopower_tracker_install() {
    global $wpdb;
    $table_name = $wpdb->prefix . 'ecopower_projects';
    $charset_collate = $wpdb->get_charset_collate();

    $sql = "CREATE TABLE $table_name (
        id mediumint(9) NOT NULL AUTO_INCREMENT,
        project_company varchar(255) NOT NULL,
        project_name varchar(255) NOT NULL,
        project_location varchar(255) NOT NULL,
        type_of_plant varchar(50) NOT NULL,
        project_cuf float NOT NULL,
        generation_capacity float NOT NULL,
        date_of_activation date NOT NULL,
        PRIMARY KEY (id)
    ) $charset_collate;";

    require_once(ABSPATH . 'wp-admin/includes/upgrade.php');
    dbDelta($sql);
}

add_action('admin_menu', 'ecopower_tracker_menu');

function ecopower_tracker_menu() {
    add_menu_page('EcoPower Tracker', 'EcoPower Tracker', 'manage_options', 'ecopower-tracker', 'ecopower_tracker_admin_page', 'dashicons-admin-generic');
}

function ecopower_tracker_admin_page() {
    ?>
    <div class="wrap">
        <h1>EcoPower Tracker</h1>
        <?php include('admin/form.php'); ?>
    </div>
    <?php
}

if (isset($_POST['submit'])) {
    global $wpdb;
    $table_name = $wpdb->prefix . 'ecopower_projects';

    $wpdb->insert($table_name, array(
        'project_company' => sanitize_text_field($_POST['project_company']),
        'project_name' => sanitize_text_field($_POST['project_name']),
        'project_location' => sanitize_text_field($_POST['project_location']),
        'type_of_plant' => sanitize_text_field($_POST['type_of_plant']),
        'project_cuf' => floatval($_POST['project_cuf']),
        'generation_capacity' => floatval($_POST['generation_capacity']),
        'date_of_activation' => sanitize_text_field($_POST['date_of_activation']),
    ));
}

function calculate_power_generated($date_of_activation, $cuf, $capacity) {
    $days_active = (strtotime(current_time('mysql')) - strtotime($date_of_activation)) / (60 * 60 * 24);
    return $days_active * $capacity * 24 * $cuf;
}

function calculate_carbon_offset($power_generated) {
    $carbon_factor = 0.85; // Average tons of CO2 per MWh
    return $power_generated * $carbon_factor / 1000;
}

add_shortcode('ecopower_total_power', 'display_total_power');
add_shortcode('ecopower_total_carbon', 'display_total_carbon');

function display_total_power() {
    global $wpdb;
    $table_name = $wpdb->prefix . 'ecopower_projects';
    $projects = $wpdb->get_results("SELECT * FROM $table_name");

    $total_power = 0;
    foreach ($projects as $project) {
        $total_power += calculate_power_generated($project->date_of_activation, $project->project_cuf, $project->generation_capacity);
    }

    return number_format($total_power, 2);
}

function display_total_carbon() {
    global $wpdb;
    $table_name = $wpdb->prefix . 'ecopower_projects';
    $projects = $wpdb->get_results("SELECT * FROM $table_name");

    $total_carbon = 0;
    foreach ($projects as $project) {
        $power_generated = calculate_power_generated($project->date_of_activation, $project->project_cuf, $project->generation_capacity);
        $total_carbon += calculate_carbon_offset($power_generated);
    }

    return number_format($total_carbon, 2);
}

function export_projects_csv() {
    if (isset($_GET['export']) && $_GET['export'] == 'csv') {
        header('Content-Type: text/csv');
        header('Content-Disposition: attachment; filename="projects.csv"');

        $output = fopen('php://output', 'w');
        fputcsv($output, array('Project#', 'Project Company', 'Project Name', 'Project Location', 'Type of Plant', 'Project CUF', 'Generation Capacity', 'Date of Activation'));

        global $wpdb;
        $table_name = $wpdb->prefix . 'ecopower_projects';
        $projects = $wpdb->get_results("SELECT * FROM $table_name", ARRAY_A);

        foreach ($projects as $project) {
            fputcsv($output, $project);
        }
        fclose($output);
        exit;
    }
}
add_action('admin_init', 'export_projects_csv');
