jQuery(document).ready(function($) {
    // Functionality for import CSV button
    $('#ecopower_tracker_import_csv_button').on('click', function(e) {
        e.preventDefault();
        $('#ecopower_tracker_csv').click();
    });

    // Functionality for CSV file input change
    $('#ecopower_tracker_csv').on('change', function() {
        $('#ecopower_tracker_import_form').submit();
    });

    // Confirm before deleting a project
    $('.ecopower-delete-project').on('click', function(e) {
        if (!confirm(ecopower_tracker.confirm_delete)) {
            e.preventDefault();
        }
    });
});