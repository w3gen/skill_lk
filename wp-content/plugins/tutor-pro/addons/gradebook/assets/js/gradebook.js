jQuery(document).ready(function($){
    'use strict';

    // Backend Filter Output
    $(document).on('click', '.tutor-gradebook-filter', function (e) {
        var srcRefirect = '',
            location = window.location.href,
            dataVal = $('.tutor-gradebook-filter-select option').filter(":selected").val();
        if(location.includes('courseid')) {
            let href = new URL(location);
            href.searchParams.set('courseid', dataVal);
            srcRefirect = href.toString();
        } else {
            srcRefirect = location + '&courseid=' + dataVal;
        }
        window.location.href = srcRefirect;
    });


    /*
    $(document).on('click', '.tutor-rating-delete-link', function (e) {
        e.preventDefault();

        var $that= $(this);
        $.ajax({
            url : ajaxurl,
            type : 'POST',
            data : {review_id : $that.attr('data-rating-id'), action : 'tutor_review_delete' },
            beforeSend: function () {
                $that.addClass('updating-message');
            },
            success: function (data) {
                if (data.success){
                    $that.closest('tr').remove();
                }
            },
            complete: function () {
                $that.removeClass('updating-message');
            }
        });
    });
    */



});