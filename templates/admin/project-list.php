<?php
if (!defined('ABSPATH')) {
    exit;
}

// Check for admin messages
if (isset($_GET['message'])) {
    $message = sanitize_text_field($_GET['message']);
    if (isset($messages[$message])) {
        echo '<div class="notice notice-success is-dismissible"><p>' . esc_html($messages[$message]) . '</p></div>';
    }
}
?>

<div class="wrap">
    <h1 class="wp-heading-inline"><?php esc_html_e('Projects', 'ecopower-tracker'); ?></h1>
    <a href="<?php echo esc_url(admin_url('admin.php?page=ecopower-tracker-add')); ?>" class="page-title-action">
        <?php esc_html_e('Add New', 'ecopower-tracker'); ?>
    </a>
    <hr class="wp-header-end">

    <div class="ecopower-tracker-admin">
        <?php if (!empty($projects)) : ?>
            <table class="wp-list-table widefat fixed striped">
                <thead>
                    <tr>
                        <th><?php esc_html_e('Project Number', 'ecopower-tracker'); ?></th>
                        <th><?php esc_html_e('Company', 'ecopower-tracker'); ?></th>
                        <th><?php esc_html_e('Project Name', 'ecopower-tracker'); ?></th>
                        <th><?php esc_html_e('Location', 'ecopower-tracker'); ?></th>
                        <th><?php esc_html_e('Plant Type', 'ecopower-tracker'); ?></th>
                        <th><?php esc_html_e('Capacity (MW)', 'ecopower-tracker'); ?></th>
                        <th><?php esc_html_e('Actions', 'ecopower-tracker'); ?></th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($projects as $project) : ?>
                        <tr>
                            <td><?php echo esc_html($project->project_number); ?></td>
                            <td><?php echo esc_html($project->project_company); ?></td>
                            <td><?php echo esc_html($project->project_name); ?></td>
                            <td><?php echo esc_html($project->project_location); ?></td>
                            <td><?php echo esc_html(ucfirst($project->type_of_plant)); ?></td>
                            <td><?php echo esc_html(number_format($project->generation_capacity, 2)); ?></td>
                            <td>
                                <a href="<?php echo esc_url(add_query_arg(array('action' => 'edit', 'id' => $project->id), admin_url('admin.php?page=ecopower-tracker'))); ?>" class="button">
                                    <?php esc_html_e('Edit', 'ecopower-tracker'); ?>
                                </a>
                                <a href="<?php echo esc_url(wp_nonce_url(add_query_arg(array('action' => 'delete', 'id' => $project->id), admin_url('admin.php?page=ecopower-tracker')), 'ecopower_tracker_delete_' . $project->id)); ?>" class="button button-link-delete" onclick="return confirm(ecoPowerTracker.strings.confirmDelete);">
                                    <?php esc_html_e('Delete', 'ecopower-tracker'); ?>
                                </a>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        <?php else : ?>
            <div class="notice notice-info">
                <p><?php esc_html_e('No projects found. Add your first project using the button above.', 'ecopower-tracker'); ?></p>
            </div>
        <?php endif; ?>
    </div>
</div>
