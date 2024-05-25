<?php
if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

function ecopower_tracker_project_data_page() {
    if ( ! current_user_can( 'manage_options' ) ) {
        return;
    }

    if ( isset( $_POST['ecopower_tracker_import_csv'] ) ) {
        ecopower_tracker_import_csv();
    }

    if ( isset( $_GET['action'] ) && $_GET['action'] == 'edit' && isset( $_GET['project_id'] ) ) {
        include ECOPOWER_TRACKER_DIR . 'templates/edit-project.php';
    } else {
        include ECOPOWER_TRACKER_DIR . 'templates/project-list.php';
    }
}
?>
