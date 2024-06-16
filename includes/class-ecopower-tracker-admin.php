// Path: EcoPower-Tracker/includes/class-ecopower-tracker-admin.php
// File: class-ecopower-tracker-admin.php

<?php

if (!defined('ABSPATH')) {
    exit; // Exit if accessed directly
}

class EcoPower_Tracker_Admin {

    public function __construct() {
        // Hook to enqueue admin scripts and styles
        add_action('admin_enqueue_scripts', array($this, 'enqueue_admin_assets'));

        // Hook to add custom admin columns
        add_filter('manage_edit-ecopower_tracker_columns', array($this, 'set_custom_columns'));
        add_action('manage_ecopower_tracker_posts_custom_column', array($this, 'custom_column_content'), 10, 2);
    }

    // Function to enqueue admin scripts and styles
    public function enqueue_admin_assets() {
        // Enqueue admin styles
        wp_enqueue_style(
            'ecopower-tracker-admin-css',
            plugin_dir_url(__FILE__) . '../assets/css/ecopower-tracker-admin.css',
            array(),
            '1.0.0',
            'all'
        );

        // Enqueue admin scripts
        wp_enqueue_script(
            'ecopower-tracker-admin-js',
            plugin_dir_url(__FILE__) . '../assets/js/ecopower-tracker-admin.js',
            array('jquery'),
            '1.0.0',
            true
        );
    }

    // Function to set custom columns in the admin listing
    public function set_custom_columns($columns) {
        $columns['project_number'] = __('Project Number', 'ecopower-tracker');
        $columns['project_company'] = __('Project Company', 'ecopower-tracker');
        $columns['project_name'] = __('Project Name', 'ecopower-tracker');
        $columns['project_location'] = __('Project Location', 'ecopower-tracker');
        $columns['type_of_plant'] = __('Type of Plant', 'ecopower-tracker');
        $columns['generation_capacity'] = __('Generation Capacity', 'ecopower-tracker');
        return $columns;
    }

    // Function to display content for custom columns
    public function custom_column_content($column, $post_id) {
        switch ($column) {
            case 'project_number':
                echo get_post_meta($post_id, 'project_number', true);
                break;
            case 'project_company':
                echo get_post_meta($post_id, 'project_company', true);
                break;
            case 'project_name':
                echo get_post_meta($post_id, 'project_name', true);
                break;
            case 'project_location':
                echo get_post_meta($post_id, 'project_location', true);
                break;
            case 'type_of_plant':
                echo get_post_meta($post_id, 'type_of_plant', true);
                break;
            case 'generation_capacity':
                echo get_post_meta($post_id, 'generation_capacity', true);
                break;
        }
    }
}

// Initialize the admin functionalities
new EcoPower_Tracker_Admin();

?>