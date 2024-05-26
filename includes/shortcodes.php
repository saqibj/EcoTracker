<?php
// Shortcodes for displaying various data views
function ecopower_tracker_total_power_shortcode() {
    return ecopower_tracker_display_total_power();
}
add_shortcode('total_power_generated', 'ecopower_tracker_total_power_shortcode');

function ecopower_tracker_total_carbon_shortcode() {
    return ecopower_tracker_display_total_carbon();
}
add_shortcode('total_carbon_offset', 'ecopower_tracker_total_carbon_shortcode');

// Additional shortcodes can be defined similarly
?>
