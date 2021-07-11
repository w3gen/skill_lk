(function($) {

    var gamipress_widget_select2_users = {
        ajax: {
            url: ajaxurl,
            dataType: 'json',
            delay: 250,
            type: 'POST',
            data: function( params ) {
                return {
                    q: params.term,
                    page: params.page || 1,
                    action: 'gamipress_get_users',
                    nonce: gamipress_admin_widgets.nonce,
                };
            },
            processResults: gamipress_select2_users_process_results
        },
        escapeMarkup: function ( markup ) { return markup; }, // Let our custom formatter work
        templateResult: gamipress_select2_users_template_result,
        theme: 'default gamipress-select2',
        placeholder: gamipress_admin_widgets.user_placeholder,
        allowClear: true,
        multiple: false
    };

    // User ajax
    $( '#widgets-right select[id^="widget-gamipress"][id$="[user_id]"]:not(.select2-hidden-accessible)' ).gamipress_select2( gamipress_widget_select2_users );

    // Current user field
    $('body').on('change', 'input[id^="widget-gamipress"][id$="[current_user]"]', function() {
        var target = $(this).closest('.cmb-row').next(); // User ID field

        if( $(this).prop('checked') ) {
            // Hide the target
            target.slideUp().addClass('cmb2-tab-ignore');
        } else {
            if( target.closest('.cmb-tabs-wrap').length ) {
                // Just show if item tab is active
                if( target.hasClass('cmb-tab-active-item') ) {
                    target.slideDown();
                }
            } else {
                target.slideDown();
            }

            target.removeClass('cmb2-tab-ignore');
        }
    });

    $('input[id^="widget-gamipress"][id$="[current_user]"]').trigger('change');

    // Earners user field
    $('body').on('change', 'input[id^="widget-gamipress"][id$="[earners]"]', function() {
        var target = $(this).closest('.cmb-row').next(); // Earners limit field

        if( ! $(this).prop('checked') ) {
            // Hide the target
            target.slideUp().addClass('cmb2-tab-ignore');
        } else {
            if( target.closest('.cmb-tabs-wrap').length ) {
                // Just show if item tab is active
                if( target.hasClass('cmb-tab-active-item') ) {
                    target.slideDown();
                }
            } else {
                target.slideDown();
            }

            target.removeClass('cmb2-tab-ignore');
        }
    });

    $('input[id^="widget-gamipress"][id$="[earners]"]').trigger('change');

    // Period field
    $('body').on('change', 'select[id^="widget-gamipress_points"][id$="[period]"], ' +
        'select[id^="widget-gamipress_user_points"][id$="[period]"], ' +
        'select[id^="widget-gamipress_site_points"][id$="[period]"]', function() {
        // Get the period start and end fields
        var target = $(this).closest('.cmb2-wrap').find(
            '.cmb-row[class*="period-start"], '
            + '.cmb-row[class*="period-end"]'
        );

        if( $(this).val() !== 'custom' ) {
            // Hide the target
            target.slideUp().addClass('cmb2-tab-ignore');
        } else {
            // Show the target
            target.slideDown().removeClass('cmb2-tab-ignore');
        }
    });

    $('select[id^="widget-gamipress_points"][id$="[period]"]').trigger('change');
    $('select[id^="widget-gamipress_user_points"][id$="[period]"]').trigger('change');
    $('select[id^="widget-gamipress_site_points"][id$="[period]"]').trigger('change');

    // Inline field
    $('body').on('change', 'input[id^="widget-gamipress_points"][id$="[inline]"], ' +
        'input[id^="widget-gamipress_user_points"][id$="[inline]"], ' +
        'input[id^="widget-gamipress_site_points"][id$="[inline]"]', function() {
        // Get the columns and layout fields
        var target = $(this).closest('.cmb2-wrap').find(
            '.cmb-row[class*="columns"], '
            + '.cmb-row[class*="layout"]'
        );

        if( $(this).prop('checked') ) {
            target.slideUp().addClass('cmb2-tab-ignore');
        } else {
            target.slideDown().removeClass('cmb2-tab-ignore');
        }
    });

    // User earnings
    $('body').on('change', 'input[id^="widget-gamipress_earnings"][id$="[points]"], '
        + 'input[id^="widget-gamipress_earnings"][id$="[achievements]"], '
        + 'input[id^="widget-gamipress_earnings"][id$="[ranks]"]', function() {

        var id_parts = $(this).attr('id').split('[');
        var id = id_parts[id_parts.length - 1].replace(']', '');
        var n = $(this).closest('form').find('input[name="widget_number"]').val();
        var target = undefined;

        if( id === 'points' ) {
            target = $('.cmb2-id-widget-gamipress-earnings-widget' + n + 'points-types, .cmb2-id-widget-gamipress-earnings-widget' + n + 'awards, .cmb2-id-widget-gamipress-earnings-widget' + n + 'deducts');
        } else if( id === 'achievements' ) {
            target = $('.cmb2-id-widget-gamipress-earnings-widget' + n + 'achievement-types, .cmb2-id-widget-gamipress-earnings-widget' + n + 'steps');
        } else if( id === 'ranks' ) {
            target = $('.cmb2-id-widget-gamipress-earnings-widget' + n + 'rank-types, .cmb2-id-widget-gamipress-earnings-widget' + n + 'rank-requirements');
        }

        if( $(this).prop('checked') ) {
            // Just show if current tab active is ours
            if( $(this).closest('.cmb-tabs-wrap').find('.cmb-tab.active[id$="[' + id + ']"]').length ) {
                target.slideDown();
            }

            target.removeClass('cmb2-tab-ignore');
        } else {
            target.slideUp().addClass('cmb2-tab-ignore');
        }
    });

    $('input[id^="widget-gamipress_earnings"][id$="[points]"], '
        + 'input[id^="widget-gamipress_earnings"][id$="[achievements]"], '
        + 'input[id^="widget-gamipress_earnings"][id$="[ranks]"]').trigger('change');

    // Initialize on widgets area
    $(document).on('widget-updated widget-added', function(e, widget) {

        // User ajax
        widget.find( 'select[id^="widget-gamipress"][id$="[user_id]"]:not(.select2-hidden-accessible)' ).gamipress_select2( gamipress_widget_select2_users );

        // Current user field
        var current_user = widget.find( 'input[id^="widget-gamipress"][id$="[current_user]"]');
        var current_user_target = current_user.closest('.cmb-row').next(); // User ID field

        if( current_user.prop('checked') ) {
            current_user_target.hide().addClass('cmb2-tab-ignore');
        } else {
            if( current_user_target.closest('.cmb-tabs-wrap').length ) {
                // Just show if item tab is active
                if( current_user_target.hasClass('cmb-tab-active-item') ) {
                    current_user_target.show();
                }
            } else {
                current_user_target.show();
            }

            current_user_target.removeClass('cmb2-tab-ignore');
        }

        // Earners field
        var earners = widget.find( 'input[id^="widget-gamipress"][id$="[earners]"]');
        var earners_target = earners.closest('.cmb-row').next(); // Earners limit field

        if( ! earners.prop('checked') ) {
            earners_target.hide().addClass('cmb2-tab-ignore');
        } else {
            if( earners_target.closest('.cmb-tabs-wrap').length ) {
                // Just show if item tab is active
                if( earners_target.hasClass('cmb-tab-active-item') ) {
                    earners_target.show();
                }
            } else {
                earners_target.show();
            }

            earners_target.removeClass('cmb2-tab-ignore');
        }

        // Period field
        var period = widget.find( 'input[id^="widget-gamipress_points"][id$="[period]"], ' +
            'input[id^="widget-gamipress_user_points"][id$="[period]"], ' +
            'input[id^="widget-gamipress_site_points"][id$="[period]"]');

        // Get the period start and end fields
        var period_target = period.closest('.cmb2-wrap').find(
            '.cmb-row[class*="period-start"], '
            + '.cmb-row[class*="period-end"]'
        );

        if( period.val() !== 'custom' ) {
            period_target.hide().addClass('cmb2-tab-ignore');
        } else {
            period_target.show().removeClass('cmb2-tab-ignore');
        }

        // Inline field
        var inline = widget.find( 'input[id^="widget-gamipress_points"][id$="[inline]"], ' +
            'input[id^="widget-gamipress_user_points"][id$="[inline]"], ' +
            'input[id^="widget-gamipress_site_points"][id$="[inline]"]');

        // Get the columns and layout fields
        var inline_target = inline.closest('.cmb2-wrap').find(
            '.cmb-row[class*="columns"], '
            + '.cmb-row[class*="layout"]'
        );

        if( inline.prop('checked') ) {
            inline_target.hide().addClass('cmb2-tab-ignore');
        } else {
            inline_target.show().removeClass('cmb2-tab-ignore');
        }

        // User earnings
        widget.find('change', 'input[id^="widget-gamipress_earnings"][id$="[points]"], '
            + 'input[id^="widget-gamipress_earnings"][id$="[achievements]"], '
            + 'input[id^="widget-gamipress_earnings"][id$="[ranks]"]').each(function() {

            var id_parts = $(this).attr('id').split('[');
            var id = id_parts[id_parts.length - 1].replace(']', '');
            var n = $(this).closest('form').find('input[name="widget_number"]').val();
            var target = undefined;

            if( id === 'points' ) {
                target = $('.cmb2-id-widget-gamipress-earnings-widget' + n + 'points-types, .cmb2-id-widget-gamipress-earnings-widget' + n + 'awards, .cmb2-id-widget-gamipress-earnings-widget' + n + 'deducts');
            } else if( id === 'achievements' ) {
                target = $('.cmb2-id-widget-gamipress-earnings-widget' + n + 'achievement-types, .cmb2-id-widget-gamipress-earnings-widget' + n + 'steps');
            } else if( id === 'ranks' ) {
                target = $('.cmb2-id-widget-gamipress-earnings-widget' + n + 'rank-types, .cmb2-id-widget-gamipress-earnings-widget' + n + 'rank-requirements');
            }

            if( $(this).prop('checked') ) {
                // Just show if current tab active is ours
                if( $(this).closest('.cmb-tabs-wrap').find('.cmb-tab.active[id$="[' + id + ']"]').length ) {
                    target.slideDown();
                }

                target.removeClass('cmb2-tab-ignore');
            } else {
                target.slideUp().addClass('cmb2-tab-ignore');
            }
        });
    });
})(jQuery);