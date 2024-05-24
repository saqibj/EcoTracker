<?php
if ( ! defined( 'ABSPATH' ) ) {
    exit; // Exit if accessed directly
}

$options = get_option( 'ecopower_tracker_options' );

if ( ! $options ) {
    echo '<div class="wrap"><h1>EcoPower Tracker Data</h1><p>No project data available.</p></div>';
    return;
}

$projects = array($options);

echo '<div class="wrap"><h1>EcoPower Tracker Data</h1>';
echo '<table class="widefat fixed" cellspacing="0">';
echo '<thead><tr>';
echo '<th>Project Company</th>';
echo '<th>Project Name</th>';
echo '<th>Project Location</th>';
echo '<th>Type of Plant</th>';
echo '<th>Project CUF</th>';
echo '<th>Generation Capacity (KW)</th>';
echo '<th>Date of Activation</th>';
echo '</tr></thead>';
echo '<tbody>';

foreach ( $projects as $project ) {
    echo '<tr>';
    echo '<td>' . esc_html( $project['project_company'] ) . '</td>';
    echo '<td>' . esc_html( $project['project_name'] ) . '</td>';
    echo '<td>' . esc_html( $project['project_location'] ) . '</td>';
    echo '<td>' . esc_html( $project['type_of_plant'] ) . '</td>';
    echo '<td>' . esc_html( $project['project_cuf'] ) . '</td>';
    echo '<td>' . esc_html( $project['generation_capacity'] ) . '</td>';
    echo '<td>' . esc_html( $project['date_of_activation'] ) . '</td>';
    echo '</tr>';
}

echo '</tbody></table></div>';
?>
