window.jQuery(document).ready(function($){
    
    var toggle_button=function(btn, enable){
        btn.prop('disabled', !enable);
        
        !enable ? 
            btn.append('<img style="width: 13px; margin-left: 9px; vertical-align: middle; display:inline-block;" src="'+window.tutor_gc_loading_icon_url+'"/>'):
            btn.find('img').remove();
    }

    /* Credential Upload Manager */
    var credential = function(){
        
        this.upload_area = $('#tutor_gc_dashboard .tutor-upload-area');
        this.input_field = this.upload_area.find('[type="file"]');
        this.load_button = $('#tutor_gc_credential_upload>button');
        this.uploaded_file = null;
        this.is_file_valid = null;

        this.init=function(){

            this.on_click();
            this.on_change();
            this.on_drop();
            this.on_save();
        }

        this.load_file=function(files){
            if(files && files[0]){

                var file = files[0];
                this.is_file_valid = this.is_valid_file(file);
                this.load_button.prop('disabled', !this.is_file_valid);
                
                if(this.is_file_valid){
                    this.uploaded_file=file;
                    
                    this.upload_area.find('span.file_name').remove();
                    this.upload_area.append('<span class="file_name">'+this.uploaded_file.name+'</span>');
                }    
                else{
                    alert('Invalid File.');
                }            
            }
        }

        this.is_valid_file=function(file){
            var type = file.type || '';
            type = type.toLowerCase();

            var is_valid = type=='application/json';

            return is_valid;
        }

        this.on_click=function(){
            var _this=this;
            
            this.upload_area.find('button').click(function(e){
                e.preventDefault();
                _this.input_field.trigger('click');
            });
        }

        this.on_change=function(){
            var _this=this;
            this.input_field.change(function(e){
                _this.load_file(e.currentTarget.files);
                $(this).val('');
            });
        }

        this.on_drop=function(){
            var _this=this;

            // Process drag and drop file
            this.upload_area.on('drag dragstart dragend dragover dragenter dragleave drop', function(e) {
                e.preventDefault();
                e.stopPropagation();
            })
            .on('dragover dragenter', function() {
                $(this).addClass('dragover');
            })
            .on('dragleave dragend drop', function() {
                $(this).removeClass('dragover');
            })
            .on('drop', function(e) {
                _this.load_file(e.originalEvent.dataTransfer.files);
            });
        }

        this.on_save=function(){
            var _this=this;

            this.load_button.click(function(){
                
                if(!_this.is_file_valid){
                    // Make sure file is selected and valid
                    return;
                }

                var btn = $(this);
                var form = new FormData();
                form.append('credential', _this.uploaded_file, _this.uploaded_file.name);
                form.append('action', 'tutor_gc_credential_save');

                // Append nonce to the object manually, ajaxSetup doen't as it is formadata
                var nonce = tutor_get_nonce_data(true);
                form.append(nonce.key, nonce.value);
                
                toggle_button(btn, false);

                $.ajax({
                    url: window.ajaxurl,
                    type: 'POST',
                    processData:false,
                    contentType:false,
                    data:form,
                    success:function(){
                        window.location.reload();
                    },
                    error:function(){
                        toggle_button(btn, true);
                        alert('Request Failed.');
                    }
                })
            })
        }
    }
    new credential().init();


    /* ---------Credential JSON Upload manager--------- */
    var classlist = $('#tutor_gc_dashboard .google-classroom-class-list');
    
    /* -----------------Google class actions like import, delete, publish etc.----------------- */
    var enroll_student = 'no';
    var class_action_queue = [];
    var toggle_bulk_action_state=function(state_enable){

        var was_running = $('#tutor_gc_bulk_action_button').data('process_running')==true;

        !state_enable ? class_action_queue=[] : 0;

        $('#tutor_gc_bulk_action_button').text(state_enable ? 'Abort' : 'Apply').data('process_running', state_enable); 

        return was_running;
    }
    
    var pop_up_confirmation=function(icon, title, description, buttons, callback){

        var container=$('<div class="tutor-gc-pop-up-container">\
                    <div>\
                        <img src="'+window.tutor_gc_base_url+'assets/images/'+icon+'"/>\
                        <h3>'+title+'</h3>\
                        <p>'+description+'</p>\
                        <button class="'+buttons[0].class+'" data-action="'+buttons[0].action+'">'+buttons[0].text+'</button>\
                        <button class="'+buttons[1].class+'" data-action="'+buttons[1].action+'">'+buttons[1].text+'</button>\
                    </div>\
                </div>');

        container.find('button').click(function(){
            callback($(this).data('action'));
            container.remove();
        });

        container.click(function(){
            container.remove();
        }).children().click(function(e){
            e.stopImmediatePropagation();
        });

        $('body').append(container);
    }

    var import_confirmation=function(callback){
        
        var title = 'Do you want to import students from this Classroom?';
        var description = 'This is not recommended for paid courses as importing will skip the payment procedure.';
        var buttons = [
            {class:'tutor-gc-button-secondary', text:'No', action:'no'},
            {class:'tutor-gc-button-primary', text:'Yes, Import Student', action:'yes'}
        ];

        pop_up_confirmation('import-icon.svg', title, description, buttons,  function(action){
            enroll_student=action;
            callback();
        });
    }

    var delete_confirmation=function(callback){
        
        var title = 'Do you want to remove this course from the system?';
        var description = 'This will not delete it from Google Classroom, it will only remove the connection.';
        var buttons = [
            {class:'tutor-gc-button-secondary', text:'Cancel', action:'no'},
            {class:'tutor-gc-button-primary', text:'Yes, Delete Course', action:'yes'}
        ];

        pop_up_confirmation('delete-icon.svg', title, description, buttons,  function(action){
            action=='yes' ? callback() : 0;
        });
    }

    classlist.find('[data-action]').not('a').click(function(e){
        var btn = $(this);
        var class_id = btn.data('classroom_id');
        var class_post_id = btn.data('class_post_id');
        var class_action = btn.data('action');

        var error_message=function(e){
            alert('Something Went Wrong!');
            toggle_button(btn, true);
        }

        function internal(){
            toggle_button(btn, false);
            
            $.ajax({
                url:window.ajaxurl,
                data:{
                class_id: class_id, 
                action: 'tutor_gc_class_action', 
                action_name: class_action, 
                post_id: class_post_id,
                enroll_student: enroll_student
            },
                type:'POST',
                success:function(r){
                    
                    try{r=JSON.parse(r)}catch(e){}
                    
                    // Show error message if invalid response
                    if(typeof r!=='object'){
                        error_message();
                        return;
                    }

                    // Change row state
                    var parent = btn.parent().attr('class', 'class-status-'+r.class_status);
                    r.edit_link ? parent.find('.class-edit-link').attr('href', r.edit_link) : 0;
                    r.preview_link ? parent.find('.class-preview-link').attr('href', r.preview_link) : 0;
                    r.post_id ? parent.find('[data-action]').attr('data-class_post_id', r.post_id) : 0;
                    parent.parent().find('.tutor-status').attr('class', 'tutor-status tutor-status-'+r.class_status).text(r.status_text);

                    toggle_button(btn, true);     
                    
                    if(class_action_queue.length>0){
                        var button = class_action_queue.shift();
                        button.trigger('click');
                    }
                    else{
                        toggle_bulk_action_state(false);
                    }
                },
                error:error_message
            })
        }

        if(class_action=='delete' && e.originalEvent){
            delete_confirmation(internal);            
            return;
        }
        else if(class_action!=='import' || !e.originalEvent){
            internal();
        }
        else {
            import_confirmation(internal);
        }
    });

    /* -------------Save who can see classroom code------------- */
    $('#tutor_gc_classroom_code_privilege').change(function(){

        var value = $(this).prop('checked') ? 'yes' : 'no';

        $.ajax({
            url:window.ajaxurl,
            data:{action:'tutor_gc_classroom_code_privilege', enabled:value},
            type:'POST',
            success:function(){
                
            },
            error:function(){
                alert('Action Failed.');
            }
        });
    });

    /* -------------------Execute bulk action------------------- */
    $('#tutor_gc_bulk_action_button').click(function(){

        // Abort if previous process is running
        if(toggle_bulk_action_state(false)==true){
            return;
        }

        var action = $(this).prev().val();
        var btns = [];

        $('.google-classroom-class-list [name="google_class_check"]:checked').each(function(){
            var button = $(this).parent().parent().find('[data-action="'+action+'"]:visible');
            button.length>0 ? btns.push(button.eq(0)) : 0;
        });

        if(!btns.length){
            alert('Please select the correct option to take an action.');
            return;
        }

        function internal(){
            class_action_queue = btns.slice(1);
            toggle_bulk_action_state(true);
            btns[0].trigger('click');
        }

        if(action=='import'){
            import_confirmation(internal)
        }
        else if(action=='delete'){
            delete_confirmation(internal);
        }
        else {
            internal();
        }
    });

    /* Search Google Class */
    $('#tutor_gc_dashboard').on('input', '#tutor_gc_search_class', function(){
        
        var table=$('.google-classroom-class-list>tbody');

        var val = $(this).val() || '';
        val = val.trim();

        if(val==''){
            table.children().show();
            return;
        }

        table.children().each(function(){
            var columns = $(this).children();

            var class_title = columns.filter('.tutor-gc-title').text().toLowerCase().trim();
            var class_code = columns.filter('.tutor-gc-code').text().toLowerCase().trim();
            
            var matched = class_title.indexOf(val.toLowerCase())>-1 || class_code==val;
            
            $(this)[matched ? 'show' : 'hide']();
        });
    });

    /* Credential upgrade */
    var reload=function(){window.location.reload();};
    $('#tutor_gc_credential_upgrade').click(function(e){

        e.preventDefault();

        var prompt = $(this).data('message');
        if(prompt && !confirm(prompt)){
            // Take confirmation if prompt message defined
            return;
        }

        toggle_button($(this), false);

        $.ajax({
            url:window.ajaxurl,
            data:{action:'tutor_gc_credential_upgrade'},
            type:'POST',
            success:reload,
            error:reload
        })
    });

    // Copy text
    $('.tutor-gc-copy-text').click(function(e){

        e.stopImmediatePropagation();
        e.preventDefault();

        var $temp = $("<input>");
        $("body").append($temp);
        $temp.val($(this).data('text')).select();
        document.execCommand("copy");
        $temp.remove();
    });
});