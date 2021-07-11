window.jQuery(document).ready(function($){
    
    var toggle_button=function(btn, enable){
        btn.prop('disabled', !enable);
        
        !enable ? 
            btn.append('<img style="width: 13px; margin-left: 9px; vertical-align: middle; display:inline-block;" src="'+window.tutor_gc_loading_icon_url+'"/>'):
            btn.find('img').remove();
    }
    
    // Keep aspect ratio of course thumbnail loaded by short code
    $(window).resize(function(){
        var banner = $('.tutor-gc-class-shortcode .class-header');
        banner.css('height', (1/2)*banner.eq(0).outerWidth()+'px');

    }).trigger('resize');

    // Handle password set interface after auto registration
    $('#tutor_gc_student_password_set button').click(function(){
        var btn = $(this);
        var parent = btn.parent().parent();
        var pass_1 = parent.find('[name="password-1"]').val();
        var pass_2 = parent.find('[name="password-2"]').val();
        var token = parent.find('[name="token"]').val();

        if(!pass_1 || !pass_2 || pass_1!==pass_2){
            alert('Invalid Password');
            return;
        }

        toggle_button(btn, false);

        $.ajax({
            url:window.tutor_gc_ajax_url,
            data:{
                action:'tutor_gc_student_set_password',
                token: token,
                password: pass_1
            },
            type:'POST',
            success:function(r){
                window.location.replace(window.tutor_gc_dashboard_url);
            },
            error:function(){
                toggle_button(btn, true);
                alert('Request Failed.');
            }
        });
    });

    /* One click copy text */
    $('.tutor-gc-copy-text').click(function(e){

        e.stopImmediatePropagation();
        e.preventDefault();

        var $temp = $("<input>");
        $("body").append($temp);
        $temp.val($(this).data('text')).select();
        document.execCommand("copy");
        $temp.remove();
    });

    /* Lazy load google thumbnails */
    $('.tutor-gc-google-thumbnail').each(function(){
        var element = $(this);
        var url = element.data('thumbnail_url');
        var image = new Image();

        image.onload=function(){
            element.css('background-image', 'url('+url+')');
        }

        image.src=url;
    });

    /* Load more stream */
    $('#tutor_gc_stream_loader a').click(function(e){
        e.preventDefault();

        var anchor = $(this);
        var parent = anchor.parent();
        var loading = parent.find('img');
        var token = parent.data('next_token');
        var course_id = parent.data('course_id');

        anchor.add(loading).toggle();

        $.ajax({
            url: window.tutor_gc_ajax_url,
            type:'POST',
            data:{next_token:token, action:'tutor_gc_load_more_stream', course_id:course_id},
            success:function(r){
                try{r=JSON.parse(r)}catch(e){}

                anchor.add(loading).toggle();
                
                if(typeof r=='object'){

                    if(!r.html || /\S+/.test(r.html)==false){
                        parent.remove();
                        return;
                    }

                    parent.data('next_token', r.next_token);
                    parent.before(r.html);
                    !r.next_token ? parent.remove() : 0;
                }
            },
            error:function(){
                anchor.add(loading).toggle();
            }
        })
    });
});