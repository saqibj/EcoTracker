<?php
if ( ! defined( 'ABSPATH' ) ) {
    exit; // Exit if accessed directly.
}

function ecopower_tracker_process_csv( $uploaded_file ) {
    $csv_file = fopen( $uploaded_file, 'r' );

    if (!$csv_file) {
        add_action( 'admin_notices', function() {
            echo '<div class="notice notice-error is-dismissible"><p>' . esc_html__( 'CSV import failed. Could not open file.', 'ecopower-tracker' ) . '</p></div>';
        } );
        error_log( 'CSV import failed. Could not open file.' );
        return;
    }

    // Skip the header row
    $header = fgetcsv( $csv_file );
    if ($header === false) {
        add_action( 'admin_notices', function() {
            echo '<div class="notice notice-error is-dismissible"><p>' . esc_html__( 'CSV import failed. The file is empty or not readable.', 'ecopower-tracker' ) . '</p></div>';
        } );
        error_log( 'CSV import failed. The file is empty or not readable.' );
        fclose($csv_file);
        return;
    }

    global $wpdb;
    $table_name = $wpdb->prefix . 'ecopower_projects';
    $row = 1;
    $success_count = 0;
    $error_count = 0;

    while ( ( $data = fgetcsv( $csv_file ) ) !== FALSE ) {
        error_log( 'Processing row ' . $row . ': ' . print_r($data, true) );
        if ( count( $data ) != 7 ) {
            error_log( 'Row ' . $row . ': Incorrect number of columns. Expected 7, found ' . count( $data ) );
            $error_count++;
            continue;
        }

        // Ensure data is not empty before inserting
        $project_company = sanitize_text_field( $data[0] );
        $project_name = sanitize_text_field( $data[1] );
        $project_location = sanitize_text_field( $data[2] );
        $type_of_plant = sanitize_text_field( $data[3] );
        $project_cuf = floatval( $data[4] );
        $generation_capacity = floatval( $data[5] );
        $date_of_activation = sanitize_text_field( $data[6] );

        if ( !empty($project_company) && !empty($project_name) && !empty($project_location) && !empty($type_of_plant) && !empty($project_cuf) && !empty($generation_capacity) && !empty($date_of_activation) ) {
            $result = $wpdb->insert(
                $table_name,
                [
                    'project_company' => $project_company,
                    'project_name' => $project_name,
                    'project_location' => $project_location,
                    'type_of_plant' => $type_of_plant,
                    'project_cuf' => $project_cuf,
                    'generation_capacity' => $generation_capacity,
                    'date_of_activation' => $date_of_activation
                ]
            );

            // Add debug logging
            if ( false === $result ) {
                error_log( 'Row ' . $row . ': Error inserting project data: ' . $wpdb->last_error );
                $error_count++;
            } else {
                error_log( 'Row ' . $row . ': Successfully inserted project data.' );
                $success_count++;
            }
        } else {
            error_log( 'Row ' . $row . ': Missing data, skipping row. Data: ' . print_r($data, true) );
            $error_count++;
        }

        $row++;
    }

    fclose( $csv_file );

    // Set an admin notice based on the results
    if ( $success_count > 0 && $error_count == 0 ) {
        add_action( 'admin_notices', function() {
            echo '<div class="notice notice-success is-dismissible"><p>' . esc_html__( 'CSV import successful.', 'ecopower-tracker' ) . '</p></div>';
        } );
    } elseif ( $success_count > 0 && $error_count > 0 ) {
        add_action( 'admin_notices', function() use ( $success_count, $error_count ) {
            echo '<div class="notice notice-warning is-dismissible"><p>' . esc_html__( 'CSV import partially successful. ', 'ecopower-tracker' ) . $success_count . esc_html__( ' rows imported, ', 'ecopower-tracker' ) . $error_count . esc_html__( ' rows failed.', 'ecopower-tracker' ) . '</p></div>';
        } );
    } else {
        add_action( 'admin_notices', function() {
            echo '<div class="notice notice-error is-dismissible"><p>' . esc_html__( 'CSV import failed. No data was imported.', 'ecopower-tracker' ) . '</p></div>';
        } );
    }

    wp_redirect( admin_url( 'admin.php?page=ecopower-tracker-all-projects' ) );
    exit;
}
?>