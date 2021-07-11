/*--------------------------------------
*         Table of Content
*  --------------------------------------
    1. Sticky Nav
    2. Social Share
    3. Pagination JS   
    4. ajax login register
    5. Related course Slide
    6. Author Slide
    7. Sticky Course single tab menu
    8. Course sigle tab
    9. Course Success alert
    10. Course Archive filter
    11. LMS Search On Change
    12. Load More Pagination
    13. Course Archive Page Search
*  -------------------------------------- 
*  -------------------------------------- */

jQuery(document).ready(function ($) {
    'use strict';

    /* --------------------------------------
    *       1. Sticky Nav
    *  -------------------------------------- */
    jQuery(window).on('scroll', function(){
        if($(window).width() > 1199){
            if ( jQuery(window).scrollTop() > 130 ) {
                jQuery('#masthead.enable-sticky, #course-single-navigation').addClass('sticky');
            } else {
                jQuery('#masthead.enable-sticky, #course-single-navigation').removeClass('sticky');
            }
        }
    });


    /* --------------------------------------
     *       2. Social Share
     *  -------------------------------------- */
    $('.social-share-wrap a').prettySocial();
    $('.skillate-mobile-search').click(function(){
        $('.skillate-header-search').slideToggle();
    })

    /*----------------------------------
    *        3. Pagination JS           
    ------------------------------------ */
    if ($('.skillate-pagination').length > 0) {
        if (!$(".skillate-pagination ul li:first-child a").hasClass('prev')) {
            $(".skillate-pagination ul").prepend('<li class="p-2 first"><span>' + $(".skillate-pagination").data("preview") + '</span></li>');
        }
        if (!$(".skillate-pagination ul li:last-child a").hasClass('next')) {
            $(".skillate-pagination ul").append('<li class="p-2 first"><span>' + $(".skillate-pagination").data("nextview") + '</span></li>');
        }
        $(".skillate-pagination ul li:last-child").addClass("ml-auto");
        $(".skillate-pagination ul").addClass("justify-content-start").find('li').addClass('p-2').eq(1).addClass('ml-auto');
    }

    // Add Active Class on mobile menu bottom
    // Get all buttons with class="btn" inside the container
    if ($('.skillate-menu-bottom-inner').length > 0) {
        var btnContainer = document.querySelector('.skillate-menu-bottom-inner');
        var btns = btnContainer.getElementsByClassName("skillate-single-menu-bottom");

        // Loop through the buttons and add the active class to the current/clicked button
        for (var i = 0; i < btns.length; i++) {
            btns[i].addEventListener("click", function() {
                var current = document.getElementsByClassName("active");
                current[0].className = current[0].className.replace(" active", "");
                this.className += " active";
            });
        }
    }

    // Add Active Class on Course Single 2 Top Menu
    // Get all buttons with class="btn" inside the container
    // var menuContainer = document.querySelector('.skillate-course-single-2-menu');
    // var menuItems = menuContainer.getElementsByClassName("course-single-2-menu-item");

    // Loop through the buttons and add the active class to the current/clicked button
    // for (var i = 0; i < menuItems.length; i++) {
    //     menuItems[i].addEventListener("click", function() {
    //         var current = document.getElementsByClassName("active");
    //         current[0].className = current[0].className.replace(" active", "");
    //         this.className += " active";
    //     });
    // }

    /* --------------------------------------
     *      4. ajax login register
     *  -------------------------------------- */
    $('form#login').on('submit', function (e) {
        'use strict';
        e.preventDefault();

        $('form#login p.status').show().text(ajax_object.loadingmessage);

        $.ajax({
            type: 'POST',
            dataType: 'json',
            url: ajax_object.ajaxurl,
            data: {
                'action': 'ajaxlogin', //calls wp_ajax_nopriv_ajaxlogin
                'username': $('form#login #username2').val(),
                'password': $('form#login #password2').val(),
                'rememberme': $('form#login #rememberme').val(),
                'security': $('form#login #security2').val()
            },
            success: function (data) {
                if (data.loggedin == true) {
                    $('form#login p.status').removeClass('text-danger').addClass('text-success');
                    $('form#login p.status').text(data.message);
                    document.location.href = ajax_object.redirecturl;
                } else {
                    $('form#login p.status').removeClass('text-success').addClass('text-danger');
                    $('form#login p.status').text(data.message);
                }
                if ($('form#login p.status').text() == '') {
                    $('form#login p.status').hide();
                } else {
                    $('form#login p.status').show();
                }
            }
        });
    });

    if ($('form#login .login-error').text() == '') {
        $('form#login  p.status').hide();
    } else {
        $('form#login  p.status').show();
    }

    // Register New User
    $('.register_button').click(function (e) {
        e.preventDefault();
        var form_data = $(this).closest('form').serialize() + '&action=ajaxregister';
        $.ajax({
            type: 'POST',
            dataType: 'json',
            url: ajax_object.ajaxurl,
            data: form_data,
            success: function (data) {
                //var jdata = json.parse(data);
                $('#registerform  p.status').show();
                if (data.loggedin) {
                    $('#registerform  p.status').removeClass('text-danger').addClass('text-success');
                    $('#registerform  p.status').text(data.message);
                    $('#registerform')[0].reset();
                } else {
                    $('#registerform  p.status').removeClass('text-success').addClass('text-danger');
                    $('#registerform  p.status').text(data.message);
                }

            }
        });
    });
    if ($('form#registerform  p.status').text() == '') {
        $('form#registerform  p.status').hide();
    } else {
        $('form#registerform  p.status').show();
    }


    /* --------------------------------------
     *       5. Related course Slide
     *  -------------------------------------- */
    $(document).ready( function () {
        var slide_count  = $('.skillate-related-course-slide').data('columns');
        var slide_mode   = $('.skillate-related-course-slide').data('slidemode');
        if(!$('.skillate-related-course-slide').length){
            return
        }

        $('.skillate-related-course-slide').slick({
            dots: false,
            rtl: true,
            infinite: true,
            autoplay: false,
            centerMode: slide_mode,
            centerPadding: '100px',
            arrows: false,
            speed: 700,
            slidesToShow: 7,
            swipeToSlide: true,
            responsive: [
                {
                    breakpoint: 2100,
                    settings: {
                        slidesToShow: slide_count,
                        slidesToScroll: 2,
                        infinite: true,
                        dots: false
                    }
                },
                {
                    breakpoint: 1300,
                    settings: {
                        slidesToShow: 4,
                        //slidesToScroll: 3,
                        infinite: true,
                        dots: false
                    }
                },
                {
                    breakpoint: 1023,
                    settings: {
                        slidesToShow: 3,
                        slidesToScroll: 2,
                        infinite: true,
                        dots: false,
                        centerMode: false
                    }
                },
                {
                    breakpoint: 600,
                    settings: {
                        slidesToShow: 2,
                        slidesToScroll: 2,
                        centerMode: false,
                        swipeToSlide: true
                    }
                },
                {
                    breakpoint: 400,
                    settings: {
                        slidesToShow: 1,
                        slidesToScroll: 1,
                        centerMode: false,
                        swipeToSlide: true
                    }
                }
            ]
        });

        let winWidth = $(window).width();
        if(winWidth < 768){
            $('.course-slide-mobile-only').slick({
                slidesToShow: 2,
                slidesToScroll: 1,
                swipeToSlide: true,
                centerMode: true,
                centerPadding: '10%',
                dots: false,
                autoplay: false,
                arrows: false,
            })
        }
    });

    //Course Filter Mobile Show
    $('.courses-mobile-filter').click(function(){
        $('.skillate-sidebar-filter-col').slideToggle();
    });

    // $('.skillate-archive-single-cat, .single-filter label').click(function(){
    //     $('.skillate-sidebar-filter-col').hide();
    // });

    //Header dashboard show hide on mobile
    if($(window).width() < 991){
        $('.header_profile_menu ul').css('display', 'none');
        $('.header_profile_menu').click(function(){
            $('.header_profile_menu ul').slideToggle();
            console.log('>>>');
        });
    }

    // Mobile Search
    $(".search-open-icon").on('click',function(e){
        e.preventDefault();
        $(".top-search-input-wrap").toggleClass('search-open');
        $('.skillate-mobile-category-menu').removeClass('toggle-category');
        $('#mobile-menu').removeClass('show');
    });

    // Mobile Menu collapse
    $('.skillate-single-menu-bottom .navbar-toggle').click(function(){
        //$('#mobile-menu').toggleClass('toggle-left');
        $(".top-search-input-wrap").removeClass('search-open');
        $('.skillate-mobile-category-menu').removeClass('toggle-category');
    })
  
    $('.skillate-single-bottom-category').click(function(){
        $('.skillate-mobile-category-menu').toggleClass('toggle-category');
        $('.top-search-input-wrap').removeClass('search-open');
        $('#mobile-menu').removeClass('show');
    })

    //Skip Login
    $('.skillate-skip-login').click(function(){
        localStorage.setItem("skip_login", "skip_login");
    })
    var skip_login = localStorage.getItem("skip_login");
    if(skip_login !== 'skip_login'){
        $('.skillate-splash-screen').addClass('login-true');
    }

    /* --------------------------------------
     *       6. Author Slide
     *  -------------------------------------- */
    var headline = $('.author-slide-parent');
    var columns  = headline.data('columns');

    $(window).on('load', function () {
        $('.author-slide-parent').slick({
            dots: false,
            rtl: true,
            infinite: true,
            autoplay: false,
            centerMode: true,
            centerPadding: '100px',
            arrows: false,
            speed: 400,
            slidesToShow: 7,
            swipeToSlide: true,
            slidesToScroll: 3,
            responsive: [
                {
                    breakpoint: 2100,
                    settings: {
                        slidesToShow: columns,
                        slidesToScroll: 3,
                        infinite: true,
                        dots: false
                    }
                },
                {
                    breakpoint: 1400,
                    settings: {
                        slidesToShow: 3,
                        slidesToScroll: 3,
                        infinite: true,
                        dots: false
                    }
                },
                {
                    breakpoint: 900,
                    settings: {
                        slidesToShow: 3,
                        slidesToScroll: 2,
                        infinite: true,
                        dots: false,
                        centerMode: false
                    }
                },
                {
                    breakpoint: 600,
                    settings: {
                        slidesToShow: 2,
                        slidesToScroll: 1,
                        centerMode: false,
                    }
                }
            ]
        });
    });


    //Client Logo Carousel 
    var slide_count  = $('.client-logo-carousel').data('columns');
    var autoPlay     = $('.client-logo-carousel').data('autoplay');
    $('.client-logo-carousel').slick({
        dots: false,
        rtl: true,
        infinite: false,
        arrows: false,
        speed: 500,
        slidesToShow: 3,
        swipeToSlide: true,
        slidesToScroll: 1,
        autoplay: autoPlay,
        responsive: [{
            breakpoint: 350,
            settings: {
                slidesToShow: 2,
            }
        }]
    });



    /* --------------------------------------------------------
    ------------ 8. Sticky Course single tab menu -------------
    ----------------------------------------------------------- */
    var single_course_tab_menu = parseInt( $('.skillate-tab-menu-wrap').attr('data-isSticky') );
    var courseItemTab = $('.single-course-item-tab a[href*="#"]');
    var tab_menu = $('.skillate-tab-menu-wrap');
    if( single_course_tab_menu ) {
        $(window).on('load', function () {

            $(courseItemTab).on('click', function(e) {
                course_tab_menu_active( $(this) );
                var courseItemTabName = $( $(this).attr('href') );
                if( courseItemTabName.offset().top == 0 ) {
                    var single_course_item_tab = $(this).closest('ul.single-course-item-tab li');
                    course_tab_switcher( single_course_item_tab );
                }
                var stickyTabHeight = tab_menu.height() + 167;
                if( tab_menu.hasClass("sticky-tab") ) {
                    stickyTabHeight = tab_menu.height() + 20;
                }
                $('html,body').animate({
                    scrollTop: $($(this).attr('href')).offset().top - stickyTabHeight
                },500);

            });

            if ( tab_menu.length ) {
                var tab_offset = tab_menu.offset().top;
            }
            var tab_position = 0;
            $(window).on('scroll', function () {
                tab_position = tab_offset - $(window).scrollTop();
                if ( tab_position < 120 ) {
                    $(tab_menu).addClass('sticky-tab');
                } else {
                    $(tab_menu).removeClass('sticky-tab');
                }
            });

        });
    }

    function course_tab_menu_active( that ) {
        let course_tab_menu_parent = that.closest('.single-course-item-tab');
        let course_tab_menu_parent_li = course_tab_menu_parent.children();
        course_tab_menu_parent_li.each(function() {
            $(this).children().each(function() {
                $(this).removeClass('active');
            });
        });
        that.addClass('active');
    }

    /* ---------------------------------------------
    ------------ 9. Course sigle tab ---------------
    ------------------------------------------------ */
    $('ul.single-course-item-tab li').click(function (event) {
        course_tab_switcher( $(this) );
    });
    function course_tab_switcher( that ) {
        if( that.hasClass('current') ) { return 0; }
        let tab_id = that.attr('data-tab');
        $('ul.single-course-item-tab li').removeClass('current');
        $('.skillate-tab-content').removeClass('current');

        that.addClass('current');
        $("#" + tab_id).addClass('current');
    }

    

    //Course Benefit css padding
    $('#tab-learn h3').next().addClass('tab-learn-p')


    /* ---------------------------------------------
    ------------ 10. Course Success alert -------------
    ------------------------------------------------ */

    //Course Complete Notify close
    $('.course-complete-notify-panel .notify-close').click(function(){
        $('.course-complete-notify-panel').hide();
    })

    window.onload = function () {
        let notify_panel = $('.course-complete-notify-panel');
        if (!notify_panel.length) {
            return
        }

        var radius = 1.5, // set the radius of the circle
            circumference = 2 * radius * Math.PI;

        var els = document.querySelectorAll('circle');
        Array.prototype.forEach.call(els, function (el) {
            el.setAttribute('stroke-dasharray', circumference + 'em');
            el.setAttribute('r', radius + 'em');
        });

        document.querySelector('.radial-progress-center').setAttribute('r', (radius - 0.01 + 'em'));

        var currentCount = 1,
            maxCount = 100;

        var intervalId = setInterval(function () {
            if (currentCount > 100) {
                clearInterval(intervalId);
                return;
            }
            var offset = -(circumference / maxCount) * currentCount + 'em';

            document.querySelector('.radial-progress-cover').setAttribute('stroke-dashoffset', offset);

            currentCount++;
        }, 10);
    };

    /* -------------------------------------------------
    ------------ 11. Course Archive filter -------------
    ---------------------------------------------------- */
    $(document).on('change', '.skillate-course-filter-form', function (e) {
        e.preventDefault();
        $(this).closest('form').submit();
    });


    $(document).ready(function () {
        $('select:not(.ignore-nice-select)').niceSelect();
    });

    $(".skillate-archive-single-cat .category-toggle").on('click', function () {
        $(this).next('.skillate-archive-childern').slideToggle();
        if ($(this).hasClass('fa-plus')) {
            $(this).removeClass('fa-plus').addClass('fa-minus');
        } else {
            $(this).removeClass('fa-minus').addClass('fa-plus');
        }
    });

    $('.skillate-archive-childern input').each(function () {
        if ($(this).is(':checked')) {
            var aChild = $(this).closest('.skillate-archive-childern');
            aChild.show();
            aChild.siblings('.fas').removeClass('fa-plus').addClass('fa-minus');
        }
    });

    $('.skillate-sidebar-filter input').on('change', function () {
        $('.skillate-sidebar-filter').submit();
    });


    /* ------------------------------------------------
    ------------ 12. LMS Search On Change -------------
    --------------------------------------------------- */
    $('#searchword').on('keyup ', function (e) {
        var $that = $(this);
        var raw_data = $that.val(), // Item Container
            ajaxUrl = $that.data('url');
        $.ajax({
                url: ajaxUrl,
                type: 'POST',
                data: {
                    raw_data: raw_data
                },
                beforeSend: function () {
                    if (!$('.form-inlines .skillate-search-wrapper.search .fa-spinner').length) {
                        $('.form-inlines .skillate-search-wrapper.search').addClass('spinner');
                        $('<i class="fa fa-spinner fa-spin"></i>').appendTo(".form-inlines .skillate-search-wrapper.search .skillate-course-search-icons").fadeIn(100);
                    }
                },
                complete: function () {
                    $('.form-inlines .skillate-search-wrapper.search .fa-spinner ').remove();
                    $('.form-inlines .skillate-search-wrapper.search').removeClass('spinner');
                }
            })
            .done(function (data) {
                if (e.type == 'blur') {
                    $(".skillate-course-search-results").html('');
                } else {
                    $(".skillate-course-search-results").html(data);
                }
            })
            .fail(function () {
                console.log("error");
            });
    });

    var $skillate_course_search = $(".skillate-coursesearch-input");	
    $(window).on("click", function(event){	
        if($(this).length){
            if ( $skillate_course_search.has(event.target).length == 0 && !$skillate_course_search.is(event.target) ){
                $('.skillate-course-search-results').hide();
            }else {
                $('.skillate-course-search-results').show();
            }
        }
    });
    // End Search.


    /* -------------------------------------- */
    /*       13. Load More Pagination
    /* -------------------------------------- */
    $('.post-loadmore').on('click', function (event) {
 
        event.preventDefault();
        let $that = $(this);
        if ($that.hasClass('disable')) {
            return false;
        }
        let container = $that.closest('.course-container'), // Item Container
            total_posts = $that.data('total_posts'),
            perpage = $that.data('per_page'),
            column = $that.data('show_column'),
            layout = $that.data('show_layout');

        let items = container.find('.tutor-course-grid-item'),
            itemNumbers = items.length,
            paged = (itemNumbers / perpage) + 1; // Paged Number

        $.ajax({
                type: 'POST',
                url: ajax_object.ajaxurl,
                data: {
                    'action': 'thmloadmore',
                    perpage: perpage,
                    paged: paged,
                    column: column,
                    layout: layout
                },
                beforeSend: function () {
                    $that.addClass('disable');
                    $('<i class="fa fa-spinner fa-spin" style="margin-left:10px;"></i>').appendTo($that).fadeIn(100);
                },
                complete: function (data) {
                    $that.find('.fa-spinner ').remove();
                }
            })
            .done(function (data) {
                let newLenght = container.find('.tutor-course-grid-item').length;
                if (total_posts >= newLenght) {
                    $('.load-wrap').fadeOut(400, function () {
                        $('.load-wrap').remove();
                    });
                }
                $that.removeClass('disable');
                container.find('.skillate-course').append(data);
            })
    });

    
    /* ----------------------------------------------------------
    * ------------ 14. Course Archive Page Search ---------------
    * ----------------------------------------------------------- */

    // Course Level( Select Just One )
    $('.course-level').on('click', function() {
        $('.course-level').not(this).prop('checked', false);  
    });

    $('.course-price').on('click', function() {
        $('.course-price').not(this).prop('checked', false);  
    });

    $('.course_searchword').on('click', function (e) {
        $(".skillate-courses-wrap.course-archive, .archive-course-pagination").addClass("course-archive-remove");
        let filter_form = $('.skillate-sidebar-filter input[type="checkbox"]:checked').length;
        console.log(filter_form);
        if(filter_form > 0){
            $('.filter-clear-btn a').addClass('search-active');
            $('.filter-clear-btn a').removeClass('empty-search');
        }else if(filter_form == 0){
            $('.filter-clear-btn a').addClass('empty-search');
            $('.filter-clear-btn a').removeClass('search-active');
        }

        // Course Category.
        var i = 0;
        var data_category = [];
        $('.course-category:checked').each(function () {
            data_category[i++] = $(this).val();
        }); 

        // Course Tag.
        var j = 0;
        var data_language = [];
        $('.course-tag:checked').each(function () {
            data_language[j++] = $(this).val();
        }); 

        // Course Level.
        var data_level = [];
        $('.course-level:checked').each(function () {
            data_level = $(this).val();
        }); 

        // Price
        var data_price = [];
        $('.course-price:checked').each(function () {
            data_price = $(this).val();
        }); 

        var $that = $(this);
        var ajaxUrl     = $that.data('url'),
            category    = $("#searchtype").val();

        $.ajax({
            url: ajaxUrl,
            type: 'POST',
            data: {
                data_level: data_level,
                data_category: data_category,
                data_price: data_price,
                data_language: data_language
            },
             beforeSend: function () {
                if (!$('.single-filter .fa-spinner').length) {
                    $('.single-filter').addClass('spinner');
                    $('<i class="fa fa-spinner fa-spin"></i>').appendTo(".spinner-cls").fadeIn(100);
                }
            },
            complete: function () {
                $('.single-filter .fa-spinner ').remove();
                $('.single-filter').removeClass('spinner');
            }
        })

        .done(function (data) {
            if (e.type == 'blur') {
                $(".course-search-results").html('');
            } else {
                $(".course-search-results").html(data);
                var totalCourse = $('.course-search-results .skillate-course-col');
                $(".total-courses strong").html(totalCourse.length);
            }
        })

        .fail(function () {
            console.log("error");
        });
    });
    // End Search.

    // BG line shape class 

    let parentDiv = $('.skillate-line-shape-wrap .qubely-container');
    let string = `<div class="skillate-bg-line-wrap">
                  <span></span>
                  <span></span>
                  <span></span>
                  <span></span>
              </div>`;

    window.onload = function(){
        parentDiv.append(string);
        //console.log(string, parentDivChild);
    }
    

    // Course single 2
    // const section = document.querySelector(".what-you-will-section");

    // createExtraWrapper(section);

    // function createExtraWrapper(section) {
    //     const children = [...section.children];
    //     let fragment = new DocumentFragment();

    //     for (let i = 0; i < children.length; i++) {
    //         const child = children[i];
    //         if (
    //         child.tagName.toLowerCase() === "h3" &&
    //         child.nextElementSibling &&
    //         child.nextElementSibling.tagName.toLowerCase() === "p"
    //         ) {
    //         const wrapper = createWrapper(child, child.nextElementSibling);
    //         fragment.appendChild(wrapper);
    //         i++;
    //         } else {
    //         fragment.appendChild(child);
    //         }
    //     }
    //     return [...fragment.childNodes].forEach((node) => section.appendChild(node));
    // }

    // function createWrapper(...nodes) {
    //     const wrapper = document.createElement("div");
    //     wrapper.classList.add("what-you-learn-single");
    //     nodes.forEach((node) => wrapper.appendChild(node));
    //     return wrapper;
    // }

    


});

