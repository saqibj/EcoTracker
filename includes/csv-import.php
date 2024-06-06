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

function ecopower_tracker_display_import_export() {
    ?>
    <h2><?php _e( 'Import/Export Projects', 'ecopower-tracker' ); ?></h2>
    <form method="post" enctype="multipart/form-data">
        <input type="file" name="csv_file" />
        <input type="submit" name="import_csv" class="button-primary" value="<?php _e( 'Import CSV', 'ecopower-tracker' ); ?>" />
    </form>
    <form method="get">
        <input type="hidden" name="page" value="ecopower-tracker" />
        <input type="submit" name="export_csv" class="button-primary" value="<?php _e( 'Export CSV', 'ecopower-tracker' ); ?>" />
    </form>
    <?php
}

add_action( 'admin_menu', 'ecopower_tracker_add_import_export_menu' );

function ecopower_tracker_add_import_export_menu() {
    add_submenu_page(
        'ecopower-tracker',
        __( 'Import/Export Projects', 'ecopower-tracker' ),
        __( 'Import/Export', 'ecopower-tracker' ),
        'manage_options',
        'ecopower-tracker-import-export',
        'ecopower_tracker_display_import_export'
    );
}

add_action( 'admin_init', 'ecopower_tracker_import_csv' );
?>