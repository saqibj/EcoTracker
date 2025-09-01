<?php
/**
 * Block Editor (Gutenberg) support for EcoPower Tracker
 *
 * @package EcoPowerTracker
 * @since 2.3.0
 */

namespace EcoPowerTracker;

if (!defined('ABSPATH')) {
    exit;
}

/**
 * Class EcoPower_Tracker_Blocks
 */
class EcoPower_Tracker_Blocks {

    /**
     * Constructor
     */
    public function __construct() {
        add_action('init', array($this, 'register_blocks'));
        add_action('enqueue_block_editor_assets', array($this, 'enqueue_block_editor_assets'));
    }

    /**
     * Register blocks
     *
     * @return void
     */
    public function register_blocks() {
        // Only register if Gutenberg is available
        if (!function_exists('register_block_type')) {
            return;
        }

        // Register project statistics block
        register_block_type('ecopower-tracker/project-stats', array(
            'editor_script' => 'ecopower-tracker-blocks',
            'render_callback' => array($this, 'render_project_stats_block'),
            'attributes' => array(
                'showTotal' => array(
                    'type' => 'boolean',
                    'default' => true,
                ),
                'showByType' => array(
                    'type' => 'boolean',
                    'default' => true,
                ),
                'showByLocation' => array(
                    'type' => 'boolean',
                    'default' => false,
                ),
                'displayStyle' => array(
                    'type' => 'string',
                    'default' => 'cards',
                ),
            ),
        ));

        // Register single project block
        register_block_type('ecopower-tracker/single-project', array(
            'editor_script' => 'ecopower-tracker-blocks',
            'render_callback' => array($this, 'render_single_project_block'),
            'attributes' => array(
                'projectId' => array(
                    'type' => 'number',
                    'default' => 0,
                ),
                'showDetails' => array(
                    'type' => 'boolean',
                    'default' => true,
                ),
                'showStats' => array(
                    'type' => 'boolean',
                    'default' => true,
                ),
            ),
        ));

        // Register projects grid block
        register_block_type('ecopower-tracker/projects-grid', array(
            'editor_script' => 'ecopower-tracker-blocks',
            'render_callback' => array($this, 'render_projects_grid_block'),
            'attributes' => array(
                'columns' => array(
                    'type' => 'number',
                    'default' => 3,
                ),
                'postsPerPage' => array(
                    'type' => 'number',
                    'default' => 9,
                ),
                'filterByType' => array(
                    'type' => 'string',
                    'default' => '',
                ),
                'showFilters' => array(
                    'type' => 'boolean',
                    'default' => true,
                ),
                'showPagination' => array(
                    'type' => 'boolean',
                    'default' => true,
                ),
            ),
        ));
    }

    /**
     * Enqueue block editor assets
     *
     * @return void
     */
    public function enqueue_block_editor_assets() {
        wp_enqueue_script(
            'ecopower-tracker-blocks',
            ECOPOWER_TRACKER_URL . 'assets/js/blocks.js',
            array('wp-blocks', 'wp-element', 'wp-editor', 'wp-components'),
            ECOPOWER_TRACKER_VERSION,
            true
        );

        wp_localize_script(
            'ecopower-tracker-blocks',
            'ecoPowerTrackerBlocks',
            array(
                'restUrl' => rest_url('ecopower-tracker/v1/'),
                'nonce' => wp_create_nonce('wp_rest'),
            )
        );

        wp_enqueue_style(
            'ecopower-tracker-blocks-editor',
            ECOPOWER_TRACKER_URL . 'assets/css/blocks-editor.css',
            array('wp-edit-blocks'),
            ECOPOWER_TRACKER_VERSION
        );
    }

    /**
     * Render project statistics block
     *
     * @param array $attributes
     * @return string
     */
    public function render_project_stats_block($attributes) {
        $atts = wp_parse_args($attributes, array(
            'showTotal' => true,
            'showByType' => true,
            'showByLocation' => false,
            'displayStyle' => 'cards',
        ));

        // Use the existing public class method
        $public = new EcoPower_Tracker_Public('ecopower-tracker', ECOPOWER_TRACKER_VERSION);
        return $public->render_stats_shortcode($atts);
    }

    /**
     * Render single project block
     *
     * @param array $attributes
     * @return string
     */
    public function render_single_project_block($attributes) {
        $atts = wp_parse_args($attributes, array(
            'id' => 0,
            'showDetails' => true,
            'showStats' => true,
        ));

        if (empty($atts['id'])) {
            return '<p class="ecopower-error">' . __('Please select a project to display.', 'ecopower-tracker') . '</p>';
        }

        // Use the existing public class method
        $public = new EcoPower_Tracker_Public('ecopower-tracker', ECOPOWER_TRACKER_VERSION);
        return $public->render_single_project_shortcode(array('id' => $atts['id']));
    }

    /**
     * Render projects grid block
     *
     * @param array $attributes
     * @return string
     */
    public function render_projects_grid_block($attributes) {
        $atts = wp_parse_args($attributes, array(
            'columns' => 3,
            'limit' => 9,
            'type' => '',
            'show_filters' => true,
            'pagination' => true,
        ));

        // Map block attributes to shortcode attributes
        $shortcode_atts = array(
            'columns' => $atts['columns'],
            'limit' => $atts['limit'],
            'type' => $atts['filterByType'],
            'show_filters' => $atts['showFilters'] ? 'yes' : 'no',
            'pagination' => $atts['showPagination'] ? 'yes' : 'no',
        );

        // Use the existing public class method
        $public = new EcoPower_Tracker_Public('ecopower-tracker', ECOPOWER_TRACKER_VERSION);
        return $public->render_projects_shortcode($shortcode_atts);
    }
}

// Initialize blocks
function ecopower_tracker_blocks_init() {
    new EcoPower_Tracker_Blocks();
}
add_action('init', 'EcoPowerTracker\\ecopower_tracker_blocks_init');
