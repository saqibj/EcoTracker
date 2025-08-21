/**
 * EcoPower Tracker Admin Scripts
 * 
 * @package EcoPowerTracker
 * @since 2.0.1
 */

(function($) {
    'use strict';

    // Main admin object
    const EcoPowerTrackerAdmin = {
        /**
         * Initialize admin functionality
         */
        init: function() {
            this.bindEvents();
            this.initializeDataTables();
            this.initializeDatePickers();
            this.initializeCharts();
        },

        /**
         * Bind event handlers
         */
        bindEvents: function() {
            // Project deletion
            $('.ecopower-tracker-delete').on('click', this.handleProjectDelete);

            // Form submission
            $('#ecopower-tracker-form').on('submit', this.handleFormSubmit);

            // CSV import preview
            $('#ecopower-tracker-csv').on('change', this.handleCsvPreview);

            // Filter changes
            $('.ecopower-tracker-filter').on('change', this.handleFilterChange);

            // Refresh data
            $('.ecopower-tracker-refresh').on('click', this.refreshData);
        },

        /**
         * Initialize DataTables
         */
        initializeDataTables: function() {
            if ($.fn.DataTable && $('.ecopower-tracker-table').length) {
                $('.ecopower-tracker-table').DataTable({
                    pageLength: 25,
                    responsive: true,
                    dom: 'Bfrtip',
                    buttons: ['copy', 'csv', 'excel', 'pdf', 'print'],
                    language: {
                        search: ecoPowerTrackerAdmin.i18n.search,
                        lengthMenu: ecoPowerTrackerAdmin.i18n.lengthMenu,
                        info: ecoPowerTrackerAdmin.i18n.info
                    }
                });
            }
        },

        /**
         * Initialize date pickers
         */
        initializeDatePickers: function() {
            if ($.fn.datepicker && $('.ecopower-tracker-date').length) {
                $('.ecopower-tracker-date').datepicker({
                    dateFormat: 'yy-mm-dd',
                    changeMonth: true,
                    changeYear: true
                });
            }
        },

        /**
         * Initialize charts
         */
        initializeCharts: function() {
            if (typeof Chart === 'undefined' || !$('#powerGenerationChart').length) {
                return;
            }

            this.loadChartData();
        },

        /**
         * Handle project deletion
         * @param {Event} e Click event
         */
        handleProjectDelete: function(e) {
            e.preventDefault();

            if (!confirm(ecoPowerTrackerAdmin.i18n.confirmDelete)) {
                return;
            }

            const $button = $(this);
            const projectId = $button.data('project-id');

            $.ajax({
                url: ecoPowerTrackerAdmin.ajaxUrl,
                type: 'POST',
                data: {
                    action: 'ecopower_tracker_delete_project',
                    nonce: ecoPowerTrackerAdmin.nonce,
                    project_id: projectId
                },
                beforeSend: function() {
                    $button.prop('disabled', true);
                },
                success: function(response) {
                    if (response.success) {
                        $button.closest('tr').fadeOut(function() {
                            $(this).remove();
                        });
                    } else {
                        alert(response.data.message || ecoPowerTrackerAdmin.i18n.error);
                    }
                },
                error: function() {
                    alert(ecoPowerTrackerAdmin.i18n.error);
                },
                complete: function() {
                    $button.prop('disabled', false);
                }
            });
        },

        /**
         * Handle form submission
         * @param {Event} e Submit event
         */
        handleFormSubmit: function(e) {
            e.preventDefault();

            const $form = $(this);
            const $submit = $form.find('[type="submit"]');

            $.ajax({
                url: ecoPowerTrackerAdmin.ajaxUrl,
                type: 'POST',
                data: new FormData(this),
                processData: false,
                contentType: false,
                beforeSend: function() {
                    $submit.prop('disabled', true);
                },
                success: function(response) {
                    if (response.success) {
                        window.location.href = response.data.redirect;
                    } else {
                        alert(response.data.message || ecoPowerTrackerAdmin.i18n.error);
                    }
                },
                error: function() {
                    alert(ecoPowerTrackerAdmin.i18n.error);
                },
                complete: function() {
                    $submit.prop('disabled', false);
                }
            });
        },

        /**
         * Handle CSV file preview
         * @param {Event} e Change event
         */
        handleCsvPreview: function(e) {
            const file = e.target.files[0];
            if (!file) return;

            const reader = new FileReader();
            reader.onload = function(e) {
                const csv = e.target.result;
                EcoPowerTrackerAdmin.displayCsvPreview(csv);
            };
            reader.readAsText(file);
        },

        /**
         * Display CSV preview
         * @param {string} csv CSV content
         */
        displayCsvPreview: function(csv) {
            const rows = csv.split('\n');
            const headers = rows[0].split(',');
            let html = '<table class="wp-list-table widefat fixed striped">';
            
            // Headers
            html += '<thead><tr>';
            headers.forEach(header => {
                html += `<th>${header.trim()}</th>`;
            });
            html += '</tr></thead><tbody>';

            // Preview first 5 rows
            for (let i = 1; i < Math.min(rows.length, 6); i++) {
                const cells = rows[i].split(',');
                html += '<tr>';
                cells.forEach(cell => {
                    html += `<td>${cell.trim()}</td>`;
                });
                html += '</tr>';
            }

            html += '</tbody></table>';
            $('#csv-preview').html(html);
        },

        /**
         * Handle filter changes
         */
        handleFilterChange: function() {
            const filters = {};
            $('.ecopower-tracker-filter').each(function() {
                const value = $(this).val();
                if (value) {
                    filters[$(this).data('filter')] = value;
                }
            });

            EcoPowerTrackerAdmin.refreshData(filters);
        },

        /**
         * Refresh data with filters
         * @param {Object} filters Filter values
         */
        refreshData: function(filters = {}) {
            const $container = $('.ecopower-tracker-content');

            $.ajax({
                url: ecoPowerTrackerAdmin.ajaxUrl,
                type: 'POST',
                data: {
                    action: 'ecopower_tracker_refresh_data',
                    nonce: ecoPowerTrackerAdmin.nonce,
                    filters: filters
                },
                beforeSend: function() {
                    $container.addClass('loading');
                },
                success: function(response) {
                    if (response.success) {
                        $container.html(response.data.html);
                        EcoPowerTrackerAdmin.initializeDataTables();
                        EcoPowerTrackerAdmin.initializeCharts();
                    } else {
                        alert(response.data.message || ecoPowerTrackerAdmin.i18n.error);
                    }
                },
                error: function() {
                    alert(ecoPowerTrackerAdmin.i18n.error);
                },
                complete: function() {
                    $container.removeClass('loading');
                }
            });
        },

        /**
         * Load chart data
         */
        loadChartData: function() {
            $.ajax({
                url: ecoPowerTrackerAdmin.ajaxUrl,
                type: 'POST',
                data: {
                    action: 'ecopower_tracker_chart_data',
                    nonce: ecoPowerTrackerAdmin.nonce
                },
                success: function(response) {
                    if (response.success) {
                        EcoPowerTrackerAdmin.renderChart(response.data);
                    }
                }
            });
        },

        /**
         * Render chart
         * @param {Object} data Chart data
         */
        renderChart: function(data) {
            new Chart($('#powerGenerationChart'), {
                type: 'bar',
                data: data,
                options: {
                    responsive: true,
                    scales: {
                        y: {
                            beginAtZero: true
                        }
                    }
                }
            });
        }
    };

    // Initialize when document is ready
    $(document).ready(function() {
        EcoPowerTrackerAdmin.init();
    });

})(jQuery);

/**
 * EcoPower Tracker Admin JavaScript
 */
(function($) {
    'use strict';

    // Document ready
    $(function() {
        // Initialize datepicker
        $('.datepicker').datepicker({
            dateFormat: 'yy-mm-dd',
            changeMonth: true,
            changeYear: true,
            yearRange: '2000:2030',
            showButtonPanel: true,
            showAnim: 'fadeIn'
        });

        // Handle delete confirmation
        $('.ecopower-tracker-admin').on('click', '.button-link-delete', function(e) {
            if (!confirm(ecoPowerTracker.strings.confirmDelete)) {
                e.preventDefault();
                return false;
            }
            return true;
        });

        // Form validation
        $('form.ecopower-tracker-form').on('submit', function(e) {
            var isValid = true;
            var $form = $(this);
            
            // Check required fields
            $form.find('[required]').each(function() {
                var $field = $(this);
                if (!$field.val().trim()) {
                    $field.addClass('error');
                    isValid = false;
                    
                    // Add error message if not exists
                    if (!$field.next('.error-message').length) {
                        $field.after('<span class="error-message" style="color:#dc3232;display:block;margin:5px 0 0;">' + ecoPowerTracker.strings.requiredField + '</span>');
                    }
                } else {
                    $field.removeClass('error');
                    $field.next('.error-message').remove();
                }
            });

            // Validate number fields
            $form.find('input[type="number"]').each(function() {
                var $field = $(this);
                var min = parseFloat($field.attr('min'));
                var max = parseFloat($field.attr('max'));
                var value = parseFloat($field.val());
                
                if (!isNaN(min) && value < min) {
                    $field.addClass('error');
                    isValid = false;
                    $field.next('.error-message').remove();
                    $field.after('<span class="error-message" style="color:#dc3232;display:block;margin:5px 0 0;">' + 
                        ecoPowerTracker.strings.minValue.replace('%s', min) + '</span>');
                } else if (!isNaN(max) && value > max) {
                    $field.addClass('error');
                    isValid = false;
                    $field.next('.error-message').remove();
                    $field.after('<span class="error-message" style="color:#dc3232;display:block;margin:5px 0 0;">' + 
                        ecoPowerTracker.strings.maxValue.replace('%s', max) + '</span>');
                } else {
                    $field.removeClass('error');
                    $field.next('.error-message').remove();
                }
            });

            if (!isValid) {
                e.preventDefault();
                
                // Scroll to first error
                var $firstError = $('.error').first();
                if ($firstError.length) {
                    $('html, body').animate({
                        scrollTop: $firstError.offset().top - 100
                    }, 500);
                }
                
                return false;
            }
            
            return true;
        });

        // Remove error class on input
        $('form.ecopower-tracker-form').on('input', '[required]', function() {
            var $field = $(this);
            if ($field.val().trim()) {
                $field.removeClass('error');
                $field.next('.error-message').remove();
            }
        });
    });

    // Add custom datepicker styling
    $.datepicker.regional[''] = {
        closeText: 'Close',
        prevText: 'Prev',
        nextText: 'Next',
        currentText: 'Today',
        monthNames: [
            'January', 'February', 'March', 'April', 'May', 'June',
            'July', 'August', 'September', 'October', 'November', 'December'
        ],
        monthNamesShort: [
            'Jan', 'Feb', 'Mar', 'Apr', 'May', 'Jun',
            'Jul', 'Aug', 'Sep', 'Oct', 'Nov', 'Dec'
        ],
        dayNames: ['Sunday', 'Monday', 'Tuesday', 'Wednesday', 'Thursday', 'Friday', 'Saturday'],
        dayNamesShort: ['Sun', 'Mon', 'Tue', 'Wed', 'Thu', 'Fri', 'Sat'],
        dayNamesMin: ['Su', 'Mo', 'Tu', 'We', 'Th', 'Fr', 'Sa'],
        weekHeader: 'Wk',
        dateFormat: 'yy-mm-dd',
        firstDay: 1,
        isRTL: false,
        showMonthAfterYear: false,
        yearSuffix: ''
    };

    // Set default datepicker options
    $.datepicker.setDefaults($.datepicker.regional['']);

})(jQuery);
