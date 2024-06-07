<?php
if ( ! defined( 'ABSPATH' ) ) {
    exit; // Exit if accessed directly.
}

// Shortcode to display the total power generated
function ecopower_tracker_total_power_shortcode() {
    global $wpdb;
    $table_name = $wpdb->prefix . 'ecopower_projects';
    $projects = $wpdb->get_results( "SELECT * FROM $table_name", ARRAY_A );
    $total_power = 0;

    foreach ( $projects as $project ) {
        $activation_date = new DateTime( $project['date_of_activation'] );
        $now = new DateTime();
        $interval = $activation_date->diff( $now );
        $total_hours = $interval->days * 24 + $interval->h;
        $power_generated = $project['project_cuf'] * $project['generation_capacity'] * $total_hours / 1000; // in MWh
        $total_power += $power_generated;
    }
    
    return number_format( $total_power, 2 ) . ' MWh';
}
add_shortcode( 'ecopower_tracker_total_power', 'ecopower_tracker_total_power_shortcode' );

// Shortcode to display the total CO2 offset
function ecopower_tracker_total_co2_shortcode() {
    global $wpdb;
    $table_name = $wpdb->prefix . 'ecopower_projects';
    $projects = $wpdb->get_results( "SELECT * FROM $table_name", ARRAY_A );
    $total_power = 0;

    foreach ( $projects as $project ) {
        $activation_date = new DateTime( $project['date_of_activation'] );
        $now = new DateTime();
        $interval = $activation_date->diff( $now );
        $total_hours = $interval->days * 24 + $interval->h;
        $power_generated = $project['project_cuf'] * $project['generation_capacity'] * $total_hours / 1000; // in MWh
        $total_power += $power_generated;
    }

    $total_co2_offset = $total_power * 0.85; // CO2 offset in kg
    return number_format( $total_co2_offset, 2 ) . ' kg';
}
add_shortcode( 'ecopower_tracker_total_co2', 'ecopower_tracker_total_co2_shortcode' );

// Shortcode to display the table of all projects
function ecopower_tracker_projects_shortcode() {
    global $wpdb;
    $table_name = $wpdb->prefix . 'ecopower_projects';
    $projects = $wpdb->get_results( "SELECT * FROM $table_name", ARRAY_A );

    if ( empty( $projects ) ) {
        return __( 'No projects found.', 'ecopower-tracker' );
    }

    ob_start();
    echo '<table class="widefat">';
    echo '<thead>';
    echo '<tr><th>' . __( 'Project #', 'ecopower-tracker' ) . '</th><th>' . __( 'Project Company', 'ecopower-tracker' ) . '</th><th>' . __( 'Project Name', 'ecopower-tracker' ) . '</th><th>' . __( 'Project Location', 'ecopower-tracker' ) . '</th><th>' . __( 'Type of Plant', 'ecopower-tracker' ) . '</th><th>' . __( 'Project CUF', 'ecopower-tracker' ) . '</th><th>' . __( 'Generation Capacity (KW)', 'ecopower-tracker' ) . '</th><th>' . __( 'Date of Activation', 'ecopower-tracker' ) . '</th></tr>';
    echo '</thead>';
    echo '<tbody>';

    foreach ( $projects as $project ) {
        echo '<tr>';
        echo '<td>' . esc_html( $project['id'] ) . '</td>';
        echo '<td>' . esc_html( $project['project_company'] ) . '</td>';
        echo '<td>' . esc_html( $project['project_name'] ) . '</td>';
        echo '<td>' . esc_html( $project['project_location'] ) . '</td>';
        echo '<td>' . esc_html( $project['type_of_plant'] ) . '</td>';
        echo '<td>' . esc_html( $project['project_cuf'] ) . '</td>';
        echo '<td>' . esc_html( $project['generation_capacity'] ) . '</td>';
        echo '<td>' . esc_html( $project['date_of_activation'] ) . '</td>';
        echo '</tr>';
    }

    echo '</tbody>';
    echo '</table>';
    return ob_get_clean();
}
add_shortcode( 'ecopower_tracker_projects', 'ecopower_tracker_projects_shortcode' );

// Shortcode to display the total power generated as a number
function ecopower_tracker_total_power_number_shortcode() {
    global $wpdb;
    $table_name = $wpdb->prefix . 'ecopower_projects';
    $projects = $wpdb->get_results( "SELECT * FROM $table_name", ARRAY_A );
    $total_power = 0;

    foreach ( $projects as $project ) {
        $activation_date = new DateTime( $project['date_of_activation'] );
        $now = new DateTime();
        $interval = $activation_date->diff( $now );
        $total_hours = $interval->days * 24 + $interval->h;
        $power_generated = $project['project_cuf'] * $project['generation_capacity'] * $total_hours / 1000; // in MWh
        $total_power += $power_generated;
    }
    
    return number_format( $total_power, 2 );
}
add_shortcode( 'ecopower_tracker_total_power_number', 'ecopower_tracker_total_power_number_shortcode' );

// Shortcode to display the total CO2 offset as a number
function ecopower_tracker_total_co2_number_shortcode() {
    global $wpdb;
    $table_name = $wpdb->prefix . 'ecopower_projects';
    $projects = $wpdb->get_results( "SELECT * FROM $table_name", ARRAY_A );
    $total_power = 0;

    foreach ( $projects as $project ) {
        $activation_date = new DateTime( $project['date_of_activation'] );
        $now = new DateTime();
        $interval = $activation_date->diff( $now );
        $total_hours = $interval->days * 24 + $interval->h;
        $power_generated = $project['project_cuf'] * $project['generation_capacity'] * $total_hours / 1000; // in MWh
        $total_power += $power_generated;
    }

    $total_co2_offset = $total_power * 0.85; // CO2 offset in kg
    return number_format( $total_co2_offset, 2 );
}
add_shortcode( 'ecopower_tracker_total_co2_number', 'ecopower_tracker_total_co2_number_shortcode' );

// Shortcode to display the total generation capacity
function ecopower_tracker_total_capacity_shortcode() {
    global $wpdb;
    $table_name = $wpdb->prefix . 'ecopower_projects';
    $projects = $wpdb->get_results( "SELECT * FROM $table_name", ARRAY_A );
    $total_capacity = 0;

    foreach ( $projects as $project ) {
        $total_capacity += $project['generation_capacity'];
    }
    
    return number_format( $total_capacity, 2 ) . ' KW';
}
add_shortcode( 'ecopower_tracker_total_capacity', 'ecopower_tracker_total_capacity_shortcode' );

// Shortcode to display the generation capacity for a specific project
function ecopower_tracker_project_capacity_shortcode( $atts ) {
    global $wpdb;
    $atts = shortcode_atts( array( 'project_name' => '' ), $atts, 'ecopower_tracker_project_capacity' );

    if ( empty( $atts['project_name'] ) ) {
        return __( 'No project specified.', 'ecopower-tracker' );
    }

    $table_name = $wpdb->prefix . 'ecopower_projects';
    $project = $wpdb->get_row( $wpdb->prepare( "SELECT * FROM $table_name WHERE project_name = %s", $atts['project_name'] ), ARRAY_A );

    if ( ! $project ) {
        return __( 'Project not found.', 'ecopower-tracker' );
    }

    return number_format( $project['generation_capacity'], 2 ) . ' KW';
}
add_shortcode( 'ecopower_tracker_project_capacity', 'ecopower_tracker_project_capacity_shortcode' );

// Shortcode to display the total generation capacity grouped by company
function ecopower_tracker_company_capacity_shortcode( $atts ) {
    global $wpdb;
    $atts = shortcode_atts( array( 'company_name' => '' ), $atts, 'ecopower_tracker_company_capacity' );

    if ( empty( $atts['company_name'] ) ) {
        return __( 'No company specified.', 'ecopower-tracker' );
    }

    $table_name = $wpdb->prefix . 'ecopower_projects';
    $projects = $wpdb->get_results( $wpdb->prepare( "SELECT * FROM $table_name WHERE project_company = %s", $atts['company_name'] ), ARRAY_A );

    if ( empty( $projects ) ) {
        return __( 'No projects found for this company.', 'ecopower-tracker' );
    }

    $total_capacity = 0;

    foreach ( $projects as $project ) {
        $total_capacity += $project['generation_capacity'];
    }

    return number_format( $total_capacity, 2 ) . ' KW';
}
add_shortcode( 'ecopower_tracker_company_capacity', 'ecopower_tracker_company_capacity_shortcode' );

// Shortcode to display the total generation capacity grouped by location
function ecopower_tracker_location_capacity_shortcode( $atts ) {
    global $wpdb;
    $atts = shortcode_atts( array( 'location' => '' ), $atts, 'ecopower_tracker_location_capacity' );

    if ( empty( $atts['location'] ) ) {
        return __( 'No location specified.', 'ecopower-tracker' );
    }

    $table_name = $wpdb->prefix . 'ecopower_projects';
    $projects = $wpdb->get_results( $wpdb->prepare( "SELECT * FROM $table_name WHERE project_location = %s", $atts['location'] ), ARRAY_A );

    if ( empty( $projects ) ) {
        return __( 'No projects found for this location.', 'ecopower-tracker' );
    }

    $total_capacity = 0;

    foreach ( $projects as $project ) {
        $total_capacity += $project['generation_capacity'];
    }

    return number_format( $total_capacity, 2 ) . ' KW';
}
add_shortcode( 'ecopower_tracker_location_capacity', 'ecopower_tracker_location_capacity_shortcode' );

// Shortcode to display the total generation capacity grouped by type of plant
function ecopower_tracker_type_capacity_shortcode( $atts ) {
    global $wpdb;
    $atts = shortcode_atts( array( 'plant_type' => '' ), $atts, 'ecopower_tracker_type_capacity' );

    if ( empty( $atts['plant_type'] ) ) {
        return __( 'No plant type specified.', 'ecopower-tracker' );
    }

    $table_name = $wpdb->prefix . 'ecopower_projects';
    $projects = $wpdb->get_results( $wpdb->prepare( "SELECT * FROM $table_name WHERE type_of_plant = %s", $atts['plant_type'] ), ARRAY_A );

    if ( empty( $projects ) ) {
        return __( 'No projects found for this plant type.', 'ecopower-tracker' );
    }

    $total_capacity = 0;

    foreach ( $projects as $project ) {
        $total_capacity += $project['generation_capacity'];
    }

    return number_format( $total_capacity, 2 ) . ' KW';
}
add_shortcode( 'ecopower_tracker_type_capacity', 'ecopower_tracker_type_capacity_shortcode' );

?>