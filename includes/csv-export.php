<?php
if ( ! defined( 'ABSPATH' ) ) {
    exit; // Exit if accessed directly.
}

function ecopower_tracker_export_csv() {
    if ( isset( $_GET['export_csv'] ) ) {
        global $wpdb;
        $table_name = $wpdb->prefix . 'ecopower_projects';
        $projects = $wpdb->get_results( "SELECT * FROM $table_name", ARRAY_A );

        if ( ! empty( $projects ) ) {
            header( 'Content-Type: text/csv; charset=utf-8' );
            header( 'Content-Disposition: attachment; filename=ecopower_projects.csv' );

            $output = fopen( 'php://output', 'w' );

            // Output the column headings
            fputcsv( $output, [ 'Project Company', 'Project Name', 'Project Location', 'Type of Plant', 'Project CUF', 'Generation Capacity (KW)', 'Date of Activation' ] );

            // Loop over the rows, outputting them
            foreach ( $projects as $project ) {
                fputcsv( $output, $project );
            }

            fclose( $output );
            exit;
        }
    }
}

add_action( 'admin_init', 'ecopower_tracker_export_csv' );
?>