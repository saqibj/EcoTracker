<?php
if ( ! defined( 'ABSPATH' ) ) {
    exit; // Exit if accessed directly.
}

function ecopower_tracker_sanitize( $input ) {
    $sanitized_input = array();
    foreach ( $input as $key => $value ) {
        if ( is_array( $value ) ) {
            $sanitized_input[ $key ] = ecopower_tracker_sanitize( $value );
        } else {
            $sanitized_input[ $key ] = sanitize_text_field( $value );
        }
    }
    return $sanitized_input;
}
?>
