window.jQuery(document).ready(function($) {
    $('[data-assignment_action="delete"]').click(function(e) {

        e.preventDefault();

        var $that = $(this);
        var warning = $that.data('warning_message');
        var assignment_id = $that.data('assignment_id');
        var row = $that.closest('tr');

        if(!window.confirm(warning)) {
            // Maybe accidental click
            return;
        }

        $that.addClass('tutor-updating-message');

        $.ajax({
            url: window.ajaxurl,
            data: {action: 'delete_tutor_course_assignment_submission', assignment_id: assignment_id},
            success: function(data) {
                if(data.success) {
                    tutor_toast($that.data('toast_success'), $that.data('toast_success_message'), 'success');

                    row.fadeOut('fast', function() {
                        $(this).remove();
                    });
                } else {
                    tutor_toast($that.data('toast_error'), $that.data('toast_error_message'), 'error');
                }
            },
            error: function() {
                tutor_toast($that.data('toast_error'), $that.data('toast_error_message'), 'error');
            },
            complete: function() {
                ($that && $that.length) ? $that.removeClass('tutor-updating-message') : 0;
            }
        });
    });

   /**
    * @since 1.8.0
    * assignment sorting
    * date picker
    */

   if(jQuery.datepicker){
       $("#tutor_assignment_calender").datepicker({"dateFormat" : 'yy-mm-dd'});        
          
    }

    //sorting
    function urlPrams(type, val){
        var url = new URL(window.location.href);
        var search_params = url.searchParams;
        search_params.set(type, val);
        
        url.search = search_params.toString();
        
        search_params.set('paged', 1);
        url.search = search_params.toString();

        return url.toString();
    }
    $('.tutor-assignment-course-sorting').on('change', function(e){
        window.location = urlPrams( 'course-id', $(this).val() );
    });
    $('.tutor-assignment-order-sorting').on('change', function(e){
        window.location = urlPrams( 'order', $(this).val() );
    });
    $('.tutor-assignment-date-sorting').on('change', function(e){
        window.location = urlPrams( 'date', $(this).val() );
    });
    $('.tutor-assignment-search-sorting').on('click', function(e){
        window.location = urlPrams( 'search', $(".tutor-assignment-search-field").val() );
    });
    //assignment end    
});