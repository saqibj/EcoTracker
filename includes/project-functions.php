<?php
if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

function ecopower_tracker_create_table() {
    global $wpdb;
    $table_name = $wpdb->prefix . 'ecopower_tracker';
    $charset_collate = $wpdb->get_charset_collate();

    $sql = "CREATE TABLE $table_name (
        id mediumint(9) NOT NULL AUTO_INCREMENT,
        project_number varchar(255) NOT NULL,
        company varchar(255) NOT NULL,
        name varchar(255) NOT NULL,
        location varchar(255) NOT NULL,
        type varchar(50) NOT NULL,
        cuf float NOT NULL,
        capacity float NOT NULL,
        activation_date date NOT NULL,
        PRIMARY KEY  (id)
    ) $charset_collate;";

    require_once( ABSPATH . 'wp-admin/includes/upgrade.php' );
    dbDelta( $sql );
}

function ecopower_tracker_add_project( $data ) {
    global $wpdb;
    $table_name = $wpdb->prefix . 'ecopower_tracker';

    $wpdb->insert(
        $table_name,
        array(
            'project_number' => sanitize_text_field( $data['project_number'] ),
            'company' => sanitize_text_field( $data['company'] ),
            'name' => sanitize_text_field( $data['name'] ),
            'location' => sanitize_text_field( $data['location'] ),
            'type' => sanitize_text_field( $data['type'] ),
            'cuf' => floatval( $data['cuf'] ),
            'capacity' => floatval( $data['capacity'] ),
            'activation_date' => sanitize_text_field( $data['activation_date'] ),
        )
    );
}

function ecopower_tracker_update_project( $id, $data ) {
    global $wpdb;
    $table_name = $wpdb->prefix . 'ecopower_tracker';

    $wpdb->update(
        $table_name,
        array(
            'project_number' => sanitize_text_field( $data['project_number'] ),
            'company' => sanitize_text_field( $data['company'] ),
            'name' => sanitize_text_field( $data['name'] ),
            'location' => sanitize_text_field( $data['location'] ),
            'type' => sanitize_text_field( $data['type'] ),
            'cuf' => floatval( $data['cuf'] ),
            'capacity' => floatval( $data['capacity'] ),
            'activation_date' => sanitize_text_field( $data['activation_date'] ),
        ),
        array( 'id' => intval( $id ) )
    );
}

function ecopower_tracker_delete_project( $id ) {
    global $wpdb;
    $table_name = $wpdb->prefix . 'ecopower_tracker';
    $wpdb->delete( $table_name, array( 'id' => intval( $id ) ) );
}

function ecopower_tracker_get_projects() {
    global $wpdb;
    $table_name = $wpdb->prefix . 'ecopower_tracker';
    $results = $wpdb->get_results( "SELECT * FROM $table_name", ARRAY_A );
    return $results;
}

function ecopower_tracker_get_project( $id ) {
    global $wpdb;
    $table_name = $wpdb->prefix . 'ecopower_tracker';
    $project = $wpdb->get_row( $wpdb->prepare( "SELECT * FROM $table_name WHERE id = %d", $id ), ARRAY_A );
    return $project;
}
?>
