<?php
if ( ! defined( 'ABSPATH' ) ) {
    exit; // Exit if accessed directly.
}

function ecopower_tracker_about_page() {
    ?>
    <div class="wrap">
        <h1><?php _e( 'About EcoPower Tracker', 'ecopower-tracker' ); ?></h1>
        <p><?php _e( 'This is a simplified test message for the About page.', 'ecopower-tracker' ); ?></p>
    </div>
    <?php
}
?>