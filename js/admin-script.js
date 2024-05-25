jQuery(document).ready(function($) {
    $('.delete-project').on('click', function(e) {
        e.preventDefault();

        if (confirm('Are you sure you want to delete this project?')) {
            window.location.href = $(this).attr('href');
        }
    });
});
