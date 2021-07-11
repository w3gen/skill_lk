jQuery(document).ready(function($){
    'use strict';

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
    
    $(document).on('click', '.tutor-quiz-attempt-delete-btn', function (e) {
        e.preventDefault();
        var $that= $(this);
        $.ajax({
            url : ajaxurl,
            type : 'POST',
            data : {attempt_id : $that.attr('data-attempt-id'), action : 'treport_quiz_atttempt_delete' },
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

    /**
     * Datepicker initiate
     */
    if (jQuery.datepicker){
        $( ".tutor_report_datepicker" ).datepicker({"dateFormat" : 'yy-mm-dd'});
    }

    function urlPrams(type, val){
        var url = new URL(window.location.href);
        var search_params = url.searchParams;
        search_params.set(type, val);
        
        url.search = search_params.toString();
        
        search_params.set('paged', 1);
        url.search = search_params.toString();

        return url.toString();
    }

    $('.tutor-report-category').on('change', function(e){
        window.location = urlPrams( 'cat', $(this).val() );
    });
    $('.tutor-report-sort').on('change', function(e){
        window.location = urlPrams( 'order', $(this).val() ) ;
    });
    $('.tutor-report-date').on('change', function(e){
        window.location = urlPrams( 'date', $(this).val() ) ;
    });
    $(document).on('click', '.tutor-report-search-btn', function (e) {
        window.location = urlPrams( 'search', $('.tutor-report-search').val() );
    });

    $(document).on('click', '.tutor-report-search-action', function(e){
        e.preventDefault();
        window.location = urlPrams( 'search', $('.tutor-report-search').val() );
    });

    
    $(document).on('click', '.details-link', function(e){
        e.preventDefault();
        if($(this).hasClass('active')){
            $(this).removeClass('active');
        } else {
            $(this).addClass('active');
        }
        let infoRow = $('#table-toggle-'+$(this).data('count'));
        if(infoRow.hasClass('open')){
            infoRow.removeClass('open');
        } else {
            infoRow.addClass('open');
        }

    });

});
