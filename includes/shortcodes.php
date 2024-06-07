<?php
if ( ! defined( 'ABSPATH' ) ) {
    exit; // Exit if accessed directly.
}

// Shortcode to display a table of all projects
function ecopower_tracker_projects_shortcode() {
    global $wpdb;
    $table_name = $wpdb->prefix . 'ecopower_projects';
    $projects = $wpdb->get_results( "SELECT * FROM $table_name", ARRAY_A );

    ob_start(); // Start output buffering
    ?>
    <table class="widefat">
        <thead>
            <tr>
                <th><?php _e( 'Project #', 'ecopower-tracker' ); ?></th>
                <th><?php _e( 'Project Company', 'ecopower-tracker' ); ?></th>
                <th><?php _e( 'Project Name', 'ecopower-tracker' ); ?></th>
                <th><?php _e( 'Project Location', 'ecopower-tracker' ); ?></th>
                <th><?php _e( 'Type of Plant', 'ecopower-tracker' ); ?></th>
                <th><?php _e( 'Project CUF', 'ecopower-tracker' ); ?></th>
                <th><?php _e( 'Generation Capacity (KW)', 'ecopower-tracker' ); ?></th>
                <th><?php _e( 'Date of Activation', 'ecopower-tracker' ); ?></th>
            </tr>
        </thead>
        <tbody>
            <?php
            if ( ! empty( $projects ) ) {
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
            } else {
                echo '<tr><td colspan="8">' . __( 'No projects found.', 'ecopower-tracker' ) . '</td></tr>';
            }
            ?>
        </tbody>
    </table>
    <?php
    return ob_get_clean(); // Return the buffered output
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

// Shortcode to display the total CO2 offset
function ecopower_tracker_total_co2_shortcode() {
    global $wpdb;
    $table_name = $wpdb->prefix . 'ecopower_projects';
    $projects = $wpdb->get_results( "SELECT * FROM $table_name", ARRAY_A );

    $total_co2_offset = 0;

    foreach ( $projects as $project ) {
        $activation_date = new DateTime( $project['date_of_activation'] );
        $now = new DateTime();
        $interval = $activation_date->diff( $now );
        $total_hours = $interval->days * 24 + $interval->h;
        $power_generated = $project['project_cuf'] * $project['generation_capacity'] * $total_hours / 1000; // in MWh
        $co2_offset = $power_generated * 0.85; // 0.85 kg CO2 per kWh
        $total_co2_offset += $co2_offset;
    }

    return number_format( $total_co2_offset, 2 ) . ' kg';
}

// Shortcode to display just the total power generated (number only)
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

    return number_format( $total_power, 2 ); // Return only the number
}

// Shortcode to display just the total CO2 offset (number only)
function ecopower_tracker_total_co2_number_shortcode() {
    global $wpdb;
    $table_name = $wpdb->prefix . 'ecopower_projects';
    $projects = $wpdb->get_results( "SELECT * FROM $table_name", ARRAY_A );

    $total_co2_offset = 0;

    foreach ( $projects as $project ) {
        $activation_date = new DateTime( $project['date_of_activation'] );
        $now = new DateTime();
        $interval = $activation_date->diff( $now );
        $total_hours = $interval->days * 24 + $interval->h;
        $power_generated = $project['project_cuf'] * $project['generation_capacity'] * $total_hours / 1000; // in MWh
        $co2_offset = $power_generated * 0.85; // 0.85 kg CO2 per kWh
        $total_co2_offset += $co2_offset;
    }

    return number_format( $total_co2_offset, 2 ); // Return only the number
}

// Register shortcodes
function ecopower_tracker_register_shortcodes() {
    add_shortcode( 'ecopower_tracker_projects', 'ecopower_tracker_projects_shortcode' );
    add_shortcode( 'ecopower_tracker_total_power', 'ecopower_tracker_total_power_shortcode' );
    add_shortcode( 'ecopower_tracker_total_co2', 'ecopower_tracker_total_co2_shortcode' );
    add_shortcode( 'ecopower_tracker_total_power_number', 'ecopower_tracker_total_power_number_shortcode' );
    add_shortcode( 'ecopower_tracker_total_co2_number', 'ecopower_tracker_total_co2_number_shortcode' );
}
add_action( 'init', 'ecopower_tracker_register_shortcodes' );

?>