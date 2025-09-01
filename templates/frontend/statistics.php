<?php
if (!defined('ABSPATH')) exit;
wp_enqueue_style('ecopower-tracker-frontend');
?>

<div class="ecopower-stats">
    <h2><?php esc_html_e('Project Statistics', 'ecopower-tracker'); ?></h2>
    
    <div class="ecopower-stats-grid">
        <!-- Total Projects -->
        <div class="ecopower-stat-card">
            <h3><?php esc_html_e('Total Projects', 'ecopower-tracker'); ?></h3>
            <div class="ecopower-stat-value"><?php echo number_format_i18n($stats['total_projects']); ?></div>
        </div>
        
        <!-- Total Capacity -->
        <div class="ecopower-stat-card">
            <h3><?php esc_html_e('Total Capacity', 'ecopower-tracker'); ?></h3>
            <div class="ecopower-stat-value">
                <?php echo number_format_i18n($stats['total_capacity'], 2); ?> <small>MW</small>
            </div>
        </div>
    </div>
    
    <?php if (!empty($stats['by_type'])) : ?>
    <div class="ecopower-chart-section">
        <h3><?php esc_html_e('Projects by Type', 'ecopower-tracker'); ?></h3>
        <ul class="ecopower-stats-list">
            <?php foreach ($stats['by_type'] as $type) : ?>
                <li>
                    <span class="ecopower-stat-label"><?php echo esc_html(ucfirst($type->type_of_plant)); ?>:</span>
                    <span class="ecopower-stat-value"><?php echo absint($type->count); ?></span>
                </li>
            <?php endforeach; ?>
        </ul>
    </div>
    <?php endif; ?>
    
    <?php if (!empty($stats['by_location'])) : ?>
    <div class="ecopower-chart-section">
        <h3><?php esc_html_e('Projects by Location', 'ecopower-tracker'); ?></h3>
        <ul class="ecopower-stats-list">
            <?php foreach (array_slice($stats['by_location'], 0, 5) as $location) : ?>
                <li>
                    <span class="ecopower-stat-label"><?php echo esc_html($location->project_location); ?>:</span>
                    <span class="ecopower-stat-value"><?php echo absint($location->count); ?></span>
                </li>
            <?php endforeach; ?>
        </ul>
    </div>
    <?php endif; ?>
    
    <div class="ecopower-stats-updated">
        <?php 
        printf(
            /* translators: %s: Last update date and time */
            esc_html__('Last updated: %s', 'ecopower-tracker'),
            '<time datetime="' . esc_attr(current_time('c')) . '">' . esc_html(current_time(get_option('date_format'))) . '</time>'
        );
        ?>
    </div>
</div>
