// Path: EcoPower-Tracker/assets/js/ecopower-tracker-admin.js
// File: ecopower-tracker-admin.js

jQuery(document).ready(function($) {
    // JavaScript for handling any admin-specific functionalities

    // Example: Confirm before deleting a project
    $('.ecopower-tracker-delete-link').on('click', function(e) {
        if (!confirm('Are you sure you want to delete this project?')) {
            e.preventDefault();
        }
    });
    
    // Additional scripts can be added here
});
