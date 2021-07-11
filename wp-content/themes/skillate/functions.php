<?php
define( 'SKILLATE_CSS', get_template_directory_uri().'/css/' );
define( 'SKILLATE_JS', get_template_directory_uri().'/js/' );
define( 'SKILLATE_DIR', get_template_directory() );
define( 'SKILLATE_URI', trailingslashit(get_template_directory_uri()) );


/* -------------------------------------------- *
 * Include TGM Plugins
 * -------------------------------------------- */
get_template_part('lib/class-tgm-plugin-activation');


/* -------------------------------------------- *
* Navwalker
* -------------------------------------------- */
get_template_part('lib/menu/mobile-navwalker');


/* -------------------------------------------- *
 * skillate Register
 * -------------------------------------------- */
get_template_part('lib/main-function/skillate-register');

/* -------------------------------------------- *
 * skillate Core
 * -------------------------------------------- */
get_template_part('lib/main-function/skillate-core');
get_template_part('woocommerce/skillate-color-variations');


// Comments
include( get_parent_theme_file_path('lib/skillate_comment.php') );

// Comments Callback Function
include( get_parent_theme_file_path('lib/skillate-comments.php') );

// Archive Courses
include( get_parent_theme_file_path('lib/shortcode/course-archive.php') );

//Membership Pro Sign Up Shortcode
if (function_exists('pmpro_hasMembershipLevel')) {
    get_template_part('lib/shortcode/pmpro/pmpro-advanced-levels-shortcode');
}

/* -------------------------------------------- *
 * Customizer
 * -------------------------------------------- */
get_template_part('lib/customizer/libs/googlefonts');
get_template_part('lib/customizer/customizer');

/* -------------------------------------------- *
 * Custom Excerpt Length
 * -------------------------------------------- */
if(!function_exists('skillate_excerpt_max_charlength')):
	function skillate_excerpt_max_charlength($charlength) {
		$excerpt = get_the_excerpt();
		$charlength++;

		if ( mb_strlen( $excerpt ) > $charlength ) {
			$subex = mb_substr( $excerpt, 0, $charlength - 5 );
			$exwords = explode( ' ', $subex );
			$excut = - ( mb_strlen( $exwords[ count( $exwords ) - 1 ] ) );
			if ( $excut < 0 ) {
				return mb_substr( $subex, 0, $excut );
			} else {
				return $subex;
			}
		} else {
			return $excerpt;
		}
	}
endif;

/* ------------------------------------------ *
*				WooCommerce support
* ------------------------------------------- */
function skillate_woocommerce_support() {
    add_theme_support( 'woocommerce' );
}
add_action( 'after_setup_theme', 'skillate_woocommerce_support' );

/* -------------------------------------------- *
 * Custom body class
 * -------------------------------------------- */
add_filter( 'body_class', 'skillate_body_class' );
function skillate_body_class( $classes ) {
    $layout = get_theme_mod( 'boxfull_en', 'fullwidth' );
    $classes[] = $layout.'-bg'.' body-content';
	return $classes;
}

/* ------------------------------------------- *
 * Add a pingback url auto-discovery header for 
 * single posts, pages, or attachments
 * ------------------------------------------- */
function skillate_pingback_header() {
	if ( is_singular() && pings_open() ) {
		echo '<link rel="pingback" href="', esc_url( get_bloginfo( 'pingback_url' ) ), '">';
	}
}
add_action( 'wp_head', 'skillate_pingback_header' );


/* -------------------------------------------
*   show header cart
* -------------------------------------------- */

if ( ! function_exists( 'skillate_header_cart' ) ) {
    function skillate_header_cart() {?>
		<div id="site-header-cart" class="site-header-cart menu">
            <span class="cart-icon">
				<img src="<?php echo get_template_directory_uri(); ?>/images/cart-icon.svg" alt="">	
				<a class="cart-contents" data-toggle="modal" href="#modal-cart" title="<?php esc_attr_e( 'View your shopping cart', 'skillate' ); ?>">
                <span class="count"><?php echo wp_kses_data( sprintf( _n( '%d', '%d', WC()->cart->get_cart_contents_count(), 'skillate' ), WC()->cart->get_cart_contents_count() ) );?></span>
                </a>
           </span>
		</div>
        <?php
    }
}

# Cart Fragments
add_filter( 'woocommerce_add_to_cart_fragments', 'skillate_cart_link_fragment' );
if ( ! function_exists( 'skillate_cart_link_fragment' ) ) {
    function skillate_cart_link_fragment( $fragments ) {
        global $woocommerce;
        ob_start(); ?>

		<a class="cart-contents" data-toggle="modal" href="#modal-cart" title="<?php esc_attr_e( 'View your shopping cart', 'skillate' ); ?>">
            <span class="count">
            	<?php echo wp_kses_data( sprintf( _n( '%d', '%d', WC()->cart->get_cart_contents_count(), 'skillate' ), WC()->cart->get_cart_contents_count() ) );?>	
            </span>
        </a>
          
        <?php
        $fragments['a.cart-contents'] = ob_get_clean();
        return $fragments;
    }
}


# Course cart item permalink
if(function_exists('tutor_utils')){
    if(!function_exists('skillate_shop_redirect')){
        function skillate_shop_redirect(){
            return get_post_type_archive_link(tutor()->course_post_type);
        }
    }
    add_filter('woocommerce_return_to_shop_redirect', 'skillate_shop_redirect');


    function skillate_cart_permalink($permalink, $item, $key){
        $course = tutor_utils()->product_belongs_with_course($item['product_id']);
        if($course){
            return get_permalink($course->post_id);
        }
        return $permalink;
    }
    add_filter('woocommerce_cart_item_permalink', 'skillate_cart_permalink', 10, 3);
}

/* -------------------------------------------
*           License for Skillate Theme
* -------------------------------------------- */
get_template_part('lib/license/class.skillate-theme-license');

/* -------------------------------------------
*        		Licence Code
* ------------------------------------------- */
add_action('admin_menu', 'skillate_options_menu');
if ( ! function_exists('skillate_options_menu')){
  function skillate_options_menu(){
    $personalblog_option_page = add_menu_page('Skillate Options', 'Skillate Options', 'manage_options', 'skillate-options', 'skillate_option_callback');
    add_action('load-'.$personalblog_option_page, 'skillate_option_page_check');
  }
}

function skillate_option_callback(){}
function skillate_option_page_check(){
	global $current_screen;
	if ($current_screen->id === 'toplevel_page_skillate-options'){
		wp_redirect(admin_url('customize.php'));
	}
}


/* -------------------------------------------
*        Woocommerce product single zoom
* ------------------------------------------- */
add_action( 'after_setup_theme', 'skillate_woo_lightbox_support' );
function skillate_woo_lightbox_support() {
    add_theme_support( 'wc-product-gallery-zoom');
    add_theme_support( 'wc-product-gallery-lightbox' );
    add_theme_support( 'wc-product-gallery-slider' );
}

