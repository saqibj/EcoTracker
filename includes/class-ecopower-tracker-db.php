<?php
/**
 * Database operations handler for EcoPower Tracker
 *
 * @package EcoPowerTracker
 * @since 2.1.0
 */

namespace EcoPowerTracker;

if (!defined('ABSPATH')) {
    exit;
}

class EcoPower_Tracker_DB {
    /**
     * The database table name (with WordPress prefix)
     *
     * @var string
     */
    private $table_name;

    /**
     * Constructor
     */
    public function __construct() {
        global $wpdb;
        $this->table_name = $wpdb->prefix . 'ecopower_tracker_projects';
    }

    /**
     * Create a new project
     *
     * @param array $data Project data
     * @return int|false The number of rows inserted, or false on error
     */
    public function create_project($data) {
        global $wpdb;
        
        // Validate and sanitize input data
        $sanitized_data = $this->sanitize_project_data($data);
        
        // Insert the project
        $result = $wpdb->insert(
            $this->table_name,
            $sanitized_data,
            $this->get_format_for_data($sanitized_data)
        );
        
        if ($result) {
            // Clear shortcode cache when a new project is added
            $this->clear_shortcode_cache();
            return $wpdb->insert_id;
        }
        
        return false;
    }
    
    /**
     * Get a project by ID
     *
     * @param int $id Project ID
     * @return object|false Project data or false if not found
     */
    public function get_project($id) {
        global $wpdb;
        $id = absint($id);
        
        return $wpdb->get_row(
            $wpdb->prepare(
                "SELECT * FROM {$this->table_name} WHERE id = %d",
                $id
            )
        );
    }
    
    /**
     * Get all projects with optional filtering
     *
     * @param array $args Query arguments
     * @return array List of projects
     */
    public function get_projects($args = array()) {
        global $wpdb;
        
        $defaults = array(
            'number'  => 20,
            'offset'  => 0,
            'orderby' => 'id',
            'order'   => 'DESC',
            'company' => '',
            'location' => '',
            'type'    => '',
        );
        
        $args = wp_parse_args($args, $defaults);
        
        // Build the query
        $where = array('1=1');
        $values = array();
        
        if (!empty($args['company'])) {
            $where[] = 'project_company = %s';
            $values[] = sanitize_text_field($args['company']);
        }
        
        if (!empty($args['location'])) {
            $where[] = 'project_location = %s';
            $values[] = sanitize_text_field($args['location']);
        }
        
        if (!empty($args['type'])) {
            $where[] = 'type_of_plant = %s';
            $values[] = sanitize_text_field($args['type']);
        }
        
        $where_clause = implode(' AND ', $where);
        $order_clause = sanitize_sql_orderby($args['orderby'] . ' ' . $args['order']);
        
        // Prepare and execute the query
        $query = $wpdb->prepare(
            "SELECT * FROM {$this->table_name} WHERE {$where_clause} ORDER BY {$order_clause} LIMIT %d, %d",
            array_merge($values, array($args['offset'], $args['number']))
        );
        
        return $wpdb->get_results($query);
    }
    
    /**
     * Update a project
     *
     * @param int $id Project ID
     * @param array $data Project data to update
     * @return bool Whether the project was updated
     */
    public function update_project($id, $data) {
        global $wpdb;
        $id = absint($id);
        
        // Validate and sanitize input data
        $sanitized_data = $this->sanitize_project_data($data);
        
        // Don't update the ID
        if (isset($sanitized_data['id'])) {
            unset($sanitized_data['id']);
        }
        
        // Update the project
        $result = $wpdb->update(
            $this->table_name,
            $sanitized_data,
            array('id' => $id),
            $this->get_format_for_data($sanitized_data),
            array('%d') // Where format (id is integer)
        );
        
        if ($result !== false) {
            // Clear shortcode cache when a project is updated
            $this->clear_shortcode_cache($id);
        }
        
        return (bool) $result;
    }
    
    /**
     * Delete a project
     *
     * @param int $id Project ID
     * @return bool Whether the project was deleted
     */
    public function delete_project($id) {
        global $wpdb;
        $id = absint($id);
        
        $result = $wpdb->delete(
            $this->table_name,
            array('id' => $id),
            array('%d')
        );
        
        if ($result) {
            // Clear shortcode cache when a project is deleted
            $this->clear_shortcode_cache($id);
        }
        
        return (bool) $result;
    }
    
    /**
     * Sanitize project data before saving to database
     *
     * @param array $data Raw project data
     * @return array Sanitized data
     */
    private function sanitize_project_data($data) {
        $sanitized = array();
        
        if (isset($data['project_number'])) {
            $sanitized['project_number'] = sanitize_text_field($data['project_number']);
        }
        
        if (isset($data['project_company'])) {
            $sanitized['project_company'] = sanitize_text_field($data['project_company']);
        }
        
        if (isset($data['project_name'])) {
            $sanitized['project_name'] = sanitize_text_field($data['project_name']);
        }
        
        if (isset($data['project_location'])) {
            $sanitized['project_location'] = sanitize_text_field($data['project_location']);
        }
        
        if (isset($data['type_of_plant'])) {
            $sanitized['type_of_plant'] = sanitize_text_field($data['type_of_plant']);
        }
        
        if (isset($data['project_cuf'])) {
            $sanitized['project_cuf'] = (float) $data['project_cuf'];
        }
        
        if (isset($data['generation_capacity'])) {
            $sanitized['generation_capacity'] = (float) $data['generation_capacity'];
        }
        
        if (isset($data['date_of_activation'])) {
            $sanitized['date_of_activation'] = sanitize_text_field($data['date_of_activation']);
        }
        
        return $sanitized;
    }
    
    /**
     * Get the format strings for wpdb operations
     *
     * @param array $data Data array
     * @return array Format strings
     */
    private function get_format_for_data($data) {
        $formats = array();
        
        foreach ($data as $key => $value) {
            if (in_array($key, array('project_cuf', 'generation_capacity'))) {
                $formats[] = '%f';
            } elseif (in_array($key, array('date_of_activation', 'created_at', 'updated_at'))) {
                $formats[] = '%s';
            } else {
                $formats[] = '%s';
            }
        }
        
        return $formats;
    }
    
    /**
     * Clear shortcode cache
     * 
     * @param int $project_id Optional project ID to clear specific cache
     * @return void
     */
    private function clear_shortcode_cache($project_id = null) {
        // Get the shortcodes instance and clear its cache
        global $ecopower_tracker_shortcodes;
        
        if ($ecopower_tracker_shortcodes && method_exists($ecopower_tracker_shortcodes, 'clear_cache')) {
            $ecopower_tracker_shortcodes->clear_cache($project_id);
        } else {
            // Fallback: manually increment cache version
            $cache_version = get_option('ecopower_tracker_cache_version', 1);
            update_option('ecopower_tracker_cache_version', $cache_version + 1);
        }
        
        // Clear any related transients
        delete_transient('ecopower_tracker_project_stats');
        
        // Fire action hook for other plugins/themes to hook into
        do_action('ecopower_tracker_cache_cleared', $project_id);
    }
}

// Initialize the database handler
function ecopower_tracker_db_init() {
    global $ecopower_tracker_db;
    $ecopower_tracker_db = new EcoPower_Tracker_DB();
    return $ecopower_tracker_db;
}
add_action('plugins_loaded', 'EcoPowerTracker\\ecopower_tracker_db_init');
