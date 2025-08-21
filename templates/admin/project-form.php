<?php
if (!defined('ABSPATH')) {
    exit;
}

$is_edit = !empty($project);
$action = $is_edit ? 'edit' : 'add';
$title = $is_edit ? __('Edit Project', 'ecopower-tracker') : __('Add New Project', 'ecopower-tracker');
?>

<div class="wrap">
    <h1 class="wp-heading-inline"><?php echo esc_html($title); ?></h1>
    <a href="<?php echo esc_url(admin_url('admin.php?page=ecopower-tracker')); ?>" class="page-title-action">
        <?php esc_html_e('Back to Projects', 'ecopower-tracker'); ?>
    </a>
    <hr class="wp-header-end">

    <div class="ecopower-tracker-admin">
        <form method="post" action="<?php echo esc_url(admin_url('admin.php?page=ecopower-tracker' . ($is_edit ? '&action=edit&id=' . $project->id : '-add'))); ?>">
            <?php wp_nonce_field('ecopower_tracker_save_project', 'ecopower_tracker_nonce'); ?>
            
            <table class="form-table">
                <tr>
                    <th scope="row">
                        <label for="project_number"><?php esc_html_e('Project Number', 'ecopower-tracker'); ?> <span class="required">*</span></label>
                    </th>
                    <td>
                        <input type="text" name="project_number" id="project_number" class="regular-text" 
                               value="<?php echo $is_edit ? esc_attr($project->project_number) : ''; ?>" required>
                        <p class="description"><?php esc_html_e('A unique identifier for this project.', 'ecopower-tracker'); ?></p>
                    </td>
                </tr>
                
                <tr>
                    <th scope="row">
                        <label for="project_company"><?php esc_html_e('Company', 'ecopower-tracker'); ?> <span class="required">*</span></label>
                    </th>
                    <td>
                        <input type="text" name="project_company" id="project_company" class="regular-text" 
                               value="<?php echo $is_edit ? esc_attr($project->project_company) : ''; ?>" required>
                    </td>
                </tr>
                
                <tr>
                    <th scope="row">
                        <label for="project_name"><?php esc_html_e('Project Name', 'ecopower-tracker'); ?> <span class="required">*</span></label>
                    </th>
                    <td>
                        <input type="text" name="project_name" id="project_name" class="regular-text" 
                               value="<?php echo $is_edit ? esc_attr($project->project_name) : ''; ?>" required>
                    </td>
                </tr>
                
                <tr>
                    <th scope="row">
                        <label for="project_location"><?php esc_html_e('Location', 'ecopower-tracker'); ?> <span class="required">*</span></label>
                    </th>
                    <td>
                        <input type="text" name="project_location" id="project_location" class="regular-text" 
                               value="<?php echo $is_edit ? esc_attr($project->project_location) : ''; ?>" required>
                    </td>
                </tr>
                
                <tr>
                    <th scope="row">
                        <label for="type_of_plant"><?php esc_html_e('Type of Plant', 'ecopower-tracker'); ?> <span class="required">*</span></label>
                    </th>
                    <td>
                        <select name="type_of_plant" id="type_of_plant" class="regular-text" required>
                            <option value=""><?php esc_html_e('Select a plant type', 'ecopower-tracker'); ?></option>
                            <?php foreach ($plant_types as $type) : ?>
                                <option value="<?php echo esc_attr($type); ?>" <?php selected($is_edit && $project->type_of_plant === $type); ?>>
                                    <?php echo esc_html(ucfirst($type)); ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </td>
                </tr>
                
                <tr>
                    <th scope="row">
                        <label for="generation_capacity"><?php esc_html_e('Generation Capacity (MW)', 'ecopower-tracker'); ?> <span class="required">*</span></label>
                    </th>
                    <td>
                        <input type="number" name="generation_capacity" id="generation_capacity" class="small-text" 
                               step="0.01" min="0" value="<?php echo $is_edit ? esc_attr($project->generation_capacity) : '0'; ?>" required>
                        <p class="description"><?php esc_html_e('In megawatts (MW)', 'ecopower-tracker'); ?></p>
                    </td>
                </tr>
                
                <tr>
                    <th scope="row">
                        <label for="project_cuf"><?php esc_html_e('Capacity Utilization Factor (CUF)', 'ecopower-tracker'); ?></label>
                    </th>
                    <td>
                        <input type="number" name="project_cuf" id="project_cuf" class="small-text" 
                               step="0.01" min="0" max="100" value="<?php echo $is_edit ? esc_attr($project->project_cuf) : '0'; ?>">
                        <p class="description"><?php esc_html_e('As a percentage (0-100)', 'ecopower-tracker'); ?></p>
                    </td>
                </tr>
                
                <tr>
                    <th scope="row">
                        <label for="date_of_activation"><?php esc_html_e('Date of Activation', 'ecopower-tracker'); ?></label>
                    </th>
                    <td>
                        <input type="text" name="date_of_activation" id="date_of_activation" class="regular-text datepicker" 
                               value="<?php echo $is_edit ? esc_attr($project->date_of_activation) : ''; ?>">
                        <p class="description"><?php esc_html_e('Format: YYYY-MM-DD', 'ecopower-tracker'); ?></p>
                    </td>
                </tr>
            </table>
            
            <p class="submit">
                <button type="submit" class="button button-primary">
                    <?php echo $is_edit ? esc_attr__('Update Project', 'ecopower-tracker') : esc_attr__('Add Project', 'ecopower-tracker'); ?>
                </button>
                <a href="<?php echo esc_url(admin_url('admin.php?page=ecopower-tracker')); ?>" class="button">
                    <?php esc_html_e('Cancel', 'ecopower-tracker'); ?>
                </a>
            </p>
        </form>
    </div>
</div>

<script>
jQuery(document).ready(function($) {
    $('.datepicker').datepicker({
        dateFormat: 'yy-mm-dd',
        changeMonth: true,
        changeYear: true,
        yearRange: '2000:2030'
    });
});
</script>
