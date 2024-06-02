<?php
if (!defined('ABSPATH')) {
    exit; // Exit if accessed directly
}

function ecopower_tracker_admin_page() {
    global $wpdb;
    $table_name = $wpdb->prefix . 'ecopower_projects';

    if (isset($_POST['ecopower_tracker_add_project'])) {
        $project_company = sanitize_text_field($_POST['project_company']);
        $project_name = sanitize_text_field($_POST['project_name']);
        $project_location = sanitize_text_field($_POST['project_location']);
        $plant_type = sanitize_text_field($_POST['plant_type']);
        $project_cuf = floatval($_POST['project_cuf']);
        $generation_capacity = floatval($_POST['generation_capacity']);
        $activation_date = sanitize_text_field($_POST['activation_date']);

        $wpdb->insert($table_name, array(
            'project_company' => $project_company,
            'project_name' => $project_name,
            'project_location' => $project_location,
            'plant_type' => $plant_type,
            'project_cuf' => $project_cuf,
            'generation_capacity' => $generation_capacity,
            'activation_date' => $activation_date
        ));
    }

    if (isset($_POST['ecopower_tracker_delete_project'])) {
        $project_id = intval($_POST['project_id']);
        $wpdb->delete($table_name, array('id' => $project_id));
    }

    if (isset($_POST['ecopower_tracker_edit_project'])) {
        $project_id = intval($_POST['project_id']);
        $project_company = sanitize_text_field($_POST['project_company']);
        $project_name = sanitize_text_field($_POST['project_name']);
        $project_location = sanitize_text_field($_POST['project_location']);
        $plant_type = sanitize_text_field($_POST['plant_type']);
        $project_cuf = floatval($_POST['project_cuf']);
        $generation_capacity = floatval($_POST['generation_capacity']);
        $activation_date = sanitize_text_field($_POST['activation_date']);

        $wpdb->update($table_name, array(
            'project_company' => $project_company,
            'project_name' => $project_name,
            'project_location' => $project_location,
            'plant_type' => $plant_type,
            'project_cuf' => $project_cuf,
            'generation_capacity' => $generation_capacity,
            'activation_date' => $activation_date
        ), array('id' => $project_id));
    }

    $projects = $wpdb->get_results("SELECT * FROM $table_name");

    ?>
    <div class="wrap">
        <h1><?php _e('EcoPower Tracker', 'ecopower-tracker'); ?></h1>

        <h2><?php _e('Add New Project', 'ecopower-tracker'); ?></h2>
        <form method="post" action="">
            <table class="form-table">
                <tr>
                    <th scope="row"><?php _e('Project Company', 'ecopower-tracker'); ?></th>
                    <td><input type="text" name="project_company" required /></td>
                </tr>
                <tr>
                    <th scope="row"><?php _e('Project Name', 'ecopower-tracker'); ?></th>
                    <td><input type="text" name="project_name" required /></td>
                </tr>
                <tr>
                    <th scope="row"><?php _e('Project Location', 'ecopower-tracker'); ?></th>
                    <td><input type="text" name="project_location" required /></td>
                </tr>
                <tr>
                    <th scope="row"><?php _e('Type of Plant', 'ecopower-tracker'); ?></th>
                    <td>
                        <select name="plant_type" required>
                            <option value="Wind"><?php _e('Wind', 'ecopower-tracker'); ?></option>
                            <option value="Solar"><?php _e('Solar', 'ecopower-tracker'); ?></option>
                        </select>
                    </td>
                </tr>
                <tr>
                    <th scope="row"><?php _e('Project CUF', 'ecopower-tracker'); ?></th>
                    <td><input type="number" step="0.01" name="project_cuf" required /></td>
                </tr>
                <tr>
                    <th scope="row"><?php _e('Generation Capacity (in KWs)', 'ecopower-tracker'); ?></th>
                    <td><input type="number" step="0.01" name="generation_capacity" required /></td>
                </tr>
                <tr>
                    <th scope="row"><?php _e('Date of Activation', 'ecopower-tracker'); ?></th>
                    <td><input type="date" name="activation_date" required /></td>
                </tr>
            </table>
            <p class="submit"><input type="submit" name="ecopower_tracker_add_project" class="button-primary" value="<?php _e('Add Project', 'ecopower-tracker'); ?>" /></p>
        </form>

        <h2><?php _e('All Projects', 'ecopower-tracker'); ?></h2>
        <table class="wp-list-table widefat fixed striped">
            <thead>
                <tr>
                    <th><?php _e('ID', 'ecopower-tracker'); ?></th>
                    <th><?php _e('Project Company', 'ecopower-tracker'); ?></th>
                    <th><?php _e('Project Name', 'ecopower-tracker'); ?></th>
                    <th><?php _e('Project Location', 'ecopower-tracker'); ?></th>
                    <th><?php _e('Type of Plant', 'ecopower-tracker'); ?></th>
                    <th><?php _e('Project CUF', 'ecopower-tracker'); ?></th>
                    <th><?php _e('Generation Capacity (in KWs)', 'ecopower-tracker'); ?></th>
                    <th><?php _e('Date of Activation', 'ecopower-tracker'); ?></th>
                    <th><?php _e('Actions', 'ecopower-tracker'); ?></th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($projects as $project) { ?>
                <tr>
                    <td><?php echo esc_html($project->id); ?></td>
                    <td><?php echo esc_html($project->project_company); ?></td>
                    <td><?php echo esc_html($project->project_name); ?></td>
                    <td><?php echo esc_html($project->project_location); ?></td>
                    <td><?php echo esc_html($project->plant_type); ?></td>
                    <td><?php echo esc_html($project->project_cuf); ?></td>
                    <td><?php echo esc_html(number_format($project->generation_capacity)); ?></td>
                    <td><?php echo esc_html($project->activation_date); ?></td>
                    <td>
                        <form method="post" action="" style="display:inline;">
                            <input type="hidden" name="project_id" value="<?php echo esc_html($project->id); ?>" />
                            <input type="submit" name="ecopower_tracker_delete_project" class="button" value="<?php _e('Delete', 'ecopower-tracker'); ?>" />
                        </form>
                        <form method="post" action="" style="display:inline;">
                            <input type="hidden" name="project_id" value="<?php echo esc_html($project->id); ?>" />
                            <input type="hidden" name="project_company" value="<?php echo esc_html($project->project_company); ?>" />
                            <input type="hidden" name="project_name" value="<?php echo esc_html($project->project_name); ?>" />
                            <input type="hidden" name="project_location" value="<?php echo esc_html($project->project_location); ?>" />
                            <input type="hidden" name="plant_type" value="<?php echo esc_html($project->plant_type); ?>" />
                            <input type="hidden" name="project_cuf" value="<?php echo esc_html($project->project_cuf); ?>" />
                            <input type="hidden" name="generation_capacity" value="<?php echo esc_html($project->generation_capacity); ?>" />
                            <input type="hidden" name="activation_date" value="<?php echo esc_html($project->activation_date); ?>" />
                            <input type="submit" name="ecopower_tracker_edit_project" class="button" value="<?php _e('Edit', 'ecopower-tracker'); ?>" />
                        </form>
                    </td>
                </tr>
                <?php } ?>
            </tbody>
        </table>
    </div>
    <?php
}
?>