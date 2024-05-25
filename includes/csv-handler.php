<?php
if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

function ecopower_tracker_import_csv() {
    if ( ! isset( $_FILES['ecopower_tracker_csv'] ) || empty( $_FILES['ecopower_tracker_csv']['tmp_name'] ) ) {
        return;
    }

    $file = fopen( $_FILES['ecopower_tracker_csv']['tmp_name'], 'r' );
    if ( $file === false ) {
        return;
    }

    global $wpdb;
    $table_name = $wpdb->prefix . 'ecopower_tracker';

    while ( ( $row = fgetcsv( $file, 1000, ',' ) ) !== false ) {
        if ( count( $row ) < 8 ) {
            continue;
        }

        $wpdb->insert(
            $table_name,
            array(
                'project_number' => sanitize_text_field( $row[0] ),
                'company' => sanitize_text_field( $row[1] ),
                'name' => sanitize_text_field( $row[2] ),
                'location' => sanitize_text_field( $row[3] ),
                'type' => sanitize_text_field( $row[4] ),
                'cuf' => floatval( $row[5] ),
                'capacity' => floatval( $row[6] ),
                'activation_date' => sanitize_text_field( $row[7] ),
            )
        );
    }

    fclose( $file );
}
?>
