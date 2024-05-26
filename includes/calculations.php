<?php
// Function to fetch all projects from the database
function get_all_projects() {
    global $wpdb;
    $table_name = $wpdb->prefix . 'ecopower_projects';
    return $wpdb->get_results("SELECT * FROM $table_name", ARRAY_A);
}

// Function to calculate power generated for a project
function ecopower_tracker_calculate_power_generated($generation_capacity, $cuf, $hours) {
    return $generation_capacity * $cuf * $hours;
}

// Function to calculate CO2 offset for a project
function ecopower_tracker_calculate_co2_offset($power_generated, $emission_factor = 0.92) {
    return $power_generated * $emission_factor;
}

// Function to calculate total power generated
function ecopower_tracker_calculate_total_power() {
    $total_power = 0;
    // Fetch all projects from the database
    $projects = get_all_projects();
    foreach ($projects as $project) {
        $generation_capacity = $project['generation_capacity']; // KW
        $cuf = $project['cuf']; // Capacity Utilization Factor
        $hours = (time() - strtotime($project['date_of_activation'])) / 3600; // Total hours since activation
        $total_power += ecopower_tracker_calculate_power_generated($generation_capacity, $cuf, $hours);
    }
    return $total_power;
}

// Function to calculate total carbon offset
function ecopower_tracker_calculate_total_carbon() {
    $total_carbon = 0;
    // Fetch all projects from the database
    $projects = get_all_projects();
    foreach ($projects as $project) {
        $generation_capacity = $project['generation_capacity']; // KW
        $cuf = $project['cuf']; // Capacity Utilization Factor
        $hours = (time() - strtotime($project['date_of_activation'])) / 3600; // Total hours since activation
        $power_generated = ecopower_tracker_calculate_power_generated($generation_capacity, $cuf, $hours);
        $total_carbon += ecopower_tracker_calculate_co2_offset($power_generated);
    }
    return $total_carbon;
}
?>