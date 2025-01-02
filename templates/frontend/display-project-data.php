// Path: EcoPower-Tracker/templates/frontend/display-project-data.php
// File: display-project-data.php

<?php
/**
 * Frontend project data display template
 *
 * @package EcoPowerTracker
 * @since 2.0.1
 */

namespace EcoPowerTracker;

if (!defined('ABSPATH')) {
    exit;
}

/**
 * @var array $projects Array of project data
 * @var array $args Template arguments
 */

// Ensure $projects is an array
$projects = is_array($projects) ? $projects : array();

// Get display options from args
$show_header = isset($args['show_header']) ? (bool) $args['show_header'] : true;
$show_filters = isset($args['show_filters']) ? (bool) $args['show_filters'] : true;
$columns = isset($args['columns']) ? (array) $args['columns'] : array(
    'project_number' => __('Project Number', 'ecopower-tracker'),
    'project_name' => __('Project Name', 'ecopower-tracker'),
    'project_company' => __('Company', 'ecopower-tracker'),
    'type_of_plant' => __('Plant Type', 'ecopower-tracker'),
    'generation_capacity' => __('Capacity (KWs)', 'ecopower-tracker'),
    'project_cuf' => __('CUF (%)', 'ecopower-tracker'),
    'date_of_activation' => __('Activation Date', 'ecopower-tracker')
);
?>

<div class="ecopower-tracker-projects" data-nonce="<?php echo esc_attr(wp_create_nonce('ecopower_tracker_frontend')); ?>">
    <?php if ($show_header): ?>
        <div class="ecopower-tracker-header">
            <h2><?php esc_html_e('Renewable Energy Projects', 'ecopower-tracker'); ?></h2>
            
            <?php if ($show_filters): ?>
                <div class="ecopower-tracker-filters">
                    <select class="filter-type">
                        <option value=""><?php esc_html_e('All Plant Types', 'ecopower-tracker'); ?></option>
                        <?php foreach (EcoPower_Tracker_Utils::get_plant_types() as $type => $label): ?>
                            <option value="<?php echo esc_attr($type); ?>">
                                <?php echo esc_html($label); ?>
                            </option>
                        <?php endforeach; ?>
                    </select>

                    <select class="filter-company">
                        <option value=""><?php esc_html_e('All Companies', 'ecopower-tracker'); ?></option>
                        <?php
                        $companies = array_unique(array_column($projects, 'project_company'));
                        sort($companies);
                        foreach ($companies as $company):
                        ?>
                            <option value="<?php echo esc_attr($company); ?>">
                                <?php echo esc_html($company); ?>
                            </option>
                        <?php endforeach; ?>
                    </select>

                    <button type="button" class="ecopower-tracker-refresh">
                        <?php esc_html_e('Refresh Data', 'ecopower-tracker'); ?>
                    </button>
                </div>
            <?php endif; ?>
        </div>
    <?php endif; ?>

    <?php if (empty($projects)): ?>
        <div class="ecopower-tracker-empty">
            <?php esc_html_e('No projects found.', 'ecopower-tracker'); ?>
        </div>
    <?php else: ?>
        <table>
            <thead>
                <tr>
                    <?php foreach ($columns as $key => $label): ?>
                        <th class="column-<?php echo esc_attr($key); ?>" data-sort="<?php echo esc_attr($key); ?>">
                            <?php echo esc_html($label); ?>
                            <span class="sort-indicator"></span>
                        </th>
                    <?php endforeach; ?>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($projects as $project): ?>
                    <tr data-project-id="<?php echo esc_attr($project->id); ?>">
                        <?php foreach ($columns as $key => $label): ?>
                            <td class="column-<?php echo esc_attr($key); ?>">
                                <?php
                                switch ($key) {
                                    case 'generation_capacity':
                                        echo esc_html(number_format($project->$key, 2));
                                        break;
                                    case 'project_cuf':
                                        echo esc_html(number_format($project->$key, 2));
                                        break;
                                    case 'date_of_activation':
                                        echo esc_html(date_i18n(
                                            get_option('date_format'),
                                            strtotime($project->$key)
                                        ));
                                        break;
                                    default:
                                        echo esc_html($project->$key);
                                        break;
                                }
                                ?>
                            </td>
                        <?php endforeach; ?>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>

        <div class="ecopower-tracker-pagination">
            <?php
            $total_pages = ceil(count($projects) / 10);
            if ($total_pages > 1):
                for ($i = 1; $i <= $total_pages; $i++):
            ?>
                <a href="#" class="page-number<?php echo $i === 1 ? ' current' : ''; ?>" 
                   data-page="<?php echo esc_attr($i); ?>">
                    <?php echo esc_html($i); ?>
                </a>
            <?php
                endfor;
            endif;
            ?>
        </div>
    <?php endif; ?>

    <div class="ecopower-tracker-loading" style="display: none;">
        <?php esc_html_e('Loading...', 'ecopower-tracker'); ?>
    </div>
</div>

<style>
<?php include ECOPOWER_TRACKER_PATH . 'assets/css/ecopower-tracker-frontend.css'; ?>
</style>