<?php
if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

$projects = ecopower_tracker_get_projects();
?>

<div class="wrap">
    <h1>EcoPower Tracker</h1>
    <form method="post" enctype="multipart/form-data">
        <input type="file" name="ecopower_tracker_csv" />
        <input type="submit" name="ecopower_tracker_import_csv" class="button button-primary" value="Import CSV" />
    </form>
    <table class="wp-list-table widefat fixed striped">
        <thead>
            <tr>
                <th>Project Number</th>
                <th>Company</th>
                <th>Name</th>
                <th>Location</th>
                <th>Type</th>
                <th>CUF</th>
                <th>Capacity (KWs)</th>
                <th>Activation Date</th>
                <th>Actions</th>
            </tr>
        </thead>
        <tbody>
            <?php if ( ! empty( $projects ) ) : ?>
                <?php foreach ( $projects as $project ) : ?>
                    <tr>
                        <td><?php echo esc_html( $project['project_number'] ); ?></td>
                        <td><?php echo esc_html( $project['company'] ); ?></td>
                        <td><?php echo esc_html( $project['name'] ); ?></td>
                        <td><?php echo esc_html( $project['location'] ); ?></td>
                        <td><?php echo esc_html( $project['type'] ); ?></td>
                        <td><?php echo esc_html( $project['cuf'] ); ?></td>
                        <td><?php echo esc_html( $project['capacity'] ); ?></td>
                        <td><?php echo esc_html( $project['activation_date'] ); ?></td>
                        <td>
                            <a href="<?php echo admin_url( 'admin.php?page=ecopower-tracker&action=edit&project_id=' . $project['id'] ); ?>" class="button">Edit</a>
                            <a href="<?php echo admin_url( 'admin.php?page=ecopower-tracker&action=delete&project_id=' . $project['id'] ); ?>" class="button delete-project" data-project-id="<?php echo $project['id']; ?>">Delete</a>
                        </td>
                    </tr>
                <?php endforeach; ?>
            <?php else : ?>
                <tr>
                    <td colspan="10">No projects found.</td>
                </tr>
            <?php endif; ?>
        </tbody>
    </table>
</div>
