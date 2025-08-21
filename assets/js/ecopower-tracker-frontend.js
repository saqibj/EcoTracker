/**
 * EcoPower Tracker Frontend JavaScript
 */
(function($) {
    'use strict';

    // Document ready
    $(document).ready(function() {
        // Initialize tooltips
        if ($.fn.tooltip) {
            $('[data-toggle="tooltip"]').tooltip();
        }

        // Handle filter form submission
        $('.ecopower-filter-form').on('submit', function(e) {
            e.preventDefault();
            const form = $(this);
            const container = form.closest('.ecopower-projects-grid');
            const loader = $('<div class="ecopower-loader">Loading...</div>');
            
            // Show loading state
            container.append(loader);
            
            // Get form data
            const formData = form.serialize();
            
            // Get current URL and add form data
            let url = window.location.href.split('?')[0];
            if (formData) {
                url += '?' + formData;
            }
            
            // Update URL without page reload
            window.history.pushState({}, '', url);
            
            // Reload the shortcode content
            $.ajax({
                url: ecopowerTracker.ajaxurl,
                type: 'POST',
                data: {
                    action: 'ecopower_tracker_filter_projects',
                    nonce: ecopowerTracker.nonce,
                    form_data: formData
                },
                success: function(response) {
                    if (response.success) {
                        container.html(response.data.html);
                        // Re-initialize any plugins or handlers
                        initializeFrontend();
                    } else {
                        alert('Error loading projects. Please try again.');
                    }
                },
                error: function() {
                    alert('Error loading projects. Please try again.');
                },
                complete: function() {
                    loader.remove();
                }
            });
        });
        
        // Handle pagination links
        $(document).on('click', '.ecopower-pagination a', function(e) {
            e.preventDefault();
            const link = $(this).attr('href');
            const container = $('.ecopower-projects-grid');
            const loader = $('<div class="ecopower-loader">Loading...</div>');
            
            // Show loading state
            container.append(loader);
            
            // Load the page
            $.ajax({
                url: link,
                type: 'GET',
                success: function(data) {
                    const newContent = $(data).find('.ecopower-projects-grid').html();
                    container.html(newContent);
                    // Re-initialize any plugins or handlers
                    initializeFrontend();
                    // Scroll to top
                    $('html, body').animate({
                        scrollTop: container.offset().top - 20
                    }, 500);
                },
                error: function() {
                    alert('Error loading page. Please try again.');
                },
                complete: function() {
                    loader.remove();
                }
            });
        });
        
        // Initialize any frontend functionality
        initializeFrontend();
    });
    
    /**
     * Initialize frontend functionality
     */
    function initializeFrontend() {
        // Initialize any tooltips or popovers
        if ($.fn.tooltip) {
            $('[data-toggle="tooltip"]').tooltip();
        }
        
        // Initialize any accordions
        $('.ecopower-accordion-header').on('click', function() {
            $(this).toggleClass('active').next('.ecopower-accordion-content').slideToggle();
        });
        
        // Initialize any tabs
        $('.ecopower-tabs-nav a').on('click', function(e) {
            e.preventDefault();
            const tabId = $(this).attr('href');
            
            // Update active tab
            $(this).addClass('active').parent().siblings().find('a').removeClass('active');
            
            // Show active tab content
            $(tabId).addClass('active').siblings('.ecopower-tab-content').removeClass('active');
        });
    }
    
    // Handle browser back/forward buttons
    window.onpopstate = function() {
        window.location.reload();
    };
    
})(jQuery);
