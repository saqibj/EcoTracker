<?php
if ( ! defined( 'ABSPATH' ) ) {
    exit; // Exit if accessed directly.
}

// Function to display all projects
function ecopower_tracker_all_projects_page() {
    $projects = ecopower_tracker_get_all_projects();
    ?>
    <div class="wrap">
        <h1><?php _e( 'All Projects', 'ecopower-tracker' ); ?></h1>
        <table class="wp-list-table widefat fixed striped">
            <thead>
                <tr>
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
                <?php foreach ( $projects as $project ) : ?>
                    <tr>
                        <td><?php echo esc_html( $project['project_company'] ); ?></td>
                        <td><?php echo esc_html( $project['project_name'] ); ?></td>
                        <td><?php echo esc_html( $project['project_location'] ); ?></td>
                        <td><?php echo esc_html( $project['type_of_plant'] ); ?></td>
                        <td><?php echo esc_html( $project['project_cuf'] ); ?></td>
                        <td><?php echo esc_html( $project['generation_capacity'] ); ?></td>
                        <td><?php echo esc_html( $project['date_of_activation'] ); ?></td>
                        <td>
                            <a href="<?php echo admin_url( 'admin.php?page=ecopower-tracker-edit-project&id=' . $project['id'] ); ?>"><?php _e( 'Edit', 'ecopower-tracker' ); ?></a>
                            |
                            <a href="<?php echo wp_nonce_url( admin_url( 'admin.php?page=ecopower-tracker-delete-project&id=' . $project['id'] ), 'delete_project', 'ecopower_tracker_nonce' ); ?>" onclick="return confirm('<?php _e( 'Are you sure you want to delete this project?', 'ecopower-tracker' ); ?>');"><?php _e( 'Delete', 'ecopower-tracker' ); ?></a>
                        </td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>
    <?php
}

// Function to add or edit a project
function ecopower_tracker_add_new_project_page() {
    $action = isset( $_GET['id'] ) ? 'edit' : 'add';
    $project = $action === 'edit' ? ecopower_tracker_get_project( $_GET['id'] ) : [];

    if ( $_SERVER['REQUEST_METHOD'] === 'POST' && check_admin_referer( 'save_project', 'ecopower_tracker_nonce' ) ) {
        $data = [
            'project_company' => $_POST['project_company'],
            'project_name' => $_POST['project_name'],
            'project_location' => $_POST['project_location'],
            'type_of_plant' => $_POST['type_of_plant'],
            'project_cuf' => $_POST['project_cuf'],
            'generation_capacity' => $_POST['generation_capacity'],
            'date_of_activation' => $_POST['date_of_activation']
        ];

        if ( $action === 'edit' ) {
            ecopower_tracker_update_project( $_GET['id'], $data );
        } else {
            ecopower_tracker_create_project( $data );
        }

        wp_redirect( admin_url( 'admin.php?page=ecopower-tracker-all-projects' ) );
        exit;
    }

    ?>
    <div class="wrap">
        <h1><?php echo $action === 'edit' ? __( 'Edit Project', 'ecopower-tracker' ) : __( 'Add New Project', 'ecopower-tracker' ); ?></h1>
        <form method="post">
            <?php wp_nonce_field( 'save_project', 'ecopower_tracker_nonce' ); ?>
            <table class="form-table">
                <tr>
                    <th><?php _e( 'Project Company', 'ecopower-tracker' ); ?></th>
                    <td><input type="text" name="project_company" value="<?php echo isset( $project['project_company'] ) ? esc_attr( $project['project_company'] ) : ''; ?>" required /></td>
                </tr>
                <tr>
                    <th><?php _e( 'Project Name', 'ecopower-tracker' ); ?></th>
                    <td><input type="text" name="project_name" value="<?php echo isset( $project['project_name'] ) ? esc_attr( $project['project_name'] ) : ''; ?>" required /></td>
                </tr>
                <tr>
                    <th><?php _e( 'Project Location', 'ecopower-tracker' ); ?></th>
                    <td><input type="text" name="project_location" value="<?php echo isset( $project['project_location'] ) ? esc_attr( $project['project_location'] ) : ''; ?>" required /></td>
                </tr>
                <tr>
                    <th><?php _e( 'Type of Plant', 'ecopower-tracker' ); ?></th>
                    <td>
                        <select name="type_of_plant">
                            <option value="Wind" <?php selected( isset( $project['type_of_plant'] ) && $project['type_of_plant'] === 'Wind' ); ?>><?php _e( 'Wind', 'ecopower-tracker' ); ?></option>
                            <option value="Solar" <?php selected( isset( $project['type_of_plant'] ) && $project['type_of_plant'] === 'Solar' ); ?>><?php _e( 'Solar', 'ecopower-tracker' ); ?></option>
                        </select>
                    </td>
                </tr>
                <tr>
                    <th><?php _e( 'Project CUF', 'ecopower-tracker' ); ?></th>
                    <td><input type="number" step="0.01" name="project_cuf" value="<?php echo isset( $project['project_cuf'] ) ? esc_attr( $project['project_cuf'] ) : ''; ?>" required /></td>
                </tr>
                <tr>
                    <th><?php _e( 'Generation Capacity (KW)', 'ecopower-tracker' ); ?></th>
                    <td><input type="number" step="0.01" name="generation_capacity" value="<?php echo isset( $project['generation_capacity'] ) ? esc_attr( $project['generation_capacity'] ) : ''; ?>" required /></td>
                </tr>
                <tr>
                    <th><?php _e( 'Date of Activation', 'ecopower-tracker' ); ?></th>
                    <td><input type="date" name="date_of_activation" value="<?php echo isset( $project['date_of_activation'] ) ? esc_attr( $project['date_of_activation'] ) : ''; ?>" required /></td>
                </tr>
            </table>
            <?php submit_button( $action === 'edit' ? __( 'Save Changes', 'ecopower-tracker' ) : __( 'Add Project', 'ecopower-tracker' ) ); ?>
        </form>
    </div>
    <?php
}

// Function to delete a project
function ecopower_tracker_delete_project() {
    if ( isset( $_GET['id'] ) && check_admin_referer( 'delete_project', 'ecopower_tracker_nonce' ) ) {
        ecopower_tracker_delete_project( $_GET['id'] );
        wp_redirect( admin_url( 'admin.php?page=ecopower-tracker-all-projects' ) );
        exit;
    }
}

add_action( 'admin_post_ecopower_tracker_delete_project', 'ecopower_tracker_delete_project' );
?>