/**
 * content drip scripts
 * @author Themeum<www.themeum.com>
 * @since 1.8.9
*/

jQuery(document).ready(function($){
    if (jQuery().select2){
        $('.select2_multiselect').select2({
            dropdownCssClass:'increasezindex'
        });
    }
    if (jQuery.datepicker){
        $( ".tutor_date_picker" ).datepicker({"dateFormat" : 'yy-mm-dd'});
    }
})