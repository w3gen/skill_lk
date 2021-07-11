<?php
/* -------------------------------------------- *
 * skillate Supports
 * -------------------------------------------- */
if(!function_exists('skillate_setup')): 

    function skillate_setup(){
        // Register Navigation Menu
        register_nav_menus(array( 'primary'   => __( 'Main Menu', 'skillate' )));

        load_theme_textdomain( 'skillate', get_template_directory() . '/languages' );
        add_theme_support( 'title-tag' );
        add_theme_support( 'post-thumbnails' );
        add_image_size( 'skillate-large', 1140, 570, true );
        add_image_size( 'skillate-squre', 600, 600, true );
        add_image_size( 'skillate-medium', 255, 236, true );
        add_image_size( 'skillate-courses', 600, 550, true );
        add_image_size( 'blog-small', 142, 99, true );
        add_image_size( 'skillate-wide', 540, 330, true );
        add_theme_support( 'post-formats', array( 'audio','gallery','image','link','quote','video' ) );
        add_theme_support( 'html5', array( 'comment-list', 'comment-form', 'search-form' ) );
        add_theme_support( 'automatic-feed-links' );
        

        # Enable support for header image
        add_theme_support( 'custom-header', array(
            'default-text-color'    => '#fff',
            'wp_head-callback'      => 'skillate_header_image',
        ) );

        # Custom Logo.
        //add_theme_support( 'custom-logo');

        # Enable Custom Background.
        add_theme_support( 'custom-background' );

        // Add support for Block Styles.
        add_theme_support( 'wp-block-styles' );

        // Add support for full and wide align images.
        add_theme_support( 'align-wide' );

        // Add support for editor styles.
        add_theme_support( 'editor-styles' );

        // Enqueue editor styles.
        add_editor_style( 'style-editor.css' );

        # Add support for font size.
        add_theme_support( 'editor-font-sizes', array(
            array(
                'name' => __( 'small', 'skillate' ),
                'shortName' => __( 'S', 'skillate' ),
                'size' => 16,
                'slug' => 'small'
            ),
            array(
                'name' => __( 'regular', 'skillate' ),
                'shortName' => __( 'M', 'skillate' ),
                'size' => 22,
                'slug' => 'regular'
            ),
            array(
                'name' => __( 'large', 'skillate' ),
                'shortName' => __( 'L', 'skillate' ),
                'size' => 28,
                'slug' => 'large'
            ),
            array(
                'name' => __( 'larger', 'skillate' ),
                'shortName' => __( 'XL', 'skillate' ),
                'size' => 38,
                'slug' => 'larger'
            )
        ) );


        if ( ! isset( $content_width ) ){
            $content_width = 660;
        }
    }
    add_action('after_setup_theme','skillate_setup');

endif;



/*-------------------------------------------------------
*           skillate Breadcrumb
*-------------------------------------------------------*/
if(!function_exists('skillate_breadcrumb')):

    function skillate_breadcrumb(){ ?>

        <ol class="breadcrumb">
            <li>
                <a href="<?php echo esc_url(site_url()); ?>" class="breadcrumb_home">
                    <?php echo esc_html__('Home', 'skillate'); ?>
                </a>
            </li>
            <i class="fas fa-angle-right"></i>
            <li class="active">
                <?php if( is_tag() ) { ?>
                <?php esc_html_e('Posts Tagged ', 'skillate') ?><span class="raquo">/</span><?php single_tag_title(); echo('/'); ?>
                <?php } elseif (is_day()) { ?>
                <?php esc_html_e('Posts made in', 'skillate') ?> <?php the_time('F jS, Y'); ?>
                <?php } elseif (is_month()) { ?>
                <?php esc_html_e('Posts made in', 'skillate') ?> <?php the_time('F, Y'); ?>
                <?php } elseif (is_year()) { ?>
                <?php esc_html_e('Posts made in', 'skillate') ?> <?php the_time('Y'); ?>
                <?php } elseif (is_search()) { ?>
                <?php esc_html_e('Search results for', 'skillate') ?> <?php the_search_query() ?>
                <?php } elseif (is_single()) { ?>
                <?php $category = get_the_category();
                    if ( $category ) {
                        $catlink = get_category_link( $category[0]->cat_ID );
                        echo ('<a href="'.esc_url($catlink).'">'.esc_html($category[0]->cat_name).'</a> '.'<span class="raquo"></span> ');
                    } elseif (get_post_type() == 'product'){
                        echo get_the_title();
                    } ?>
                <?php } elseif (is_category()) { ?>
                <?php single_cat_title(); ?>
                <?php } elseif (is_tax()) { ?>
                <?php
                $skillate_taxonomy_links = array();
                $skillate_term = get_queried_object();
                $skillate_term_parent_id = $skillate_term->parent;
                $skillate_term_taxonomy = $skillate_term->taxonomy;
                while ( $skillate_term_parent_id ) {
                    $skillate_current_term = get_term( $skillate_term_parent_id, $skillate_term_taxonomy );
                    $skillate_taxonomy_links[] = '<a href="' . esc_url( get_term_link( $skillate_current_term, $skillate_term_taxonomy ) ) . '" title="' . esc_attr( $skillate_current_term->name ) . '">' . esc_html( $skillate_current_term->name ) . '</a>';
                    $skillate_term_parent_id = $skillate_current_term->parent;
                }
                if ( !empty( $skillate_taxonomy_links ) ) echo implode( ' <span class="raquo">/</span> ', array_reverse( $skillate_taxonomy_links ) ) . ' <span class="raquo">/</span> ';
                    echo esc_html( $skillate_term->name );
                } elseif (is_author()) {
                    global $wp_query;
                    $curauth = $wp_query->get_queried_object();
                    esc_html_e('Posts by ', 'skillate'); echo ' ',$curauth->nickname;
                } elseif (is_page()) {
                    echo get_the_title();
                } elseif (is_home()) {
                    esc_html_e('Blog', 'skillate');
                }elseif (is_archive()){
                    esc_html_e('Archive', 'skillate');
                } ?>

            </li>
        </ol>
    <?php
    }

endif;


/* -------------------------------------------- *
 * skillate Pagination
 * -------------------------------------------- */
if(!function_exists('skillate_pagination')):

    function skillate_pagination( $page_numb , $max_page ){
        $output = '';
        $big = 999999999;
        $output .= '<div class="skillate-pagination" data-preview="'.__( "Prev","skillate" ).'" data-nextview="'.__( "Next","skillate" ).'">';
        $output .= paginate_links( array(
            'base'          => esc_url_raw(str_replace( $big, '%#%', esc_url( get_pagenum_link( $big ) ) )),
            'format'        => '?paged=%#%',
            'current'       => $page_numb,
            'prev_text'     => __('Prev','skillate'),
            'next_text'     => __('Next','skillate'),
            'total'         => $max_page,
            'type'          => 'list',
        ) );
        $output .= '</div>';
        return $output;
    }

endif;

/* -------------------------------------------- *
 * skillate Comment
 * -------------------------------------------- */
if(!function_exists('skillate_comment')):

    function skillate_comment($comment, $args, $depth){
        // $GLOBALS['comment'] = $comment;
        switch ( $comment->comment_type ) :
            case 'pingback' :
            case 'trackback' :
        ?>
        <li <?php comment_class(); ?> id="comment-<?php comment_ID(); ?>">
        <?php
            break;
            default :
            // global $post;
        ?>
        <li <?php comment_class(); ?> id="li-comment-<?php comment_ID(); ?>">
            <div id="comment-<?php comment_ID(); ?>" class="comment-body">
                <div class="comment-avartar pull-left">
                    <?php echo get_avatar( $comment, $args['avatar_size'] ); ?>
                </div>
                <div class="comment-context">
                    <div class="comment-head">
                        <?php echo '<span class="comment-author">' . get_the_author() . '</span>'; ?>
                        <span class="comment-date"><i class="far fa-calendar" aria-hidden="true"></i> <?php echo esc_attr(get_comment_date()); ?></span>
                        <?php edit_comment_link( esc_html__( 'Edit', 'skillate' ), '<span class="edit-link">', '</span>' ); ?>
                    </div>
                    <?php if ( '0' == $comment->comment_approved ) : ?>
                        <p class="comment-awaiting-moderation"><?php esc_html_e( 'Your comment is awaiting moderation.', 'skillate' ); ?></p>
                    <?php endif; ?>
                    <span class="comment-reply">
                        <?php comment_reply_link( array_merge( $args, array( 'reply_text' => '<i class="fas fa-reply"></i> '.esc_html__( 'Reply', 'skillate' ), 'after' => '', 'depth' => $depth, 'max_depth' => $args['max_depth'] ) ) ); ?>
                    </span>
                </div>
                <div class="comment-content">
                    <?php comment_text(); ?>
                </div>
            </div>
        <?php
            break;
        endswitch;
    }

endif;


/* -------------------------------------------- *
 * CSS Generator
 * -------------------------------------------- */
if(!function_exists('skillate_css_generator')){
    function skillate_css_generator(){
        $output = '';
        $preset = get_theme_mod( 'preset', '1' );
        if( $preset ){
            if( get_theme_mod( 'custom_preset_en', true ) ) {
                // CSS Color
                $major_color = get_theme_mod( 'major_color', '#ff5248' );
                if($major_color){
                    $output .= '
                    a,
                    .bottom-widget .widget ul li a:hover,
                    .single-include-wrap-content:hover h3, 
                    .lesson-preview-title:hover .tutor-course-lesson-content, 
                    .skillate-course-single-2-menu.common-menu-wrap ul li a:hover, 
                    .skillate-course-single-2-menu.common-menu-wrap ul li a:focus, 
                    .skillate-course-single-2-menu.common-menu-wrap ul li a:active, 
                    .instructor-single-social a:hover, 
                    .course-single-2-attribute .tutor-star-rating-group, 
                    .view-course-topics:hover, 
                    .pmpro_level-select a.pmpro_btn, 
                    .tutor-dashboard-inline-links ul li a:hover, .tutor-dashboard-inline-links ul li.active a, 
                    .skillate-order-success, 
                    .skillate-sidebar-filter .single-filter label:hover, 
                    h2.subscription-price, 
                    .skillate-error-wrapper a.btn.btn-secondary, 
                    .woocommerce-info a.showcoupon, 
                    .tab-content .tutor-course-grid-item .course-price-mobile .price, 
                    .common-menu-wrap .nav>li.current-menu-parent ul li.current-menu-item a, 
                    .header-cat-menu ul li:hover .media-body h4, 
                    .header-login-wrap a:hover, 
                    .course-single-mobile .tutor-course-wishlist a.has-wish-listed, 
                    .tab-content .tutor-course-grid-item .course-price-mobile span.woocommerce-Price-amount, 
                    .subtitle-cover .breadcrumb a:hover,
                    #pmpro_levels .pmpro_level:nth-child(2) .pmpro_level-select a.pmpro_btn:hover,
                    .membership-block-title .pmpro_checkout-h3-name,
                    .skillate-course-body h3 a:hover, 
                    .skillate-instructor-content:hover .instructor-name, 
                    .tutor-courses-grid-title a:hover, 
                    .tutor-courses-grid-title:hover a, 
                    .tutor-social-share-wrap button:hover,
                    .header-white .skillate-mobile-search, 
                    .course-media-hover .tutor-loop-cart-btn-wrap a,
                    .skillate-course-cart-btn .course-buy-now,
                    .skillate-related-course .tutor-course-grid-item .tutor-course-grid-content .tutor-course-overlay .tutor-course-grid-enroll .btn.btn-no-fill,
                    .skillate-related-course .tutor-course-grid-item .tutor-course-grid-content .tutor-course-overlay .tutor-course-grid-enroll .tutor-loop-cart-btn-wrap a,
                    .tutor-course-topics-contents .tutor-course-title h4 i,
                    .course-related-price,
                    .skillate-course-cart-btn .tutor-course-wishlist a.has-wish-listed,
                    .skillate-pagination .page-numbers li:hover a,
                    .bottom-widget ul.themeum-about-info li span i,
                    .page-numbers li span:hover, 
                    .skillate-pagination .page-numbers li a:hover, 
                    .page-numbers li .current:before, 
                    .article-details h3.article-title a:hover, 
                    .article-introtext a.blog-btn-wrap, 
                    #sidebar .widget_categories ul li a:hover, 
                    .related-entries .relatedcontent h3 a:hover,
                    .section-content-second .article-details h3.article-title a:hover, 
                    .skillate-pagination .page-numbers li span.current, 
                    .page-numbers li a.page-numbers:hover, 
                    .skillate-pagination .page-numbers li a,
                    .widget-blog-posts-section .entry-title  a:hover,
                    .entry-header h2.entry-title.blog-entry-title a:hover,
                    .entry-summary .wrap-btn-style a.btn-style:hover,
                    .main-menu-wrap .navbar-toggle:hover,
                    .skillate-post .blog-post-meta li a:hover,
                    .skillate-post .blog-post-meta li i,
                    .skillate-post .content-item-title a:hover,
                    .wpneo-campaign-creator-details p:first-child a:hover,
                    #mobile-menu ul li a:hover,
                    .btn.btn-border-skillate,
                    .entry-summary .wrap-btn-style a.btn-style:hover,
                    .social-share-wrap ul li a:hover,
                    .overlay-btn,
                    .tutor-tabs-btn-group a i, 
                    .skillate-post .content-item-title a:hover,
                    .skillate-post .blog-post-meta li a,
                    .skillate-widgets a:hover,.elementor-accordion-title.active .elementor-accordion-icon i,
                    .header-solid .common-menu-wrap .nav>li.active>a:after,
                    .social-share ul li a:hover,
                    .portfolio-grid-title a:hover,
                    .portfolio-cat a:hover,
                    .skillate-btn-start-learning, 
                    .tutor-dashboard-permalinks a:before, 
                    a.tutor-button.bordered-button, .tutor-button.bordered-button, a.tutor-btn.bordered-btn, .tutor-btn.bordered-btn, 
                    .tutor-next-previous-pagination-wrap a, 
                    #tutor-lesson-sidebar-qa-tab-content .tutor-add-question-wrap h3, 
                    .postblock-intro-in a:hover, .postblock-intro a:hover, .postblock-more,
                    .skillate-pagination .page-numbers li a.next.page-numbers:hover, .skillate-pagination .page-numbers li a.prev:hover,
                    .bottom-widget .mc4wp-form-fields button, .skillate-pagination .page-numbers li a.page-numbers:hover,
                    ul.wp-block-archives li a, .wp-block-categories li a, .wp-block-latest-posts li a,
                    .tutor-course-lesson h5 a:hover, .woo-cart ul li span.quantity, .woo-cart .woocommerce.widget_shopping_cart .buttons a.button.wc-forward,
                    .postblock-more,.productBlock-title a:hover, .productBlock-price, .course-spinner,
                    .woocommerce ul.products li.product .price ins, .woocommerce ul.products li.product .price del,
                    .woocommerce ul.products li.product .price,
                    .woocommerce div.product p.price, .woocommerce div.product span.price,
                    .tutor-course-overlay-element .price, .view-all-course-btn .btn-full-width,
                    .tab-vertical .skillate-tab-nav-link.active,
                    .tab-vertical .skillate-tab-nav-link:hover,
                    .tab-vertical .skillate-tab-nav-link.active:before,
                    .tab-vertical .skillate-tab-nav-link:hover:before,
                    .tutor-dashboard-item-group>h4,
                    .tutor-mycourse-content h3 a:hover,
                    .tutor-dashboard-content-inner .tutor-mycourse-wrap .tutor-mycourse-rating a:hover,
                    .tutor-mycourse-edit:hover, .tutor-mycourse-delete:hover,
                    .tutor-mycourse-edit i, .tutor-mycourse-delete i,
                    .single-course-item-tab li a:hover, .skillate-course-col .course-single-price .price a:hover { color: '. esc_attr($major_color) .'; }';
                }
                // CSS Background Color
                if($major_color){
                    $output .= '
                    .info-wrapper a.white, 
                    button.tutor-profile-photo-upload-btn, 
                    .course-single-2-attribute .course-buy-now, 
                    .skillate-btn-start-learning:hover,
                    .skillate-topbar-wrap, 
                    .what-you-get-wrap .skillate-course-cart-btn button, 
                    .course-2-review-btn a.skillate-write-review-btn, 
                    span.tutor-text-avatar, 
                    .tutor-form-group button[type="submit"], 
                    .tutor-progress-bar .tutor-progress-filled, 
                    .tutor-single-page-top-bar, 
                    .tutor-lesson-sidebar-hide-bar, 
                    .modal button.close, 
                    .skillate-order-success:hover, 
                    .best-sell-tag, 
                    .skillate-error-wrapper a.btn.btn-secondary:hover, 
                    .tutor-single-lesson-segment .plyr__control--overlaid:hover, 
                    #pmpro_levels .pmpro_level:nth-child(2) .entry:after,
                    #pmpro_levels .pmpro_level:nth-child(2) .pmpro_level-select a.pmpro_btn,
                    .pmpro_level-select a.pmpro_btn:hover,
                    .splash-login-btn a, 
                    .tutor-course-enrolled-review-wrap .write-course-review-link-btn, 
                    .header-white .hamburger-menu-button-open, 
                    .header-white .hamburger-menu-button-open::before, 
                    .header-white .hamburger-menu-button-open::after, 
                    .tutor-form-group.tutor-reg-form-btn-wrap .tutor-button, 
                    a.view-all-course:hover, 
                    .tutor-login-form-wrap input[type="submit"],
                    .sennd-msg-to-instructor a, 
                    .skillate-course-media::after, 
                    .skillate-sidebar-filter .single-filter label input:checked + .filter-checkbox, 
                    .tutor-course-grid-item .tutor-course-grid-content .tutor-course-overlay:after,
                    .skillate-course-cart-btn .tutor-button.tutor-success,
                    .tutor-course-compelte-form-wrap .course-complete-button:hover,
                    .skillate-course-cart-btn button,
                    .wpmm_mobile_menu_btn,
                    .wpmm_mobile_menu_btn:hover,
                    .wpmm-gridcontrol-left:hover, 
                    .tutor-dashboard-permalinks li.active a, 
                    .wpmm-gridcontrol-right:hover,
                    .header-top .social-share ul li a:hover,
                    .single_add_to_cart_button, a.tutor-button, .tutor-button, a.tutor-btn, .tutor-btn, 
                    .widget .tagcloud a:hover,
                    .single_related:hover .overlay-content,.page-numbers li .current:before,
                    #edd_checkout_form_wrap .edd-cart-adjustment .edd-apply-discount.edd-submit,
                    .woo-cart .woocommerce.widget_shopping_cart .buttons a.button.wc-forward:hover,
                    .woo-cart .woocommerce.widget_shopping_cart .buttons a.button.checkout.wc-forward,
                    .woocommerce ul.products li.product .onsale,
                    .woocommerce #respond input#submit, .woocommerce a.button, 
                    .woocommerce button.button, .woocommerce input.button, 
                    .woocommerce .addtocart-btn a.added_to_cart,
                    .woocommerce button.button.alt, .woocommerce input.button.alt, .info-wrapper .btn.btn-skillate,
                    .woocommerce-tabs.wc-tabs-wrapper ul li,
                    .woocommerce span.onsale,
                    .tab-vertical .skillate-tab-nav-link:before,
                    .woocommerce .cart .button,
                    .woocommerce .cart input.button,
                    .woocommerce-cart .wc-proceed-to-checkout a.checkout-button,
                    .woocommerce-page #payment #place_order,
                    .site-header-cart .cart-contents span,
                    .edd-submit.button.blue,
                    .report-top-sub-menu a.active,
                    .date-range-input button,
                    .tutor-course-purchase-box .edd-submit.button.blue,
                    .site-header-cars-edd .cart-content-edd span
                    { background: '. esc_attr($major_color) .'; }';

                } 

                if($major_color){
                    $output .= '
                    span.tutor-text-avatar, .woocommerce table.shop_table td a.remove:hover{ background: '. esc_attr($major_color) .'!important; }
                    ';
                }

                if($major_color){
                    $output .= '
                    .woo-cart .woocommerce.widget_shopping_cart .buttons a.button.wc-forward,
                    .bottom-widget .mc4wp-form input[type="email"]:focus { border-color: '. esc_attr($major_color) .'; }
                    ';
                }

                if($major_color){
                    $output .= '.woocommerce-info a.showcoupon { color: '. esc_attr($major_color) .'; }';
                }

                // CSS Border
                if($major_color){
                    $output .= '
                    input:focus,
                    .skillate-btn-start-learning, 
                    .single_add_to_cart_button, a.tutor-button, .tutor-button, a.tutor-btn, .tutor-btn, 
                    a.tutor-button.bordered-button, .tutor-button.bordered-button, a.tutor-btn.bordered-btn, .tutor-btn.bordered-btn, 
                    .tutor-dashboard-inline-links ul li a:hover, .tutor-dashboard-inline-links ul li.active a, 
                    .tutor-progress-bar .tutor-progress-filled:after, 
                    .tutor-option-field textarea:focus, .tutor-option-field input:not([type="submit"]):focus, .tutor-form-group textarea:focus, .tutor-form-group input:not([type="submit"]):focus, 
                    .skillate-order-success, 
                    .woocommerce-info a.showcoupon, 
                    .wp-block-quote, 
                    .skillate-error-wrapper a.btn.btn-secondary, 
                    .skillate-error-wrapper a.btn.btn-secondary:hover, 
                    .pmpro_level-select a.pmpro_btn,
                    .pmpro_level-select a.pmpro_btn:hover,
                    .view-all-course:hover, 
                    #pmpro_levels .pmpro_level:nth-child(2) .pmpro_level-select a.pmpro_btn:hover,
                    .tutor-form-group.tutor-reg-form-btn-wrap .tutor-button, 
                    .tutor-course-topics-contents .tutor-course-title h4 i,
                    .skillate-sidebar-filter .single-filter label input:checked + .filter-checkbox, 
                    .skillate-course-cart-btn button,
                    .modal.right .modal-body input[type="submit"], 
                    .comments-area .comment-form input[type=text]:focus, 
                    .comments-area textarea:focus,
                    .call-to-action a.btn, 
                    textarea:focus,
                    .wpmm-gridcontrol-left:hover, 
                    .wpmm-gridcontrol-right:hover,
                    keygen:focus,
                    select:focus,
                    .carousel-woocommerce .owl-nav .owl-next:hover,
                    .carousel-woocommerce .owl-nav .owl-prev:hover,
                    .common-menu-wrap .nav>li.current>a,
                    .header-solid .common-menu-wrap .nav>li.current>a,
                    .latest-review-single-layout2 .latest-post-title a:hover,
                    .blog-arrows a:hover,
                    .wpcf7-submit,
                    .woocommerce nav.woocommerce-pagination ul li a:hover,
                    .woocommerce nav.woocommerce-pagination ul li span.current,
                    .wpcf7-form input:focus,
                    .btn.btn-border-skillate,
                    .btn.btn-border-white:hover,
                    .info-wrapper a.white:hover,
                    .portfolio-cat a:hover, .bottom-widget .mc4wp-form input[type="email"]:focus,
                    .tutor-course-purchase-box .edd-submit.button.blue
                    { border-color: '. esc_attr($major_color) .'; }';
                }

                // CSS Background Color & Border
                if($major_color){
                    $output .= '    
                    .wpcf7-submit:hover,
                    #tutor-lesson-sidebar-qa-tab-content .tutor-add-question-wrap button.tutor_ask_question_btn, 
                    .mc4wp-form-fields .send-arrow button,
                    .post-meta-info-list-in a:hover,
                    .comingsoon .mc4wp-form-fields input[type=submit], #sidebar h3.widget_title:before
                    {   background-color: '. esc_attr($major_color) .'; border-color: '. esc_attr($major_color) .'; }';
                }

            }

            // Custom Color
            if( get_theme_mod( 'custom_preset_en', true ) ) {
                $hover_color = get_theme_mod( 'hover_color', '#1f2949' );
                if( $hover_color ){
                    $output .= 'a:hover,
                    .widget.widget_rss ul li a,
                    .footer-copyright a:hover,
                    .entry-summary .wrap-btn-style a.btn-style:hover { color: '.esc_attr( $hover_color ) .'; }';
                    
                    $output .= '.error-page-inner a.btn.btn-primary.btn-lg:hover,
                    input[type=button]:hover,
                    .widget.widget_search #searchform .btn-search:hover,
                    .woocommerce #respond input#submit.alt:hover,
                     .woocommerce a.button.alt:hover,
                     .woocommerce button.button.alt:hover,
                     .woocommerce input.button.alt:hover,
                     .order-view .label-info:hover,
                     .skillate-related-course .tutor-course-grid-enroll a:hover,
                     .tutor-course-grid-enroll input.button.blue:hover,
                     .tutor-course-grid-enroll .edd_go_to_checkout.button.blue:hover,
                     #edd_checkout_form_wrap .edd-cart-adjustment .edd-apply-discount.edd-submit:hover,
                     a.tutor-profile-photo-upload-btn:hover, 
                     button.tutor-profile-photo-upload-btn:hover
                     { background: '.esc_attr( $hover_color ) .'!important; }';

                     $output .= '.edd-submit.button.blue:hover,
                     .edd-submit.button.blue:focus, .tutor-course-overlay .edd-submit.button.blue,
                     .edd-submit.button.blue { background: '.esc_attr( $hover_color ) .'; }';

                     $output .= '.edd-submit.button.blue:hover,.edd-submit.button.blue,
                     .edd-submit.button.blue:focus{ border-color: '.esc_attr( $hover_color ) .'; }';

                    // Background hover color
                    $output .= '.modal.right .modal-body input[type="submit"]:hover { background-color: '.esc_attr( $hover_color ) .'; border-color: '.esc_attr( $hover_color ).'; color: '.esc_attr( get_theme_mod( 'button_hover_text_color', '#fff' ) ) .' !important; }';
                    
        

                    $output .= '.woocommerce a.button:hover, 
                    .skillate-related-course .tutor-course-grid-enroll a:hover,
                    .course-media-hover .tutor-loop-cart-btn-wrap:hover, 
                    .tutor-course-enrolled-review-wrap .write-course-review-link-btn:hover, 
                    .skillate-course-cart-btn .course-buy-now:hover, 
                    a.skillate-write-review-btn:hover, 
                    a.tutor-button:hover, .tutor-button:hover, a.tutor-btn:hover, .tutor-btn:hover, 
                    #tutor-lesson-sidebar-qa-tab-content .tutor-add-question-wrap button.tutor_ask_question_btn:hover,  
                    .tutor-single-page-top-bar .tutor-single-lesson-segment button.course-complete-button:hover, 
                    .bottom-widget .mc4wp-form input[type="email"]:focus { border-color: '.esc_attr( $hover_color ) .'!important; background: '.esc_attr( $hover_color ) .'!important;}';
                }
            }
        }

        $bstyle = $mstyle = $h1style = $h2style = $h3style = $h4style = $h5style = '';
        //body
        if ( get_theme_mod( 'body_font_size', '16' ) ) { $bstyle .= 'font-size:'.get_theme_mod( 'body_font_size', '16' ).'px;'; }
        if ( get_theme_mod( 'body_google_font', 'Open Sans' ) ) { $bstyle .= 'font-family:'.get_theme_mod( 'body_google_font', 'Open Sans' ).';'; }
        if ( get_theme_mod( 'body_font_weight', '400' ) ) { $bstyle .= 'font-weight: '.get_theme_mod( 'body_font_weight', '400' ).';'; }
        if ( get_theme_mod('body_font_height', '27') ) { $bstyle .= 'line-height: '.get_theme_mod('body_font_height', '27').'px;'; }
        if ( get_theme_mod('body_font_color', '#6a6a6a') ) { $bstyle .= 'color: '.get_theme_mod('body_font_color', '#1f2949').';'; }
        
        //menu
        $mstyle = '';
        if ( get_theme_mod( 'menu_font_size', '14' ) ) { $mstyle .= 'font-size:'.get_theme_mod( 'menu_font_size', '14' ).'px;'; }
        if ( get_theme_mod( 'menu_google_font', 'Open Sans' ) ) { $mstyle .= 'font-family:'.get_theme_mod( 'menu_google_font', 'Open Sans' ).';'; }
        if ( get_theme_mod( 'menu_font_weight', '400' ) ) { $mstyle .= 'font-weight: '.get_theme_mod( 'menu_font_weight', '400' ).';'; }
        if ( get_theme_mod('menu_font_height', '20') ) { $mstyle .= 'line-height: '.get_theme_mod('menu_font_height', '20').'px;'; }

        //heading1
        $h1style = '';
        if ( get_theme_mod( 'h1_font_size', '44' ) ) { $h1style .= 'font-size:'.get_theme_mod( 'h1_font_size', '44' ).'px;'; }
        if ( get_theme_mod( 'h1_google_font', 'Open Sans' ) ) { $h1style .= 'font-family:'.get_theme_mod( 'h1_google_font', 'Open Sans' ).';'; }
        if ( get_theme_mod( 'h1_font_weight', '600' ) ) { $h1style .= 'font-weight: '.get_theme_mod( 'h1_font_weight', '600' ).';'; }
        if ( get_theme_mod('h1_font_height', '42') ) { $h1style .= 'line-height: '.get_theme_mod('h1_font_height', '42').'px;'; }

        # heading2
        $h2style = '';
        if ( get_theme_mod( 'h2_font_size', '40' ) ) { $h2style .= 'font-size:'.get_theme_mod( 'h2_font_size', '40' ).'px;'; }
        if ( get_theme_mod( 'h2_google_font', 'Open Sans' ) ) { $h2style .= 'font-family:'.get_theme_mod( 'h2_google_font', 'Open Sans' ).';'; }
        if ( get_theme_mod( 'h2_font_weight', '600' ) ) { $h2style .= 'font-weight: '.get_theme_mod( 'h2_font_weight', '600' ).';'; }
        if ( get_theme_mod('h2_font_height', '46') ) { $h2style .= 'line-height: '.get_theme_mod('h2_font_height', '46').'px;'; }

        //heading3
        $h3style = '';
        if ( get_theme_mod( 'h3_font_size', '20' ) ) { $h3style .= 'font-size:'.get_theme_mod( 'h3_font_size', '20' ).'px ;'; }
        if ( get_theme_mod( 'h3_google_font', 'Open Sans' ) ) { $h3style .= 'font-family:'.get_theme_mod( 'h3_google_font', 'Open Sans' ).' ;'; }
        if ( get_theme_mod( 'h3_font_weight', '600' ) ) { $h3style .= 'font-weight: '.get_theme_mod( 'h3_font_weight', '600' ).';'; }
        if ( get_theme_mod('h3_font_height', '28') ) { $h3style .= 'line-height: '.get_theme_mod('h3_font_height', '28').'px;'; }

        //heading4
        $h4style = '';
        if ( get_theme_mod( 'h4_font_size', '16' ) ) { $h4style .= 'font-size:'.get_theme_mod( 'h4_font_size', '16' ).'px ;'; }
        if ( get_theme_mod( 'h4_google_font', 'Open Sans' ) ) { $h4style .= 'font-family:'.get_theme_mod( 'h4_google_font', 'Open Sans' ).' ;'; }
        if ( get_theme_mod( 'h4_font_weight', '600' ) ) { $h4style .= 'font-weight: '.get_theme_mod( 'h4_font_weight', '600' ).';'; }
        if ( get_theme_mod('h4_font_height', '26') ) { $h4style .= 'line-height: '.get_theme_mod('h4_font_height', '26').'px;'; }

        //heading5
        $h5style = '';
        if ( get_theme_mod( 'h5_font_size', '14' ) ) { $h5style .= 'font-size:'.get_theme_mod( 'h5_font_size', '14' ).'px ;'; }
        if ( get_theme_mod( 'h5_google_font', 'Open Sans' ) ) { $h5style .= 'font-family:'.get_theme_mod( 'h5_google_font', 'Open Sans' ).' ;;'; }
        if ( get_theme_mod( 'h5_font_weight', '600' ) ) { $h5style .= 'font-weight: '.get_theme_mod( 'h5_font_weight', '600' ).';'; }
        if ( get_theme_mod('h5_font_height', '26') ) { $h5style .= 'line-height: '.get_theme_mod('h5_font_height', '26').'px;'; }

        $output .= 'body {'.$bstyle.'}';
        $output .= '.common-menu-wrap .nav>li>a, .header-cat-menu>div.header-cat-text, .header-login-wrap a {'.$mstyle.'}';
        $output .= 'h1 {'.$h1style.'}';
        $output .= 'h2 {'.$h2style.'}';
        $output .= 'h3 {'.$h3style.'}';
        $output .= 'h4 {'.$h4style.'}';
        $output .= 'h5 {'.$h5style.'}';


        // login-reg-screen
        $login_reg_screen_bg_color = get_theme_mod( 'login_reg_screen_bg_color' , '#fff' );
        $login_reg_screen_text_color = get_theme_mod( 'login_reg_screen_text_color', '#1f2949' );
        if($login_reg_screen_bg_color) {
            $output .= '
                .skillate-signin-popup-inner.modal-content{ 
                    background-color: ' . esc_attr($login_reg_screen_bg_color) . ';
                }';
        }
        if($login_reg_screen_text_color) {
            $output .= '
                .skillate-signin-modal-form h2,
                .skillate-signin-modal-form h4,
                .new-user-login,
                .skillate-login-remember,
                .skillate-signin-modal-form .forgot-pass{
                    color : ' . esc_attr($login_reg_screen_text_color) . ';
                }';
        }

        //Header
        $menu_border_c = get_theme_mod( 'menu_border_color', '#fff' );

        // Header Transparent
        if ( $menu_border_c ){
            $output .= '.wp-megamenu-wrap .wpmm-nav-wrap > ul.wp-megamenu > li.wpmm_dropdown_menu, 
            #wp-megamenu-primary>.wpmm-nav-wrap ul.wp-megamenu li:last-child, 
            .wp-megamenu-wrap .wpmm-nav-wrap > ul.wp-megamenu > li.menu-item-has-children, .wp-megamenu-wrap .wpmm-nav-wrap > ul.wp-megamenu > li.wpmm_mega_menu, 
            .wp-megamenu-wrap .wpmm-nav-wrap > ul.wp-megamenu > li.wpmm_dropdown_menu.wpmm-logo-item:hover, .wp-megamenu-wrap .wpmm-nav-wrap > ul.wp-megamenu > li.wpmm_dropdown_menu.wpmm-logo-item, .wp-megamenu-wrap .wpmm-nav-wrap > ul.wp-megamenu > li.wpmm_dropdown_menu:last-child{ border-color: '.esc_attr($menu_border_c).'}';
        }
        $output .= '.site-header{ margin-bottom: '. (int) esc_attr( get_theme_mod( 'header_margin_bottom', '0' ) ) .'px; }';


        //Header
        $header_bgc = get_post_meta( get_the_ID() , 'skillate_header_bgc', true );
        if($header_bgc){
            $output .= '.site-header{ background-color: '. $header_bgc .'; }';
        }elseif(get_theme_mod( 'header_color', '#0d0e12' )){
            $output .= '.site-header{ background-color: '.esc_attr( get_theme_mod( 'header_color', '#0d0e12' ) ) .'; }';
        }

        $headerlayout = get_theme_mod( 'head_style', 'transparent' );
        $header_style = get_post_meta( get_the_ID(), "skillate_header_style", true );
        if($header_style){
            if($header_style == 'transparent_header'){
                $headerlayout =  'transparent';
            }else{
                $headerlayout =  'solid';
            }
        }

        // Header Transparent
        if ( $headerlayout == 'transparent' ){
            $output .= '.site-header.header-transparent{ background-color: transparent;}';
        }

        //sticky header
        if ( get_theme_mod( 'header_fixed', true ) ){
            $output .= '.site-header.sticky{ position:fixed;top:0;left:auto; z-index:99999;margin:0 auto; width:100%;-webkit-animation: fadeInDown 500ms;animation: fadeInDown 500ms;}';
            $output .= '.admin-bar .site-header.sticky{top: 32px;}';
            $output .= '.site-header.sticky.header-transparent{ margin-top: 0;}';
            
        }

        //logo Height, Width
        if (get_theme_mod( 'logo_width' )) {
            $output .= '.logo-wrapper img{width:'.esc_attr(get_theme_mod( 'logo_width')).'px;}';
        }
        if (get_theme_mod( 'logo_height' )) {
            $output .= '.logo-wrapper img{height:'.esc_attr(get_theme_mod( 'logo_height' )).'px;}';
        }

        // sub header
        $output .= '.subtitle-cover h2, .subtitle-cover .breadcrumb a, .subtitle-cover .breadcrumb>.active, .breadcrumb {color:'.esc_attr(get_theme_mod( 'sub_header_title_color', '#1f2949' )).';}';

        $output .= '.page-leading{ font-size: '.esc_attr(get_theme_mod( 'sub_header_title_size', '40')).'px; }';
        
        $output .= '.subtitle-cover{padding:'.esc_attr(get_theme_mod( 'sub_header_padding_top', '60' )).'px 0 '.esc_attr(get_theme_mod( 'sub_header_padding_bottom', '50' )).'px; }';


        $output .= '.site-header{ padding-top: '. (int) esc_attr( get_theme_mod( 'header_padding_top', '10' ) ) .'px; }';


        $output .= '.site-header{ padding-bottom: '. (int) esc_attr( get_theme_mod( 'header_padding_bottom', '10' ) ) .'px; }';
        //$output .= '.site-header{ margin-bottom: '. (int) esc_attr( get_theme_mod( 'header_margin_bottom', '0' ) ) .'px; }';

        // Button color setting
        if($major_color){
        $output .= 'input[type=submit],input[type="button"].wpneo-image-upload,
                    .btn.btn-border-skillate:hover,.btn.btn-border-white:hover{ background-color: '.esc_attr($major_color) .'; border-color: '.esc_attr($major_color) .'; color: '.esc_attr( get_theme_mod( 'button_text_color', '#fff' ) ) .' !important; border-radius: '.esc_attr(get_theme_mod( 'button_radius', 4 )).'px; }';
        $output .= '.skillate-login-register a.skillate-dashboard, .skillate-widgets span.blog-cat:before{ background-color: '.esc_attr($major_color) .'; }';
        }

        $sticybg = '';
        $page_sticybg  = get_post_meta( get_the_ID(), "skillate_sticky_bg_color", '' );
        if($page_sticybg){
            $sticybg = $page_sticybg;
        }else{
            $sticybg = get_theme_mod( 'sticky_header_color', '#1f2949');
        }

        if ( $page_sticybg ){
            $output .= '.site-header.sticky{ background-color: '.$sticybg[0].';}';
        }else{
           // $sticybg = get_theme_mod( 'sticky_header_color', '#1f2949');
            $output .= '.site-header.sticky{ background-color: '.get_theme_mod( 'sticky_header_color', '#1f2949').';}';
        }

        if(get_theme_mod('splash_bg', get_template_directory_uri().'/images/mobile-screen-bg.png')){
            $output .= '.skillate-splash-screen{ background:url('.get_theme_mod('splash_bg', get_template_directory_uri().'/images/mobile-screen-bg.png').'); background-color: #fff; background-size:cover;background-position:center center;background-repeat:no-repeat;background-attachment:fixed; }';
        }else{
            $output .= '.skillate-splash-screen{ background:'.get_theme_mod('splash_bg_color', '#fff').' }';
        }

        #menu color
        $menu_color    = '';
        $page_menu_color    = get_post_meta( get_the_ID(), "skillate_header_color", '' );
        if($page_menu_color){
            $menu_color = $page_menu_color[0];
        }else{
            $menu_color = get_theme_mod( 'menu_font_color' );
        }

        //Menu hover color
        $menu_hover_color    = '';
        $page_menu_hover_color    = get_post_meta( get_the_ID(), "skillate_header_hover_color", '' );
        if($page_menu_hover_color){
            $menu_hover_color = $page_menu_hover_color;
        }else{
            $menu_hover_color = get_theme_mod( 'navbar_hover_text_color' );
        }

        if ( $menu_color ) {
        $output .= '.header-cat-menu > div.header-cat-text, .header-login-wrap a, .common-menu-wrap .nav>li>a, .primary-menu .common-menu-wrap .nav>li>a, .common-menu-wrap .nav>li.menu-item-has-children > a:after{ color: '.esc_attr( $menu_color ) .'!important; }';
        }
        if($page_menu_color){
            $output .= '.site-header-cart .cart-contents span{
                background: '.$page_menu_color[0].';
            }';
        }

        if ( $page_menu_hover_color) {
            $output .= '.header-login-wrap a:hover{ color: '.esc_attr( $menu_hover_color[0] ) .'!important; }';
            $output .= 'primary-menu .common-menu-wrap .nav>li.current-menu-item > a:hover:before, 
            .site-header-cart .cart-contents span {background: '.esc_attr($menu_hover_color[0]).'}';
        }else{
            $output .= '.header-login-wrap a:hover{ color: '.esc_attr( get_theme_mod( 'navbar_hover_text_color' ) ) .'!important; }';
        }

        $triangle_position = get_theme_mod('category_triangle_position', 150);
        if($triangle_position){
            $output .= '.header-cat-menu ul::after { left: '. (int) esc_attr( $triangle_position ) .'px; }';
        }

        # Active Color
        $output .= '.primary-menu .common-menu-wrap .nav>li.active>a,.common-menu-wrap .nav>li.active.menu-item-has-children > a:after { color: '.esc_attr( get_theme_mod( 'navbar_active_text_color', '#fff' ) ) .' !important; }';


        //submenu color
        $output .= '.common-menu-wrap .nav>li ul{ background-color: '.esc_attr( get_theme_mod( 'sub_menu_bg', '#fff' ) ) .'; }';
        $output .= '.common-menu-wrap .nav>li>ul li a,.common-menu-wrap .nav > li > ul li.mega-child > a, .common-menu-wrap .sub-menu > li.active > a{ color: '.esc_attr( get_theme_mod( 'sub_menu_text_color', '#1f2949' ) ) .'; border-color: '.esc_attr( get_theme_mod( 'sub_menu_border', '#eef0f2' ) ) .'; }';

        $output .= '.common-menu-wrap .nav>li>ul li a:hover, .common-menu-wrap .nav>li>ul li a:hover,.common-menu-wrap .sub-menu li.active.mega-child a:hover{ color: '.esc_attr( get_theme_mod( 'sub_menu_text_color_hover', '#ff5248' ) ) .';}';
        $output .= '.common-menu-wrap .nav>li > ul::after{ border-color: transparent transparent '.esc_attr( get_theme_mod( 'sub_menu_bg', '#fff' ) ) .' transparent; }';



        //bottom
        $output .= '#bottom-wrap{ background-color: '.esc_attr( get_theme_mod( 'bottom_color', '#fff' ) ) .'; }';
        $output .= '#bottom-wrap,.bottom-widget .widget h3.widget-title{ color: '.esc_attr( get_theme_mod( 'bottom_title_color', '#1f2949' ) ) .'; }';
        $output .= '#bottom-wrap a, #menu-footer-menu li a{ color: '.esc_attr( get_theme_mod( 'bottom_link_color', '#797c7f' ) ) .'; }';
        $output .= '#bottom-wrap .skillate-widgets .latest-widget-date, #bottom-wrap .bottom-widget ul li, div.about-desc, .bottom-widget .textwidget p{ color: '.esc_attr( get_theme_mod( 'bottom_text_color', '#797c7f' ) ) .'; }';
        $output .= '#bottom-wrap a:hover{ color: '.esc_attr( get_theme_mod( 'bottom_hover_color', '#ff5248' ) ) .' !important; }';
        $output .= '#bottom-wrap { padding-top: '. (int) esc_attr( get_theme_mod( 'bottom_padding_top', '70' ) ) .'px; }';
        $output .= '#bottom-wrap { padding-bottom: '. (int) esc_attr( get_theme_mod( 'bottom_padding_bottom', '70' ) ) .'px; }';


        //footer
        $output .= '#footer-wrap{ background-color: '.esc_attr( get_theme_mod( 'copyright_bg_color', '#f3f4f7' ) ) .'; }';
        $output .= '.footer-copyright, .footer-payment-method-widget h5, span.footer-theme-design { color: '.esc_attr( get_theme_mod( 'copyright_text_color', '#797c7f' ) ) .'; }';
        $output .= '.menu-footer-menu a, .footer-copyright a, .footer-theme-design a{ color: '.esc_attr( get_theme_mod( 'copyright_link_color', '#6c6d8b' ) ) .'; }';
        $output .= '.menu-footer-menu a:hover, .footer-copyright a:hover, .footer-theme-design a:hover{ color: '.esc_attr( get_theme_mod( 'copyright_hover_color', '#ff5248' ) ) .'; }';
        $output .= '#footer-wrap{ padding-top: '. (int) esc_attr( get_theme_mod( 'copyright_padding_top', '25' ) ) .'px; }';
        $output .= '#footer-wrap{ padding-bottom: '. (int) esc_attr( get_theme_mod( 'copyright_padding_bottom', '25' ) ) .'px; }';

        $coming_soon_bg = get_theme_mod('coming_soon_bg', '');

        # 404 page.
        $output .= "body.page-template-coming-soon{
            width: 100%;
            height: 100%;
            min-height: 100%;
            background-image: url(".esc_url($coming_soon_bg).");
            background-size: cover;
            background-repeat: no-repeat;
        }";
        return $output;
    }
}


/* -------------------------------------------- *
 * CSS Generator Backend
 * -------------------------------------------- */
if(!function_exists('skillate_css_backend_generator')){
    function skillate_css_backend_generator(){
        $skillate_pro_backend_output = '';

        $bstyle = $mstyle = $h1style = $h2style = $h3style = $h4style = $h5style = $pstyle = '';
        //body
        if ( get_theme_mod( 'body_font_size', '14' ) ) { $bstyle .= 'font-size:'.get_theme_mod( 'body_font_size', '14' ).'px;'; }
        if ( get_theme_mod( 'body_google_font', 'Open Sans' ) ) { $bstyle .= 'font-family:'.get_theme_mod( 'body_google_font', 'Open Sans' ).';'; }
        if ( get_theme_mod( 'body_font_weight', '400' ) ) { $bstyle .= 'font-weight: '.get_theme_mod( 'body_font_weight', '400' ).';'; }
        if ( get_theme_mod('body_font_height', '27') ) { $bstyle .= 'line-height: '.get_theme_mod('body_font_height', '27').'px;'; }
        if ( get_theme_mod('body_font_color', '#535967') ) { $bstyle .= 'color: '.get_theme_mod('', '#535967').';'; }

        $pstyle .= 'font-family: Open Sans; font-size: 18px; line-height: 28px';
        
        //heading1
        $h1style = '';
        if ( get_theme_mod( 'h1_font_size', '46' ) ) { $h1style .= 'font-size:'.get_theme_mod( 'h1_font_size', '46' ).'px;'; }
        if ( get_theme_mod( 'h1_google_font', 'Open Sans' ) ) { $h1style .= 'font-family:'.get_theme_mod( 'h1_google_font', 'Open Sans' ).';'; }
        if ( get_theme_mod( 'h1_font_weight', '700' ) ) { $h1style .= 'font-weight: '.get_theme_mod( 'h1_font_weight', '700' ).';'; }
        if ( get_theme_mod('h1_font_height', '42') ) { $h1style .= 'line-height: '.get_theme_mod('h1_font_height', '42').'px;'; }
        if ( get_theme_mod('h1_font_color', '#1f2949') ) { $h1style .= 'color: '.get_theme_mod('h1_font_color', '#1f2949').';'; }
        
        # heading2
        $h2style = '';
        if ( get_theme_mod( 'h2_font_size', '30' ) ) { $h2style .= 'font-size:'.get_theme_mod( 'h2_font_size', '30' ).'px;'; }
        if ( get_theme_mod( 'h2_google_font', 'Open Sans' ) ) { $h2style .= 'font-family:'.get_theme_mod( 'h2_google_font', 'Open Sans' ).';'; }
        if ( get_theme_mod( 'h2_font_weight', '600' ) ) { $h2style .= 'font-weight: '.get_theme_mod( 'h2_font_weight', '600' ).';'; }
        if ( get_theme_mod('h2_font_height', '36') ) { $h2style .= 'line-height: '.get_theme_mod('h2_font_height', '36').'px;'; }
        if ( get_theme_mod('h2_font_color', '#1f2949') ) { $h2style .= 'color: '.get_theme_mod('h2_font_color', '#1f2949').';'; }
        
        //heading3
        $h3style = '';
        if ( get_theme_mod( 'h3_font_size', '24' ) ) { $h3style .= 'font-size:'.get_theme_mod( 'h3_font_size', '24' ).'px;'; }
        if ( get_theme_mod( 'h3_google_font', 'Open Sans' ) ) { $h3style .= 'font-family:'.get_theme_mod( 'h3_google_font', 'Open Sans' ).';'; }
        if ( get_theme_mod( 'h3_font_weight', '400' ) ) { $h3style .= 'font-weight: '.get_theme_mod( 'h3_font_weight', '400' ).';'; }
        if ( get_theme_mod('h3_font_height', '28') ) { $h3style .= 'line-height: '.get_theme_mod('h3_font_height', '28').'px;'; }
        if ( get_theme_mod('h3_font_color', '#1f2949') ) { $h3style .= 'color: '.get_theme_mod('h3_font_color', '#1f2949').';'; }
        
        //heading4
        $h4style = '';
        if ( get_theme_mod( 'h4_font_size', '18' ) ) { $h4style .= 'font-size:'.get_theme_mod( 'h4_font_size', '18' ).'px;'; }
        if ( get_theme_mod( 'h4_google_font', 'Open Sans' ) ) { $h4style .= 'font-family:'.get_theme_mod( 'h4_google_font', 'Open Sans' ).';'; }
        if ( get_theme_mod( 'h4_font_weight', '600' ) ) { $h4style .= 'font-weight: '.get_theme_mod( 'h4_font_weight', '600' ).';'; }
        if ( get_theme_mod('h4_font_height', '26') ) { $h4style .= 'line-height: '.get_theme_mod('h4_font_height', '26').'px;'; }
        if ( get_theme_mod('h4_font_color', '#1f2949') ) { $h4style .= 'color: '.get_theme_mod('h4_font_color', '#1f2949').';'; }
        
        //heading5
        $h5style = '';
        if ( get_theme_mod( 'h5_font_size', '14' ) ) { $h5style .= 'font-size:'.get_theme_mod( 'h5_font_size', '14' ).'px;'; }
        if ( get_theme_mod( 'h5_google_font', 'Open Sans' ) ) { $h5style .= 'font-family:'.get_theme_mod( 'h5_google_font', 'Open Sans' ).';'; }
        if ( get_theme_mod( 'h5_font_weight', '600' ) ) { $h5style .= 'font-weight: '.get_theme_mod( 'h5_font_weight', '600' ).';'; }
        if ( get_theme_mod('h5_font_height', '26') ) { $h5style .= 'line-height: '.get_theme_mod('h5_font_height', '26').'px;'; }
        if ( get_theme_mod('h5_font_color', '#1f2949') ) { $h5style .= 'color: '.get_theme_mod('h5_font_color', '#1f2949').';'; }
        
        // CSS Color
        $major_color = get_theme_mod( 'major_color', '#ff5248' );
        if($major_color){
            $skillate_pro_backend_output .= 'a, .tutor-course-topics-contents .tutor-course-title h4 i { color: '. esc_attr($major_color) .'; }';
        }
        if($major_color){
            $skillate_pro_backend_output .= '.tutor-course-topics-contents .tutor-course-title h4 i { border-color: '. esc_attr($major_color) .'; }';
        }
        // CSS Color
        $hover_color = get_theme_mod( 'hover_color', '#1f2949' );
        if($hover_color){
            $skillate_pro_backend_output .= 'a:hover { color: '. esc_attr($hover_color) .'; }';
        }

        $skillate_pro_backend_output .= '.editor-block-list__block, .editor-post-title__block .editor-post-title__input{'.$bstyle.'}';
        $skillate_pro_backend_output .= '.edit-post-visual-editor .editor-block-list__block h1{'.$h1style.'}';
        $skillate_pro_backend_output .= '.edit-post-visual-editor .editor-block-list__block h2{'.$h2style.'}';
        $skillate_pro_backend_output .= '.edit-post-visual-editor .editor-block-list__block h3{'.$h3style.'}';
        $skillate_pro_backend_output .= '.edit-post-visual-editor .editor-block-list__block h4{'.$h4style.'}';
        $skillate_pro_backend_output .= '.edit-post-visual-editor .editor-block-list__block h5{'.$h5style.'}';
        $skillate_pro_backend_output .= '.edit-post-visual-editor p.wp-block-paragraph{'.$pstyle.'}';

        return $skillate_pro_backend_output;
    }
}
