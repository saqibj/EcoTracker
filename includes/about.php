<?php
if ( ! defined( 'ABSPATH' ) ) {
    exit; // Exit if accessed directly.
}

function ecopower_tracker_about_page() {
    ?>
    <div class="wrap">
        <h1><?php _e( 'About EcoPower Tracker', 'ecopower-tracker' ); ?></h1>
        <div style="text-align: center;">
            <img src="<?php echo plugin_dir_url( __FILE__ ) . '../img/EcoTracker-Logo.webp'; ?>" alt="EcoPower Tracker Logo" style="max-width: 20%;" />
        </div>
        <p><?php _e( 'EcoPower Tracker is a WordPress plugin to manage and display renewable energy project data.', 'ecopower-tracker' ); ?></p>
        <p><?php _e( 'For support, please visit the ', 'ecopower-tracker' ); ?><a href="https://github.com/saqibj/EcoTracker/" target="_blank"><?php _e( 'GitHub repository', 'ecopower-tracker' ); ?></a><?php _e( ' and open an issue on the ', 'ecopower-tracker' ); ?><a href="https://github.com/saqibj/EcoTracker/issues" target="_blank"><?php _e( 'GitHub issues page', 'ecopower-tracker' ); ?></a>.</p>
    </div>
    <?php
}
?>