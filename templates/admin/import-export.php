<?php
if (!defined('ABSPATH')) {
    exit;
}
?>

<div class="wrap eco-import-export">
    <h1><?php esc_html_e('Import/Export Projects', 'ecopower-tracker'); ?></h1>
    
    <div class="card">
        <h2><?php esc_html_e('Export Projects', 'ecopower-tracker'); ?></h2>
        <p><?php esc_html_e('Export your projects to a CSV file. This file can be used to import projects into another installation.', 'ecopower-tracker'); ?></p>
        
        <form method="post" action="<?php echo esc_url(admin_url('admin-post.php')); ?>">
            <input type="hidden" name="action" value="ecopower_tracker_export_csv">
            <?php wp_nonce_field('ecopower_tracker_export_csv', 'ecopower_tracker_nonce'); ?>
            
            <p class="submit">
                <button type="submit" class="button button-primary">
                    <span class="dashicons dashicons-download"></span>
                    <?php esc_html_e('Export Projects', 'ecopower-tracker'); ?>
                </button>
            </p>
        </form>
    </div>
    
    <div class="card">
        <h2><?php esc_html_e('Import Projects', 'ecopower-tracker'); ?></h2>
        <p><?php esc_html_e('Import projects from a CSV file. The file should be in the same format as the exported file.', 'ecopower-tracker'); ?></p>
        
        <div class="notice notice-info">
            <p><strong><?php esc_html_e('CSV Format:', 'ecopower-tracker'); ?></strong></p>
            <ul style="list-style-type: disc; margin-left: 20px;">
                <li><?php esc_html_e('First row should contain column headers', 'ecopower-tracker'); ?></li>
                <li><?php esc_html_e('Required columns: Project Number, Company, Project Name, Location, Type of Plant, Generation Capacity', 'ecopower-tracker'); ?></li>
                <li><?php esc_html_e('Optional columns: Project CUF, Date of Activation', 'ecopower-tracker'); ?></li>
                <li><?php esc_html_e('Date format: YYYY-MM-DD', 'ecopower-tracker'); ?></li>
            </ul>
        </div>
        
        <form method="post" enctype="multipart/form-data" action="<?php echo esc_url(admin_url('admin-post.php')); ?>">
            <input type="hidden" name="action" value="ecopower_tracker_upload_csv">
            <?php wp_nonce_field('ecopower_tracker_upload_csv', 'ecopower_tracker_nonce'); ?>
            
            <table class="form-table">
                <tr>
                    <th scope="row">
                        <label for="csv_file"><?php esc_html_e('CSV File', 'ecopower-tracker'); ?></label>
                    </th>
                    <td>
                        <input type="file" name="ecopower_tracker_csv" id="csv_file" accept=".csv" required>
                        <p class="description"><?php esc_html_e('Select a CSV file to import.', 'ecopower-tracker'); ?></p>
                    </td>
                </tr>
                <tr>
                    <th scope="row">
                        <?php esc_html_e('Import Options', 'ecopower-tracker'); ?>
                    </th>
                    <td>
                        <fieldset>
                            <label>
                                <input type="checkbox" name="update_existing" value="1" checked>
                                <?php esc_html_e('Update existing projects with matching project numbers', 'ecopower-tracker'); ?>
                            </label>
                            <br>
                            <label>
                                <input type="checkbox" name="skip_first_row" value="1" checked>
                                <?php esc_html_e('Skip first row (header row)', 'ecopower-tracker'); ?>
                            </label>
                        </fieldset>
                    </td>
                </tr>
            </table>
            
            <p class="submit">
                <button type="submit" class="button button-primary">
                    <span class="dashicons dashicons-upload"></span>
                    <?php esc_html_e('Import Projects', 'ecopower-tracker'); ?>
                </button>
            </p>
        </form>
    </div>
    
    <div class="card">
        <h2><?php esc_html_e('Download Sample CSV', 'ecopower-tracker'); ?></h2>
        <p><?php esc_html_e('Download a sample CSV file to see the correct format for importing projects.', 'ecopower-tracker'); ?></p>
        
        <p>
            <a href="<?php echo esc_url(plugins_url('samples/sample-projects.csv', ECOPOWER_TRACKER_FILE)); ?>" class="button">
                <span class="dashicons dashicons-media-spreadsheet"></span>
                <?php esc_html_e('Download Sample CSV', 'ecopower-tracker'); ?>
            </a>
        </p>
    </div>
</div>

<style>
.eco-import-export .card {
    background: #fff;
    padding: 20px;
    margin: 20px 0;
    border: 1px solid #ccd0d4;
    box-shadow: 0 1px 1px rgba(0, 0, 0, 0.04);
    max-width: 800px;
}

.eco-import-export h2 {
    margin-top: 0;
    padding-bottom: 10px;
    border-bottom: 1px solid #eee;
}

.eco-import-export .button .dashicons {
    vertical-align: middle;
    margin-right: 5px;
}

.eco-import-export .form-table th {
    width: 200px;
}

@media screen and (max-width: 782px) {
    .eco-import-export .form-table th {
        width: 100%;
        display: block;
        padding-bottom: 5px;
    }
    
    .eco-import-export .form-table td {
        display: block;
        padding: 0 0 15px;
    }
}
</style>
