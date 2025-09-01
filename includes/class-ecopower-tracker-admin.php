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
     * The database handler instance
     *
     * @var EcoPower_Tracker_DB
     */
    private $db;

    /**
     * The current page
     *
     * @var string
     */
    private $current_page;

    /**
     * Constructor
     */
    public function __construct() {
        global $ecopower_tracker_db;
        $this->db = $ecopower_tracker_db;
        $this->current_page = isset($_GET['page']) ? sanitize_text_field($_GET['page']) : '';

        // Hook to enqueue admin scripts and styles
        add_action('admin_enqueue_scripts', array($this, 'enqueue_admin_assets'));
        
        // Add admin menu
        add_action('admin_menu', array($this, 'add_admin_menu'));
        
        // Handle form submissions
        add_action('admin_init', array($this, 'handle_form_submissions'));
    }

    /**
     * Add admin menu items
     */
    public function add_admin_menu() {
        $hook = add_menu_page(
            __('EcoPower Tracker', 'ecopower-tracker'),
            __('EcoPower Tracker', 'ecopower-tracker'),
            'manage_options',
            'ecopower-tracker',
            array($this, 'render_admin_page'),
            'dashicons-chart-area',
            30
        );

        // Add submenu items
        add_submenu_page(
            'ecopower-tracker',
            __('All Projects', 'ecopower-tracker'),
            __('All Projects', 'ecopower-tracker'),
            'manage_options',
            'ecopower-tracker',
            array($this, 'render_admin_page')
        );

        add_submenu_page(
            'ecopower-tracker',
            __('Add New Project', 'ecopower-tracker'),
            __('Add New', 'ecopower-tracker'),
            'manage_options',
            'ecopower-tracker-add',
            array($this, 'render_add_edit_page')
        );
    }

    /**
     * Handle form submissions
     */
    public function handle_form_submissions() {
        if (!isset($_POST['ecopower_tracker_nonce']) || !wp_verify_nonce($_POST['ecopower_tracker_nonce'], 'ecopower_tracker_save_project')) {
            return;
        }

        if (!current_user_can('manage_options')) {
            wp_die(__('You do not have sufficient permissions to access this page.', 'ecopower-tracker'));
        }

        $action = isset($_GET['action']) ? sanitize_text_field($_GET['action']) : '';
        $project_id = isset($_GET['id']) ? absint($_GET['id']) : 0;
        $data = array();

        // Sanitize and prepare project data
        $fields = array(
            'project_number', 'project_company', 'project_name', 'project_location',
            'type_of_plant', 'project_cuf', 'generation_capacity', 'date_of_activation'
        );

        foreach ($fields as $field) {
            if (isset($_POST[$field])) {
                $data[$field] = sanitize_text_field($_POST[$field]);
            }
        }

        // Handle different actions
        try {
            switch ($action) {
                case 'edit':
                    if ($project_id) {
                        $this->db->update_project($project_id, $data);
                        $redirect = add_query_arg('message', 'updated', admin_url('admin.php?page=ecopower-tracker'));
                    }
                    break;

                case 'delete':
                    if ($project_id) {
                        $this->db->delete_project($project_id);
                        $redirect = add_query_arg('message', 'deleted', admin_url('admin.php?page=ecopower-tracker'));
                    }
                    break;

                default: // Add new
                    $this->db->create_project($data);
                    $redirect = add_query_arg('message', 'added', admin_url('admin.php?page=ecopower-tracker'));
            }

            wp_redirect(esc_url_raw($redirect));
            exit;

        } catch (\Exception $e) {
            wp_die(esc_html($e->getMessage()));
        }
    }

    /**
     * Render the main admin page
     */
    public function render_admin_page() {
        $action = isset($_GET['action']) ? sanitize_text_field($_GET['action']) : '';
        $project_id = isset($_GET['id']) ? absint($_GET['id']) : 0;

        // Handle view/edit/delete based on action
        switch ($action) {
            case 'edit':
                if ($project_id) {
                    $this->render_add_edit_page($project_id);
                    return;
                }
                break;
            
            case 'delete':
                if ($project_id && isset($_GET['_wpnonce']) && wp_verify_nonce($_GET['_wpnonce'], 'ecopower_tracker_delete_' . $project_id)) {
                    $this->db->delete_project($project_id);
                    wp_redirect(admin_url('admin.php?page=ecopower-tracker&message=deleted'));
                    exit;
                }
                break;
        }

        // Show project listing by default
        $this->render_project_list();
    }

    /**
     * Render the project listing table
     */
    private function render_project_list() {
        $projects = $this->db->get_projects();
        $messages = array(
            'added' => __('Project added successfully.', 'ecopower-tracker'),
            'updated' => __('Project updated successfully.', 'ecopower-tracker'),
            'deleted' => __('Project deleted successfully.', 'ecopower-tracker')
        );
        
        // Include the template
        include ECOPOWER_TRACKER_PATH . 'templates/admin/project-list.php';
    }

    /**
     * Render the add/edit project form
     */
    public function render_add_edit_page($project_id = 0) {
        $project = $project_id ? $this->db->get_project($project_id) : null;
        $plant_types = array('solar', 'wind', 'hydro', 'biomass', 'geothermal');
        
        // Include the template
        include ECOPOWER_TRACKER_PATH . 'templates/admin/project-form.php';
    }

    /**
     * Enqueue admin scripts and styles
     */
    public function enqueue_admin_assets($hook) {
        // Only load on our plugin pages
        if (strpos($hook, 'ecopower-tracker') === false) {
            return;
        }

        $suffix = $this->get_asset_suffix();

        // Enqueue admin styles
        wp_enqueue_style(
            'ecopower-tracker-admin-css',
            ECOPOWER_TRACKER_URL . "assets/css/ecopower-tracker-admin{$suffix}.css",
            array(),
            ECOPOWER_TRACKER_VERSION,
            'all'
        );

        // Enqueue datepicker
        wp_enqueue_script('jquery-ui-datepicker');
        wp_enqueue_style('jquery-ui-css', 'https://code.jquery.com/ui/1.12.1/themes/base/jquery-ui.css');

        // Enqueue admin scripts
        wp_enqueue_script(
            'ecopower-tracker-admin-js',
            ECOPOWER_TRACKER_URL . "assets/js/ecopower-tracker-admin{$suffix}.js",
            array('jquery', 'jquery-ui-datepicker'),
            ECOPOWER_TRACKER_VERSION,
            true
        );

        // Localize admin script
        wp_localize_script(
            'ecopower-tracker-admin-js',
            'ecoPowerTrackerAdmin',
            array(
                'ajaxUrl' => admin_url('admin-ajax.php'),
                'nonce' => wp_create_nonce('ecopower_tracker_admin'),
                'confirmDelete' => __('Are you sure you want to delete this project?', 'ecopower-tracker'),
                'confirmBulkDelete' => __('Are you sure you want to delete the selected projects?', 'ecopower-tracker'),
                'i18n' => array(
                    'error' => __('An error occurred', 'ecopower-tracker'),
                    'success' => __('Operation completed successfully', 'ecopower-tracker'),
                    'loading' => __('Loading...', 'ecopower-tracker')
                )
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
}

// Initialize the admin functionalities
function ecopower_tracker_admin_init() {
    global $ecopower_tracker_admin;
    $ecopower_tracker_admin = new EcoPower_Tracker_Admin();
    return $ecopower_tracker_admin;
}
add_action('plugins_loaded', 'EcoPowerTracker\\ecopower_tracker_admin_init');