<?php

if (!defined('ABSPATH')) {
    exit; // Exit if accessed directly
}

function ecopower_tracker_admin_page() {
    // Check for import status
    if (isset($_GET['import_status'])) {
        if ($_GET['import_status'] === 'success') {
            echo '<div class="notice notice-success is-dismissible"><p>' . __('CSV imported successfully.', 'ecopower-tracker') . '</p></div>';
        } elseif ($_GET['import_status'] === 'error') {
            echo '<div class="notice notice-error is-dismissible"><p>' . __('Error importing CSV. Please check the logs for more details.', 'ecopower-tracker') . '</p></div>';
        }
    }
    ?>
    <div class="wrap">
        <h1><?php esc_html_e('EcoPower Tracker', 'ecopower-tracker'); ?></h1>
        <form method="post" action="">
            <table class="form-table">
                <tr>
                    <th scope="row"><label for="project_company"><?php esc_html_e('Project Company', 'ecopower-tracker'); ?></label></th>
                    <td><input name="project_company" type="text" id="project_company" value="" class="regular-text"></td>
                </tr>
                <tr>
                    <th scope="row"><label for="project_name"><?php esc_html_e('Project Name', 'ecopower-tracker'); ?></label></th>
                    <td><input name="project_name" type="text" id="project_name" value="" class="regular-text"></td>
                </tr>
                <tr>
                    <th scope="row"><label for="project_location"><?php esc_html_e('Project Location', 'ecopower-tracker'); ?></label></th>
                    <td><input name="project_location" type="text" id="project_location" value="" class="regular-text"></td>
                </tr>
                <tr>
                    <th scope="row"><label for="plant_type"><?php esc_html_e('Type of Plant', 'ecopower-tracker'); ?></label></th>
                    <td>
                        <select name="plant_type" id="plant_type">
                            <option value="Wind"><?php esc_html_e('Wind', 'ecopower-tracker'); ?></option>
                            <option value="Solar"><?php esc_html_e('Solar', 'ecopower-tracker'); ?></option>
                        </select>
                    </td>
                </tr>
                <tr>
                    <th scope="row"><label for="project_cuf"><?php esc_html_e('Project CUF', 'ecopower-tracker'); ?></label></th>
                    <td><input name="project_cuf" type="number" step="0.01" id="project_cuf" value="" class="regular-text"></td>
                </tr>
                <tr>
                    <th scope="row"><label for="generation_capacity"><?php esc_html_e('Generation Capacity (in KWs)', 'ecopower-tracker'); ?></label></th>
                    <td><input name="generation_capacity" type="number" step="0.01" id="generation_capacity" value="" class="regular-text"></td>
                </tr>
                <tr>
                    <th scope="row"><label for="activation_date"><?php esc_html_e('Date of Activation', 'ecopower-tracker'); ?></label></th>
                    <td><input name="activation_date" type="date" id="activation_date" value="" class="regular-text"></td>
                </tr>
            </table>
            <?php submit_button(__('Save Project', 'ecopower-tracker')); ?>
        </form>
        <h2><?php esc_html_e('Projects', 'ecopower-tracker'); ?></h2>
        <table class="wp-list-table widefat fixed striped">
            <thead>
                <tr>
                    <th><?php esc_html_e('Project Company', 'ecopower-tracker'); ?></th>
                    <th><?php esc_html_e('Project Name', 'ecopower-tracker'); ?></th>
                    <th><?php esc_html_e('Project Location', 'ecopower-tracker'); ?></th>
                    <th><?php esc_html_e('Type of Plant', 'ecopower-tracker'); ?></th>
                    <th><?php esc_html_e('Project CUF', 'ecopower-tracker'); ?></th>
                    <th><?php esc_html_e('Generation Capacity', 'ecopower-tracker'); ?></th>
                    <th><?php esc_html_e('Date of Activation', 'ecopower-tracker'); ?></th>
                    <th><?php esc_html_e('Actions', 'ecopower-tracker'); ?></th>
                </tr>
            </thead>
            <tbody>
                <?php
                global $wpdb;
                $table_name = $wpdb->prefix . 'ecopower_projects';
                $projects = $wpdb->get_results("SELECT * FROM $table_name");

                foreach ($projects as $project) {
                    echo '<tr>';
                    echo '<td>' . esc_html($project->project_company) . '</td>';
                    echo '<td>' . esc_html($project->project_name) . '</td>';
                    echo '<td>' . esc_html($project->project_location) . '</td>';
                    echo '<td>' . esc_html($project->plant_type) . '</td>';
                    echo '<td>' . esc_html($project->project_cuf) . '</td>';
                    echo '<td>' . esc_html($project->generation_capacity) . '</td>';
                    echo '<td>' . esc_html($project->activation_date) . '</td>';
                    echo '<td><a href="' . admin_url('admin.php?page=ecopower-tracker&action=edit&id=' . $project->id) . '">' . __('Edit', 'ecopower-tracker') . '</a> | <a href="' . admin_url('admin.php?page=ecopower-tracker&action=delete&id=' . $project->id) . '" onclick="return confirm(\'' . __('Are you sure you want to delete this project?', 'ecopower-tracker') . '\');">' . __('Delete', 'ecopower-tracker') . '</a></td>';
                    echo '</tr>';
                }
                ?>
            </tbody>
        </table>
        <h2><?php esc_html_e('CSV Import/Export', 'ecopower-tracker'); ?></h2>
        <form method="post" enctype="multipart/form-data" action="<?php echo admin_url('admin-post.php'); ?>">
            <input type="hidden" name="action" value="ecopower_tracker_import_csv" />
            <input type="file" name="ecopower_tracker_csv" />
    <?php submit_button(__('Import CSV', 'ecopower-tracker')); ?>
        </form>
        <form method="post" action="<?php echo admin_url('admin-post.php'); ?>">
            <input type="hidden" name="action" value="ecopower_tracker_export_csv" />
            <?php submit_button(__('Export CSV', 'ecopower-tracker')); ?>
        </form>
        <h2><?php esc_html_e('Shortcodes', 'ecopower-tracker'); ?></h2>
        <ul>
            <li>[ecopower_total_power_generated] - <?php esc_html_e('Total power generated', 'ecopower-tracker'); ?></li>
            <li>[ecopower_total_carbon_offset] - <?php esc_html_e('Total carbon offset', 'ecopower-tracker'); ?></li>
            <li>[ecopower_project_power_generated project_id="X"] - <?php esc_html_e('Power generated by specific project', 'ecopower-tracker'); ?></li>
            <li>[ecopower_project_carbon_offset project_id="X"] - <?php esc_html_e('Carbon offset by specific project', 'ecopower-tracker'); ?></li>
            <li>[ecopower_subgroup_power_generated project_ids="X,Y,Z"] - <?php esc_html_e('Power generated by subgroup of projects', 'ecopower-tracker'); ?></li>
            <li>[ecopower_subgroup_carbon_offset project_ids="X,Y,Z"] - <?php esc_html_e('Carbon offset by subgroup of projects', 'ecopower-tracker'); ?></li>
        </ul>
    </div>
    <?php
}
// Handle form submission for adding/editing projects
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['project_company'])) {
    global $wpdb;
    $table_name = $wpdb->prefix . 'ecopower_projects';

    $data = array(
        'project_company' => sanitize_text_field($_POST['project_company']),
        'project_name' => sanitize_text_field($_POST['project_name']),
        'project_location' => sanitize_text_field($_POST['project_location']),
        'plant_type' => sanitize_text_field($_POST['plant_type']),
        'project_cuf' => floatval($_POST['project_cuf']),
        'generation_capacity' => floatval($_POST['generation_capacity']),
        'activation_date' => sanitize_text_field($_POST['activation_date']),
    );

    if (isset($_GET['action']) && $_GET['action'] == 'edit' && isset($_GET['id'])) {
        $wpdb->update($table_name, $data, array('id' => intval($_GET['id'])));
    } else {
        $wpdb->insert($table_name, $data);
    }

    wp_redirect(admin_url('admin.php?page=ecopower-tracker'));
    exit;
}

// Handle delete action
if (isset($_GET['action']) && $_GET['action'] == 'delete' && isset($_GET['id'])) {
    global $wpdb;
    $table_name = $wpdb->prefix . 'ecopower_projects';
    $wpdb->delete($table_name, array('id' => intval($_GET['id'])));
    wp_redirect(admin_url('admin.php?page=ecopower-tracker'));
    exit;
}
?>