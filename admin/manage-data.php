<div class="wrap">
    <h1>Manage Projects</h1>
    <?php
    global $wpdb;
    $table_name = $wpdb->prefix . 'ecopower_projects';
    $projects = $wpdb->get_results("SELECT * FROM $table_name", ARRAY_A);

    if ($projects) {
        echo '<table class="wp-list-table widefat fixed striped">';
        echo '<thead><tr><th>Project Company</th><th>Project Name</th><th>Project Location</th><th>Type of Plant</th><th>CUF</th><th>Generation Capacity</th><th>Date of Activation</th><th>Actions</th></tr></thead>';
        echo '<tbody>';
        foreach ($projects as $project) {
            echo '<tr>';
            echo '<td>' . esc_html($project['project_company']) . '</td>';
            echo '<td>' . esc_html($project['project_name']) . '</td>';
            echo '<td>' . esc_html($project['project_location']) . '</td>';
            echo '<td>' . esc_html($project['type_of_plant']) . '</td>';
            echo '<td>' . esc_html($project['cuf']) . '</td>';
            echo '<td>' . esc_html($project['generation_capacity']) . '</td>';
            echo '<td>' . esc_html($project['date_of_activation']) . '</td>';
            echo '<td>';
            echo '<a href="' . esc_url(admin_url('admin.php?page=ecopower-tracker-manage&action=edit&project_number=' . $project['project_number'])) . '">Edit</a> | ';
            echo '<a href="' . esc_url(admin_url('admin-post.php?action=ecopower_tracker_delete_project&project_number=' . $project['project_number'])) . '" onclick="return confirm(\'Are you sure you want to delete this project?\')">Delete</a>';
            echo '</td>';
            echo '</tr>';
        }
        echo '</tbody>';
        echo '</table>';
    } else {
        echo '<p>No projects found.</p>';
    }
    ?>
    <h2>Add New Project</h2>
    <form method="post" action="<?php echo esc_url(admin_url('admin-post.php')); ?>">
        <input type="hidden" name="action" value="ecopower_tracker_add_project">
        <table class="form-table">
            <tr>
                <th scope="row"><label for="project_company">Project Company</label></th>
                <td><input name="project_company" type="text" id="project_company" value="" class="regular-text"></td>
            </tr>
            <tr>
                <th scope="row"><label for="project_name">Project Name</label></th>
                <td><input name="project_name" type="text" id="project_name" value="" class="regular-text"></td>
            </tr>
            <tr>
                <th scope="row"><label for="project_location">Project Location</label></th>
                <td><input name="project_location" type="text" id="project_location" value="" class="regular-text"></td>
            </tr>
            <tr>
                <th scope="row"><label for="type_of_plant">Type of Plant</label></th>
                <td>
                    <select name="type_of_plant" id="type_of_plant">
                        <option value="Wind">Wind</option>
                        <option value="Solar">Solar</option>
                    </select>
                </td>
            </tr>
            <tr>
                <th scope="row"><label for="cuf">CUF</label></th>
                <td><input name="cuf" type="number" step="0.01" id="cuf" value="" class="regular-text"></td>
            </tr>
            <tr>
                <th scope="row"><label for="generation_capacity">Generation Capacity (KW)</label></th>
                <td><input name="generation_capacity" type="number" step="0.01" id="generation_capacity" value="" class="regular-text"></td>
            </tr>
            <tr>
                <th scope="row"><label for="date_of_activation">Date of Activation</label></th>
                <td><input name="date_of_activation" type="date" id="date_of_activation" value="" class="regular-text"></td>
            </tr>
        </table>
        <p class="submit">
            <input type="submit" name="ecopower_tracker_submit" id="submit" class="button button-primary" value="Add Project">
        </p>
    </form>
</div>