jQuery(document).ready(function ($) {

    var match = window.navigator.userAgent.match(/Firefox\/([0-9]+)\./);
    var is_firefox = match ? parseInt(match[1]) : 0;
    var is_safari = /^((?!chrome|android).)*safari/i.test(navigator.userAgent);


    $('body').append('<svg id="tutor_svg_font_id" width="0" height="0" style="background-color:white;"></svg>');

    const loadFont=(callback)=> {
        
        const request = new XMLHttpRequest();
        request.open("get", "?tutor_action=get_fonts");
        request.responseType = "text";
        request.send();
        request.onloadend = () => {
              
            //(2)find all font urls.
            let css = request.response;
            const fontURLs = css.match(/https?:\/\/[^ \)]+/g);
            let loaded = 0;
            
            fontURLs.forEach(url => {
                  
                //(3)get each font binary.
                const request = new XMLHttpRequest();
                request.open("get", url);
                request.responseType = "blob";
                request.onloadend = () => {
                    
                    //(4)conver font blob to binary string.
                    const reader = new FileReader();
                    reader.onloadend = () => {
                        
                        //(5)replace font url by binary string.
                        css = css.replace(new RegExp(url), reader.result);
                        loaded++;
                        //check all fonts are replaced.
                        if(loaded == fontURLs.length){
                            
                            $('#tutor_svg_font_id').prepend(`<style>${css}</style>`);
                            callback();
                        }
                    };
                    reader.readAsDataURL(request.response);
                };
                request.send();
            });
        };
    }	
      
    // HTML to Images related functionalities
    const image = function (course_id, cert_hash, view_url) {
        // Open the data url in new window
        this.view = url => {
            window.location.assign(view_url);
        }

        // Convert data url to octet stream
        // and Show image download dialogue
        this.download = (url, width, height) => {
            var doc = new window.jsPDF((width > height ? 'l' : 'p'), 'px', [width, height]); 
            doc.addImage(url, 'jpeg', 0, 0, width-(.249*width), height-(.249*height));
            doc.save('certificate-'+(new Date().getTime())+'.pdf');
        }

        this.reload=function(){
            window.location.reload();
        }

        this.store_certificate = (blob, callback) => {

            var nonce = tutor_get_nonce_data(true);
            var form_data = new FormData();

            form_data.append('tutor_action', 'store_certificate_image');
            form_data.append('cert_hash', cert_hash);
            form_data.append('certificate_image', blob);
            form_data.append(nonce.key, nonce.value);

            $.ajax({
                url: window.location.origin+window.location.pathname,
                type: 'POST',
                data: form_data,
                processData: false,
                contentType: false,
                success: response => callback(response == 'ok'),
                error: () => callback(false)
            });
        }

        // Call various method like image converter and after action
        this.dispatch_conversion_methods = (action, iframe_document, callback) => {
            var body = iframe_document.getElementsByTagName('body')[0];
            var water_mark = iframe_document.getElementById('watermark');

            var width = water_mark.offsetWidth;
            var height = water_mark.offsetHeight;

            // Now set this dimension body
            body.style.display = 'inline-block';
            body.style.overflow = 'hidden';
            body.style.width = width + 'px';
            body.style.height = height + 'px';
            body.style.padding = '0px';
            body.style.margin = '0px';

            // Now capture the iframe using library
            var container = iframe_document.getElementsByTagName('body')[0];
            var configs = {
                scale:3, 
                letterRendering:true, 
                allowTaint: true, 
                useCORS: true, 
                x: 0,
                y: 0,
                width: width,
                height: height,
                windowWidth: width, 
                windowHeight:height
            };

            html2canvas(container, configs).then(canvas => {
                
                var data_url = canvas.toDataURL('image/jpeg', 1.0);

                // Store the blob on server
                this.store_certificate(data_url, (success) => {

                    // Show error if fails
                    !success ? alert('Something Went Wrong.') : 0;

                    // Execute other actions
                    (success && typeof this[action]=='function') ? this[action](data_url, canvas.width, canvas.height) : 0;

                    // Execute callback if callable
                    typeof callback=='function' ? callback(success) : 0;
                });
            });
        }

        // Fetch certificate html from server
        // and initialize converters
        this.init_render_certificate = (action, callback) => {
            var hash = cert_hash ? '&certificate_hash='+cert_hash : '';
            var certificate_url = '?tutor_action=generate_course_certificate&course_id=' + course_id + hash;

            // Get the HTML from server
            $.get(certificate_url, html => {
                // We need to put the html into iframe to make the certificate styles isolated from parent document
                // Otherwise style might be overridden/influenced
                var iframe = document.createElement('iframe');

                var write_content=function(iframe_document) {
                    
                    iframe_document.write(html);
                    iframe_document.write($('<div></div>').append($('#tutor_svg_font_id').clone()).html());
                    
                    if(is_firefox) {
                        // Increase word spacing, other wise firefox compresses texts.
                        var style = window.document.createElement('style');
                        style.innerHTML='*{word-spacing:3px !important; letter-spacing:2px !important;}';
                        iframe_document.getElementsByTagName('head')[0].appendChild(style);
                    }
                }

                if(is_firefox || is_safari) {

                    iframe.addEventListener('load', ()=> {

                        var iframe_document = iframe.contentWindow || iframe.contentDocument.document || iframe.contentDocument;
                        iframe_document = iframe_document.document;

                        write_content(iframe_document);

                        // Load font and then call dispatcher
                        loadFont(()=>this.dispatch_conversion_methods(action, iframe_document, callback));
                    });     
                }  
                else {
                    loadFont(()=> {

                        var iframe_document = iframe.contentWindow || iframe.contentDocument.document || iframe.contentDocument;
                        iframe_document = iframe_document.document;

                        // Render the html in iframe
                        iframe_document.open();
                        write_content(iframe_document);
                        iframe_document.close();

                        iframe.onload = () => this.dispatch_conversion_methods(action, iframe_document, callback);
                    });  
                }


                iframe.style.position = 'absolute';
                iframe.style.left = '-999999px';
                document.getElementsByTagName('body')[0].appendChild(iframe);
            });
        }
    }

    // Instantiate image processor for this scope
    var downloader_btn = $('#tutor-download-certificate-pdf');
    var downloader_btn_from_preview = $('#tutor-pro-certificate-download-pdf');
    var downloader = downloader_btn.length > 0 ? downloader_btn : downloader_btn_from_preview;

    // Configure working state
    var loading_ = $('<img class="tutor_progress_spinner" style="display:inline;margin-left:5px" src="'+window.tutor_loading_icon_url+'"/>');

    var viewer_button = $('#tutor-view-certificate-image');

    var course_id = downloader.data('course_id');
    var cert_hash = downloader.data('cert_hash');
    var view_url = viewer_button.data('href');

    var image_processor = new image(course_id, cert_hash, view_url);

    // register event listener for course page
    downloader_btn.add(viewer_button).add(downloader_btn_from_preview).click(function (event) {
        // Prevent default action
        event.preventDefault();

        $(this).find('.tutor_progress_spinner').length==0 ? $(this).append(loading_) : 0;

        // Invoke the render method according to action type 
        var action = $(this).attr('id') == 'tutor-view-certificate-image' ? 'view' : 'download';

        image_processor.init_render_certificate(action, ()=>{
            $(this).find('.tutor_progress_spinner').remove();
        });
    });

    // Download image directly without further processing (in individual certificate page)
    var image_downloader = $('#tutor-pro-certificate-download-image');
    image_downloader.click(function () {
        var downloader = $('#tutor-pro-certificate-preview');

        var a = document.createElement('A');
        a.href = downloader.attr('src');
        a.download = 'certificate-'+(new Date().getTime())+'.jpg';
        document.body.appendChild(a);
        a.click();
        document.body.removeChild(a);
    });

    // Regenerate certificate image (in individual page)
    if(image_downloader.length>0 && $('#tutor-pro-certificate-preview').data('is_generated')=='no') {
        image_processor.init_render_certificate('', function()
        {
            window.location.reload();
        });
    }
});