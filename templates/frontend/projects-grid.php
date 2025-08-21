<?php
if (!defined('ABSPATH')) {
    exit;
}

// Enqueue frontend styles and scripts
wp_enqueue_style('ecopower-tracker-frontend');
?>

<div class="ecopower-projects-grid">
    <?php if ($atts['show_filters'] === 'yes') : ?>
    <div class="ecopower-filters">
        <form method="get" class="ecopower-filter-form">
            <div class="ecopower-filter-row">
                <div class="ecopower-filter-group">
                    <label for="ecopower-type-filter"><?php esc_html_e('Type', 'ecopower-tracker'); ?></label>
                    <select name="type" id="ecopower-type-filter" class="ecopower-filter">
                        <option value=""><?php esc_html_e('All Types', 'ecopower-tracker'); ?></option>
                        <?php foreach ($this->db->get_project_types() as $type) : ?>
                            <option value="<?php echo esc_attr($type->type_of_plant); ?>" <?php selected($args['type'], $type->type_of_plant); ?>>
                                <?php echo esc_html(ucfirst($type->type_of_plant)); ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>
                
                <div class="ecopower-filter-group">
                    <label for="ecopower-orderby"><?php esc_html_e('Sort By', 'ecopower-tracker'); ?></label>
                    <select name="orderby" id="ecopower-orderby" class="ecopower-filter">
                        <option value="date_of_activation" <?php selected($args['orderby'], 'date_of_activation'); ?>><?php esc_html_e('Activation Date', 'ecopower-tracker'); ?></option>
                        <option value="project_name" <?php selected($args['orderby'], 'project_name'); ?>><?php esc_html_e('Project Name', 'ecopower-tracker'); ?></option>
                        <option value="generation_capacity" <?php selected($args['orderby'], 'generation_capacity'); ?>><?php esc_html_e('Capacity', 'ecopower-tracker'); ?></option>
                    </select>
                    
                    <select name="order" class="ecopower-filter">
                        <option value="DESC" <?php selected($args['order'], 'DESC'); ?>><?php esc_html_e('Descending', 'ecopower-tracker'); ?></option>
                        <option value="ASC" <?php selected($args['order'], 'ASC'); ?>><?php esc_html_e('Ascending', 'ecopower-tracker'); ?></option>
                    </select>
                </div>
                
                <div class="ecopower-filter-group">
                    <button type="submit" class="button"><?php esc_html_e('Apply Filters', 'ecopower-tracker'); ?></button>
                    <a href="?" class="button button-link"><?php esc_html_e('Reset', 'ecopower-tracker'); ?></a>
                </div>
            </div>
        </form>
    </div>
    <?php endif; ?>
    
    <?php if (!empty($projects)) : ?>
        <div class="ecopower-grid ecopower-grid--<?php echo esc_attr($atts['columns']); ?>-columns">
            <?php foreach ($projects as $project) : ?>
                <div class="ecopower-grid-item">
                    <div class="ecopower-project-card">
                        <div class="ecopower-project-card__header">
                            <h3 class="ecopower-project-card__title">
                                <a href="<?php echo esc_url(add_query_arg('project_id', $project->id, get_permalink())); ?>">
                                    <?php echo esc_html($project->project_name); ?>
                                </a>
                            </h3>
                            <span class="ecopower-project-card__type ecopower-badge ecopower-badge--<?php echo esc_attr(sanitize_title($project->type_of_plant)); ?>">
                                <?php echo esc_html(ucfirst($project->type_of_plant)); ?>
                            </span>
                        </div>
                        
                        <div class="ecopower-project-card__content">
                            <div class="ecopower-project-card__meta">
                                <div class="ecopower-meta">
                                    <span class="ecopower-meta__label"><?php esc_html_e('Capacity', 'ecopower-tracker'); ?>:</span>
                                    <span class="ecopower-meta__value"><?php echo esc_html($this->format_capacity($project->generation_capacity)); ?></span>
                                </div>
                                
                                <?php if (!empty($project->project_location)) : ?>
                                <div class="ecopower-meta">
                                    <span class="ecopower-meta__label"><?php esc_html_e('Location', 'ecopower-tracker'); ?>:</span>
                                    <span class="ecopower-meta__value"><?php echo esc_html($project->project_location); ?></span>
                                </div>
                                <?php endif; ?>
                                
                                <?php if (!empty($project->date_of_activation)) : ?>
                                <div class="ecopower-meta">
                                    <span class="ecopower-meta__label"><?php esc_html_e('Activated', 'ecopower-tracker'); ?>:</span>
                                    <span class="ecopower-meta__value"><?php echo esc_html($this->format_date($project->date_of_activation)); ?></span>
                                </div>
                                <?php endif; ?>
                            </div>
                            
                            <?php if (!empty($project->project_cuf)) : ?>
                            <div class="ecopower-project-card__progress">
                                <div class="ecopower-progress">
                                    <div class="ecopower-progress__bar" style="width: <?php echo esc_attr($project->project_cuf); ?>%;">
                                        <span class="ecopower-progress__label"><?php echo esc_html($project->project_cuf); ?>% CUF</span>
                                    </div>
                                </div>
                            </div>
                            <?php endif; ?>
                        </div>
                        
                        <div class="ecopower-project-card__footer">
                            <a href="<?php echo esc_url(add_query_arg('project_id', $project->id, get_permalink())); ?>" class="button button-small">
                                <?php esc_html_e('View Details', 'ecopower-tracker'); ?>
                            </a>
                        </div>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>
        
        <?php if ($atts['pagination'] === 'yes' && $total_pages > 1) : ?>
        <div class="ecopower-pagination">
            <?php
            echo paginate_links(array(
                'base' => str_replace(999999999, '%#%', esc_url(get_pagenum_link(999999999))),
                'format' => '?paged=%#%',
                'current' => max(1, $args['paged']),
                'total' => $total_pages,
                'prev_text' => '&larr; ' . __('Previous', 'ecopower-tracker'),
                'next_text' => __('Next', 'ecopower-tracker') . ' &rarr;',
            ));
            ?>
        </div>
        <?php endif; ?>
        
    <?php else : ?>
        <div class="ecopower-no-results">
            <p><?php esc_html_e('No projects found matching your criteria.', 'ecopower-tracker'); ?></p>
        </div>
    <?php endif; ?>
</div>
