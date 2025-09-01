<?php
/**
 * The public-facing functionality of the plugin.
 *
 * @package EcoPowerTracker
 * @since 2.0.1
 */

namespace EcoPowerTracker;

if (!defined('ABSPATH')) {
    exit; // Exit if accessed directly
}

/**
 * Class EcoPower_Tracker_Public
 */
class EcoPower_Tracker_Public {

    /**
     * The ID of this plugin.
     */
    private $plugin_name;

    /**
     * The version of this plugin.
     */
    private $version;

    /**
     * Initialize the class and set its properties.
     */
    public function __construct($plugin_name, $version) {
        $this->plugin_name = $plugin_name;
        $this->version = $version; // Use the passed version parameter instead of hardcoded value
    }

    /**
     * Register the stylesheets for the public-facing side of the site.
     */
    public function enqueue_styles() {
        $suffix = $this->get_asset_suffix();
        
        wp_register_style(
            $this->plugin_name . '-frontend',
            plugin_dir_url(__FILE__) . "../assets/css/ecopower-tracker-frontend{$suffix}.css",
            array(),
            $this->version,
            'all'
        );
    }

    /**
     * Register the JavaScript for the public-facing side of the site.
     */
    public function enqueue_scripts() {
        $suffix = $this->get_asset_suffix();
        
        wp_register_script(
            $this->plugin_name . '-frontend',
            plugin_dir_url(__FILE__) . "../assets/js/ecopower-tracker-frontend{$suffix}.js",
            array('jquery'),
            $this->version,
            true
        );

        // Localize script with AJAX URL and nonce
        wp_localize_script(
            $this->plugin_name . '-frontend',
            'ecopowerTracker',
            array(
                'ajaxurl' => admin_url('admin-ajax.php'),
                'nonce' => wp_create_nonce('ecopower_tracker_nonce')
            )
        );
    }

    /**
     * Get asset suffix for minified files
     *
     * @return string
     */
    private function get_asset_suffix() {
        return (defined('SCRIPT_DEBUG') && SCRIPT_DEBUG) ? '' : '.min';
    }

    /**
     * Render the projects shortcode
     */
    public function render_projects_shortcode($atts) {
        // Enqueue required scripts and styles
        wp_enqueue_style($this->plugin_name . '-frontend');
        wp_enqueue_script($this->plugin_name . '-frontend');

        // Parse attributes
        $atts = shortcode_atts(array(
            'type' => '',
            'limit' => 10,
            'columns' => 3,
            'show_filters' => 'yes',
            'pagination' => 'yes',
            'orderby' => 'date_of_activation',
            'order' => 'DESC'
        ), $atts, 'ecopower_projects');

        // Get projects
        global $wpdb;
        $table_name = $wpdb->prefix . 'ecopower_tracker_projects';
        
        $args = array(
            'post_type' => 'ecopower_project',
            'posts_per_page' => (int) $atts['limit'],
            'orderby' => sanitize_sql_orderby($atts['orderby']),
            'order' => in_array(strtoupper($atts['order']), array('ASC', 'DESC')) ? strtoupper($atts['order']) : 'DESC',
            'paged' => max(1, get_query_var('paged', 1))
        );

        // Add type filter if set
        if (!empty($atts['type'])) {
            $args['tax_query'] = array(
                array(
                    'taxonomy' => 'project_type',
                    'field' => 'slug',
                    'terms' => sanitize_text_field($atts['type'])
                )
            );
        }

        $projects_query = new WP_Query($args);
        $total_projects = $projects_query->found_posts;
        $total_pages = $projects_query->max_num_pages;

        // Start output buffering
        ob_start();
        
        // Include template
        include plugin_dir_path(__FILE__) . '../templates/frontend/projects-grid.php';
        
        // Return the buffered content
        return ob_get_clean();
    }

    /**
     * Render single project shortcode
     */
    public function render_single_project_shortcode($atts) {
        // Enqueue required scripts and styles
        wp_enqueue_style($this->plugin_name . '-frontend');
        
        // Parse attributes
        $atts = shortcode_atts(array(
            'id' => '',
            'project_number' => ''
        ), $atts, 'ecopower_project');

        // Get project by ID or project number
        global $wpdb;
        $table_name = $wpdb->prefix . 'ecopower_tracker_projects';
        
        if (!empty($atts['id'])) {
            $project = $wpdb->get_row($wpdb->prepare(
                "SELECT * FROM $table_name WHERE id = %d",
                (int) $atts['id']
            ));
        } elseif (!empty($atts['project_number'])) {
            $project = $wpdb->get_row($wpdb->prepare(
                "SELECT * FROM $table_name WHERE project_number = %s",
                sanitize_text_field($atts['project_number'])
            ));
        } else {
            return '<p class="ecopower-error">' . __('No project specified.', 'ecopower-tracker') . '</p>';
        }

        // Check if project exists
        if (!$project) {
            return '<p class="ecopower-error">' . __('Project not found.', 'ecopower-tracker') . '</p>';
        }

        // Start output buffering
        ob_start();
        
        // Include template
        include plugin_dir_path(__FILE__) . '../templates/frontend/single-project.php';
        
        // Return the buffered content
        return ob_get_clean();
    }

    /**
     * Render statistics shortcode
     */
    public function render_stats_shortcode($atts) {
        // Enqueue required scripts and styles
        wp_enqueue_style($this->plugin_name . '-frontend');
        
        // Parse attributes
        $atts = shortcode_atts(array(
            'show' => 'all'
        ), $atts, 'ecopower_stats');

        global $wpdb;
        $table_name = $wpdb->prefix . 'ecopower_tracker_projects';
        
        // Get statistics
        $stats = array(
            'total_projects' => $wpdb->get_var("SELECT COUNT(*) FROM $table_name"),
            'total_capacity' => (float) $wpdb->get_var("SELECT SUM(generation_capacity) FROM $table_name"),
            'by_type' => $wpdb->get_results("SELECT type_of_plant, COUNT(*) as count FROM $table_name GROUP BY type_of_plant"),
            'by_location' => $wpdb->get_results("SELECT project_location, COUNT(*) as count FROM $table_name GROUP BY project_location ORDER BY count DESC")
        );

        // Start output buffering
        ob_start();
        
        // Include template
        include plugin_dir_path(__FILE__) . '../templates/frontend/statistics.php';
        
        // Return the buffered content
        return ob_get_clean();
    }

    /**
     * AJAX handler for filtering projects
     */
    public function ajax_filter_projects() {
        // Verify nonce
        if (!isset($_POST['nonce']) || !wp_verify_nonce($_POST['nonce'], 'ecopower_tracker_nonce')) {
            wp_send_json_error('Invalid nonce');
        }

        // Parse form data
        parse_str($_POST['form_data'], $form_data);
        
        // Build query args
        $args = array(
            'post_type' => 'ecopower_project',
            'posts_per_page' => !empty($form_data['limit']) ? (int) $form_data['limit'] : 10,
            'paged' => max(1, get_query_var('paged', 1))
        );

        // Add type filter
        if (!empty($form_data['type'])) {
            $args['tax_query'] = array(
                array(
                    'taxonomy' => 'project_type',
                    'field' => 'slug',
                    'terms' => sanitize_text_field($form_data['type'])
                )
            );
        }

        // Add ordering
        if (!empty($form_data['orderby'])) {
            $args['orderby'] = sanitize_sql_orderby($form_data['orderby']);
            $args['order'] = !empty($form_data['order']) ? strtoupper($form_data['order']) : 'DESC';
        }

        $projects_query = new WP_Query($args);
        $total_projects = $projects_query->found_posts;
        $total_pages = $projects_query->max_num_pages;

        // Start output buffering
        ob_start();
        
        // Include template
        include plugin_dir_path(__FILE__) . '../templates/frontend/projects-grid.php';
        
        // Get the output
        $html = ob_get_clean();

        // Return the response
        wp_send_json_success(array('html' => $html));
    }

    /**
     * Format capacity with units
     */
    public static function format_capacity($capacity) {
        $capacity = floatval($capacity);
        return number_format_i18n($capacity, 2) . ' MW';
    }

    /**
     * Format date
     */
    public static function format_date($date) {
        return date_i18n(get_option('date_format'), strtotime($date));
    }

    /**
     * Get chart color for plant type
     */
    public static function get_chart_color($type) {
        $colors = array(
            'solar' => '#f1c40f',
            'wind' => '#3498db',
            'hydro' => '#1abc9c',
            'biomass' => '#e67e22',
            'geothermal' => '#e74c3c'
        );
        
        return isset($colors[strtolower($type)]) ? $colors[strtolower($type)] : '#95a5a6';
    }
}
