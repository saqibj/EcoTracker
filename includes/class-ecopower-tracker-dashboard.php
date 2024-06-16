// Path: EcoPower-Tracker/includes/class-ecopower-tracker-dashboard.php
// File: class-ecopower-tracker-dashboard.php

<?php

if (!defined('ABSPATH')) {
    exit; // Exit if accessed directly
}

class EcoPower_Tracker_Dashboard {

    public function __construct() {
        // Hook to display the dashboard page
        add_action('admin_menu', array($this, 'setup_dashboard_page'));
    }

    // Function to set up the dashboard page
    public function setup_dashboard_page() {
        // Add a new submenu under the main EcoPower Tracker menu
        add_submenu_page(
            'ecopower-tracker',               // Parent slug
            __('Dashboard', 'ecopower-tracker'), // Page title
            __('Dashboard', 'ecopower-tracker'), // Menu title
            'manage_options',                 // Capability
            'ecopower-tracker',               // Menu slug
            array($this, 'render_dashboard')  // Callback function
        );
    }

    // Function to render the dashboard page
    public function render_dashboard() {
        // Include the dashboard template
        include plugin_dir_path(__FILE__) . '../templates/admin/dashboard.php';
    }

    // Function to display the total power generated and CO2 offset
    public static function display_totals() {
        global $wpdb;

        // Calculate total power generated and total CO2 offset
        $total_power = $wpdb->get_var("SELECT SUM(generation_capacity) FROM {$wpdb->prefix}ecopower_tracker_projects");
        $total_co2_offset = self::calculate_total_co2_offset($total_power);

        // Display totals in a formatted way
        echo '<div class="ecopower-tracker-totals">';
        echo '<h2>' . __('Total Power Generated: ', 'ecopower-tracker') . number_format($total_power) . ' KWs</h2>';
        echo '<h2>' . __('Total CO2 Offset: ', 'ecopower-tracker') . number_format($total_co2_offset) . ' tons</h2>';
        echo '</div>';
    }

    // Function to calculate total CO2 offset
    private static function calculate_total_co2_offset($total_power) {
        // Example conversion factor: 1 KW = 0.001 tons of CO2 offset (adjust as needed)
        $conversion_factor = 0.001;
        return $total_power * $conversion_factor;
    }

    // Function to list all projects with basic details
    public static function list_projects() {
        global $wpdb;

        // Retrieve all projects from the database
        $projects = $wpdb->get_results("SELECT * FROM {$wpdb->prefix}ecopower_tracker_projects");

        // Display projects in a table
        echo '<table class="widefat fixed" cellspacing="0">';
        echo '<thead><tr>';
        echo '<th>' . __('Project Number', 'ecopower-tracker') . '</th>';
        echo '<th>' . __('Project Company', 'ecopower-tracker') . '</th>';
        echo '<th>' . __('Project Name', 'ecopower-tracker') . '</th>';
        echo '<th>' . __('Project Location', 'ecopower-tracker') . '</th>';
        echo '<th>' . __('Type of Plant', 'ecopower-tracker') . '</th>';
        echo '<th>' . __('Generation Capacity (KWs)', 'ecopower-tracker') . '</th>';
        echo '<th>' . __('Date of Activation', 'ecopower-tracker') . '</th>';
        echo '<th>' . __('Actions', 'ecopower-tracker') . '</th>';
        echo '</tr></thead>';
        echo '<tbody>';

        foreach ($projects as $project) {
            echo '<tr>';
            echo '<td>' . esc_html($project->project_number) . '</td>';
            echo '<td>' . esc_html($project->project_company) . '</td>';
            echo '<td>' . esc_html($project->project_name) . '</td>';
            echo '<td>' . esc_html($project->project_location) . '</td>';
            echo '<td>' . esc_html($project->type_of_plant) . '</td>';
            echo '<td>' . number_format($project->generation_capacity) . '</td>';
            echo '<td>' . esc_html(date('Y-m-d', strtotime($project->date_of_activation))) . '</td>';
            echo '<td>';
            echo '<a href="' . admin_url('admin.php?page=ecopower-tracker-edit&project_id=' . $project->id) . '">' . __('Edit', 'ecopower-tracker') . '</a> | ';
            echo '<a href="' . admin_url('admin.php?page=ecopower-tracker-delete&project_id=' . $project->id) . '" onclick="return confirm(\'' . __('Are you sure you want to delete this project?', 'ecopower-tracker') . '\')">' . __('Delete', 'ecopower-tracker') . '</a>';
            echo '</td>';
            echo '</tr>';
        }

        echo '</tbody></table>';
    }
}

// Initialize the dashboard functionalities
new EcoPower_Tracker_Dashboard();

?>