<?php
/**
 * REST API functionality for EcoPower Tracker
 *
 * @package EcoPowerTracker
 * @since 2.3.0
 */

namespace EcoPowerTracker;

if (!defined('ABSPATH')) {
    exit;
}

/**
 * Class EcoPower_Tracker_REST_API
 */
class EcoPower_Tracker_REST_API {

    /**
     * Constructor
     */
    public function __construct() {
        add_action('rest_api_init', array($this, 'register_routes'));
    }

    /**
     * Register REST API routes
     *
     * @return void
     */
    public function register_routes() {
        register_rest_route('ecopower-tracker/v1', '/projects', array(
            array(
                'methods' => \WP_REST_Server::READABLE,
                'callback' => array($this, 'get_projects'),
                'permission_callback' => array($this, 'get_projects_permissions_check'),
                'args' => array(
                    'per_page' => array(
                        'default' => 10,
                        'sanitize_callback' => 'absint',
                    ),
                    'page' => array(
                        'default' => 1,
                        'sanitize_callback' => 'absint',
                    ),
                    'type' => array(
                        'sanitize_callback' => 'sanitize_text_field',
                    ),
                    'company' => array(
                        'sanitize_callback' => 'sanitize_text_field',
                    ),
                    'location' => array(
                        'sanitize_callback' => 'sanitize_text_field',
                    ),
                ),
            ),
        ));

        register_rest_route('ecopower-tracker/v1', '/projects/(?P<id>\d+)', array(
            array(
                'methods' => \WP_REST_Server::READABLE,
                'callback' => array($this, 'get_project'),
                'permission_callback' => array($this, 'get_projects_permissions_check'),
                'args' => array(
                    'id' => array(
                        'validate_callback' => function($param, $request, $key) {
                            return is_numeric($param);
                        }
                    ),
                ),
            ),
        ));

        register_rest_route('ecopower-tracker/v1', '/stats', array(
            array(
                'methods' => \WP_REST_Server::READABLE,
                'callback' => array($this, 'get_stats'),
                'permission_callback' => array($this, 'get_projects_permissions_check'),
            ),
        ));
    }

    /**
     * Get projects via REST API
     *
     * @param \WP_REST_Request $request
     * @return \WP_REST_Response|\WP_Error
     */
    public function get_projects($request) {
        global $ecopower_tracker_db;
        
        $args = array(
            'number' => $request['per_page'],
            'offset' => ($request['page'] - 1) * $request['per_page'],
            'type' => $request['type'],
            'company' => $request['company'],
            'location' => $request['location'],
        );

        $projects = $ecopower_tracker_db->get_projects($args);
        
        if (is_wp_error($projects)) {
            return new \WP_Error(
                'ecopower_tracker_error',
                __('Failed to retrieve projects', 'ecopower-tracker'),
                array('status' => 500)
            );
        }

        // Format projects for API response
        $formatted_projects = array_map(array($this, 'format_project_for_api'), $projects);

        return new \WP_REST_Response($formatted_projects, 200);
    }

    /**
     * Get single project via REST API
     *
     * @param \WP_REST_Request $request
     * @return \WP_REST_Response|\WP_Error
     */
    public function get_project($request) {
        global $ecopower_tracker_db;
        
        $project = $ecopower_tracker_db->get_project($request['id']);
        
        if (!$project) {
            return new \WP_Error(
                'ecopower_tracker_project_not_found',
                __('Project not found', 'ecopower-tracker'),
                array('status' => 404)
            );
        }

        return new \WP_REST_Response($this->format_project_for_api($project), 200);
    }

    /**
     * Get statistics via REST API
     *
     * @param \WP_REST_Request $request
     * @return \WP_REST_Response|\WP_Error
     */
    public function get_stats($request) {
        global $wpdb;
        $table_name = $wpdb->prefix . 'ecopower_tracker_projects';

        $stats = array(
            'total_projects' => (int) $wpdb->get_var("SELECT COUNT(*) FROM {$table_name}"),
            'total_capacity' => (float) $wpdb->get_var("SELECT SUM(generation_capacity) FROM {$table_name}"),
            'total_power_generation' => (float) $wpdb->get_var("SELECT SUM(generation_capacity * project_cuf / 100) FROM {$table_name}"),
            'by_type' => $wpdb->get_results("SELECT type_of_plant as type, COUNT(*) as count, SUM(generation_capacity) as capacity FROM {$table_name} GROUP BY type_of_plant"),
            'by_location' => $wpdb->get_results("SELECT project_location as location, COUNT(*) as count, SUM(generation_capacity) as capacity FROM {$table_name} GROUP BY project_location ORDER BY count DESC"),
        );

        // Calculate CO2 offset
        $co2_factor = get_option('ecopower_tracker_co2_factor', 0.001);
        $stats['total_co2_offset'] = $stats['total_power_generation'] * $co2_factor;

        return new \WP_REST_Response($stats, 200);
    }

    /**
     * Check permissions for reading projects
     *
     * @return bool
     */
    public function get_projects_permissions_check() {
        // Allow public access to read project data
        // For write operations, you would check for 'manage_options' capability
        return true;
    }

    /**
     * Format project data for API response
     *
     * @param object $project
     * @return array
     */
    private function format_project_for_api($project) {
        return array(
            'id' => (int) $project->id,
            'project_number' => $project->project_number,
            'company' => $project->project_company,
            'name' => $project->project_name,
            'location' => $project->project_location,
            'type' => $project->type_of_plant,
            'cuf' => (float) $project->project_cuf,
            'capacity' => (float) $project->generation_capacity,
            'power_generation' => (float) ($project->generation_capacity * $project->project_cuf / 100),
            'activation_date' => $project->date_of_activation,
            'created_at' => $project->created_at,
            'updated_at' => $project->updated_at,
        );
    }
}

// Initialize REST API
function ecopower_tracker_rest_api_init() {
    new EcoPower_Tracker_REST_API();
}
add_action('init', 'EcoPowerTracker\\ecopower_tracker_rest_api_init');
