(function ($) {
    'use strict';
    $(document).ready(function () {

        $('.tutor-zoom-accordion-panel').on('click', function (e) {
            e.preventDefault();
            $(this).find('i').toggleClass("tutor-icon-angle-down tutor-icon-angle-up");
            $(this).parent().find('.tutor-zoom-accordion-body').slideToggle();
        });

        $('#tutor-zoom-settings').on('change', '.btn-switch, .single-radio', function (e) {
            $(this).closest('form').submit();
        });

        $('#tutor-zoom-settings').submit(function (e) {
            
            e.preventDefault();
            var $form = $(this);
            var data = $form.serializeObject();

            $.ajax({
                url: ajaxurl,
                type: 'POST',
                data: data,
                beforeSend: function () {
                    $form.find('#save-changes').addClass('tutor-updating-message');
                    $form.parent().append('<span id="saving-msg">Saving...</span>');
                },
                success: function (data) {
                    if (data.success) {
                    }
                },
                complete: function () {
                    $form.find('#save-changes').removeClass('tutor-updating-message');
                    $form.parent().find('#saving-msg').remove();
                }
            });
        });

        $('#check-zoom-api-connection').click(function (e) {
            e.preventDefault();
            var $that = $(this);
            $.ajax({
                url: ajaxurl,
                type: 'POST',
                data: {action: 'tutor_check_api_connection'},
                beforeSend: function () {
                    $that.addClass('tutor-updating-message');
                },
                success: function (result) {
                    alert(result);
                },
                complete: function () {
                    $that.removeClass('tutor-updating-message');
                }
            });
        });

        function urlPrams(type, val){
            var url = new URL(window.location.href);
            var search_params = url.searchParams;
            search_params.set(type, val);
            url.search = search_params.toString();
            search_params.set('paged', 1);
            url.search = search_params.toString();
            return url.toString();
        }

        $(document).on('click', '.tutor-zoom-search-action', function(e){
            e.preventDefault();
            window.location = urlPrams( 'search', $('.tutor-report-search').val() );
        });
        $('.tutor-zoom-course').on('change', function(e){
            window.location = urlPrams( 'course', $(this).val() );
        });
        $('.tutor-zoom-date').on('change', function(e){
            window.location = urlPrams( 'date', $(this).val() ) ;
        });
        $('.tutor-zoom-filter').on('change', function(e){
            window.location = urlPrams( 'filter', $(this).val() ) ;
        });
        
    });
})(jQuery);