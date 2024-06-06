<?php
if ( ! defined( 'ABSPATH' ) ) {
    exit; // Exit if accessed directly.
}

// Function to display the reporting intervals settings page
function ecopower_tracker_reporting_intervals_page() {
    ?>
    <div class="wrap">
        <h1><?php _e( 'Reporting Intervals', 'ecopower-tracker' ); ?></h1>
        <form method="post" action="options.php">
            <?php
            settings_fields( 'ecopower_tracker_reporting_intervals_group' );
            do_settings_sections( 'ecopower_tracker_reporting_intervals_group' );
            ?>
            <table class="form-table">
                <tr valign="top">
                    <th scope="row"><?php _e( 'Reporting Interval', 'ecopower-tracker' ); ?></th>
                    <td>
                        <select name="ecopower_tracker_reporting_interval">
                            <option value="daily" <?php selected( get_option( 'ecopower_tracker_reporting_interval' ), 'daily' ); ?>><?php _e( 'Daily', 'ecopower-tracker' ); ?></option>
                            <option value="weekly" <?php selected( get_option( 'ecopower_tracker_reporting_interval' ), 'weekly' ); ?>><?php _e( 'Weekly', 'ecopower-tracker' ); ?></option>
                            <option value="monthly" <?php selected( get_option( 'ecopower_tracker_reporting_interval' ), 'monthly' ); ?>><?php _e( 'Monthly', 'ecopower-tracker' ); ?></option>
                        </select>
                    </td>
                </tr>
            </table>
            <?php submit_button(); ?>
        </form>
    </div>
    <?php
}

// Function to add the reporting intervals settings page to the menu
function ecopower_tracker_add_reporting_intervals_page() {
    add_submenu_page(
        'ecopower-tracker',
        __( 'Reporting Intervals', 'ecopower-tracker' ),
        __( 'Reporting Intervals', 'ecopower-tracker' ),
        'manage_options',
        'ecopower-tracker-reporting-intervals',
        'ecopower_tracker_reporting_intervals_page'
    );
}

add_action( 'admin_menu', 'ecopower_tracker_add_reporting_intervals_page' );

// Function to register the reporting interval setting
function ecopower_tracker_register_reporting_interval_setting() {
    register_setting( 'ecopower_tracker_reporting_intervals_group', 'ecopower_tracker_reporting_interval' );
}

add_action( 'admin_init', 'ecopower_tracker_register_reporting_interval_setting' );
?>
