<?php
/* -------------------------------------------- *
 * skillate Widget
 * -------------------------------------------- */
if(!function_exists('skillate_widdget_init')):
    function skillate_widdget_init() {
        register_sidebar(array(
                'name'          => esc_html__( 'Skillate Gamipress Widget Area', 'skillate' ),
                'id'            => 'skillate_gamipress_widget',
                'description'   => esc_html__( 'Widgets in this area will be shown on Instructor single page.', 'skillate' ),
                'before_title'  => '<h3 class="widget_title">',
                'after_title'   => '</h3>',
                'before_widget' => '<div id="%1$s" class=" %2$s" >',
                'after_widget'  => '</div>'
            )
        );
        register_sidebar(array(
                'name'          => esc_html__( 'Sidebar', 'skillate' ),
                'id'            => 'sidebar',
                'description'   => esc_html__( 'Widgets in this area will be shown on Sidebar.', 'skillate' ),
                'before_title'  => '<h3 class="widget_title">',
                'after_title'   => '</h3>',
                'before_widget' => '<div id="%1$s" class="widget %2$s" >',
                'after_widget'  => '</div>'
            )
        );
        register_sidebar(array(
                'name'          => esc_html__( 'Client Logo Widget', 'skillate' ),
                'id'            => 'footer_top',
                'description'   => esc_html__( 'Widgets in this area will be shown before Footer Top.' , 'skillate'),
                'before_title'  => '<h3 class="widget-title">',
                'after_title'   => '</h3>',
                'before_widget' => '<div class="client-logo-single-widget"><div id="%1$s" class="widget %2$s" >',
                'after_widget'  => '</div></div>'
            )
        );
        register_sidebar(array(
                'name'          => esc_html__( 'Footer Payment Method', 'skillate' ),
                'id'            => 'footer_bottom',
                'description'   => esc_html__( 'Widgets in this area will be shown before Footer Bottom Right.' , 'skillate'),
                'before_title'  => '<h5 class="widget-title">',
                'after_title'   => '</h5>',
                'before_widget' => '<div class="payment-method-widget d-inline-block ml-lg-4 ml-2"><div id="%1$s" class="widget %2$s" >',
                'after_widget'  => '</div></div>'
            )
        );
        register_sidebar(array(
                'name'          => esc_html__( 'Bottom 1', 'skillate' ),
                'id'            => 'bottom1',
                'description'   => esc_html__( 'Widgets in this area will be shown before Bottom 1.' , 'skillate'),
                'before_title'  => '<h3 class="widget-title">',
                'after_title'   => '</h3>',
                'before_widget' => '<div class="bottom-widget"><div id="%1$s" class="widget %2$s" >',
                'after_widget'  => '</div></div>'
            )
        );
        register_sidebar(array(
            'name'          => esc_html__( 'Bottom 2', 'skillate' ),
            'id'            => 'bottom2',
            'description'   => esc_html__( 'Widgets in this area will be shown before Bottom 2.' , 'skillate'),
            'before_title'  => '<h3 class="widget-title">',
            'after_title'   => '</h3>',
            'before_widget' => '<div class="bottom-widget"><div id="%1$s" class="widget %2$s" >',
            'after_widget'  => '</div></div>'
            )
        );
        register_sidebar(array(
            'name'          => esc_html__( 'Bottom 3', 'skillate' ),
            'id'            => 'bottom3',
            'description'   => esc_html__( 'Widgets in this area will be shown before Bottom 3.' , 'skillate'),
            'before_title'  => '<h3 class="widget-title">',
            'after_title'   => '</h3>',
            'before_widget' => '<div class="bottom-widget"><div id="%1$s" class="widget %2$s" >',
            'after_widget'  => '</div></div>'
            )
        );
        register_sidebar(array(
            'name'          => esc_html__( 'Bottom 4', 'skillate' ),
            'id'            => 'bottom4',
            'description'   => esc_html__( 'Widgets in this area will be shown before Bottom 4.' , 'skillate'),
            'before_title'  => '<h3 class="widget-title">',
            'after_title'   => '</h3>',
            'before_widget' => '<div class="bottom-widget"><div id="%1$s" class="widget %2$s" >',
            'after_widget'  => '</div></div>'
            )
        );
    }
    add_action('widgets_init','skillate_widdget_init');

endif;


# Google Font
if ( ! function_exists( 'skillate_fonts_url' ) ) :
    function skillate_fonts_url() {
    $fonts_url = '';

    $open_sans = _x( 'on', 'Open Sans font: on or off', 'skillate' );
     
    if ( 'off' !== $open_sans ) {
    $font_families = array();
     
    if ( 'off' !== $open_sans ) {
    $font_families[] = 'Open Sans:300,400,600,700,800';
    }
     
    $query_args = array(
    'family'  => urlencode( implode( '|', $font_families ) ),
    'subset'  => urlencode( 'latin' ),
    );
     
    $fonts_url = add_query_arg( $query_args, '//fonts.googleapis.com/css' );
    }
     
    return esc_url_raw( $fonts_url );
    }
endif;


/* -------------------------------------------- *
 * skillate Style
 * -------------------------------------------- */
if(!function_exists('skillate_style')):

    function skillate_style(){
        wp_enqueue_style( 'default-google-font', '//fonts.googleapis.com/css?family=Montserrat:100,200,300,400,500,600,700' );

        wp_enqueue_style( 'skillate-font', skillate_fonts_url(), array(), null );
        wp_enqueue_media(); 
        # CSS
        if(is_rtl()) {
            wp_enqueue_style( 'bootstrap-rtl', SKILLATE_CSS . 'bootstrap.rtl.min.css',false,'all');
        }else{
            wp_enqueue_style( 'bootstrap-min', SKILLATE_CSS . 'bootstrap.min.css',false,'all');
        }
        //wp_enqueue_style( 'bootstrap.min', SKILLATE_CSS . 'bootstrap.min.css',false,'all');
        wp_enqueue_style( 'slick-css', SKILLATE_CSS . 'slick.css',false,'all');
        wp_enqueue_style( 'custom-css', SKILLATE_CSS . 'custom-style.css',false,'all');
        wp_enqueue_style( 'nice-select', SKILLATE_CSS . 'nice-select.css',false,'all');
        wp_enqueue_style( 'fontawesome.min', SKILLATE_CSS . 'fontawesome.min.css',false,'all');
        wp_enqueue_style( 'skillate-main', SKILLATE_CSS . 'main.css',false,'all');
        wp_enqueue_style( 'skillate-style',get_stylesheet_uri());
        wp_enqueue_style( 'skillate-responsive', SKILLATE_CSS . 'responsive.css',false,'all');
        wp_add_inline_style( 'skillate-style', skillate_css_generator() );
        # JS
        wp_enqueue_script('prettySocial',SKILLATE_JS.'jquery.prettySocial.min.js',array(),false,true);
        wp_enqueue_script('tether','https://npmcdn.com/tether@1.2.4/dist/js/tether.min.js',array(),false,true);
        wp_enqueue_script('bootstrap',SKILLATE_JS.'bootstrap.min.js',array(),false,true);
        wp_enqueue_script('slick-js',SKILLATE_JS.'slick.min.js',array(),false,true);
        wp_enqueue_script('nice-select-js',SKILLATE_JS.'jquery.nice-select.min.js',array(),false,true);
        wp_enqueue_script('loopcounter',SKILLATE_JS.'loopcounter.js',array(),false,true);
        wp_enqueue_script('skillate_main',SKILLATE_JS.'main.js',array(),false,true);


        // For Ajax URL
        global $wp;
        wp_localize_script( 'skillate_main', 'ajax_object', array(
            'ajaxurl'           => admin_url( 'admin-ajax.php' ),
            'redirecturl'       => home_url($wp->request),
            'home_url'           => home_url(),
            'loadingmessage'    => __('Sending user info, please wait...','skillate')
        ));

        # Single Comments
        if ( is_singular() ) { wp_enqueue_script( 'comment-reply' ); }

    }
    add_action('wp_enqueue_scripts','skillate_style');

endif;


function skillate_customize_control_js() {
    wp_enqueue_script( 'thmc-customizer', SKILLATE_URI.'lib/customizer/assets/js/customizer.js', array('jquery', 'jquery-ui-datepicker'), '1.0', true );
}
add_action( 'customize_controls_enqueue_scripts', 'skillate_customize_control_js' );

add_action('enqueue_block_editor_assets', 'skillate_action_enqueue_block_editor_assets');
function skillate_action_enqueue_block_editor_assets() {
    wp_enqueue_style( 'bootstrap-grid.min', SKILLATE_CSS . 'bootstrap-grid.min.css',false,'all');
    wp_enqueue_style( 'skillate-style', get_stylesheet_uri() );
    wp_enqueue_style( 'skillate-gutenberg-editor-styles', get_template_directory_uri() . '/css/style-editor.css', null, 'all' );
    wp_add_inline_style( 'skillate-style', skillate_css_backend_generator() );
}

/* -------------------------------------------- *
 * TGM for Plugin activation
 * -------------------------------------------- */
add_action( 'tgmpa_register', 'skillate_plugins_include');

if(!function_exists('skillate_plugins_include')):

    function skillate_plugins_include()
    {
        $plugins = array(
                array(
                    'name'                  => esc_html__( 'Tutor - Ultimate WordPress LMS plugin', 'skillate' ),
                    'slug'                  => 'tutor',
                    'required'              => true,
                    'version'               => '',
                    'force_activation'      => false,
                    'force_deactivation'    => false,
                    'external_url'          => esc_url('https://downloads.wordpress.org/plugin/tutor.zip'),
                ),
                array(
                    'name'                  => esc_html__( 'Qubely', 'skillate' ),
                    'slug'                  => 'qubely',
                    'required'              => true,
                    'version'               => '',
                    'force_activation'      => false,
                    'force_deactivation'    => false,
                    'external_url'          => esc_url('https://downloads.wordpress.org/plugin/qubely.zip'),
                ),
                array(
                    'name'                  => esc_html__( 'Skillate Core', 'skillate' ),
                    'slug'                  => 'skillate-core',
                    'source'                => get_template_directory_uri() . '/lib/plugins/skillate-core.zip',
                    'required'              => true,
                    'version'               => '1.1.3',
                    'force_activation'      => false,
                    'force_deactivation'    => false,
                ),
                array(
                    'name'                  => esc_html__( 'Skillate Demo Importer', 'skillate' ),
                    'slug'                  => 'skillate-demo-importer',
                    'source'                => get_template_directory_uri() . '/lib/plugins/skillate-demo-importer.zip',
                    'required'              => false,
                    'version'               => '',
                    'force_activation'      => false,
                    'force_deactivation'    => false,
                ),
                array(
                    'name'                  => esc_html__( 'MailChimp for WordPress', 'skillate' ),
                    'slug'                  => 'mailchimp-for-wp',
                    'required'              => false,
                ),
                array(
                    'name'                  => esc_html__( 'GamiPress', 'skillate' ),
                    'slug'                  => 'gamipress',
                    'required'              => false,
                    'version'               => '',
                    'force_activation'      => false,
                    'force_deactivation'    => false,
                    'external_url'          => esc_url('https://downloads.wordpress.org/plugin/gamipress.1.8.0.1.zip'),
                ),
                array(
                    'name'                  => esc_html__( 'Paid Memberships Pro', 'skillate' ),
                    'slug'                  => 'paid-memberships-pro',
                    'required'              => false,
                    'version'               => '',
                    'force_activation'      => false,
                    'force_deactivation'    => false,
                    'external_url'          => esc_url('https://downloads.wordpress.org/plugin/paid-memberships-pro.2.3.4.zip'),
                ),
                array(
                    'name'                  => esc_html__( 'GamiPress', 'skillate' ),
                    'slug'                  => 'gamipress',
                    'required'              => false,
                    'version'               => '',
                    'force_activation'      => false,
                    'force_deactivation'    => false,
                    'external_url'          => esc_url('https://downloads.wordpress.org/plugin/gamipress.1.8.0.1.zip'),
                ),
                array(
                    'name'                  => esc_html__( 'GamiPress – Tutor LMS integration', 'skillate' ),
                    'slug'                  => 'gamipress-tutor-integration',
                    'required'              => false,
                    'version'               => '',
                    'force_activation'      => false,
                    'force_deactivation'    => false,
                    'external_url'          => esc_url('https://downloads.wordpress.org/plugin/gamipress-tutor-integration.1.0.2.zip'),
                ),
                array(
                    'name'                  => 'WooCoommerce',
                    'slug'                  => 'woocommerce',
                    'required'              => false,
                    'version'               => '',
                    'force_activation'      => false,
                    'force_deactivation'    => false,
                ),
   
            );
            $config = array(
                    'domain'            => 'skillate',
                    'default_path'      => '',
                    'menu'              => 'install-required-plugins',
                    'has_notices'       => true,
                    'dismissable'       => true, 
                    'dismiss_msg'       => '', 
                    'is_automatic'      => false,
                    'message'           => ''
            );
    tgmpa( $plugins, $config );
    }

endif;
