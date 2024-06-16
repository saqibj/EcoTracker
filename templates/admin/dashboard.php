// Path: EcoPower-Tracker/templates/admin/dashboard.php
// File: dashboard.php

<?php

if (!defined('ABSPATH')) {
    exit; // Exit if accessed directly
}

// Include necessary utility functions
require_once plugin_dir_path(__FILE__) . '../../includes/class-ecopower-tracker-utils.php';

// Display the admin dashboard
?>

<div class="wrap">
    <h1><?php _e('EcoPower Tracker Dashboard', 'ecopower-tracker'); ?></h1>

    <?php
    // Display totals
    EcoPower_Tracker_Dashboard::display_totals();
    ?>

    <h2><?php _e('Project List', 'ecopower-tracker'); ?></h2>

    <?php
    // List all projects
    EcoPower_Tracker_Dashboard::list_projects();
    ?>

    <h2><?php _e('Upload CSV', 'ecopower-tracker'); ?></h2>
    <form method="post" action="<?php echo admin_url('admin-post.php'); ?>" enctype="multipart/form-data">
        <?php wp_nonce_field('ecopower_tracker_upload_csv', 'ecopower_tracker_nonce'); ?>
        <input type="hidden" name="action" value="ecopower_tracker_upload_csv">
        <input type="file" name="ecopower_tracker_csv" required>
        <input type="submit" class="button-primary" value="<?php _e('Upload CSV', 'ecopower-tracker'); ?>">
    </form>

    <h2><?php _e('Export Data', 'ecopower-tracker'); ?></h2>
    <form method="post" action="<?php echo admin_url('admin-post.php'); ?>">
        <?php wp_nonce_field('ecopower_tracker_export_csv', 'ecopower_tracker_nonce'); ?>
        <input type="hidden" name="action" value="ecopower_tracker_export_csv">
        <input type="submit" class="button-primary" value="<?php _e('Export to CSV', 'ecopower-tracker'); ?>">
    </form>
</div>

<style>
    .ecopower-tracker-totals {
        margin-bottom: 20px;
        padding: 10px;
        border: 1px solid #ddd;
        background-color: #f9f9f9;
        border-radius: 5px;
    }

    .ecopower-tracker-totals h2 {
        margin: 0;
        padding: 0;
        font-size: 1.5em;
    }

    .widefat.fixed {
        margin-top: 20px;
    }

    .widefat.fixed th, .widefat.fixed td {
        padding: 10px;
    }

    .widefat.fixed th {
        background-color: #f1f1f1;
    }
</style>

<?php
?>