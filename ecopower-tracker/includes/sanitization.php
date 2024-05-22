<?php
// includes/sanitization.php

if ( ! defined( 'ABSPATH' ) ) {
    exit; // Exit if accessed directly
}

function ecopower_tracker_sanitize( $input ) {
    $sanitized_input = array();
    $sanitized_input['project_company'] = sanitize_text_field( $input['project_company'] );
    $sanitized_input['project_name'] = sanitize_text_field( $input['project_name'] );
    $sanitized_input['project_location'] = sanitize_text_field( $input['project_location'] );
    $sanitized_input['type_of_plant'] = sanitize_text_field( $input['type_of_plant'] );
    $sanitized_input['project_cuf'] = floatval( $input['project_cuf'] );
    $sanitized_input['generation_capacity'] = floatval( $input['generation_capacity'] );
    $sanitized_input['date_of_activation'] = sanitize_text_field( $input['date_of_activation'] );
    return $sanitized_input;
}
?>
