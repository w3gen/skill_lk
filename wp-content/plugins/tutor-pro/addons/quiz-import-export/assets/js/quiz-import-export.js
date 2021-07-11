jQuery(document).ready(function($){
    'use strict';

    /**
     * Quiz CSV export action
     *
     * @since 
     */
    $(document).on( 'click', '.btn-csv-download',  function( event ){
        $.ajax({
            url: ajaxurl,
            type: 'POST',
            data: {quiz_id : $(this).data('id'), 'action': 'quiz_export_data'},
            beforeSend: function () {
                // $that.addClass('updating-icon');
            },
            success: function (arr) {
                if (arr.success) {
                    let csvContent = "data:text/csv;charset=utf-8,";
                    arr.data.output_quiz_data.forEach(function(rowArray) {
                        let row = rowArray.join(",");
                        csvContent += row + "\r\n";
                    });
                    const encodedUri = encodeURI(csvContent);
                    let link = document.createElement("a");
                    link.setAttribute("href", encodedUri);
                    link.setAttribute("download", "tutor-quiz-"+arr.data.title+".csv");
                    document.body.appendChild(link);
                    link.click();
                }
            },
            complete: function () {
                // $that.removeClass('updating-icon');
            }
        });
    });

    /**
     * Quiz CSV import action
     *
     * @since 
     */
    $(document).on('change', '.tutor-add-quiz-button-wrap input[name="csv_file"]', function (e) {
        const _file = $(this).parent().find("input[name='csv_file']").prop('files')
        const that = $(this)
        if ( _file[0] ) {
            if ( _file[0].size > 0 ) {
                
                var nonce = tutor_get_nonce_data(true);
                
                let formData = new FormData();
                formData.append( 'action', 'quiz_import_data' );
                formData.append( 'csv_file', _file[0] );
                formData.append( 'topic_id', $(this).parent().find("input[name='csv_file']").data('topic') );
                formData.append( nonce.key, nonce.value );

                $.ajax({
                    url: ajaxurl,
                    type: 'POST',
                    data: formData,
                    cache: false,
                    contentType: false,
                    processData: false,
                    success: function (data) {
                        if(data.success){
                            that.val('');
                            that.closest('.tutor-topics-body').find('.tutor-lessons').append(data.data.output_quiz_row);
                        }
                    },
                });
                
            } else {
                alert('File is Empty.');
            }
        } else {
            alert('No File Selected.');
        }
    });
    $(document).on('click', '.btn-tutor-submit', function (e) {
        e.preventDefault();
        $(this).parent().find('.tutor-csv-file').click();
    });

});