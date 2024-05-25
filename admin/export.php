<?php
// Export Data page content
function ecopower_tracker_export_data_page() {
    if (isset($_POST['export'])) {
        // Handle data export
        // Fetch data from the database
        // Create and download CSV file
    }
    ?>
    <form method="post">
        <button type="submit" name="export">Export</button>
    </form>
    <?php
}
ecopower_tracker_export_data_page();
?>
