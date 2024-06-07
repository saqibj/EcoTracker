<?php
if ( ! defined( 'ABSPATH' ) ) {
    exit; // Exit if accessed directly.
}

// Display the list of all projects
function ecopower_tracker_all_projects() {
    global $wpdb;
    $table_name = $wpdb->prefix . 'ecopower_projects';
    $projects = $wpdb->get_results( "SELECT * FROM $table_name", ARRAY_A );

    ?>
    <div class="wrap">
        <h1><?php _e( 'All Projects', 'ecopower-tracker' ); ?></h1>
        <table class="widefat">
            <thead>
                <tr>
                    <th><?php _e( 'Project #', 'ecopower-tracker' ); ?></th>
                    <th><?php _e( 'Project Company', 'ecopower-tracker' ); ?></th>
                    <th><?php _e( 'Project Name', 'ecopower-tracker' ); ?></th>
                    <th><?php _e( 'Project Location', 'ecopower-tracker' ); ?></th>
                    <th><?php _e( 'Type of Plant', 'ecopower-tracker' ); ?></th>
                    <th><?php _e( 'Project CUF', 'ecopower-tracker' ); ?></th>
                    <th><?php _e( 'Generation Capacity (KW)', 'ecopower-tracker' ); ?></th>
                    <th><?php _e( 'Date of Activation', 'ecopower-tracker' ); ?></th>
                    <th><?php _e( 'Actions', 'ecopower-tracker' ); ?></th>
                </tr>
            </thead>
            <tbody>
                <?php
                if ( ! empty( $projects ) ) {
                    foreach ( $projects as $project ) {
                        echo '<tr>';
                        echo '<td>' . esc_html( $project['id'] ) . '</td>';
                        echo '<td>' . esc_html( $project['project_company'] ) . '</td>';
                        echo '<td>' . esc_html( $project['project_name'] ) . '</td>';
                        echo '<td>' . esc_html( $project['project_location'] ) . '</td>';
                        echo '<td>' . esc_html( $project['type_of_plant'] ) . '</td>';
                        echo '<td>' . esc_html( $project['project_cuf'] ) . '</td>';
                        echo '<td>' . esc_html( $project['generation_capacity'] ) . '</td>';
                        echo '<td>' . esc_html( $project['date_of_activation'] ) . '</td>';
                        echo '<td>';
                        echo '<a href="' . admin_url( 'admin.php?page=ecopower-tracker-add-new-project&id=' . $project['id'] ) . '">' . __( 'Edit', 'ecopower-tracker' ) . '</a> | ';
                        echo '<a href="' . admin_url( 'admin.php?page=ecopower-tracker-all-projects&action=delete&id=' . $project['id'] ) . '" onclick="return confirm(\'' . __( 'Are you sure you want to delete this project?', 'ecopower-tracker' ) . '\');">' . __( 'Delete', 'ecopower-tracker' ) . '</a>';
                        echo '</td>';
                        echo '</tr>';
                    }
                } else {
                    echo '<tr><td colspan="9">' . __( 'No projects found.', 'ecopower-tracker' ) . '</td></tr>';
                }
                ?>
            </tbody>
        </table>
    </div>
    <?php
}

// Display the form to add a new project or edit an existing project
function ecopower_tracker_add_new_project() {
    global $wpdb;
    $table_name = $wpdb->prefix . 'ecopower_projects';

    // Initialize project data
    $project = [
        'id' => '',
        'project_company' => '',
        'project_name' => '',
        'project_location' => '',
        'type_of_plant' => '',
        'project_cuf' => '',
        'generation_capacity' => '',
        'date_of_activation' => ''
    ];

    // Check if editing an existing project
    if ( isset( $_GET['id'] ) && is_numeric( $_GET['id'] ) ) {
        $project_id = intval( $_GET['id'] );
        $project = $wpdb->get_row( $wpdb->prepare( "SELECT * FROM $table_name WHERE id = %d", $project_id ), ARRAY_A );
    }

    // Handle form submission
    if ( $_SERVER['REQUEST_METHOD'] == 'POST' && check_admin_referer( 'ecopower_tracker_save_project', 'ecopower_tracker_nonce' ) ) {
        $project_company = sanitize_text_field( $_POST['project_company'] );
        $project_name = sanitize_text_field( $_POST['project_name'] );
        $project_location = sanitize_text_field( $_POST['project_location'] );
        $type_of_plant = sanitize_text_field( $_POST['type_of_plant'] );
        $project_cuf = floatval( $_POST['project_cuf'] );
        $generation_capacity = floatval( $_POST['generation_capacity'] );
        $date_of_activation = sanitize_text_field( $_POST['date_of_activation'] );

        if ( empty( $project_id ) ) {
            // Insert new project
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
            $project_id = $wpdb->insert_id;
            echo '<div class="updated"><p>' . __( 'Project added successfully.', 'ecopower-tracker' ) . '</p></div>';
        } else {
            // Update existing project
            $wpdb->update(
                $table_name,
                [
                    'project_company' => $project_company,
                    'project_name' => $project_name,
                    'project_location' => $project_location,
                    'type_of_plant' => $type_of_plant,
                    'project_cuf' => $project_cuf,
                    'generation_capacity' => $generation_capacity,
                    'date_of_activation' => $date_of_activation
                ],
                [ 'id' => $project_id ]
            );
            echo '<div class="updated"><p>' . __( 'Project updated successfully.', 'ecopower-tracker' ) . '</p></div>';
        }

        // Redirect to the all projects page
        wp_redirect( admin_url( 'admin.php?page=ecopower-tracker-all-projects' ) );
        exit;
    }

    ?>
    <div class="wrap">
        <h1><?php _e( 'Add New Project', 'ecopower-tracker' ); ?></h1>
        <form method="post">
            <?php wp_nonce_field( 'ecopower_tracker_save_project', 'ecopower_tracker_nonce' ); ?>
            <table class="form-table">
                <tr valign="top">
                    <th scope="row"><?php _e( 'Project Company', 'ecopower-tracker' ); ?></th>
                    <td><input type="text" name="project_company" value="<?php echo esc_attr( $project['project_company'] ); ?>" required /></td>
                </tr>
                <tr valign="top">
                    <th scope="row"><?php _e( 'Project Name', 'ecopower-tracker' ); ?></th>
                    <td><input type="text" name="project_name" value="<?php echo esc_attr( $project['project_name'] ); ?>" required /></td>
                </tr>
                <tr valign="top">
                    <th scope="row"><?php _e( 'Project Location', 'ecopower-tracker' ); ?></th>
                    <td><input type="text" name="project_location" value="<?php echo esc_attr( $project['project_location'] ); ?>" required /></td>
                </tr>
                <tr valign="top">
                    <th scope="row"><?php _e( 'Type of Plant', 'ecopower-tracker' ); ?></th>
                    <td>
                        <select name="type_of_plant" required>
                            <option value=""><?php _e( 'Select Type', 'ecopower-tracker' ); ?></option>
                            <option value="Wind" <?php selected( $project['type_of_plant'], 'Wind' ); ?>><?php _e( 'Wind', 'ecopower-tracker' ); ?></option>
                            <option value="Solar" <?php selected( $project['type_of_plant'], 'Solar' ); ?>><?php _e( 'Solar', 'ecopower-tracker' ); ?></option>
                        </select>
                    </td>
                </tr>
                <tr valign="top">
                    <th scope="row"><?php _e( 'Project CUF', 'ecopower-tracker' ); ?></th>
                    <td><input type="number" step="0.01" name="project_cuf" value="<?php echo esc_attr( $project['project_cuf'] ); ?>" required /></td>
                </tr>
                <tr valign="top">
                    <th scope="row"><?php _e( 'Generation Capacity (KW)', 'ecopower-tracker' ); ?></th>
                    <td><input type="number" step="0.01" name="generation_capacity" value="<?php echo esc_attr( $project['generation_capacity'] ); ?>" required /></td>
                </tr>
                <tr valign="top">
                    <th scope="row"><?php _e( 'Date of Activation', 'ecopower-tracker' ); ?></th>
                    <td><input type="date" name="date_of_activation" value="<?php echo esc_attr( $project['date_of_activation'] ); ?>" required /></td>
                </tr>
            </table>
            <?php submit_button( __( 'Save Project', 'ecopower-tracker' ) ); ?>
            <a href="<?php echo admin_url( 'admin.php?page=ecopower-tracker-all-projects' ); ?>" class="button"><?php _e( 'Cancel', 'ecopower-tracker' ); ?></a>
        </form>
    </div>
    <?php
}

// Handle delete project action
function ecopower_tracker_handle_delete_project() {
    global $wpdb;
    $table_name = $wpdb->prefix . 'ecopower_projects';

    if ( isset( $_GET['action'] ) && $_GET['action'] == 'delete' && isset( $_GET['id'] ) && is_numeric( $_GET['id'] ) ) {
        $project_id = intval( $_GET['id'] );
        $wpdb->delete( $table_name, [ 'id' => $project_id ] );

        // Redirect to the all projects page
        wp_redirect( admin_url( 'admin.php?page=ecopower-tracker-all-projects' ) );
        exit;
    }
}
add_action( 'admin_init', 'ecopower_tracker_handle_delete_project' );
?>