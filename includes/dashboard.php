<?php
if ( ! defined( 'ABSPATH' ) ) {
    exit; // Exit if accessed directly.
}

function ecopower_tracker_dashboard_content() {
    global $wpdb;
    $table_name = $wpdb->prefix . 'ecopower_projects';

    // Calculate total power generated and CO2 offset
    $projects = $wpdb->get_results( "SELECT * FROM $table_name", ARRAY_A );
    $total_power = 0;
    $total_co2_offset = 0;

    foreach ( $projects as $project ) {
        $activation_date = new DateTime( $project['date_of_activation'] );
        $now = new DateTime();
        $interval = $activation_date->diff( $now );
        $total_hours = $interval->days * 24 + $interval->h;
        $power_generated = $project['project_cuf'] * $project['generation_capacity'] * $total_hours / 1000; // in MWh
        $co2_offset = $power_generated * 0.85; // 0.85 kg CO2 per kWh
        $total_power += $power_generated;
        $total_co2_offset += $co2_offset;
    }
    ?>
    <div class="wrap">
        <h1><?php _e( 'EcoPower Tracker Dashboard', 'ecopower-tracker' ); ?></h1>
        <div style="border: 1px solid #ccc; padding: 10px; margin-bottom: 20px;">
            <p style="font-size: 20px;"><strong><?php _e( 'Total Power Generated:', 'ecopower-tracker' ); ?></strong> <?php echo number_format( $total_power, 2 ) . ' MWh'; ?></p>
            <p style="font-size: 20px;"><strong><?php _e( 'Total CO2 Offset:', 'ecopower-tracker' ); ?></strong> <?php echo number_format( $total_co2_offset, 2 ) . ' kg'; ?></p>
        </div>
        
        <h2><?php _e( 'All Projects', 'ecopower-tracker' ); ?></h2>
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
    </div>
    <?php
}

?>