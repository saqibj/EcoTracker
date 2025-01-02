<?php

namespace EcoPowerTracker;

if (!defined('ABSPATH')) {
    exit; // Exit if accessed directly
}

/**
 * Admin functionality handler for EcoPower Tracker
 */
class EcoPower_Tracker_Admin {

    /**
     * Constructor
     */
    public function __construct() {
        // Hook to enqueue admin scripts and styles
        add_action('admin_enqueue_scripts', array($this, 'enqueue_admin_assets'));

        // Hook to add custom admin columns
        add_filter('manage_edit-ecopower_tracker_columns', array($this, 'set_custom_columns'));
        add_action('manage_ecopower_tracker_posts_custom_column', array($this, 'custom_column_content'), 10, 2);
    }

    /**
     * Enqueue admin scripts and styles
     */
    public function enqueue_admin_assets() {
        // Enqueue admin styles
        wp_enqueue_style(
            'ecopower-tracker-admin-css',
            ECOPOWER_TRACKER_URL . 'assets/css/ecopower-tracker-admin.css',
            array(),
            ECOPOWER_TRACKER_VERSION,
            'all'
        );

        // Enqueue admin scripts
        wp_enqueue_script(
            'ecopower-tracker-admin-js',
            ECOPOWER_TRACKER_URL . 'assets/js/ecopower-tracker-admin.js',
            array('jquery'),
            ECOPOWER_TRACKER_VERSION,
            true
        );

        // Localize script for translations and variables
        wp_localize_script('ecopower-tracker-admin-js', 'ecoPowerTracker', array(
            'ajaxUrl' => admin_url('admin-ajax.php'),
            'nonce' => wp_create_nonce('ecopower_tracker_admin_nonce'),
            'strings' => array(
                'error' => __('An error occurred', 'ecopower-tracker'),
                // Add more strings as needed
            )
        ));
    }

    /**
     * Set custom columns in the admin listing
     */
    public function set_custom_columns($columns) {
        $columns['project_number'] = __('Project Number', 'ecopower-tracker');
        $columns['project_company'] = __('Project Company', 'ecopower-tracker');
        $columns['project_name'] = __('Project Name', 'ecopower-tracker');
        $columns['project_location'] = __('Project Location', 'ecopower-tracker');
        $columns['type_of_plant'] = __('Type of Plant', 'ecopower-tracker');
        $columns['generation_capacity'] = __('Generation Capacity', 'ecopower-tracker');
        return $columns;
    }

    /**
     * Display content for custom columns
     */
    public function custom_column_content($column, $post_id) {
        // Sanitize inputs
        $column = sanitize_key($column);
        $post_id = absint($post_id);

        // Verify post exists
        if (!get_post($post_id)) {
            return;
        }

        switch ($column) {
            case 'project_number':
            case 'project_company':
            case 'project_name':
            case 'project_location':
            case 'type_of_plant':
            case 'generation_capacity':
                echo esc_html(get_post_meta($post_id, $column, true));
                break;
            default:
                break;
        }
    }
}

// Initialize the admin functionalities
new EcoPower_Tracker_Admin();

?>