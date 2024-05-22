<?php
// includes/settings.php

if ( ! defined( 'ABSPATH' ) ) {
    exit; // Exit if accessed directly
}

function ecopower_tracker_register_settings() {
    register_setting( 'ecopower_tracker_options_group', 'ecopower_tracker_options', 'ecopower_tracker_sanitize' );
    
    add_settings_section(
        'ecopower_tracker_settings_section',
        'Project Details',
        'ecopower_tracker_section_callback',
        'ecopower-tracker'
    );
    
    add_settings_field(
        'ecopower_tracker_project_company',
        'Project Company',
        'ecopower_tracker_project_company_callback',
        'ecopower-tracker',
        'ecopower_tracker_settings_section'
    );
    
    add_settings_field(
        'ecopower_tracker_project_name',
        'Project Name',
        'ecopower_tracker_project_name_callback',
        'ecopower-tracker',
        'ecopower_tracker_settings_section'
    );
    
    add_settings_field(
        'ecopower_tracker_project_location',
        'Project Location',
        'ecopower_tracker_project_location_callback',
        'ecopower-tracker',
        'ecopower_tracker_settings_section'
    );
    
    add_settings_field(
        'ecopower_tracker_type_of_plant',
        'Type of Plant',
        'ecopower_tracker_type_of_plant_callback',
        'ecopower-tracker',
        'ecopower_tracker_settings_section'
    );
    
    add_settings_field(
        'ecopower_tracker_project_cuf',
        'Project CUF (Capacity Utilization Factor)',
        'ecopower_tracker_project_cuf_callback',
        'ecopower-tracker',
        'ecopower_tracker_settings_section'
    );
    
    add_settings_field(
        'ecopower_tracker_generation_capacity',
        'Generation Capacity (in KWs)',
        'ecopower_tracker_generation_capacity_callback',
        'ecopower-tracker',
        'ecopower_tracker_settings_section'
    );
    
    add_settings_field(
        'ecopower_tracker_date_of_activation',
        'Date of Activation',
        'ecopower_tracker_date_of_activation_callback',
        'ecopower-tracker',
        'ecopower_tracker_settings_section'
    );
}
add_action( 'admin_init', 'ecopower_tracker_register_settings' );

// Section callback
function ecopower_tracker_section_callback() {
    echo 'Enter the project details below:';
}

// Field callbacks
function ecopower_tracker_project_company_callback() {
    $options = get_option( 'ecopower_tracker_options' );
    echo '<input type="text" name="ecopower_tracker_options[project_company]" value="' . esc_attr( $options['project_company'] ?? '' ) . '">';
}

function ecopower_tracker_project_name_callback() {
    $options = get_option( 'ecopower_tracker_options' );
    echo '<input type="text" name="ecopower_tracker_options[project_name]" value="' . esc_attr( $options['project_name'] ?? '' ) . '">';
}

function ecopower_tracker_project_location_callback() {
    $options = get_option( 'ecopower_tracker_options' );
    echo '<input type="text" name="ecopower_tracker_options[project_location]" value="' . esc_attr( $options['project_location'] ?? '' ) . '">';
}

function ecopower_tracker_type_of_plant_callback() {
    $options = get_option( 'ecopower_tracker_options' );
    echo '<input type="text" name="ecopower_tracker_options[type_of_plant]" value="' . esc_attr( $options['type_of_plant'] ?? '' ) . '">';
}

function ecopower_tracker_project_cuf_callback() {
    $options = get_option( 'ecopower_tracker_options' );
    echo '<input type="text" name="ecopower_tracker_options[project_cuf]" value="' . esc_attr( $options['project_cuf'] ?? '' ) . '">';
}

function ecopower_tracker_generation_capacity_callback() {
    $options = get_option( 'ecopower_tracker_options' );
    echo '<input type="text" name="ecopower_tracker_options[generation_capacity]" value="' . esc_attr( $options['generation_capacity'] ?? '' ) . '">';
}

function ecopower_tracker_date_of_activation_callback() {
    $options = get_option( 'ecopower_tracker_options' );
    echo '<input type="date" name="ecopower_tracker_options[date_of_activation]" value="' . esc_attr( $options['date_of_activation'] ?? '' ) . '">';
}
?>
