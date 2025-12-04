jQuery(document).ready(function($) {
    
    // --- TAB SYSTEM ---
    $('.trvlr-tab-link').on('click', function(e) {
        e.preventDefault();
        
        var targetTab = $(this).data('tab');
        
        // Update active states
        $('.trvlr-tab-link').removeClass('active');
        $(this).addClass('active');
        
        // Show target tab
        $('.trvlr-tab-pane').removeClass('active');
        $('#trvlr-tab-' + targetTab).addClass('active');
        
        // Update URL hash (optional, for bookmarking)
        if (history.pushState) {
            history.pushState(null, null, '#' + targetTab);
        }
    });
    
    // Check URL hash on load
    if (window.location.hash) {
        var hashTab = window.location.hash.substring(1);
        var $tabLink = $('.trvlr-tab-link[data-tab="' + hashTab + '"]');
        if ($tabLink.length) {
            $tabLink.trigger('click');
        }
    }
    
    // --- PAYMENT PAGE CREATION ---
    $('#trvlr-create-payment-page').on('click', function(e) {
        e.preventDefault();
        var $btn = $(this);
        var $status = $('#trvlr-payment-page-status');
        $btn.prop('disabled', true);
        $status.text('Creating...').css('color', '#000');
        
        $.ajax({
            url: ajaxurl,
            type: 'POST',
            data: {
                action: 'trvlr_create_payment_page',
                nonce: trvlr_admin_vars.nonce
            },
            success: function(response) {
                if (response.success) {
                    $status.text('Page created! Reloading...').css('color', 'green');
                    setTimeout(function() { location.reload(); }, 1500);
                } else {
                    $status.text('Error: ' + (response.data || 'Unknown error')).css('color', 'red');
                    $btn.prop('disabled', false);
                }
            },
            error: function() {
                $status.text('Ajax error.').css('color', 'red');
                $btn.prop('disabled', false);
            }
        });
    });

    // --- EXISTING ADMIN JS ---
    // Handle Delete All (Everything)
    $('#trvlr-delete-all').on('click', function(e) {
        e.preventDefault();
        if (!confirm('Are you sure you want to delete ALL attractions AND their images? This cannot be undone.')) {
            return;
        }
        handleDelete('trvlr_delete_all_data', $(this));
    });

    // Handle Delete Posts Only
    $('#trvlr-delete-posts').on('click', function(e) {
        e.preventDefault();
        if (!confirm('Are you sure you want to delete ALL attractions? Images will be kept in Media Library.')) {
            return;
        }
        handleDelete('trvlr_delete_posts_only', $(this));
    });

    function handleDelete(action, $btn) {
        var $status = $('#trvlr-delete-status');
        $btn.prop('disabled', true);
        $status.text('Deleting...').css('color', '#000');
        
        $.ajax({
            url: ajaxurl,
            type: 'POST',
            data: {
                action: action,
                nonce: trvlr_admin_vars.nonce
            },
            success: function(response) {
                if (response.success) {
                    $status.text('Deleted successfully.').css('color', 'green');
                    setTimeout(function() { location.reload(); }, 1500);
                } else {
                    $status.text('Error: ' + (response.data || 'Unknown error')).css('color', 'red');
                    $btn.prop('disabled', false);
                }
            },
            error: function() {
                $status.text('Ajax error.').css('color', 'red');
                $btn.prop('disabled', false);
            }
        });
    }

    // Handle Sync Now
    $('#trvlr-sync-now').on('click', function(e) {
        e.preventDefault();
        var $btn = $(this);
        var $status = $('#trvlr-sync-status');
        $btn.prop('disabled', true);
        $status.text('Syncing... check logs for progress.').css('color', '#000');
        
        $.ajax({
            url: ajaxurl,
            type: 'POST',
            data: {
                action: 'trvlr_manual_sync',
                nonce: trvlr_admin_vars.nonce
            },
            success: function(response) {
                if (response.success) {
                    $status.text('Sync complete! Reloading...').css('color', 'green');
                    setTimeout(function() { location.reload(); }, 1500);
                } else {
                    $status.text('Error: ' + (response.data || 'Unknown error')).css('color', 'red');
                    $btn.prop('disabled', false);
                }
            },
            error: function() {
                $status.text('Ajax error.').css('color', 'red');
                $btn.prop('disabled', false);
            }
        });
    });

    // --- REPEATER FIELD LOGIC ---
    $('.trvlr-add-row').on('click', function(e) {
        e.preventDefault();
        var key = $(this).data('key');
        var tmpl = $('#tmpl-trvlr-repeater-' + key).html();
        var wrapper = $(this).closest('.trvlr-repeater-wrapper').find('.trvlr-repeater-rows');
        
        // Generate a unique index (timestamp + random)
        var index = new Date().getTime() + Math.floor(Math.random() * 1000);
        var rowHtml = tmpl.replace(/{{index}}/g, index);
        
        wrapper.append(rowHtml);
    });

    $(document).on('click', '.trvlr-remove-row', function(e) {
        e.preventDefault();
        if (confirm('Remove this row?')) {
            $(this).closest('.trvlr-repeater-row').remove();
        }
    });

    // --- FORCE SYNC MANAGEMENT ---
    // Toggle field selection row
    $('.trvlr-toggle-fields').on('click', function() {
        var postId = $(this).data('post-id');
        var $row = $('#trvlr-fields-' + postId);
        var $icon = $(this).find('.dashicons');
        
        $row.toggle();
        
        if ($row.is(':visible')) {
            $icon.removeClass('dashicons-arrow-down-alt2').addClass('dashicons-arrow-up-alt2');
        } else {
            $icon.removeClass('dashicons-arrow-up-alt2').addClass('dashicons-arrow-down-alt2');
        }
    });

    // Select all fields for a post
    $('.trvlr-select-all-fields').on('change', function() {
        var postId = $(this).data('post-id');
        var checked = $(this).is(':checked');
        $('.trvlr-field-checkbox[data-post-id="' + postId + '"]').prop('checked', checked);
    });

    // Update select all checkbox when individual checkboxes change
    $('.trvlr-field-checkbox').on('change', function() {
        var postId = $(this).data('post-id');
        var $allCheckbox = $('.trvlr-select-all-fields[data-post-id="' + postId + '"]');
        var $checkboxes = $('.trvlr-field-checkbox[data-post-id="' + postId + '"]');
        var allChecked = $checkboxes.length === $checkboxes.filter(':checked').length;
        $allCheckbox.prop('checked', allChecked);
    });

    // Save force sync settings
    $('#trvlr-save-force-sync').on('click', function(e) {
        e.preventDefault();
        var $btn = $(this);
        var $status = $('#trvlr-force-sync-status');
        var force_sync_fields = {};
        
        // Collect all checked fields for each post
        $('.trvlr-field-checkbox:checked').each(function() {
            var postId = $(this).data('post-id');
            var field = $(this).val();
            
            if (!force_sync_fields[postId]) {
                force_sync_fields[postId] = [];
            }
            force_sync_fields[postId].push(field);
        });
        
        $btn.prop('disabled', true);
        $status.text('Saving...').css('color', '#000');
        
        $.ajax({
            url: ajaxurl,
            type: 'POST',
            data: {
                action: 'trvlr_save_force_sync_settings',
                nonce: trvlr_admin_vars.nonce,
                force_sync_fields: force_sync_fields
            },
            success: function(response) {
                if (response.success) {
                    $status.text(response.data.message).css('color', 'green');
                    setTimeout(function() { location.reload(); }, 1500);
                } else {
                    $status.text('Error: ' + (response.data || 'Unknown error')).css('color', 'red');
                    $btn.prop('disabled', false);
                }
            },
            error: function() {
                $status.text('Ajax error.').css('color', 'red');
                $btn.prop('disabled', false);
            }
        });
    });

    // Clear all force sync settings
    $('#trvlr-clear-all-edits').on('click', function(e) {
        e.preventDefault();
        if (!confirm('Are you sure you want to clear all force sync settings? Custom edit flags will remain, but no fields will be force-synced on next sync.')) {
            return;
        }
        
        var $btn = $(this);
        var $status = $('#trvlr-force-sync-status');
        
        $btn.prop('disabled', true);
        $status.text('Clearing...').css('color', '#000');
        
        $.ajax({
            url: ajaxurl,
            type: 'POST',
            data: {
                action: 'trvlr_clear_all_custom_edits',
                nonce: trvlr_admin_vars.nonce
            },
            success: function(response) {
                if (response.success) {
                    $status.text(response.data.message).css('color', 'green');
                    setTimeout(function() { location.reload(); }, 1500);
                } else {
                    $status.text('Error: ' + (response.data || 'Unknown error')).css('color', 'red');
                    $btn.prop('disabled', false);
                }
            },
            error: function() {
                $status.text('Ajax error.').css('color', 'red');
                $btn.prop('disabled', false);
            }
        });
    });

    // --- LOGS MANAGEMENT ---
    // Filter logs by type
    $('#log-type-filter').on('change', function() {
        var filterType = $(this).val();
        var $rows = $('.trvlr-logs-table tbody tr');
        
        if (filterType === '') {
            $rows.show();
        } else {
            $rows.hide();
            $rows.filter('[data-log-type="' + filterType + '"]').show();
        }
    });

    // View log details
    $(document).on('click', '.trvlr-view-log-details', function(e) {
        e.preventDefault();
        var details = $(this).data('details');
        var formatted = JSON.stringify(JSON.parse(details), null, 2);
        $('#trvlr-log-details-content').text(formatted);
        $('#trvlr-log-details-modal').fadeIn();
    });

    // Close modal
    $('.trvlr-modal-close, .trvlr-modal').on('click', function(e) {
        if (e.target === this) {
            $('#trvlr-log-details-modal').fadeOut();
        }
    });

    // Clear old logs
    $('#trvlr-clear-old-logs').on('click', function(e) {
        e.preventDefault();
        if (!confirm('Clear logs older than 30 days?')) {
            return;
        }
        
        var $btn = $(this);
        var $status = $('#trvlr-logs-status');
        
        $btn.prop('disabled', true);
        $status.text('Clearing...').css('color', '#000');
        
        $.ajax({
            url: ajaxurl,
            type: 'POST',
            data: {
                action: 'trvlr_clear_old_logs',
                nonce: trvlr_admin_vars.nonce
            },
            success: function(response) {
                if (response.success) {
                    $status.text(response.data.message).css('color', 'green');
                    setTimeout(function() { location.reload(); }, 1500);
                } else {
                    $status.text('Error: ' + (response.data || 'Unknown error')).css('color', 'red');
                    $btn.prop('disabled', false);
                }
            },
            error: function() {
                $status.text('Ajax error.').css('color', 'red');
                $btn.prop('disabled', false);
            }
        });
    });

    // Clear all logs
    $('#trvlr-clear-all-logs').on('click', function(e) {
        e.preventDefault();
        if (!confirm('Clear ALL logs? This cannot be undone!')) {
            return;
        }
        
        var $btn = $(this);
        var $status = $('#trvlr-logs-status');
        
        $btn.prop('disabled', true);
        $status.text('Clearing...').css('color', '#000');
        
        $.ajax({
            url: ajaxurl,
            type: 'POST',
            data: {
                action: 'trvlr_clear_all_logs',
                nonce: trvlr_admin_vars.nonce
            },
            success: function(response) {
                if (response.success) {
                    $status.text(response.data.message).css('color', 'green');
                    setTimeout(function() { location.reload(); }, 1500);
                } else {
                    $status.text('Error: ' + (response.data || 'Unknown error')).css('color', 'red');
                    $btn.prop('disabled', false);
                }
            },
            error: function() {
                $status.text('Ajax error.').css('color', 'red');
                $btn.prop('disabled', false);
            }
        });
    });

    // --- SCHEDULED SYNC ---
    // Enable/disable frequency select based on checkbox
    $('#trvlr-sync-enabled').on('change', function() {
        $('#trvlr-sync-frequency').prop('disabled', !$(this).is(':checked'));
    });

    // Save schedule settings
    $('#trvlr-save-schedule').on('click', function(e) {
        e.preventDefault();
        
        var $btn = $(this);
        var $status = $('#trvlr-schedule-status');
        var enabled = $('#trvlr-sync-enabled').is(':checked');
        var frequency = $('#trvlr-sync-frequency').val();
        
        $btn.prop('disabled', true);
        $status.text('Saving...').css('color', '#000');
        
        $.ajax({
            url: ajaxurl,
            type: 'POST',
            data: {
                action: 'trvlr_save_schedule_settings',
                nonce: trvlr_admin_vars.nonce,
                enabled: enabled,
                frequency: frequency
            },
            success: function(response) {
                if (response.success) {
                    $status.text(response.data.message).css('color', 'green');
                    setTimeout(function() { location.reload(); }, 1500);
                } else {
                    $status.text('Error: ' + (response.data || 'Unknown error')).css('color', 'red');
                    $btn.prop('disabled', false);
                }
            },
            error: function() {
                $status.text('Ajax error.').css('color', 'red');
                $btn.prop('disabled', false);
            }
        });
    });

    // --- NOTIFICATIONS ---
    // Save notification settings
    $('#trvlr-save-notifications').on('click', function(e) {
        e.preventDefault();
        
        var $btn = $(this);
        var $status = $('#trvlr-notifications-status');
        var email = $('#trvlr-notification-email').val();
        var enabled_types = [];
        
        $('input[name="trvlr_enabled_notifications[]"]:checked').each(function() {
            enabled_types.push($(this).val());
        });
        
        $btn.prop('disabled', true);
        $status.text('Saving...').css('color', '#000');
        
        $.ajax({
            url: ajaxurl,
            type: 'POST',
            data: {
                action: 'trvlr_save_notifications',
                nonce: trvlr_admin_vars.nonce,
                email: email,
                enabled_types: enabled_types
            },
            success: function(response) {
                if (response.success) {
                    $status.text(response.data.message).css('color', 'green');
                    setTimeout(function() { $status.fadeOut(); }, 3000);
                    $btn.prop('disabled', false);
                } else {
                    $status.text('Error: ' + (response.data || 'Unknown error')).css('color', 'red');
                    $btn.prop('disabled', false);
                }
            },
            error: function() {
                $status.text('Ajax error.').css('color', 'red');
                $btn.prop('disabled', false);
            }
        });
    });

    // Send test email
    $('#trvlr-send-test-email').on('click', function(e) {
        e.preventDefault();
        
        var $btn = $(this);
        var $status = $('#trvlr-test-email-status');
        
        $btn.prop('disabled', true);
        $status.text('Sending...').css('color', '#000');
        
        $.ajax({
            url: ajaxurl,
            type: 'POST',
            data: {
                action: 'trvlr_send_test_email',
                nonce: trvlr_admin_vars.nonce
            },
            success: function(response) {
                if (response.success) {
                    $status.text(response.data.message).css('color', 'green');
                    setTimeout(function() { $status.fadeOut(); }, 5000);
                } else {
                    $status.text('Error: ' + (response.data || 'Unknown error')).css('color', 'red');
                }
                $btn.prop('disabled', false);
            },
            error: function() {
                $status.text('Ajax error.').css('color', 'red');
                $btn.prop('disabled', false);
            }
        });
    });

    // --- LOG EXPORT ---
    // Export logs to CSV
    $('#trvlr-export-logs').on('click', function(e) {
        e.preventDefault();
        
        var type = $('#export-type-filter').val();
        var date_from = $('#export-date-from').val();
        var date_to = $('#export-date-to').val();
        var limit = $('#export-limit').val();
        
        // Build URL with parameters
        var params = {
            trvlr_export_logs: '1',
            _wpnonce: trvlr_admin_vars.nonce
        };
        
        if (type) params.type = type;
        if (date_from) params.date_from = date_from;
        if (date_to) params.date_to = date_to;
        if (limit) params.limit = limit;
        
        // Create URL
        var url = ajaxurl.replace('admin-ajax.php', 'admin.php?page=trvlr-settings') + '&' + $.param(params);
        
        // Show status
        var $status = $('#trvlr-export-status');
        $status.text('Preparing download...').css('color', '#000').show();
        
        // Trigger download by opening URL
        window.location.href = url;
        
        // Update status
        setTimeout(function() {
            $status.text('Download started').css('color', 'green');
            setTimeout(function() { $status.fadeOut(); }, 3000);
        }, 500);
    });

});
