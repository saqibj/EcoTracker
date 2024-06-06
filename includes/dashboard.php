<?php
if ( ! defined( 'ABSPATH' ) ) {
    exit; // Exit if accessed directly.
}

function ecopower_tracker_dashboard_content() {
    $projects = ecopower_tracker_get_all_projects();
    ?>
    <div class="wrap">
        <h1><?php _e( 'EcoPower Tracker Dashboard', 'ecopower-tracker' ); ?></h1>
        <table class="wp-list-table widefat fixed striped">
            <thead>
                <tr>
                    <th><?php _e( 'Project Company', 'ecopower-tracker' ); ?></th>
                    <th><?php _e( 'Project Name', 'ecopower-tracker' ); ?></th>
                    <th><?php _e( 'Project Location', 'ecopower-tracker' ); ?></th>
                    <th><?php _e( 'Type of Plant', 'ecopower-tracker' ); ?></th>
                    <th><?php _e( 'Project CUF', 'ecopower-tracker' ); ?></th>
                    <th><?php _e( 'Generation Capacity (KW)', 'ecopower-tracker' ); ?></th>
                    <th><?php _e( 'Date of Activation', 'ecopower-tracker' ); ?></th>
                    <th><?php _e( 'Power Generated (KWh)', 'ecopower-tracker' ); ?></th>
                    <th><?php _e( 'CO2 Offset (kg)', 'ecopower-tracker' ); ?></th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ( $projects as $project ) : 
                    $power_generated = ecopower_tracker_calculate_power_generated( $project );
                    $co2_offset = ecopower_tracker_calculate_co2_offset( $power_generated );
                ?>
                    <tr>
                        <td><?php echo esc_html( $project['project_company'] ); ?></td>
                        <td><?php echo esc_html( $project['project_name'] ); ?></td>
                        <td><?php echo esc_html( $project['project_location'] ); ?></td>
                        <td><?php echo esc_html( $project['type_of_plant'] ); ?></td>
                        <td><?php echo esc_html( $project['project_cuf'] ); ?></td>
                        <td><?php echo esc_html( $project['generation_capacity'] ); ?></td>
                        <td><?php echo esc_html( $project['date_of_activation'] ); ?></td>
                        <td><?php echo number_format( $power_generated, 2, '.', ',' ); ?></td>
                        <td><?php echo number_format( $co2_offset, 2, '.', ',' ); ?></td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>
    <?php
}
?>