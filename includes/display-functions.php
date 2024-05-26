<?php
// Function to display total power generated
function ecopower_tracker_display_total_power() {
    $total_power = ecopower_tracker_calculate_total_power();
    return number_format($total_power, 0, '.', ',') . ' KWh';
}

// Function to display total carbon offset
function ecopower_tracker_display_total_carbon() {
    $total_carbon = ecopower_tracker_calculate_total_carbon();
    return number_format($total_carbon, 0, '.', ',') . ' kg CO2';
}
?>
