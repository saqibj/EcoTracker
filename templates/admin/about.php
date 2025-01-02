// Path: EcoPower-Tracker/templates/admin/about.php
// File: about.php

<?php
/**
 * About page template
 *
 * @package EcoPowerTracker
 * @since 2.0.1
 */

namespace EcoPowerTracker;

if (!defined('ABSPATH')) {
    exit;
}
?>

<div class="wrap ecopower-tracker-about">
    <h1><?php esc_html_e('About EcoPower Tracker', 'ecopower-tracker'); ?></h1>

    <div class="about-text">
        <?php esc_html_e('Track and display renewable energy project data including power generation and CO2 offset calculations.', 'ecopower-tracker'); ?>
    </div>

    <div class="about-badges">
        <div class="badge">
            <span class="label"><?php esc_html_e('Version', 'ecopower-tracker'); ?></span>
            <span class="value"><?php echo esc_html(ECOPOWER_TRACKER_VERSION); ?></span>
        </div>
        <div class="badge">
            <span class="label"><?php esc_html_e('Author', 'ecopower-tracker'); ?></span>
            <span class="value">Saqib Jawaid</span>
        </div>
    </div>

    <div class="about-sections">
        <!-- Features Section -->
        <div class="about-section">
            <h2><?php esc_html_e('Key Features', 'ecopower-tracker'); ?></h2>
            <ul>
                <li><?php esc_html_e('Project Management - Easily manage renewable energy project data', 'ecopower-tracker'); ?></li>
                <li><?php esc_html_e('Data Tracking - Monitor power generation and CO2 offset', 'ecopower-tracker'); ?></li>
                <li><?php esc_html_e('CSV Import/Export - Bulk import and export project data', 'ecopower-tracker'); ?></li>
                <li><?php esc_html_e('Shortcodes - Display project data anywhere on your site', 'ecopower-tracker'); ?></li>
                <li><?php esc_html_e('Dashboard Widgets - View key statistics in the WordPress admin', 'ecopower-tracker'); ?></li>
                <li><?php esc_html_e('Responsive Design - Works great on all devices', 'ecopower-tracker'); ?></li>
            </ul>
        </div>

        <!-- Shortcodes Section -->
        <div class="about-section">
            <h2><?php esc_html_e('Available Shortcodes', 'ecopower-tracker'); ?></h2>
            <table class="wp-list-table widefat fixed striped">
                <thead>
                    <tr>
                        <th><?php esc_html_e('Shortcode', 'ecopower-tracker'); ?></th>
                        <th><?php esc_html_e('Description', 'ecopower-tracker'); ?></th>
                        <th><?php esc_html_e('Parameters', 'ecopower-tracker'); ?></th>
                    </tr>
                </thead>
                <tbody>
                    <tr>
                        <td><code>[ecopower_tracker_total_power]</code></td>
                        <td><?php esc_html_e('Display total power generated', 'ecopower-tracker'); ?></td>
                        <td>-</td>
                    </tr>
                    <tr>
                        <td><code>[ecopower_tracker_total_co2]</code></td>
                        <td><?php esc_html_e('Display total CO2 offset', 'ecopower-tracker'); ?></td>
                        <td>-</td>
                    </tr>
                    <tr>
                        <td><code>[ecopower_tracker_project_power]</code></td>
                        <td><?php esc_html_e('Display power for specific project', 'ecopower-tracker'); ?></td>
                        <td>project_id</td>
                    </tr>
                    <!-- Add more shortcodes as needed -->
                </tbody>
            </table>
        </div>

        <!-- Getting Started Section -->
        <div class="about-section">
            <h2><?php esc_html_e('Getting Started', 'ecopower-tracker'); ?></h2>
            <ol>
                <li>
                    <strong><?php esc_html_e('Add Projects:', 'ecopower-tracker'); ?></strong>
                    <?php esc_html_e('Go to EcoPower Tracker → Add New to create your first project.', 'ecopower-tracker'); ?>
                </li>
                <li>
                    <strong><?php esc_html_e('Import Data:', 'ecopower-tracker'); ?></strong>
                    <?php esc_html_e('Use the CSV import tool to bulk import your project data.', 'ecopower-tracker'); ?>
                </li>
                <li>
                    <strong><?php esc_html_e('Display Data:', 'ecopower-tracker'); ?></strong>
                    <?php esc_html_e('Use shortcodes to display project data on your pages.', 'ecopower-tracker'); ?>
                </li>
            </ol>
        </div>

        <!-- Support Section -->
        <div class="about-section">
            <h2><?php esc_html_e('Support', 'ecopower-tracker'); ?></h2>
            <p>
                <?php
                printf(
                    /* translators: %s: GitHub repository URL */
                    esc_html__('For support and feature requests, please visit our %s.', 'ecopower-tracker'),
                    '<a href="https://github.com/saqibj/EcoTracker" target="_blank">' . 
                    esc_html__('GitHub repository', 'ecopower-tracker') . 
                    '</a>'
                );
                ?>
            </p>
        </div>

        <!-- System Information -->
        <div class="about-section">
            <h2><?php esc_html_e('System Information', 'ecopower-tracker'); ?></h2>
            <table class="wp-list-table widefat fixed striped">
                <tbody>
                    <tr>
                        <td><?php esc_html_e('WordPress Version', 'ecopower-tracker'); ?></td>
                        <td><?php echo esc_html($GLOBALS['wp_version']); ?></td>
                    </tr>
                    <tr>
                        <td><?php esc_html_e('PHP Version', 'ecopower-tracker'); ?></td>
                        <td><?php echo esc_html(PHP_VERSION); ?></td>
                    </tr>
                    <tr>
                        <td><?php esc_html_e('MySQL Version', 'ecopower-tracker'); ?></td>
                        <td><?php echo esc_html($GLOBALS['wpdb']->db_version()); ?></td>
                    </tr>
                    <tr>
                        <td><?php esc_html_e('Plugin Version', 'ecopower-tracker'); ?></td>
                        <td><?php echo esc_html(ECOPOWER_TRACKER_VERSION); ?></td>
                    </tr>
                </tbody>
            </table>
        </div>
    </div>
</div>

<style>
<?php include ECOPOWER_TRACKER_PATH . 'assets/css/ecopower-tracker-admin.css'; ?>
</style>