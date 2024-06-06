<?php
if ( ! defined( 'ABSPATH' ) ) {
    exit; // Exit if accessed directly.
}

// Function to display the import/export page
function ecopower_tracker_display_import_export() {
    ?>
    <div class="wrap">
        <h1><?php _e( 'Import/Export Projects', 'ecopower-tracker' ); ?></h1>
        <form method="post" enctype="multipart/form-data">
            <?php wp_nonce_field( 'ecopower_tracker_import_export', 'ecopower_tracker_nonce' ); ?>
            <table class="form-table">
                <tr valign="top">
                    <th scope="row"><?php _e( 'Import Projects', 'ecopower-tracker' ); ?></th>
                    <td>
                        <input type="file" name="ecopower_tracker_import_file" accept=".csv" />
                        <?php submit_button( __( 'Import', 'ecopower-tracker' ), 'primary', 'ecopower_tracker_import' ); ?>
                    </td>
                </tr>
                <tr valign="top">
                    <th scope="row"><?php _e( 'Export Projects', 'ecopower-tracker' ); ?></th>
                    <td>
                        <?php submit_button( __( 'Export', 'ecopower-tracker' ), 'secondary', 'ecopower_tracker_export' ); ?>
                    </td>
                </tr>
            </table>
        </form>
    </div>
    <?php
}

// Function to handle the CSV import
function ecopower_tracker_handle_csv_import() {
    if ( ! empty( $_FILES['ecopower_tracker_import_file']['tmp_name'] ) ) {
        $file = $_FILES['ecopower_tracker_import_file']['tmp_name'];
        $handle = fopen( $file, 'r' );
        if ( $handle ) {
            global $wpdb;
            $table_name = $wpdb->prefix . 'ecopower_projects';

            // Skip the header row
            fgetcsv( $handle, 1000, ',' );

            while ( ( $row = fgetcsv( $handle, 1000, ',' ) ) !== false ) {
                $wpdb->insert(
                    $table_name,
                    [
                        'project_company' => sanitize_text_field( $row[0] ),
                        'project_name' => sanitize_text_field( $row[1] ),
                        'project_location' => sanitize_text_field( $row[2] ),
                        'type_of_plant' => sanitize_text_field( $row[3] ),
                        'project_cuf' => floatval( $row[4] ),
                        'generation_capacity' => floatval( $row[5] ),
                        'date_of_activation' => sanitize_text_field( $row[6] )
                    ]
                );
            }

            fclose( $handle );
            echo '<div class="updated"><p>' . __( 'Projects imported successfully.', 'ecopower-tracker' ) . '</p></div>';
        } else {
            echo '<div class="error"><p>' . __( 'Unable to open the file.', 'ecopower-tracker' ) . '</p></div>';
        }
    } else {
        echo '<div class="error"><p>' . __( 'No file uploaded.', 'ecopower-tracker' ) . '</p></div>';
    }
}

// Function to handle the CSV export
function ecopower_tracker_handle_csv_export() {
    if ( ! current_user_can( 'manage_options' ) ) {
        return;
    }

    global $wpdb;
    $table_name = $wpdb->prefix . 'ecopower_projects';
    $projects = $wpdb->get_results( "SELECT * FROM $table_name", ARRAY_A );

    header( 'Content-Type: text/csv' );
    header( 'Content-Disposition: attachment;filename=projects.csv' );

    $output = fopen( 'php://output', 'w' );
    fputcsv( $output, array( 'Project Company', 'Project Name', 'Project Location', 'Type of Plant', 'Project CUF', 'Generation Capacity', 'Date of Activation' ) );

    foreach ( $projects as $project ) {
        fputcsv( $output, $project );
    }

    fclose( $output );
    exit;
}
?>