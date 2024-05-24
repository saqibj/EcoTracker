<?php
if ( ! defined( 'ABSPATH' ) ) {
    exit; // Exit if accessed directly.
}

$projects = get_option( 'ecopower_projects', array() );

echo '<div class="wrap"><h1>Project Data</h1>';
if ( empty( $projects ) ) {
    echo '<p>No project data available.</p>';
} else {
    echo '<table class="widefat fixed" cellspacing="0"><thead><tr>';
    echo '<th>Project Company</th><th>Project Name</th><th>Project Location</th><th>Type of Plant</th><th>Project CUF</th><th>Generation Capacity (KW)</th><th>Date of Activation</th>';
    echo '</tr></thead><tbody>';

    foreach ( $projects as $project ) {
        if (is_array($project)) { // Ensure $project is an array
            echo '<tr>';
            echo '<td>' . esc_html( $project['Project Company'] ) . '</td>';
            echo '<td>' . esc_html( $project['Project Name'] ) . '</td>';
            echo '<td>' . esc_html( $project['Project Location'] ) . '</td>';
            echo '<td>' . esc_html( $project['Type of Plant'] ) . '</td>';
            echo '<td>' . esc_html( $project['Project CUF'] ) . '</td>';
            echo '<td>' . esc_html( $project['Generation Capacity (KW)'] ) . '</td>';
            echo '<td>' . esc_html( $project['Date of Activation'] ) . '</td>';
            echo '</tr>';
        }
    }

    echo '</tbody></table>';
}
echo '</div>';
?>
