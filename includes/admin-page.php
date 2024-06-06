<?php
if ( ! defined( 'ABSPATH' ) ) {
    exit; // Exit if accessed directly.
}

// Function to display the admin page for managing projects
function ecopower_tracker_admin_page() {
    ?>
    <div class="wrap">
        <h1><?php _e( 'EcoPower Tracker Projects', 'ecopower-tracker' ); ?></h1>
        <!-- Content for managing projects will go here -->
    </div>
    <?php
}

// Function to add the admin page to the menu
function ecopower_tracker_add_admin_page() {
    add_submenu_page(
        'ecopower-tracker',
        __( 'Manage Projects', 'ecopower-tracker' ),
        __( 'Manage Projects', 'ecopower-tracker' ),
        'manage_options',
        'ecopower-tracker-manage-projects',
        'ecopower_tracker_admin_page'
    );
}

add_action( 'admin_menu', 'ecopower_tracker_add_admin_page' );
?>