<?php
if ( ! defined( 'ABSPATH' ) ) {
    exit; // Exit if accessed directly.
}

function ecopower_tracker_import_page() {
    if ( isset( $_POST['ecopower_tracker_import_submit'] ) && check_admin_referer( 'ecopower_tracker_import_action', 'ecopower_tracker_import_nonce' ) ) {
        // Handle the file upload
        if ( ! empty( $_FILES['ecopower_tracker_import_file']['tmp_name'] ) ) {
            $file = $_FILES['ecopower_tracker_import_file']['tmp_name'];

            // Move the uploaded file to the uploads directory
            $uploads_dir = plugin_dir_path( __FILE__ ) . '../uploads/';
            if ( ! is_dir( $uploads_dir ) ) {
                mkdir( $uploads_dir, 0755, true );
            }
            $uploaded_file_path = $uploads_dir . basename( $_FILES['ecopower_tracker_import_file']['name'] );

            if ( move_uploaded_file( $file, $uploaded_file_path ) ) {
                // Process the CSV file
                if ( $csv_data = fopen( $uploaded_file_path, 'r' ) ) {
                    global $wpdb;
                    $table_name = $wpdb->prefix . 'ecopower_projects';

                    $imported_projects = [];
                    $skipped_projects = [];

                    $header = fgetcsv( $csv_data );
                    while ( $row = fgetcsv( $csv_data ) ) {
                        $data = array_combine( $header, $row );

                        // Validate and format the date
                        $activation_date = DateTime::createFromFormat( 'Y-m-d', $data['Date of Activation'] );
                        if ( !$activation_date ) {
                            $activation_date = DateTime::createFromFormat( 'd/m/Y', $data['Date of Activation'] );
                        }
                        if ( !$activation_date ) {
                            $skipped_projects[] = $data['Project Name'] . ' (Invalid Date)';
                            continue;
                        }

                        // Check for existing project by name and date
                        $existing_project = $wpdb->get_row( $wpdb->prepare( "SELECT id FROM $table_name WHERE project_name = %s AND date_of_activation = %s", $data['Project Name'], $activation_date->format('Y-m-d') ) );

                        if ( $existing_project ) {
                            $skipped_projects[] = $data['Project Name'] . ' (Duplicate)';
                            continue;
                        }

                        $wpdb->insert(
                            $table_name,
                            array(
                                'project_company' => sanitize_text_field( $data['Project Company'] ),
                                'project_name' => sanitize_text_field( $data['Project Name'] ),
                                'project_location' => sanitize_text_field( $data['Project Location'] ),
                                'type_of_plant' => sanitize_text_field( $data['Type of Plant'] ),
                                'project_cuf' => floatval( $data['Project CUF'] ),
                                'generation_capacity' => floatval( $data['Generation Capacity (KW)'] ),
                                'date_of_activation' => $activation_date->format('Y-m-d')
                            )
                        );

                        $imported_projects[] = $data['Project Name'];
                    }
                    fclose( $csv_data );

                    // Feedback messages
                    if ( ! empty( $imported_projects ) ) {
                        echo '<div class="notice notice-success is-dismissible"><p>' . __( 'Successfully imported projects:', 'ecopower-tracker' ) . ' ' . implode( ', ', $imported_projects ) . '</p></div>';
                    }
                    if ( ! empty( $skipped_projects ) ) {
                        echo '<div class="notice notice-warning is-dismissible"><p>' . __( 'Skipped projects:', 'ecopower-tracker' ) . ' ' . implode( ', ', $skipped_projects ) . '</p></div>';
                    }
                } else {
                    echo '<div class="notice notice-error is-dismissible"><p>' . __( 'Failed to open the CSV file.', 'ecopower-tracker' ) . '</p></div>';
                }
            } else {
                echo '<div class="notice notice-error is-dismissible"><p>' . __( 'Failed to upload the file.', 'ecopower-tracker' ) . '</p></div>';
            }
        } else {
            echo '<div class="notice notice-error is-dismissible"><p>' . __( 'Please upload a CSV file.', 'ecopower-tracker' ) . '</p></div>';
        }
    }

    ?>
    <div class="wrap">
        <h1><?php _e( 'Import/Export', 'ecopower-tracker' ); ?></h1>
        <h2><?php _e( 'Import Project Data', 'ecopower-tracker' ); ?></h2>
        <form method="post" enctype="multipart/form-data">
            <?php wp_nonce_field( 'ecopower_tracker_import_action', 'ecopower_tracker_import_nonce' ); ?>
            <input type="file" name="ecopower_tracker_import_file" accept=".csv" required />
            <input type="submit" name="ecopower_tracker_import_submit" class="button button-primary" value="<?php _e( 'Import CSV', 'ecopower-tracker' ); ?>" />
        </form>
    </div>
    <?php
}
?>