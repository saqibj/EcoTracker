// Path: EcoPower-Tracker/templates/admin/dashboard.php
// File: dashboard.php

<?php
/**
 * Admin dashboard template
 *
 * @package EcoPowerTracker
 * @since 2.0.1
 */

namespace EcoPowerTracker;

if (!defined('ABSPATH')) {
    exit;
}

// Get dashboard instance
$dashboard = new EcoPower_Tracker_Dashboard();
$stats = $dashboard->get_dashboard_stats();
?>

<div class="wrap ecopower-tracker-admin">
    <h1 class="wp-heading-inline">
        <?php esc_html_e('EcoPower Tracker Dashboard', 'ecopower-tracker'); ?>
    </h1>
    
    <a href="<?php echo esc_url(admin_url('admin.php?page=ecopower-tracker-new')); ?>" class="page-title-action">
        <?php esc_html_e('Add New Project', 'ecopower-tracker'); ?>
    </a>

    <hr class="wp-header-end">

    <!-- Statistics Overview -->
    <div class="ecopower-tracker-stats-grid">
        <div class="stat-box">
            <h3><?php esc_html_e('Total Projects', 'ecopower-tracker'); ?></h3>
            <div class="stat-value"><?php echo esc_html(number_format($stats['total_projects'])); ?></div>
        </div>

        <div class="stat-box">
            <h3><?php esc_html_e('Total Generation Capacity', 'ecopower-tracker'); ?></h3>
            <div class="stat-value">
                <?php echo esc_html(number_format($stats['total_capacity'], 2)); ?>
                <span class="stat-unit">KWs</span>
            </div>
        </div>
    </div>

    <!-- Plant Type Statistics -->
    <div class="ecopower-tracker-plant-types">
        <h2><?php esc_html_e('Plant Type Distribution', 'ecopower-tracker'); ?></h2>
        <table class="wp-list-table widefat fixed striped">
            <thead>
                <tr>
                    <th><?php esc_html_e('Plant Type', 'ecopower-tracker'); ?></th>
                    <th><?php esc_html_e('Number of Projects', 'ecopower-tracker'); ?></th>
                    <th><?php esc_html_e('Total Capacity', 'ecopower-tracker'); ?></th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($stats['plant_types'] as $type): ?>
                    <tr>
                        <td><?php echo esc_html($type['type']); ?></td>
                        <td><?php echo esc_html(number_format($type['count'])); ?></td>
                        <td>
                            <?php echo esc_html(number_format($type['capacity'], 2)); ?>
                            <span class="stat-unit">KWs</span>
                        </td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>

    <!-- Recent Projects -->
    <div class="ecopower-tracker-recent">
        <h2><?php esc_html_e('Recent Projects', 'ecopower-tracker'); ?></h2>
        <table class="wp-list-table widefat fixed striped">
            <thead>
                <tr>
                    <th><?php esc_html_e('Project Number', 'ecopower-tracker'); ?></th>
                    <th><?php esc_html_e('Project Name', 'ecopower-tracker'); ?></th>
                    <th><?php esc_html_e('Company', 'ecopower-tracker'); ?></th>
                    <th><?php esc_html_e('Type', 'ecopower-tracker'); ?></th>
                    <th><?php esc_html_e('Capacity', 'ecopower-tracker'); ?></th>
                    <th><?php esc_html_e('Activation Date', 'ecopower-tracker'); ?></th>
                    <th><?php esc_html_e('Actions', 'ecopower-tracker'); ?></th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($stats['recent_projects'] as $project): ?>
                    <tr>
                        <td><?php echo esc_html($project->project_number); ?></td>
                        <td><?php echo esc_html($project->project_name); ?></td>
                        <td><?php echo esc_html($project->project_company); ?></td>
                        <td><?php echo esc_html($project->type_of_plant); ?></td>
                        <td>
                            <?php echo esc_html(number_format($project->generation_capacity, 2)); ?>
                            <span class="stat-unit">KWs</span>
                        </td>
                        <td><?php echo esc_html(date_i18n(get_option('date_format'), strtotime($project->date_of_activation))); ?></td>
                        <td>
                            <a href="<?php echo esc_url(admin_url('admin.php?page=ecopower-tracker-edit&id=' . $project->id)); ?>" 
                               class="button button-small">
                                <?php esc_html_e('Edit', 'ecopower-tracker'); ?>
                            </a>
                            <a href="<?php echo esc_url(wp_nonce_url(admin_url('admin.php?page=ecopower-tracker-delete&id=' . $project->id), 'delete_project_' . $project->id)); ?>" 
                               class="button button-small button-link-delete" 
                               onclick="return confirm('<?php esc_attr_e('Are you sure you want to delete this project?', 'ecopower-tracker'); ?>')">
                                <?php esc_html_e('Delete', 'ecopower-tracker'); ?>
                            </a>
                        </td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>

    <!-- Import/Export Section -->
    <div class="ecopower-tracker-tools">
        <h2><?php esc_html_e('Tools', 'ecopower-tracker'); ?></h2>
        
        <!-- Import Form -->
        <div class="tool-section">
            <h3><?php esc_html_e('Import Projects', 'ecopower-tracker'); ?></h3>
            <form method="post" enctype="multipart/form-data" action="<?php echo esc_url(admin_url('admin-post.php')); ?>">
                <?php wp_nonce_field('ecopower_tracker_upload_csv', 'ecopower_tracker_nonce'); ?>
                <input type="hidden" name="action" value="ecopower_tracker_upload_csv">
                <input type="file" name="ecopower_tracker_csv" accept=".csv" required>
                <?php submit_button(__('Import CSV', 'ecopower-tracker'), 'secondary', 'submit', false); ?>
            </form>
        </div>

        <!-- Export Form -->
        <div class="tool-section">
            <h3><?php esc_html_e('Export Projects', 'ecopower-tracker'); ?></h3>
            <form method="post" action="<?php echo esc_url(admin_url('admin-post.php')); ?>">
                <?php wp_nonce_field('ecopower_tracker_export_csv', 'ecopower_tracker_nonce'); ?>
                <input type="hidden" name="action" value="ecopower_tracker_export_csv">
                <?php submit_button(__('Export CSV', 'ecopower-tracker'), 'secondary', 'submit', false); ?>
            </form>
        </div>
    </div>
</div>

<style>
<?php include ECOPOWER_TRACKER_PATH . 'assets/css/ecopower-tracker-admin.css'; ?>
</style>