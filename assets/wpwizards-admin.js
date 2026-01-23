/**
 * WP Wizards Admin Scripts
 */
(function($) {
    'use strict';
    
    // Wait for DOM to be ready
    $(document).ready(function() {
        console.log('WP Wizards admin script loaded');
        
        // Function to switch to a specific tab
        function switchToTab(targetTab) {
            if (!targetTab) return;
            
            // Remove active class from all tabs and contents
            $('.wpwizards-tab').removeClass('active');
            $('.wpwizards-tab-content').removeClass('active');
            
            // Find and activate the target tab
            var $tab = $('.wpwizards-tab[data-tab="' + targetTab + '"]');
            if ($tab.length) {
                $tab.addClass('active');
                
                // Show corresponding content
                var $content = $('#' + targetTab);
                if ($content.length) {
                    $content.addClass('active');
                    console.log('Switched to tab:', targetTab);
                }
            }
        }
        
        // Check for hash on page load (for direct links to tabs)
        if (window.location.hash) {
            var hash = window.location.hash.substring(1); // Remove #
            // Convert hash like 'kickoff' to tab ID like 'tab-kickoff'
            if (hash && !hash.startsWith('tab-')) {
                hash = 'tab-' + hash;
            }
            switchToTab(hash);
        }
        
        // Tab switching - use event delegation for better reliability
        $(document).on('click', '.wpwizards-tab', function(e) {
            e.preventDefault();
            e.stopPropagation();
            
            var $tab = $(this);
            var target = $tab.attr('data-tab');
            
            console.log('Tab clicked:', target);
            
            if (target) {
                switchToTab(target);
                // Update URL hash without scrolling
                if (history.pushState) {
                    var hashName = target.replace('tab-', '');
                    history.pushState(null, null, '#' + hashName);
                }
            }
        });
        
        // Copy to clipboard
        $('.wpwizards-copy-btn').on('click', function(e) {
            e.preventDefault();
            e.stopPropagation();
            var $btn = $(this);
            var codeBlock = $btn.siblings('.wpwizards-code-block');
            var text = codeBlock.find('code').text() || codeBlock.text();
            
            // Modern clipboard API
            if (navigator.clipboard && navigator.clipboard.writeText) {
                navigator.clipboard.writeText(text).then(function() {
                    $btn.text('Copied!').addClass('copied');
                    setTimeout(function() {
                        $btn.text('Copy Code').removeClass('copied');
                    }, 2000);
                }).catch(function(err) {
                    console.error('Failed to copy:', err);
                });
            } else {
                // Fallback for older browsers
                var temp = $('<textarea>');
                $('body').append(temp);
                temp.val(text).select();
                try {
                    document.execCommand('copy');
                    $btn.text('Copied!').addClass('copied');
                    setTimeout(function() {
                        $btn.text('Copy Code').removeClass('copied');
                    }, 2000);
                } catch (err) {
                    console.error('Failed to copy:', err);
                }
                temp.remove();
            }
        });
    });
})(jQuery);
