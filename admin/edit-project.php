<div class="wrap">
    <h1>Edit Project</h1>
    <?php
    global $wpdb;
    $table_name = $wpdb->prefix . 'ecopower_projects';
    
    // Check if project_number is set and valid
    if (isset($_GET['project_number']) && is_numeric($_GET['project_number'])) {
        $project_number = intval($_GET['project_number']);
        $project = $wpdb->get_row($wpdb->prepare("SELECT * FROM $table_name WHERE project_number = %d", $project_number), ARRAY_A);
        
        if ($project):
        ?>
        <form method="post" action="<?php echo esc_url(admin_url('admin-post.php')); ?>">
            <input type="hidden" name="action" value="ecopower_tracker_edit_project">
            <input type="hidden" name="project_number" value="<?php echo esc_attr($project['project_number']); ?>">
            <table class="form-table">
                <tr>
                    <th scope="row"><label for="project_company">Project Company</label></th>
                    <td><input name="project_company" type="text" id="project_company" value="<?php echo esc_attr($project['project_company']); ?>" class="regular-text"></td>
                </tr>
                <tr>
                    <th scope="row"><label for="project_name">Project Name</label></th>
                    <td><input name="project_name" type="text" id="project_name" value="<?php echo esc_attr($project['project_name']); ?>" class="regular-text"></td>
                </tr>
                <tr>
                    <th scope="row"><label for="project_location">Project Location</label></th>
                    <td><input name="project_location" type="text" id="project_location" value="<?php echo esc_attr($project['project_location']); ?>" class="regular-text"></td>
                </tr>
                <tr>
                    <th scope="row"><label for="type_of_plant">Type of Plant</label></th>
                    <td>
                        <select name="type_of_plant" id="type_of_plant">
                            <option value="Wind" <?php selected($project['type_of_plant'], 'Wind'); ?>>Wind</option>
                            <option value="Solar" <?php selected($project['type_of_plant'], 'Solar'); ?>>Solar</option>
                        </select>
                    </td>
                </tr>
                <tr>
                    <th scope="row"><label for="cuf">CUF</label></th>
                    <td><input name="cuf" type="number" step="0.01" id="cuf" value="<?php echo esc_attr($project['cuf']); ?>" class="regular-text"></td>
                </tr>
                <tr>
                    <th scope="row"><label for="generation_capacity">Generation Capacity (KW)</label></th>
                    <td><input name="generation_capacity" type="number" step="0.01" id="generation_capacity" value="<?php echo esc_attr($project['generation_capacity']); ?>" class="regular-text"></td>
                </tr>
                <tr>
                    <th scope="row"><label for="date_of_activation">Date of Activation</label></th>
                    <td><input name="date_of_activation" type="date" id="date_of_activation" value="<?php echo esc_attr($project['date_of_activation']); ?>" class="regular-text"></td>
                </tr>
            </table>
            <p class="submit">
                <input type="submit" name="ecopower_tracker_edit_submit" id="submit" class="button button-primary" value="Save Changes">
            </p>
        </form>
        <?php else: ?>
            <p>Project not found.</p>
        <?php endif;
    } else {
        echo '<p>Invalid project number.</p>';
    }
    ?>
</div>
