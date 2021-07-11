jQuery(document).ready(function($){
    'use strict';


    $(document).on('click', '.certificate-template', function(){
        $('.certificate-template').removeClass('selected-template');
        $(this).addClass('selected-template');
    });


    $(document).on('click', '.install-tutor-button', function(e){
        e.preventDefault();

        var $btn = $(this);

        $.ajax({
            type: 'POST',
            url: ajaxurl,
            data: {install_plugin: 'tutor', action: 'install_tutor_plugin'},
            beforeSend: function(){
                $btn.addClass('updating-message');
            },
            success: function (data) {
                $('.install-tutor-button').remove();
                $('#tutor_install_msg').html(data);
            },
            complete: function () {
                $btn.removeClass('updating-message');
            }
        });
    });

    /**
     * Import Sample Grade Data
     *
     * @since v.1.4.2
     */
    $(document).on('click', '#import-gradebook-sample-data', function(e){
        e.preventDefault();

        var $btn = $(this);

        $.ajax({
            type: 'POST',
            url: ajaxurl,
            data: {action: 'import_gradebook_sample_data'},
            beforeSend: function(){
                $btn.addClass('updating-icon');
            },
            success: function (data) {
                if (data.success){
                    location.reload(true);
                }
            },
            complete: function () {
                $btn.removeClass('updating-icon');
            }
        });
    });

    /**
     * Hide cron frequency on wp cron disabling
     * @since v.1.8.7
     */
    $('[name="tutor_option[tutor_email_disable_wpcron]"]').change(function(){
        $('[name="tutor_option[tutor_email_cron_frequency]"]').closest('.tutor-option-field-row')[!$(this).prop('checked') ? 'show' : 'hide']();
    }).trigger('change');
});
