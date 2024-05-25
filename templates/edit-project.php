<?php
if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

$project_id = intval( $_GET['project_id'] );
$project = ecopower_tracker_get_project( $project_id );

if ( ! $project ) {
    echo '<div class="notice notice-error"><p>Project not found.</p></div>';
    return;
}

if ( isset( $_POST['ecopower_tracker_update_project'] ) ) {
    ecopower_tracker_update_project( $project_id, $_POST );
    echo '<div class="notice notice-success"><p>Project updated successfully.</p></div>';
    $project = ecopower_tracker_get_project( $project_id );
}
?>

<div class="wrap">
    <h1>Edit Project</h1>
    <form method="post">
        <table class="form-table">
            <tr>
                <th><label for="project_number">Project Number</label></th>
                <td><input type="text" id="project_number" name="project_number" value="<?php echo esc_attr( $project['project_number'] ); ?>" class="regular-text" required /></td>
            </tr>
            <tr>
                <th><label for="company">Company</label></th>
                <td><input type="text" id="company" name="company" value="<?php echo esc_attr( $project['company'] ); ?>" class="regular-text" required /></td>
            </tr>
            <tr>
                <th><label for="name">Name</label></th>
                <td><input type="text" id="name" name="name" value="<?php echo esc_attr( $project['name'] ); ?>" class="regular-text" required /></td>
            </tr>
            <tr>
                <th><label for="location">Location</label></th>
                <td><input type="text" id="location" name="location" value="<?php echo esc_attr( $project['location'] ); ?>" class="regular-text" required /></td>
            </tr>
            <tr>
                <th><label for="type">Type</label></th>
                <td><input type="text" id="type" name="type" value="<?php echo esc_attr( $project['type'] ); ?>" class="regular-text" required /></td>
            </tr>
            <tr>
                <th><label for="cuf">CUF</label></th>
                <td><input type="number" step="0.01" id="cuf" name="cuf" value="<?php echo esc_attr( $project['cuf'] ); ?>" class="regular-text" required /></td>
            </tr>
            <tr>
                <th><label for="capacity">Capacity (KWs)</label></th>
                <td><input type="number" step="0.01" id="capacity" name="capacity" value="<?php echo esc_attr( $project['capacity'] ); ?>" class="regular-text" required /></td>
            </tr>
            <tr>
                <th><label for="activation_date">Activation Date</label></th>
                <td><input type="date" id="activation_date" name="activation_date" value="<?php echo esc_attr( $project['activation_date'] ); ?>" class="regular-text" required /></td>
            </tr>
        </table>
        <p class="submit">
            <input type="submit" name="ecopower_tracker_update_project" id="submit" class="button button-primary" value="Save Changes">
            <a href="<?php echo admin_url( 'admin.php?page=ecopower-tracker' ); ?>" class="button">Back to Projects</a>
        </p>
    </form>
</div>
