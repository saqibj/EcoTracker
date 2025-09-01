<?php
/**
 * Cache management admin interface
 *
 * @package EcoPowerTracker
 * @since 2.3.0
 */

namespace EcoPowerTracker;

if (!defined('ABSPATH')) {
    exit;
}

/**
 * Class EcoPower_Tracker_Cache_Admin
 */
class EcoPower_Tracker_Cache_Admin {

    /**
     * Constructor
     */
    public function __construct() {
        add_action('admin_menu', array($this, 'add_cache_admin_page'));
        add_action('admin_init', array($this, 'handle_cache_actions'));
        add_action('wp_ajax_ecopower_tracker_cache_stats', array($this, 'ajax_cache_stats'));
    }

    /**
     * Add cache management admin page
     *
     * @return void
     */
    public function add_cache_admin_page() {
        add_submenu_page(
            'ecopower-tracker',
            __('Cache Management', 'ecopower-tracker'),
            __('Cache', 'ecopower-tracker'),
            'manage_options',
            'ecopower-tracker-cache',
            array($this, 'render_cache_page')
        );
    }

    /**
     * Handle cache management actions
     *
     * @return void
     */
    public function handle_cache_actions() {
        if (!current_user_can('manage_options')) {
            return;
        }

        if (!isset($_POST['ecopower_cache_nonce']) || 
            !wp_verify_nonce($_POST['ecopower_cache_nonce'], 'ecopower_cache_action')) {
            return;
        }

        $action = isset($_POST['cache_action']) ? sanitize_text_field($_POST['cache_action']) : '';

        switch ($action) {
            case 'clear_all':
                $this->clear_all_cache();
                $this->add_admin_notice(__('All cache cleared successfully.', 'ecopower-tracker'), 'success');
                break;

            case 'warm_cache':
                $this->warm_cache();
                $this->add_admin_notice(__('Cache warmed successfully.', 'ecopower-tracker'), 'success');
                break;

            case 'clear_specific':
                $project_id = isset($_POST['project_id']) ? absint($_POST['project_id']) : 0;
                if ($project_id) {
                    $this->clear_project_cache($project_id);
                    $this->add_admin_notice(
                        sprintf(__('Cache cleared for project #%d.', 'ecopower-tracker'), $project_id),
                        'success'
                    );
                }
                break;
        }
    }

    /**
     * Render cache management page
     *
     * @return void
     */
    public function render_cache_page() {
        global $ecopower_tracker_shortcodes;
        
        $cache_stats = array();
        if ($ecopower_tracker_shortcodes && method_exists($ecopower_tracker_shortcodes, 'get_cache_stats')) {
            $cache_stats = $ecopower_tracker_shortcodes->get_cache_stats();
        }
        
        ?>
        <div class="wrap">
            <h1><?php _e('EcoPower Tracker - Cache Management', 'ecopower-tracker'); ?></h1>
            
            <div class="notice notice-info">
                <p><?php _e('Cache management helps improve plugin performance by storing frequently accessed data.', 'ecopower-tracker'); ?></p>
            </div>

            <div class="card">
                <h2><?php _e('Cache Statistics', 'ecopower-tracker'); ?></h2>
                <table class="widefat">
                    <tbody>
                        <tr>
                            <td><strong><?php _e('Cache Version:', 'ecopower-tracker'); ?></strong></td>
                            <td><?php echo esc_html($cache_stats['cache_version'] ?? 'N/A'); ?></td>
                        </tr>
                        <tr>
                            <td><strong><?php _e('WordPress Object Cache:', 'ecopower-tracker'); ?></strong></td>
                            <td>
                                <?php if (wp_using_ext_object_cache()): ?>
                                    <span class="dashicons dashicons-yes-alt" style="color: green;"></span>
                                    <?php _e('External cache active', 'ecopower-tracker'); ?>
                                <?php else: ?>
                                    <span class="dashicons dashicons-warning" style="color: orange;"></span>
                                    <?php _e('Using database cache', 'ecopower-tracker'); ?>
                                <?php endif; ?>
                            </td>
                        </tr>
                        <tr>
                            <td><strong><?php _e('Next Cache Warm:', 'ecopower-tracker'); ?></strong></td>
                            <td>
                                <?php 
                                $next_warm = wp_next_scheduled('ecopower_tracker_cache_warm');
                                echo $next_warm ? esc_html(date_i18n(get_option('date_format') . ' ' . get_option('time_format'), $next_warm)) : __('Not scheduled', 'ecopower-tracker');
                                ?>
                            </td>
                        </tr>
                    </tbody>
                </table>
                
                <div id="cache-stats-live" style="margin-top: 15px;">
                    <button type="button" id="refresh-cache-stats" class="button">
                        <?php _e('Refresh Statistics', 'ecopower-tracker'); ?>
                    </button>
                </div>
            </div>

            <div class="card">
                <h2><?php _e('Cache Actions', 'ecopower-tracker'); ?></h2>
                
                <form method="post" style="display: inline-block; margin-right: 10px;">
                    <?php wp_nonce_field('ecopower_cache_action', 'ecopower_cache_nonce'); ?>
                    <input type="hidden" name="cache_action" value="clear_all">
                    <button type="submit" class="button button-secondary" 
                            onclick="return confirm('<?php esc_attr_e('Are you sure you want to clear all cache?', 'ecopower-tracker'); ?>');">
                        <span class="dashicons dashicons-trash"></span>
                        <?php _e('Clear All Cache', 'ecopower-tracker'); ?>
                    </button>
                </form>

                <form method="post" style="display: inline-block; margin-right: 10px;">
                    <?php wp_nonce_field('ecopower_cache_action', 'ecopower_cache_nonce'); ?>
                    <input type="hidden" name="cache_action" value="warm_cache">
                    <button type="submit" class="button button-primary">
                        <span class="dashicons dashicons-update"></span>
                        <?php _e('Warm Cache', 'ecopower-tracker'); ?>
                    </button>
                </form>

                <div style="margin-top: 15px;">
                    <h3><?php _e('Clear Specific Project Cache', 'ecopower-tracker'); ?></h3>
                    <form method="post" style="display: flex; align-items: center; gap: 10px;">
                        <?php wp_nonce_field('ecopower_cache_action', 'ecopower_cache_nonce'); ?>
                        <input type="hidden" name="cache_action" value="clear_specific">
                        <input type="number" name="project_id" placeholder="<?php esc_attr_e('Project ID', 'ecopower-tracker'); ?>" min="1" required>
                        <button type="submit" class="button">
                            <?php _e('Clear Project Cache', 'ecopower-tracker'); ?>
                        </button>
                    </form>
                </div>
            </div>

            <div class="card">
                <h2><?php _e('Cache Best Practices', 'ecopower-tracker'); ?></h2>
                <ul>
                    <li><?php _e('Cache is automatically cleared when projects are added, updated, or deleted.', 'ecopower-tracker'); ?></li>
                    <li><?php _e('Cache warming runs automatically every 6 hours to improve performance.', 'ecopower-tracker'); ?></li>
                    <li><?php _e('For best performance, consider using an external object cache like Redis or Memcached.', 'ecopower-tracker'); ?></li>
                    <li><?php _e('Clear cache if you notice outdated data in shortcodes or widgets.', 'ecopower-tracker'); ?></li>
                </ul>
            </div>
        </div>

        <script type="text/javascript">
        jQuery(document).ready(function($) {
            $('#refresh-cache-stats').on('click', function() {
                var button = $(this);
                button.prop('disabled', true).text('<?php esc_js(_e('Loading...', 'ecopower-tracker')); ?>');
                
                $.post(ajaxurl, {
                    action: 'ecopower_tracker_cache_stats',
                    nonce: '<?php echo wp_create_nonce('ecopower_cache_stats'); ?>'
                }, function(response) {
                    if (response.success) {
                        // Update stats display
                        location.reload();
                    }
                }).always(function() {
                    button.prop('disabled', false).text('<?php esc_js(_e('Refresh Statistics', 'ecopower-tracker')); ?>');
                });
            });
        });
        </script>
        <?php
    }

    /**
     * AJAX handler for cache statistics
     *
     * @return void
     */
    public function ajax_cache_stats() {
        check_ajax_referer('ecopower_cache_stats', 'nonce');
        
        if (!current_user_can('manage_options')) {
            wp_send_json_error('Insufficient permissions');
        }

        global $ecopower_tracker_shortcodes;
        $stats = array();
        
        if ($ecopower_tracker_shortcodes && method_exists($ecopower_tracker_shortcodes, 'get_cache_stats')) {
            $stats = $ecopower_tracker_shortcodes->get_cache_stats();
        }

        wp_send_json_success($stats);
    }

    /**
     * Clear all cache
     *
     * @return void
     */
    private function clear_all_cache() {
        global $ecopower_tracker_shortcodes;
        
        if ($ecopower_tracker_shortcodes && method_exists($ecopower_tracker_shortcodes, 'clear_cache')) {
            $ecopower_tracker_shortcodes->clear_cache();
        }
    }

    /**
     * Warm cache
     *
     * @return void
     */
    private function warm_cache() {
        global $ecopower_tracker_shortcodes;
        
        if ($ecopower_tracker_shortcodes && method_exists($ecopower_tracker_shortcodes, 'warm_cache')) {
            $ecopower_tracker_shortcodes->warm_cache();
        }
    }

    /**
     * Clear project-specific cache
     *
     * @param int $project_id
     * @return void
     */
    private function clear_project_cache($project_id) {
        global $ecopower_tracker_shortcodes;
        
        if ($ecopower_tracker_shortcodes && method_exists($ecopower_tracker_shortcodes, 'clear_cache')) {
            $ecopower_tracker_shortcodes->clear_cache($project_id);
        }
    }

    /**
     * Add admin notice
     *
     * @param string $message
     * @param string $type
     * @return void
     */
    private function add_admin_notice($message, $type = 'info') {
        add_action('admin_notices', function() use ($message, $type) {
            echo '<div class="notice notice-' . esc_attr($type) . ' is-dismissible"><p>' . esc_html($message) . '</p></div>';
        });
    }
}

// Initialize cache admin
function ecopower_tracker_cache_admin_init() {
    if (is_admin()) {
        new EcoPower_Tracker_Cache_Admin();
    }
}
add_action('init', 'EcoPowerTracker\\ecopower_tracker_cache_admin_init');
