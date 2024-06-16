<?php
if ( ! defined( 'ABSPATH' ) ) {
    exit; // Exit if accessed directly.
}

function ecopower_tracker_export_page() {
    if ( isset( $_POST['ecopower_tracker_export_submit'] ) && check_admin_referer( 'ecopower_tracker_export_action', 'ecopower_tracker_export_nonce' ) ) {
        global $wpdb;
        $table_name = $wpdb->prefix . 'ecopower_projects';
        $projects = $wpdb->get_results( "SELECT * FROM $table_name", ARRAY_A );

        if ( $projects ) {
            // Create a CSV file
            $csv_file_path = plugin_dir_path( __FILE__ ) . '../uploads/ecopower_projects_export.csv';
            $csv_file = fopen( $csv_file_path, 'w' );

            // Add the header
            $header = array( 'Project Company', 'Project Name', 'Project Location', 'Type of Plant', 'Project CUF', 'Generation Capacity (KW)', 'Date of Activation' );
            fputcsv( $csv_file, $header );

            // Add the data
            foreach ( $projects as $project ) {
                $row = array(
                    $project['project_company'],
                    $project['project_name'],
                    $project['project_location'],
                    $project['type_of_plant'],
                    $project['project_cuf'],
                    $project['generation_capacity'],
                    $project['date_of_activation']
                );
                fputcsv( $csv_file, $row );
            }
            fclose( $csv_file );

            // Provide the download link
            echo '<div class="notice notice-success is-dismissible"><p>' . __( 'CSV file created successfully. ', 'ecopower-tracker' ) . '<a href="' . plugin_dir_url( __FILE__ ) . '../uploads/ecopower_projects_export.csv" download>' . __( 'Download CSV', 'ecopower-tracker' ) . '</a></p></div>';
        } else {
            echo '<div class="notice notice-error is-dismissible"><p>' . __( 'No projects found to export.', 'ecopower-tracker' ) . '</p></div>';
        }
    }

    ?>
    <div class="wrap">
        <h2><?php _e( 'Export Project Data', 'ecopower-tracker' ); ?></h2>
        <form method="post">
            <?php wp_nonce_field( 'ecopower_tracker_export_action', 'ecopower_tracker_export_nonce' ); ?>
            <input type="submit" name="ecopower_tracker_export_submit" class="button button-primary" value="<?php _e( 'Export CSV', 'ecopower-tracker' ); ?>" />
        </form>
    </div>
    <?php
}
?>