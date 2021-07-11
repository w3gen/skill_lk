jQuery(document).ready(function($)
{
    var container = $('#tutor-instructor-signature-upload');
    var uploader_button = $('#tutor_pro_custom_signature_file_uploader');
    var delete_button = $('#tutor_pro_custom_signature_file_deleter');
    var file_field = container.find('[type="file"]');
    var preview_img = container.find('img');

    uploader_button.click(function(e){
        e.preventDefault();
        file_field.trigger('click');
    });
    
    file_field.change(function(e){
        
        var files = e.target.files;

        if(!files || files.length==0){
            // Make sure file selected 
            return;
        }

        var reader = new FileReader();
        reader.onload = function(e) {
            preview_img.attr('src', e.target.result);
        }
        reader.readAsDataURL(files[0]);

        delete_button.show();
    });

    delete_button.click(function(e){
        e.preventDefault();

        var parent = $(this).parent();

        parent.find('input').val('');
        parent.find('img').removeAttr('src');
        delete_button.hide();
    });
});