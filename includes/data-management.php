<?php
if ( ! defined( 'ABSPATH' ) ) {
    exit; // Exit if accessed directly.
}

// Function to handle data CRUD operations
function ecopower_tracker_create_project( $data ) {
    global $wpdb;
    $table_name = $wpdb->prefix . 'ecopower_projects';

    $result = $wpdb->insert(
        $table_name,
        [
            'project_company' => sanitize_text_field( $data['project_company'] ),
            'project_name' => sanitize_text_field( $data['project_name'] ),
            'project_location' => sanitize_text_field( $data['project_location'] ),
            'type_of_plant' => sanitize_text_field( $data['type_of_plant'] ),
            'project_cuf' => floatval( $data['project_cuf'] ),
            'generation_capacity' => floatval( $data['generation_capacity'] ),
            'date_of_activation' => sanitize_text_field( $data['date_of_activation'] )
        ]
    );

    return $result !== false;
}

function ecopower_tracker_update_project( $id, $data ) {
    global $wpdb;
    $table_name = $wpdb->prefix . 'ecopower_projects';

    $result = $wpdb->update(
        $table_name,
        [
            'project_company' => sanitize_text_field( $data['project_company'] ),
            'project_name' => sanitize_text_field( $data['project_name'] ),
            'project_location' => sanitize_text_field( $data['project_location'] ),
            'type_of_plant' => sanitize_text_field( $data['type_of_plant'] ),
            'project_cuf' => floatval( $data['project_cuf'] ),
            'generation_capacity' => floatval( $data['generation_capacity'] ),
            'date_of_activation' => sanitize_text_field( $data['date_of_activation'] )
        ],
        [ 'id' => intval( $id ) ]
    );

    return $result !== false;
}

function ecopower_tracker_delete_project( $id ) {
    global $wpdb;
    $table_name = $wpdb->prefix . 'ecopower_projects';

    $result = $wpdb->delete( $table_name, [ 'id' => intval( $id ) ] );

    return $result !== false;
}

function ecopower_tracker_get_project( $id ) {
    global $wpdb;
    $table_name = $wpdb->prefix . 'ecopower_projects';

    $project = $wpdb->get_row( $wpdb->prepare( "SELECT * FROM $table_name WHERE id = %d", $id ), ARRAY_A );

    return $project;
}

function ecopower_tracker_get_all_projects() {
    global $wpdb;
    $table_name = $wpdb->prefix . 'ecopower_projects';

    $projects = $wpdb->get_results( "SELECT * FROM $table_name", ARRAY_A );

    return $projects;
}

// Function to calculate total power generated
function ecopower_tracker_calculate_power_generated( $project ) {
    $current_date = new DateTime();
    $activation_date = new DateTime( $project['date_of_activation'] );
    $interval = $activation_date->diff( $current_date );
    $total_hours = ( $interval->days * 24 ) + $interval->h;
    $power_generated = $project['project_cuf'] * $project['generation_capacity'] * $total_hours;

    return $power_generated;
}

// Function to calculate CO2 offset
function ecopower_tracker_calculate_co2_offset( $power_generated ) {
    $co2_offset = $power_generated * 0.85; // CO2 offset in kilograms

    return $co2_offset;
}
?>