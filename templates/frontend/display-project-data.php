// Path: EcoPower-Tracker/templates/frontend/display-project-data.php
// File: display-project-data.php

<?php

if (!defined('ABSPATH')) {
    exit; // Exit if accessed directly
}

// Assuming this template will be used by shortcodes to display project data

global $wpdb;

// Example: Display all projects (customize as needed)
$projects = $wpdb->get_results("SELECT * FROM {$wpdb->prefix}ecopower_tracker_projects");

?>

<div class="ecopower-tracker-projects">
    <?php if ($projects) : ?>
        <table class="widefat fixed" cellspacing="0">
            <thead>
                <tr>
                    <th><?php _e('Project Number', 'ecopower-tracker'); ?></th>
                    <th><?php _e('Project Company', 'ecopower-tracker'); ?></th>
                    <th><?php _e('Project Name', 'ecopower-tracker'); ?></th>
                    <th><?php _e('Project Location', 'ecopower-tracker'); ?></th>
                    <th><?php _e('Type of Plant', 'ecopower-tracker'); ?></th>
                    <th><?php _e('Generation Capacity (KWs)', 'ecopower-tracker'); ?></th>
                    <th><?php _e('Date of Activation', 'ecopower-tracker'); ?></th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($projects as $project) : ?>
                    <tr>
                        <td><?php echo esc_html($project->project_number); ?></td>
                        <td><?php echo esc_html($project->project_company); ?></td>
                        <td><?php echo esc_html($project->project_name); ?></td>
                        <td><?php echo esc_html($project->project_location); ?></td>
                        <td><?php echo esc_html($project->type_of_plant); ?></td>
                        <td><?php echo number_format($project->generation_capacity); ?> KWs</td>
                        <td><?php echo esc_html(date('Y-m-d', strtotime($project->date_of_activation))); ?></td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    <?php else : ?>
        <p><?php _e('No projects found.', 'ecopower-tracker'); ?></p>
    <?php endif; ?>
</div>

<style>
    .ecopower-tracker-projects {
        margin-top: 20px;
    }

    .ecopower-tracker-projects table {
        width: 100%;
        border-collapse: collapse;
    }

    .ecopower-tracker-projects th, .ecopower-tracker-projects td {
        padding: 10px;
        border: 1px solid #ddd;
    }

    .ecopower-tracker-projects th {
        background-color: #f9f9f9;
        text-align: left;
    }
</style>

<?php
?>