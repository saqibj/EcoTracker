<?php
/**
 * Dashboard functionality
 *
 * @package EcoPowerTracker
 * @since 2.0.1
 */

namespace EcoPowerTracker;

if (!defined('ABSPATH')) {
    exit;
}

/**
 * Class EcoPower_Tracker_Dashboard
 */
class EcoPower_Tracker_Dashboard {

    /**
     * Constructor
     */
    public function __construct() {
        add_action('admin_menu', array($this, 'setup_dashboard_page'));
        add_action('admin_init', array($this, 'handle_admin_actions'));
    }

    /**
     * Set up the dashboard page
     *
     * @return void
     */
    public function setup_dashboard_page() {
        add_menu_page(
            __('EcoPower Tracker', 'ecopower-tracker'),
            __('EcoPower Tracker', 'ecopower-tracker'),
            'manage_options',
            'ecopower-tracker',
            array($this, 'render_dashboard'),
            'dashicons-chart-area',
            30
        );

        add_submenu_page(
            'ecopower-tracker',
            __('Dashboard', 'ecopower-tracker'),
            __('Dashboard', 'ecopower-tracker'),
            'manage_options',
            'ecopower-tracker',
            array($this, 'render_dashboard')
        );
    }

    /**
     * Handle admin actions
     *
     * @return void
     */
    public function handle_admin_actions() {
        if (!current_user_can('manage_options')) {
            return;
        }

        // Handle messages
        if (isset($_GET['message'])) {
            add_action('admin_notices', array($this, 'display_admin_notices'));
        }
    }

    /**
     * Display admin notices
     *
     * @return void
     */
    public function display_admin_notices() {
        $message = sanitize_text_field($_GET['message']);
        $notice_class = 'notice-success';
        $notice_message = '';

        switch ($message) {
            case 'import_success':
                $notice_message = __('CSV data imported successfully.', 'ecopower-tracker');
                break;
            case 'export_success':
                $notice_message = __('Data exported successfully.', 'ecopower-tracker');
                break;
            case 'delete_success':
                $notice_message = __('Project deleted successfully.', 'ecopower-tracker');
                break;
            case 'error':
                $notice_class = 'notice-error';
                $notice_message = __('An error occurred. Please try again.', 'ecopower-tracker');
                break;
        }

        if ($notice_message) {
            printf(
                '<div class="notice %s is-dismissible"><p>%s</p></div>',
                esc_attr($notice_class),
                esc_html($notice_message)
            );
        }
    }

    /**
     * Render the dashboard page
     *
     * @return void
     */
    public function render_dashboard() {
        // Verify user capabilities
        if (!current_user_can('manage_options')) {
            wp_die(__('Unauthorized access', 'ecopower-tracker'));
        }

        // Get stats for template
        $stats = $this->get_dashboard_stats();

        // Display dashboard content
        include ECOPOWER_TRACKER_PATH . 'templates/admin/dashboard.php';
    }

    /**
     * Get dashboard statistics
     *
     * @return array
     */
    public function get_dashboard_stats() {
        global $wpdb;
        $table_name = $wpdb->prefix . 'ecopower_tracker_projects';

        return array(
            'total_projects' => $this->get_total_projects(),
            'total_capacity' => $this->get_total_capacity(),
            'plant_types' => $this->get_plant_type_stats(),
            'recent_projects' => $this->get_recent_projects(5)
        );
    }

    /**
     * Get total number of projects
     *
     * @return int
     */
    private function get_total_projects() {
        global $wpdb;
        $table_name = $wpdb->prefix . 'ecopower_tracker_projects';
        return (int) $wpdb->get_var("SELECT COUNT(*) FROM {$table_name}");
    }

    /**
     * Get total generation capacity
     *
     * @return float
     */
    private function get_total_capacity() {
        global $wpdb;
        $table_name = $wpdb->prefix . 'ecopower_tracker_projects';
        return (float) $wpdb->get_var("SELECT SUM(generation_capacity) FROM {$table_name}");
    }

    /**
     * Get statistics by plant type
     *
     * @return array
     */
    private function get_plant_type_stats() {
        global $wpdb;
        $table_name = $wpdb->prefix . 'ecopower_tracker_projects';
        
        $stats = $wpdb->get_results("
            SELECT 
                type_of_plant,
                COUNT(*) as count,
                SUM(generation_capacity) as total_capacity
            FROM {$table_name} 
            GROUP BY type_of_plant
        ");

        return array_map(function($stat) {
            return array(
                'type' => $stat->type_of_plant,
                'count' => (int) $stat->count,
                'capacity' => (float) $stat->total_capacity
            );
        }, $stats ?: array());
    }

    /**
     * Get recent projects
     *
     * @param int $limit Number of projects to return
     * @return array
     */
    private function get_recent_projects($limit = 5) {
        global $wpdb;
        $table_name = $wpdb->prefix . 'ecopower_tracker_projects';
        
        return $wpdb->get_results($wpdb->prepare("
            SELECT *
            FROM {$table_name}
            ORDER BY date_of_activation DESC
            LIMIT %d
        ", $limit));
    }
}

// Initialize dashboard with proper WordPress hooks
function ecopower_tracker_dashboard_init() {
    global $ecopower_tracker_dashboard;
    $ecopower_tracker_dashboard = new EcoPower_Tracker_Dashboard();
    return $ecopower_tracker_dashboard;
}
add_action('admin_init', 'EcoPowerTracker\\ecopower_tracker_dashboard_init');