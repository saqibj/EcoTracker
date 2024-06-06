<?php
if ( ! defined( 'ABSPATH' ) ) {
    exit; // Exit if accessed directly.
}

function ecopower_tracker_import_csv() {
    if ( isset( $_POST['import_csv'] ) && ! empty( $_FILES['csv_file']['tmp_name'] ) ) {
        $uploads_dir = plugin_dir_path( __FILE__ ) . '../uploads/';
        if ( ! file_exists( $uploads_dir ) ) {
            mkdir( $uploads_dir, 0755, true );
            error_log( 'Created uploads directory: ' . $uploads_dir );
        }

        $uploaded_file = $uploads_dir . basename( $_FILES['csv_file']['name'] );

        if ( move_uploaded_file( $_FILES['csv_file']['tmp_name'], $uploaded_file ) ) {
            error_log( 'File uploaded successfully to: ' . $uploaded_file );
            ecopower_tracker_process_csv( $uploaded_file );
        } else {
            add_action( 'admin_notices', function() {
                echo '<div class="notice notice-error is-dismissible"><p>' . esc_html__( 'CSV import failed. Could not move file to uploads directory.', 'ecopower-tracker' ) . '</p></div>';
            } );
            error_log( 'CSV import failed. Could not move file to uploads directory.' );
        }
    }
}

add_action( 'admin_init', 'ecopower_tracker_import_csv' );
?>