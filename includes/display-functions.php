<?php
// Function to display total power generated
function ecopower_tracker_display_total_power() {
    // Fetch data and calculate total power
    $total_power = 0;
    // Assume data fetching and calculation logic here
    return number_format($total_power, 0, '.', ',') . ' KWh';
}

// Function to display total carbon offset
function ecopower_tracker_display_total_carbon() {
    // Fetch data and calculate total carbon offset
    $total_carbon = 0;
    // Assume data fetching and calculation logic here
    return number_format($total_carbon, 0, '.', ',') . ' kg CO2';
}
