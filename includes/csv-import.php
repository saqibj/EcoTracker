<?php
if ( ! defined( 'ABSPATH' ) ) {
    exit; // Exit if accessed directly.
}

function ecopower_tracker_handle_csv_import() {
    if ( ! empty( $_FILES['ecopower_tracker_import_file']['tmp_name'] ) ) {
        $file = $_FILES['ecopower_tracker_import_file']['tmp_name'];
        $handle = fopen( $file, 'r' );
        $not_imported_projects = []; // Initialize an array to track not imported projects

        if ( $handle !== false ) {
            $header = fgetcsv( $handle, 1000, ',' ); // Read and ignore the header row

            while ( ( $data = fgetcsv( $handle, 1000, ',' ) ) !== false ) {
                $project_company = sanitize_text_field( $data[0] );
                $project_name = sanitize_text_field( $data[1] );
                $project_location = sanitize_text_field( $data[2] );
                $type_of_plant = sanitize_text_field( $data[3] );
                $project_cuf = floatval( $data[4] );
                $generation_capacity = floatval( $data[5] );
                $date_of_activation = ecopower_tracker_convert_date( $data[6] );

                if ( $date_of_activation ) {
                    if ( ! ecopower_tracker_project_exists( $project_name, $project_location ) ) {
                        ecopower_tracker_add_project_to_db( $project_company, $project_name, $project_location, $type_of_plant, $project_cuf, $generation_capacity, $date_of_activation );
                    } else {
                        $not_imported_projects[] = $project_name; // Add to not imported list if it exists
                    }
                } else {
                    // Handle invalid date format
                    error_log( "Invalid date format in CSV for project: $project_name" );
                }
            }

            fclose( $handle );

            // Add a message for successful import
            echo '<div class="updated"><p>' . __( 'CSV imported successfully. Check your projects list to verify.', 'ecopower-tracker' ) . '</p></div>';

            // Display the list of not imported projects
            if ( ! empty( $not_imported_projects ) ) {
                echo '<div class="error"><p>' . __( 'The following projects were not imported because they already exist:', 'ecopower-tracker' ) . '</p><ul>';
                foreach ( $not_imported_projects as $project_name ) {
                    echo '<li>' . esc_html( $project_name ) . '</li>';
                }
                echo '</ul></div>';
            }
        } else {
            // Add a message for failed file handling
            echo '<div class="error"><p>' . __( 'Failed to open the uploaded CSV file.', 'ecopower-tracker' ) . '</p></div>';
        }
    }
}

function ecopower_tracker_convert_date( $date_string ) {
    // Check if the date is in a valid format
    $date = date_create_from_format( 'Y-m-d', $date_string );
    if ( $date ) {
        return $date->format( 'Y-m-d' );
    } else {
        // Try to detect and convert common date formats
        $date_formats = ['d/m/Y', 'm/d/Y', 'd-m-Y', 'm-d-Y'];
        foreach ( $date_formats as $format ) {
            $date = date_create_from_format( $format, $date_string );
            if ( $date ) {
                return $date->format( 'Y-m-d' );
            }
        }
    }
    return false; // Invalid date format
}

function ecopower_tracker_project_exists( $project_name, $project_location ) {
    global $wpdb;
    $table_name = $wpdb->prefix . 'ecopower_projects';

    // Check if a project with the same name and location already exists
    $query = $wpdb->prepare( "SELECT COUNT(*) FROM $table_name WHERE project_name = %s AND project_location = %s", $project_name, $project_location );
    $count = $wpdb->get_var( $query );

    return $count > 0; // Return true if a project already exists, false otherwise
}

function ecopower_tracker_add_project_to_db( $project_company, $project_name, $project_location, $type_of_plant, $project_cuf, $generation_capacity, $date_of_activation ) {
    global $wpdb;
    $table_name = $wpdb->prefix . 'ecopower_projects';

    $wpdb->insert(
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
}

?>