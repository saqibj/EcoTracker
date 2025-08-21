<?php
if (!defined('ABSPATH')) exit;
wp_enqueue_style('ecopower-tracker-frontend');
?>

<article class="ecopower-single-project">
    <header class="ecopower-project-header">
        <h1 class="ecopower-project-title">
            <?php echo esc_html($project->project_name); ?>
            <span class="ecopower-badge ecopower-badge--<?php echo esc_attr(sanitize_title($project->type_of_plant)); ?>">
                <?php echo esc_html(ucfirst($project->type_of_plant)); ?>
            </span>
        </h1>
        
        <?php if (!empty($project->project_number)): ?>
            <div class="ecopower-project-meta">
                <span class="ecopower-project-id">
                    <?php echo esc_html__('Project ID:', 'ecopower-tracker'); ?> 
                    <strong><?php echo esc_html($project->project_number); ?></strong>
                </span>
            </div>
        <?php endif; ?>
    </header>

    <div class="ecopower-project-content">
        <div class="ecopower-project-details">
            <div class="ecopower-details-grid">
                <?php if (!empty($project->project_company)): ?>
                    <div class="ecopower-detail">
                        <span class="ecopower-detail__label"><?php esc_html_e('Company', 'ecopower-tracker'); ?></span>
                        <span class="ecopower-detail__value"><?php echo esc_html($project->project_company); ?></span>
                    </div>
                <?php endif; ?>
                
                <?php if (!empty($project->project_location)): ?>
                    <div class="ecopower-detail">
                        <span class="ecopower-detail__label"><?php esc_html_e('Location', 'ecopower-tracker'); ?></span>
                        <span class="ecopower-detail__value">
                            <i class="dashicons dashicons-location"></i>
                            <?php echo esc_html($project->project_location); ?>
                        </span>
                    </div>
                <?php endif; ?>
                
                <?php if (!empty($project->date_of_activation)): ?>
                    <div class="ecopower-detail">
                        <span class="ecopower-detail__label"><?php esc_html_e('Activated', 'ecopower-tracker'); ?></span>
                        <span class="ecopower-detail__value">
                            <?php echo esc_html($this->format_date($project->date_of_activation)); ?>
                        </span>
                    </div>
                <?php endif; ?>
                
                <div class="ecopower-detail">
                    <span class="ecopower-detail__label"><?php esc_html_e('Capacity', 'ecopower-tracker'); ?></span>
                    <span class="ecopower-detail__value">
                        <?php echo esc_html($this->format_capacity($project->generation_capacity)); ?>
                    </span>
                </div>
                
                <?php if (!empty($project->project_cuf)): ?>
                    <div class="ecopower-detail">
                        <span class="ecopower-detail__label"><?php esc_html_e('CUF', 'ecopower-tracker'); ?></span>
                        <div class="ecopower-progress">
                            <div class="ecopower-progress__bar" style="width: <?php echo esc_attr($project->project_cuf); ?>%;">
                                <span class="ecopower-progress__label"><?php echo esc_html($project->project_cuf); ?>%</span>
                            </div>
                        </div>
                    </div>
                <?php endif; ?>
            </div>
            
            <?php if (!empty($project->project_description)): ?>
                <div class="ecopower-project-description">
                    <h3><?php esc_html_e('Description', 'ecopower-tracker'); ?></h3>
                    <div class="ecopower-description-content">
                        <?php echo wp_kses_post(wpautop($project->project_description)); ?>
                    </div>
                </div>
            <?php endif; ?>
        </div>
    </div>
</article>
