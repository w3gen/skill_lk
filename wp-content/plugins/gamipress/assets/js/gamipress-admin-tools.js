(function( $ ) {

    // ----------------------------------
    // Bulk Awards/Revokes Tool
    // ----------------------------------

    // Register points movement
    $('#bulk-awards, #bulk-revokes').on('change', '#bulk_award_points_register_movement, #bulk_revoke_points_register_movement', function() {

        var target =  $(this).closest('.cmb-row').next();

        if( $(this).prop('checked') ) {
            target.slideDown(250).removeClass('cmb2-tab-ignore');
        } else {
            target.slideUp(250).addClass('cmb2-tab-ignore');
        }

    });

    $('#bulk_award_points_register_movement, #bulk_revoke_points_register_movement').each( function() {

        var target =  $(this).closest('.cmb-row').next();

        if( $(this).prop('checked') ) {
            target.show().removeClass('cmb2-tab-ignore');
        } else {
            target.hide().addClass('cmb2-tab-ignore');
        }

    });

    // Award to all users
    $('#bulk-awards, #bulk-revokes').on('change',
        '#bulk_award_points_all_users, #bulk_award_achievements_all_users, #bulk_award_rank_all_users, '
        + '#bulk_revoke_points_all_users, #bulk_revoke_achievements_all_users, #bulk_revoke_rank_all_users',
        function() {

            var users_target = $('#' + $(this).attr('id').replace('_all', '')).closest('.cmb-row');
            var roles_target = $('#' + $(this).attr('id').replace('all_users', 'roles')).closest('.cmb-row');

            if( $(this).prop('checked') ) {
                users_target.slideUp(250).addClass('cmb2-tab-ignore');
                roles_target.slideUp(250).addClass('cmb2-tab-ignore');
            } else {
                users_target.slideDown(250).removeClass('cmb2-tab-ignore');
                roles_target.slideDown(250).removeClass('cmb2-tab-ignore');
            }

        });

    function gamipress_run_bulk_tool( button, loop ) {

        // Initialize loop
        if( loop === undefined ) loop = 0;

        var response_id = button.attr('id').replace('_button', '_response');
        var active_tab = button.closest('.cmb-tabs-wrap').find('.cmb-tab.active');
        var action = ( button.attr('id').indexOf('bulk_award_') !== -1 ? 'bulk_award' : 'bulk_revoke' );
        var data;

        if( action === 'bulk_award' ) {
            data = {
                action: 'gamipress_bulk_awards_tool',
                nonce: gamipress_admin_tools.nonce,
                bulk_award: button.attr('id').replace('bulk_award_', '').replace('_button', ''),
                loop: loop
            };
        } else if( action === 'bulk_revoke' ) {
            data = {
                action: 'gamipress_bulk_revokes_tool',
                nonce: gamipress_admin_tools.nonce,
                bulk_revoke: button.attr('id').replace('bulk_revoke_', '').replace('_button', ''),
                loop: loop
            };
        }

        // Loop all fields to build the request data
        $(active_tab.data('fields')).find('input, select, textarea').each(function() {

            if( $(this).attr('type') === 'checkbox' ) {
                // Checkboxes are sent just when checked
                if( $(this).prop('checked') )
                    data[$(this).attr('name')] = $(this).val();
            } else {
                data[$(this).attr('name')] = $(this).val();
            }

        });

        // Disable the button
        button.prop('disabled', true);

        if( ! $('#' + response_id).length )
            button.parent().append('<span id="' + response_id + '" style="display: inline-block; padding: 5px 0 0 8px;"></span>');

        // Show the spinner
        if( loop === 0 )
            $('#' + response_id).html('<span class="spinner is-active" style="float: none; margin: 0;"></span>');

        $.post(
            ajaxurl,
            data,
            function( response ) {

                // Run again utility
                if( response.data.run_again !== undefined && response.data.run_again && response.success === true ) {

                    // Keep the spinner and add the server response
                    $('#' + response_id).html(
                        '<span class="spinner is-active" style="float: none; margin: 0;"></span>'
                        + '<span style="display: inline-block; padding-left: 5px;">' + ( response.data.message !== undefined ? response.data.message : response.data ) + '</span>'
                    );

                    loop++;

                    // Run again passing the next loop index
                    gamipress_run_bulk_tool( button, loop );

                    return;
                }

                // Update response color text
                if( response.success === false )
                    $('#' + response_id).css({color:'#a00'});
                else
                    $('#' + response_id).css({color:''});

                // Add the server response
                $('#' + response_id).html(response.data);

                // Increase loop count
                if( response.success !== false )
                    loop++;

                // Enable the button
                button.prop('disabled', false);
            }
        ).fail(function() {

            $('#' + response_id).html('The server has returned an internal error.');

            // Enable the button
            button.prop('disabled', false);
        });

    }

    $('#bulk_award_points_button, #bulk_award_achievements_button, #bulk_award_rank_button, '
        + '#bulk_revoke_points_button, #bulk_revoke_achievements_button, #bulk_revoke_rank_button').on('click', function(e) {
        e.preventDefault();

        gamipress_run_bulk_tool( $(this) );
    });

    // ----------------------------------
    // Recount Activity Tool
    // ----------------------------------

    function gamipress_run_recount_activity_tool( loop ) {

        if( loop === undefined )
            loop = 0;

        var button = $("#recount_activity");
        var activity = $('#activity_to_recount').val();
        var limit = $('#entries_per_loop').val();

        $.post(
            ajaxurl,
            {
                action: 'gamipress_recount_activity_tool',
                nonce: gamipress_admin_tools.nonce,
                activity: activity,
                limit: limit,
                loop: loop // Used on run again utility to let know to the tool in which loop we are now
            },
            function( response ) {

                // Run again utility
                if( response.data.run_again !== undefined && response.data.run_again && response.success === true ) {

                    var running_selector = '#recount-activity-response #running-' + activity;

                    if( ! $(running_selector).length )
                        $('#recount-activity-response').append( '<span id="running-' + activity + '"></span>' );

                    $(running_selector).html( ( response.data.message !== undefined ? response.data.message : response.data ) );

                    if( response.data.log !== undefined && response.data.log.length ) {
                        if( ! $('#recount-activity-log').length )
                            $(
                                '<div id="recount-activity-log">'
                                + '<p>'
                                    + '<a href="#" id="recount-activity-log-toggle">Show log</a>'
                                    + ' | '
                                    + '<a href="#" id="recount-activity-log-download">Download log</a>'
                                + '</p>'
                                + '<div id="recount-activity-log-content" style="display: none;"></div>'
                                + '</div>'
                            ).insertAfter( $('#recount-activity-response') );

                        $('#recount-activity-log-content').append( response.data.log );
                    }

                    loop++;

                    // Run again passing the next loop index
                    gamipress_run_recount_activity_tool( loop );

                    return;
                }

                $('#recount-activity-notice').remove();

                if( response.success === false )
                    $('#recount-activity-response').css({color:'#a00'});
                else
                    $('#recount-activity-response').css({color:''});

                $('#recount-activity-response').html(response.data);

                // Enable the button and the activity select
                button.prop('disabled', false);
                $('#activity_to_recount').prop('disabled', false);
            }
        ).fail(function() {

            $('#recount-activity-notice').remove();

            $('#recount-activity-response').html('The server has returned an internal error.');

            if( $('#recount-activity-log').length )
                $('#recount-activity-log-content').append('The server has returned an internal error.');

            // Enable the button and the activity select
            button.prop('disabled', false);
            $('#activity_to_recount').prop('disabled', false);
        });
    }

    $("#recount_activity").on('click', function(e) {
        e.preventDefault();

        $('#recount-activity-warning').remove();

        if( $('#activity_to_recount').val() === '' ) {
            $(this).parent().prepend('<p id="recount-activity-warning" class="cmb2-metabox-description" style="color: #a00;">You need to choose an activity to recount.</p>');
            return false;
        }

        var $this = $(this);

        // Disable the button and the activity select
        $this.prop('disabled', true);
        $('#activity_to_recount').prop('disabled', true);

        // Reset the activity log
        if( $('#recount-activity-log').length )
            $('#recount-activity-log').remove();

        // Show a notice to let know to the user that process could take a while
        $this.parent().prepend('<p id="recount-activity-notice" class="cmb2-metabox-description">' + gamipress_admin_tools.recount_activity_notice + '</p>');

        if( ! $('#recount-activity-response').length )
            $this.parent().append('<span id="recount-activity-response"></span>');

        // Show the spinner
        $('#recount-activity-response').html('<span class="spinner is-active" style="float: none;"></span>');

        // Make the ajax request
        gamipress_run_recount_activity_tool();
    });

    // Activity log toggle
    $('body').on('click', '#recount-activity-log-toggle', function(e) {
        e.preventDefault();

        $('#recount-activity-log-content').toggle();

        if( $(this).text() === 'Show log' ) {
            $(this).text( 'Hide log' );
        } else {
            $(this).text( 'Show log' );
        }
    });

    // Download activity log
    $('body').on('click', '#recount-activity-log-download', function(e) {
        e.preventDefault();

        var activity = $('#activity_to_recount').val();

        gamipress_download_file( $('#recount-activity-log-content').text(), 'gamipress-activity-recount-' + activity, 'log' );
    });

    // ----------------------------------
    // Logs Clean Up Tool
    // ----------------------------------

    $('#logs_clean_up_count, #logs_clean_up').on('click', function(e) {
        e.preventDefault();

        var $this = $(this);
        var form = $this.closest('#cmb2-metabox-logs-clean-up');
        var id = $this.attr('id');
        var response_id = 'logs-clean-up-response';
        var log_types = [];
        var from = form.find('input[name="from"]').val();
        var to = form.find('input[name="to"]').val();
        var error = '';

        form.find('input[name="logs_type[]"]:checked').each(function() {
            log_types.push( $(this).val() )
        });

        if( log_types.length === 0 )
            error = 'Please, choose at least 1 log type.';

        if( ! $('#' + response_id).length )
            $this.parent().append('<span id="' + response_id + '" style="display: inline-block; padding: 5px 0 0 8px;"></span>');

        // Remove error messages
        $('#' + response_id).html('');

        // If there is any error, show it to the user
        if( error !== '' ) {
            $('#' + response_id).css({color:'#a00'});
            $('#' + response_id).html(error);
            return false;
        }

        // Disable both buttons
        $('#logs_clean_up_count, #logs_clean_up').prop('disabled', true);

        // Show the spinner
        $('#' + response_id).html('<span class="spinner is-active" style="float: none; margin: 0;"></span>');

        $.post(
            ajaxurl,
            {
                action: 'gamipress_' + id + '_tool',
                nonce: gamipress_admin_tools.nonce,
                log_types: log_types,
                from: from,
                to: to
            },
            function( response ) {

                if( ! $('#' + response_id).length )
                    $this.parent().append('<span id="' + response_id + '" style="display: inline-block; padding: 5px 0 0 8px;"></span>');

                // Update response color text
                if( response.success === false )
                    $('#' + response_id).css({color:'#a00'});
                else
                    $('#' + response_id).css({color:''});

                $('#' + response_id).html( ( response.data.message !== undefined ? response.data.message : response.data ) );

                // Enable the buttons
                $('#logs_clean_up_count, #logs_clean_up').prop('disabled', false);
            }
        ).fail(function() {

            $('#' + response_id).html('The server has returned an internal error.');

            // Enable the buttons
            $('#logs_clean_up_count, #logs_clean_up').prop('disabled', false);
        });

    });

    // ----------------------------------
    // Reset Data Tool
    // ----------------------------------

    var reset_data_dialog = $("#reset-data-dialog");

    reset_data_dialog.dialog({
        dialogClass   : 'wp-dialog',
        modal         : true,
        autoOpen      : false,
        closeOnEscape : true,
        draggable     : false,
        width         : 500,
        buttons       : [
            {
                text: "Yes, delete it permanently",
                class: "button-primary reset-data-button",
                click: function() {
                    $('.reset-data-button').prop('disabled', true);

                    if( ! $('#reset-data-response').length )
                        $('.reset-data-button').parent().parent().prepend('<span id="reset-data-response"></span>');

                    // Show the spinner
                    $('#reset-data-response').html('<span class="spinner is-active" style="float: none;"></span>');

                    var items = [];

                    $('.cmb2-id-data-to-reset input:checked').each(function() {
                        items.push($(this).val());
                    });

                    $.post(
                        ajaxurl,
                        {
                            action: 'gamipress_reset_data_tool',
                            nonce: gamipress_admin_tools.nonce,
                            items: items
                        },
                        function( response ) {

                            if( response.success === false )
                                $('#reset-data-response').css({color:'#a00'});
                            else
                                $('#reset-data-response').css({color:''});

                            $('#reset-data-response').html(response.data);

                            if( response.success === true ) {

                                setTimeout(function() {
                                    $('.cmb2-id-data-to-reset input:checked').each(function() {
                                        $(this).prop( 'checked', false );
                                    });

                                    reset_data_dialog.dialog( "close" );
                                }, 5000);
                            }

                            $('.reset-data-button').prop('disabled', false);
                        }
                    );
                }
            },
            {
                text: "Cancel",
                class: "cancel-reset-data-button",
                click: function() {
                    $( this ).dialog( "close" );
                }
            }

        ]
    });

    $("#reset_data").on('click', function(e) {
        e.preventDefault();

        $('#reset-data-warning').remove();

        var checked_options = $('.cmb2-id-data-to-reset input:checked');

        if( checked_options.length ) {

            var reminder_html = '';

            checked_options.each(function() {
                reminder_html += '<li>' + $(this).next().text() + '</li>'
            });

            // Add a reminder with data to be removed
            $('#reset-data-reminder').html('<ul>' + reminder_html + '</ul>');

            // Open our dialog
            reset_data_dialog.dialog('open');

            // Remove the initial jQuery UI Dialog auto focus
            $('.ui-dialog :button').blur();
        } else {
            $(this).parent().prepend('<p id="reset-data-warning" class="cmb2-metabox-description" style="color: #a00;">You need to choose at least one option.</p>');
        }
    });

    $('.cmb2-id-data-to-reset').on('change', 'input', function() {

        $('#reset-data-warning').remove();

        var checked_option = $(this).val();

        if( checked_option === 'achievement_types' ) {
            $('.cmb2-id-data-to-reset input[value="achievements"], .cmb2-id-data-to-reset input[value="steps"]').prop( 'checked', $(this).prop( 'checked' ) );
        } else if( checked_option === 'achievements' ) {
            $('.cmb2-id-data-to-reset input[value="steps"]').prop( 'checked', $(this).prop( 'checked' ) );
        } else if( checked_option === 'points_types' ) {
            $('.cmb2-id-data-to-reset input[value="points_awards"]').prop( 'checked', $(this).prop( 'checked' ) );
            $('.cmb2-id-data-to-reset input[value="points_deducts"]').prop( 'checked', $(this).prop( 'checked' ) );
        } else if( checked_option === 'rank_types' ) {
            $('.cmb2-id-data-to-reset input[value="ranks"], .cmb2-id-data-to-reset input[value="rank_requirements"]').prop( 'checked', $(this).prop( 'checked' ) );
        } else if( checked_option === 'ranks' ) {
            $('.cmb2-id-data-to-reset input[value="rank_requirements"]').prop( 'checked', $(this).prop( 'checked' ) );
        } else if( checked_option === 'earnings' ) {
            $('.cmb2-id-data-to-reset input[value="earned_points"], .cmb2-id-data-to-reset input[value="earned_achievements"], .cmb2-id-data-to-reset input[value="earned_ranks"]').prop( 'checked', $(this).prop( 'checked' ) );
        }

    });

    // ----------------------------------
    // Export Achievements, Points and Ranks Tool
    // ----------------------------------

    $('#export_achievements, #export_points, #export_ranks, #export_earnings').on('click', function(e) {
        e.preventDefault();

        var $this = $(this);
        var type;
        var error = '';

        switch( $this.attr('id') ) {
            case 'export_achievements':
                // Achievements export
                type = 'achievements';

                // Check achievement types
                if( ! $('input[name="export_achievements_achievement_types[]"]:checked').length ) {
                    error = 'You need to choose at least 1 achievement type to export.';
                }
                break;
            case 'export_points':
                // Points export
                type = 'points';

                // Check points types
                if( ! $('input[name="export_points_points_types[]"]:checked').length ) {
                    error = 'You need to choose at least 1 points type to export.';
                }
                break;
            case 'export_ranks':
                // Ranks export
                type = 'ranks';

                // Check rank types
                if( ! $('input[name="export_ranks_rank_types[]"]:checked').length ) {
                    error = 'You need to choose at least 1 rank type to export.';
                }
                break;
            case 'export_earnings':
                // Ranks export
                type = 'earnings';

                // Check rank types
                if( ! $('input[name="export_earnings_types[]"]:checked').length ) {
                    error = 'You need to choose at least 1 type to export.';
                }
                break;
        }

        // Remove error messages
        $('#export-' + type + '-warning').remove();

        // If there is any error, show it to the user
        if( error !== '' ) {
            $this.parent().prepend('<p id="export-' + type + '-warning" class="cmb2-metabox-description" style="color: #a00;">' + error + '</p>');
            return false;
        }

        // Reset the data to export
        to_export = [];

        gamipress_run_export_tool( type );

    });

    var to_export = [];

    // Function to handle the export process
    function gamipress_run_export_tool( type, loop ) {

        var button_element = $('#export_' + type );
        var response_element = $('#export-' + type + '-response');
        var data;

        if( loop === undefined )
            loop = 0;

        // Disable the export button
        button_element.prop('disabled', true);

        // Check if response element exists
        if( ! response_element.length ) {
            button_element.parent().append('<span id="export-' + type + '-response" style="display: inline-block; padding: 5px 0 0 8px;"></span>');

            response_element = $('#export-' + type + '-response');
        }

        // Show the spinner
        if( loop === 0 )
            response_element.html('<span class="spinner is-active" style="float: none; margin: 0;"></span>');

        // Setup request data per type
        switch( type ) {
            case 'achievements':
                // Achievements data
                var achievement_types = [];

                $('input[name="export_achievements_achievement_types[]"]:checked').each(function() {
                    achievement_types.push( $(this).val() );
                });

                data = {
                    action: 'gamipress_import_export_achievements_tool_export',
                    nonce: gamipress_admin_tools.nonce,
                    achievement_types: achievement_types,
                    user_field: $('#export_achievements_user_field').val(),
                    achievement_field: $('#export_achievements_achievement_field').val(),
                    loop: loop
                };
                break;
            case 'points':
                // Points data
                var points_types = [];

                $('input[name="export_points_points_types[]"]:checked').each(function() {
                    points_types.push( $(this).val() );
                });

                data = {
                    action: 'gamipress_import_export_points_tool_export',
                    nonce: gamipress_admin_tools.nonce,
                    points_types: points_types,
                    user_field: $('#export_points_user_field').val(),
                    loop: loop
                };
                break;
            case 'ranks':
                // Ranks data
                var rank_types = [];

                $('input[name="export_ranks_rank_types[]"]:checked').each(function() {
                    rank_types.push( $(this).val() );
                });

                data = {
                    action: 'gamipress_import_export_ranks_tool_export',
                    nonce: gamipress_admin_tools.nonce,
                    rank_types: rank_types,
                    user_field: $('#export_ranks_user_field').val(),
                    rank_field: $('#export_ranks_rank_field').val(),
                    loop: loop
                };
                break;
            case 'earnings':
                // Ranks data
                var types = [];

                $('input[name="export_earnings_types[]"]:checked').each(function() {
                    types.push( $(this).val() );
                });

                data = {
                    action: 'gamipress_import_export_earnings_tool_export',
                    nonce: gamipress_admin_tools.nonce,
                    types: types,
                    user_field: $('#export_earnings_user_field').val(),
                    post_field: $('#export_earnings_post_field').val(),
                    from: $('#export_earnings_from').val(),
                    to: $('#export_earnings_to').val(),
                    loop: loop
                };
                break;
        }

        $.post(
            ajaxurl,
            data,
            function( response ) {

                if( response.data.items !== undefined ) {
                    // Concat received items
                    to_export = to_export.concat( response.data.items );
                }

                // Run again utility
                if( response.data.run_again !== undefined && response.data.run_again && response.success === true ) {

                    response_element.html( ( response.data.message !== undefined ? response.data.message : response.data ) );

                    loop++;

                    // Run again passing the next loop index
                    gamipress_run_export_tool( type, loop );

                    return;
                }

                if( response.success === false )
                    response_element.css({color:'#a00'});
                else
                    response_element.css({color:''});

                response_element.html( ( response.data.message !== undefined ? response.data.message : response.data ) );

                // Enable the export button
                button_element.prop('disabled', false);

                if( to_export.length ) {
                    // Download the CSV with the data
                    gamipress_download_csv( to_export, 'gamipress-user-' + type + '-export' );
                }
            }
        ).fail(function() {

            response_element.html('The server has returned an internal error.');

            // Enable the export button
            button_element.prop('disabled', false);
        });

    }

    // ----------------------------------
    // Import Achievements, Points and Ranks Tool
    // ----------------------------------

    $('#import_achievements, #import_points, #import_ranks').on('click', function(e) {
        e.preventDefault();

        var $this = $(this);
        var type;

        switch( $this.attr('id') ) {
            case 'import_achievements':
                // Achievements import
                type = 'achievements';
                break;
            case 'import_points':
                // Points import
                type = 'points';
                break;
            case 'import_ranks':
                // Ranks import
                type = 'ranks';
                break;
        }

        // Remove error messages
        $('#import-' + type + '-warning').remove();

        // Remove old responses
        $('#import-' + type + '-response').remove();

        // Check if CSV file has been chosen
        if( $('#import_' + type + '_file')[0].files[0] === undefined ) {
            $this.parent().prepend('<p id="import-' + type + '-warning" class="cmb2-metabox-description" style="color: #a00;">You need to choose a CSV file to import.</p>');
            return false;
        }

        // Setup the form data to send
        var form_data = new FormData();
        form_data.append( 'action', 'gamipress_import_export_' + type + '_tool_import' );
        form_data.append( 'nonce', gamipress_admin_tools.nonce );
        form_data.append( 'file', $('#import_' + type + '_file')[0].files[0] );

        // Disable the button
        $this.prop('disabled', true);

        if( ! $('#import-' + type + '-response').length )
            $this.parent().prepend('<p id="import-' + type + '-response" class="cmb2-metabox-description"></p>');

        // Show the spinner
        $('#import-' + type + '-response').html('<span class="spinner is-active" style="float: none;"></span>');

        $.ajax({
            url: ajaxurl,
            method: 'post',
            cache: false,
            contentType: false,
            processData: false,
            data: form_data,
            success: function(response) {

                // Set a red color to the response to let user known that something is going wrong
                if( response.success === false )
                    $('#import-' + type + '-response').css({color:'#a00'});
                else
                    $('#import-' + type + '-response').css({color:''});

                // Update the response content
                $('#import-' + type + '-response').html( response.data );

                // Re-enable the button
                $this.prop('disabled', false);

            }
        });

    });

    // ----------------------------------
    // Download Achievements, Points and Ranks CSV Template
    // ----------------------------------

    $('#download_achievements_csv_template, #download_points_csv_template, #download_ranks_csv_template').on('click', function(e) {
        e.preventDefault();

        var type, sample_data;

        switch( $(this).attr('id') ) {
            case 'download_achievements_csv_template':
                // Achievements sample data
                type = 'achievements';
                sample_data = [
                    {
                        user: 'User (ID, username or email)',
                        achievements: 'Achievements (Comma-separated list of IDs, titles and/or slugs)',
                        notes: 'Notes (This column won\'t be processed by the tool)',
                    },
                    {
                        user: gamipress_admin_tools.user_id,
                        achievements: '1,2,3',
                        notes: 'Awarding by user ID and passing the achievements IDs',
                    },
                    {
                        user: gamipress_admin_tools.user_name,
                        achievements: 'Test Badge,Custom Quest,Super Achievement',
                        notes: 'Awarding by username and passing the achievements titles',
                    },
                    {
                        user: gamipress_admin_tools.user_email,
                        achievements: 'test-badge,custom-quest,super-achievement',
                        notes: 'Awarding by email and passing the achievements slugs',
                    },
                    {
                        user: gamipress_admin_tools.user_id,
                        achievements: '-1,-Test Badge,-test-badge',
                        notes: 'Adding a negative sign will revoke the achievements',
                    },
                ];
                break;
            case 'download_points_csv_template':
                // Points sample data
                type = 'points';
                sample_data = [
                    {
                        user: 'User (ID, username or email)',
                        points: 'Points',
                        points_type: 'Points Type (slug)',
                        log: 'Log Description (Optional)',
                        notes: 'Notes (This column won\'t be processed by the tool)',
                    },
                    {
                        user: gamipress_admin_tools.user_id,
                        points: '100',
                        points_type: 'credits',
                        log: '100 credits awarded through the user\'s ID',
                        notes: 'Awarding points by user ID',
                    },
                    {
                        user: gamipress_admin_tools.user_name,
                        points: '1000',
                        points_type: 'coins',
                        log: '1,000 coins awarded through the user\'s username',
                        notes: 'Awarding points by username',
                    },
                    {
                        user: gamipress_admin_tools.user_email,
                        points: '50',
                        points_type: 'gems',
                        log: '50 gems awarded through the user\'s email',
                        notes: 'Awarding points by email',
                    },
                    {
                        user: gamipress_admin_tools.user_email,
                        points: '-50',
                        points_type: 'gems',
                        log: '50 gems deducted through the user\'s email',
                        notes: 'Adding a negative sign will deduct the points',
                    },
                ];
                break;
            case 'download_ranks_csv_template':
                // Ranks sample data
                type = 'ranks';
                sample_data = [
                    {
                        user: 'User (ID, username or email)',
                        rank: 'Rank (ID, title or slug of rank to assign to the user)',
                        notes: 'Notes (This column won\'t be processed by the tool)',
                    },
                    {
                        user: gamipress_admin_tools.user_id,
                        rank: '1',
                        notes: 'Setting rank by user ID and passing the rank ID',
                    },
                    {
                        user: gamipress_admin_tools.user_name,
                        rank: 'Test Rank',
                        notes: 'Setting rank by username and passing the rank title',
                    },
                    {
                        user: gamipress_admin_tools.user_email,
                        rank: 'test-rank',
                        notes: 'Setting rank by email and passing the rank slug',
                    },
                    {
                        user: gamipress_admin_tools.user_id,
                        rank: '-1',
                        notes: 'Adding a negative sign will revoke the rank to the user and will try to assign the previous one (following the priority order)',
                    },
                ];
                break;
        }

        if( Array.isArray( sample_data ) ) {
            gamipress_download_csv( sample_data, 'gamipress-' + type + '-csv-template' );
        }

    });

    // ----------------------------------
    // Export Setup Tool
    // ----------------------------------

    $('.gamipress-all-types-multicheck').on('change', 'input', function() {

        var box = $(this).closest('.cmb2-metabox').attr('id');

        if( box === 'cmb2-metabox-import-export-setup' ) {
            $('#export-setup-warning').remove();
        } else if( box === 'cmb2-metabox-import-export-earnings' ) {
            $('#export-earnings-warning').remove();
        }


        var checked_option = $(this).val();
        var type = '';

        if( checked_option === 'all-points-types' ) {

            $('.gamipress-all-types-multicheck input[value$="-points-type"]').prop( 'checked', $(this).prop( 'checked' ) );
            $('.gamipress-all-types-multicheck input[value$="-points-awards"]').prop( 'checked', $(this).prop( 'checked' ) );
            $('.gamipress-all-types-multicheck input[value$="-points-deducts"]').prop( 'checked', $(this).prop( 'checked' ) );

        } else if( checked_option.endsWith( '-points-type' ) ) {

            type = checked_option.replace( '-points-type', '' );
            $('.gamipress-all-types-multicheck input[value="' + type + '-points-awards"]').prop( 'checked', $(this).prop( 'checked' ) );
            $('.gamipress-all-types-multicheck input[value="' + type + '-points-deducts"]').prop( 'checked', $(this).prop( 'checked' ) );

        } else if( checked_option === 'all-achievement-types' ) {

            $('.gamipress-all-types-multicheck input[value$="-achievement-type"]').prop( 'checked', $(this).prop( 'checked' ) );
            $('.gamipress-all-types-multicheck input[value$="-achievements"]').prop( 'checked', $(this).prop( 'checked' ) );
            $('.gamipress-all-types-multicheck input[value$="-steps"]').prop( 'checked', $(this).prop( 'checked' ) );

        } else if( checked_option.endsWith( '-achievement-type' ) ) {

            type = checked_option.replace( '-achievement-type', '' );
            $('.gamipress-all-types-multicheck input[value="' + type + '-achievements"]').prop( 'checked', $(this).prop( 'checked' ) );
            $('.gamipress-all-types-multicheck input[value="' + type + '-steps"]').prop( 'checked', $(this).prop( 'checked' ) );

        } else if( checked_option.endsWith( '-achievements' ) ) {

            type = checked_option.replace( '-achievements', '' );
            $('.gamipress-all-types-multicheck input[value="' + type + '-steps"]').prop( 'checked', $(this).prop( 'checked' ) );

        } else if( checked_option === 'all-rank-types' ) {

            $('.gamipress-all-types-multicheck input[value$="-rank-type"]').prop( 'checked', $(this).prop( 'checked' ) );
            $('.gamipress-all-types-multicheck input[value$="-ranks"]').prop( 'checked', $(this).prop( 'checked' ) );
            $('.gamipress-all-types-multicheck input[value$="-rank-requirements"]').prop( 'checked', $(this).prop( 'checked' ) );

        } else if( checked_option.endsWith( '-rank-type' ) ) {

            type = checked_option.replace( '-rank-type', '' );
            $('.gamipress-all-types-multicheck input[value="' + type + '-ranks"]').prop( 'checked', $(this).prop( 'checked' ) );
            $('.gamipress-all-types-multicheck input[value="' + type + '-rank-requirements"]').prop( 'checked', $(this).prop( 'checked' ) );

        } else if( checked_option.endsWith( '-ranks' ) ) {

            type = checked_option.replace( '-ranks', '' );
            $('.gamipress-all-types-multicheck input[value="' + type + '-rank-requirements"]').prop( 'checked', $(this).prop( 'checked' ) );
        }

    });

    $("#export_setup").on('click', function(e) {
        e.preventDefault();

        $('#export-setup-warning').remove();

        var checked_options = $('.cmb2-id-export-setup-options input:checked');

        if( checked_options.length === 0 ) {
            $(this).parent().prepend('<p id="export-setup-warning" class="cmb2-metabox-description" style="color: #a00;">You need to choose at least one option to export.</p>');
            return;
        }

        $('.export-setup-button').prop('disabled', true);

        if( ! $('#export-setup-response').length )
            $('.export-setup-button').parent().parent().prepend('<span id="export-setup-response"></span>');

        // Show the spinner
        $('#export-setup-response').html('<span class="spinner is-active" style="float: none;"></span>');

        var items = [];

        $('.cmb2-id-export-setup-options input:checked').each(function() {
            items.push($(this).val());
        });

        $.post(
            ajaxurl,
            {
                action: 'gamipress_export_setup_tool',
                nonce: gamipress_admin_tools.nonce,
                items: items
            },
            function( response ) {

                if( response.success === false )
                    $('#export-setup-response').css({color:'#a00'});
                else
                    $('#export-setup-response').css({color:''});

                $('#export-setup-response').html( ( response.data.message !== undefined ? response.data.message : response.data ) );

                if( response.success === true ) {
                    gamipress_download_file( response.data.setup, 'setup-export', 'txt', 'text/plain' );
                }

                $('.export-setup-button').prop('disabled', false);
            }
        );

    });

    // ----------------------------------
    // Import Setup Tool
    // ----------------------------------

    $('#import_setup').on('click', function(e) {
        e.preventDefault();

        $('#import-setup-warning').remove();

        if( $('#import_setup_file')[0].files[0] === undefined ) {
            $(this).parent().prepend('<p id="import-setup-warning" class="cmb2-metabox-description" style="color: #a00;">You need to choose a configuration file to import.</p>');
            return false;
        }

        var $this = $(this);
        var form_data = new FormData();
        form_data.append( 'action', 'gamipress_import_setup_tool' );
        form_data.append( 'nonce', gamipress_admin_tools.nonce );
        form_data.append( 'file', $('#import_setup_file')[0].files[0] );

        // Disable the button
        $this.prop('disabled', true);

        if( ! $('#import-setup-response').length )
            $this.parent().append('<span id="import-setup-response"></span>');

        // Show the spinner
        $('#import-setup-response').html('<span class="spinner is-active" style="float: none;"></span>');

        $.ajax({
            url: ajaxurl,
            method: 'post',
            cache: false,
            contentType: false,
            processData: false,
            data: form_data,
            success: function(response) {

                if( response.success === false )
                    $('#import-setup-response').css({color:'#a00'});
                else
                    $('#import-setup-response').css({color:''});

                $('#import-setup-response').html(response.data);

                $this.prop('disabled', false);

            }
        });
    });

    // ----------------------------------
    // Import Settings Tool
    // ----------------------------------

    $('#import_settings').on('click', function(e) {
        e.preventDefault();

        $('#import-settings-warning').remove();

        if( $('#import_settings_file')[0].files[0] === undefined ) {
            $(this).parent().prepend('<p id="import-settings-warning" class="cmb2-metabox-description" style="color: #a00;">You need to choose a configuration file to import.</p>');
            return false;
        }

        var $this = $(this);
        var form_data = new FormData();
        form_data.append( 'action', 'gamipress_import_settings_tool' );
        form_data.append( 'nonce', gamipress_admin_tools.nonce );
        form_data.append( 'file', $('#import_settings_file')[0].files[0] );

        // Disable the button
        $this.prop('disabled', true);

        if( ! $('#import-settings-response').length )
            $this.parent().append('<span id="import-settings-response"></span>');

        // Show the spinner
        $('#import-settings-response').html('<span class="spinner is-active" style="float: none;"></span>');

        $.ajax({
            url: ajaxurl,
            method: 'post',
            cache: false,
            contentType: false,
            processData: false,
            data: form_data,
            success: function(response) {

                if( response.success === false )
                    $('#import-settings-response').css({color:'#a00'});
                else
                    $('#import-settings-response').css({color:''});

                $('#import-settings-response').html(response.data);

                $this.prop('disabled', false);

            }
        });

    });

})( jQuery );