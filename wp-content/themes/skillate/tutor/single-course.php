<?php
/**
 * Template for displaying single course
 *
 * @since v.1.0.0
 *
 * @author Themeum
 * @url https://themeum.com
 */
?>
<?php
$idd = get_the_ID();
get_header();
$course_details_best_sell_tag = get_theme_mod('course_details_best_sell_tag', true);
$course_details_rating = get_theme_mod('course_details_rating', true);
$single_course_tab_sticky_menu = get_theme_mod('single_course_tab_sticky_menu', true);
$max_new_post = get_theme_mod('new_course_count', 5);
$total_posts = get_posts(
    array(
     'numberposts'  => $max_new_post,
     'post_status'  => 'publish',
     'post_type'    => 'courses',
    )
);
$post_array = array();
foreach($total_posts as $total_post){
    $post_array [] += $total_post->ID;
}
?>

<?php do_action('tutor_course/single/before/wrap'); ?>
<div <?php tutor_post_class('tutor-full-width-course-top tutor-course-top-info tutor-page-wrap'); ?>>
    <div class="course-single-mobile d-lg-none">
        <div class="container">
            <div class="row">
                <div class="col-10">
                    <div class="tutor-single-course-rating d-block">
                        <?php
                            $skillate_course_rating = tutor_utils()->get_course_rating();
                            tutor_utils()->star_rating_generator($skillate_course_rating->rating_avg);
                            $product_id = tutor_utils()->get_course_product_id();
                            if ( class_exists( 'woocommerce' )){
                                $product = wc_get_product( $product_id );
                            } ?>
                            <p class="tutor-single-rating-count d-inline-block">
                                ( <span><?php echo esc_attr($skillate_course_rating->rating_count); ?></span>
                                <?php 
                                if( $skillate_course_rating->rating_count > 1){
                                        echo esc_html__('Reviews', 'skillate'); 
                                    } else{
                                        echo esc_html__('Review', 'skillate');  
                                    }
                                ?> )
                            </p>
                    </div>
                </div>
                <div class="col-2 text-right">
                    <?php 
                        $is_wishlisted = tutor_utils()->is_wishlisted($idd);
                        $has_wish_list = '';
                        if ($is_wishlisted){
                            $has_wish_list = 'has-wish-listed';
                        }
                        echo '<span class="tutor-course-wishlist"><a href="javascript:;" class="tutor-icon-fav-line tutor-course-wishlist-btn '.$has_wish_list.' " data-course-id="'.$idd.'"></a> </span>'; 
                    ?>
                </div>
            </div>
        </div>
    </div>
    <div class="container course-single-title-top mb-sm-2">
        <div class="row">
            <div class="col-md-8 col-11 mb-sm-2">
                <?php 
                if($course_details_best_sell_tag) :
                    $best_selling = get_post_meta(get_the_ID(), 'skillate_best_selling', true); 
                    if($best_selling == !false) {?>
                    <span class="best-sell-tag d-none d-lg-block">
                        <?php echo esc_html__('Featured', 'skillate'); ?>
                    </span>
                    <?php }else if(get_the_ID() == in_array(get_the_ID(), $post_array)){?>
                    <span class="best-sell-tag new-tag d-none d-lg-block">
                        <?php echo esc_html__('New', 'skillate'); ?>
                    </span>
                <?php }
                endif;
                ?>
                <h1 class="tutor-course-header-h1">
                    <?php the_title(); ?>
                </h1>
            </div>
            <div class="col-md-4 ml-auto text-md-right d-none d-lg-block">
                <div class="course-single-price mt-4">
                    <?php tutor_course_price();?>
                </div>
                <?php 
                if($course_details_rating) :
                ?>
                <div class="tutor-single-course-rating d-sm-inline-block">
                    <?php
                    $skillate_course_rating = tutor_utils()->get_course_rating();
                    tutor_utils()->star_rating_generator($skillate_course_rating->rating_avg);
                    $product_id = tutor_utils()->get_course_product_id();
                    if ( class_exists( 'woocommerce' )){
                        $product = wc_get_product( $product_id );
                    }?>
                    <p class="tutor-single-rating-count">
                        ( <span><?php echo esc_attr($skillate_course_rating->rating_count); ?></span>
                        <?php 
                        if( $skillate_course_rating->rating_count > 1){
                                echo esc_html__('Reviews', 'skillate'); 
                            } else{
                                echo esc_html__('Review', 'skillate');  
                            }
                        ?> )
                    </p>
                </div>
                <?php endif; ?>
            </div>
        </div>
    </div>
    <div class="container">
        <?php do_action('tutor_course/single/lead_meta/after'); ?>
    </div>
    <div class="container course-single-attribute">
        <div class="row align-items-lg-center">
            <?php if(!empty(get_tutor_course_level())){ 
            $disable_course_level = get_tutor_option('disable_course_level');
            if ( !$disable_course_level){     
            ?>
            <div class="col-lg-2 col-4 mb-sm-0 mb-1">
                <div class="course-attribute-single">
                    <span><?php echo esc_html__('Course Level', 'skillate'); ?></span>
                    <h3><?php echo esc_html(get_tutor_course_level()); ?></h3>
                </div>
            </div>
            <?php } }?>

            <?php 
                $course_duration = get_tutor_course_duration_context();
                if( !empty($course_duration) ){ ?>
                <div class="col-lg-2 col-4 mb-sm-0 mb-1">
                    <div class="course-attribute-single">
                        <span><?php echo esc_html__('Total Hour', 'skillate'); ?></span>
                        <h3><?php echo $course_duration; ?></h3>
                    </div>
                </div>
            <?php } ?>
            
            <?php $skillate_pro_tutor_lesson_count = tutor_utils()->get_lesson_count_by_course(get_the_ID());
                if($skillate_pro_tutor_lesson_count) {?>
            <div class="col-lg-2 col-4 mb-sm-0 mb-1">
                <div class="course-attribute-single d-none d-lg-block">  
                    <span><?php echo esc_html__('Video Tutorials', 'skillate'); ?></span>
                    <h3><?php echo esc_html($skillate_pro_tutor_lesson_count);?></h3>
                </div>
                <div class="course-single-price d-lg-none">
                    <?php tutor_course_price(); ?>
                </div>
            </div>
            <?php } ?>
            
            <div class="col-lg-6 col-sm-12 ml-auto text-left text-lg-right mt-lg-0 mt-3">
                <?php
                    $is_wishlisted = tutor_utils()->is_wishlisted($idd);
                    $has_wish_list = '';
                    if ($is_wishlisted){
                        $has_wish_list = 'has-wish-listed';
                    }

                    $rcp_en_class = class_exists('RCP_Requirements_Check') ? 'rcp-exits' : '';

                ?>

                <div class="skillate-course-cart-btn d-none d-lg-block <?php echo esc_attr($rcp_en_class); ?>">
                    <?php
                        if(is_user_logged_in()){
                        echo '<span class="tutor-course-wishlist"><a href="javascript:;" class="tutor-icon-fav-line tutor-course-wishlist-btn '.$has_wish_list.' " data-course-id="'.$idd.'"></a> </span>';
                        }else{
                            echo '<span class="tutor-course-wishlist"><a class="tutor-icon-fav-line" data-toggle="modal" href="#modal-login"></a></span>';
                        } 
                    ?>
                    <?php 
                        $product_id = tutor_utils()->get_course_product_id();
                        if ( class_exists( 'woocommerce' )){
                            $product = wc_get_product( $product_id );
                        } 
                        if( ! class_exists( 'Easy_Digital_Downloads' ) && class_exists( 'woocommerce' ) && tutor_utils()->is_course_purchasable() ) { ?>
                        <form class="cart" action="<?php echo wc_get_checkout_url(); ?>"
                            method="post" enctype='multipart/form-data'>
                            <button type="submit" name="add-to-cart" value="<?php echo esc_attr( $product->get_id() ); ?>" class="course-buy-now"> <?php echo esc_html__('Buy Now', 'skillate'); ?>
                            </button>
                        </form>
                    <?php } ?>
                    <div class="d-inline-block float-right">
                        <?php tutor_single_course_add_to_cart(); ?>
                    </div>
                </div>
            </div>

        </div>

    </div>
    
    <div class="container tutor-course-preview-thumbnail">
        <?php do_action('tutor_course/single/before/inner-wrap'); 
        $thumb_url = wp_get_attachment_image_src(get_post_thumbnail_id(), 'skillate-large');
        ?>
        <style>
            .tutor-single-lesson-segment .plyr__poster:before{
                background: url(<?php echo esc_url($thumb_url[0]); ?>);
            }
        </style>
        <div class="row">
            <div class="col-sm-12">
                <?php
                    if(tutor_utils()->has_video_in_single()){
                        tutor_course_video();
                     } else{
                        get_tutor_course_thumbnail();
                    }
                ?>
            </div>
        </div>

        <div class="skillate-course-cart-btn d-md-none <?php echo esc_attr($rcp_en_class); ?>">
            <div class="row">
                <div class="col-6">
                <?php 
                $product_id = tutor_utils()->get_course_product_id();
                if ( class_exists( 'woocommerce' )){
                    $product = wc_get_product( $product_id );
                }
                if( ! class_exists( 'Easy_Digital_Downloads' ) && class_exists( 'woocommerce' ) && tutor_utils()->is_course_purchasable() ) { ?>
                    <form class="cart" action="<?php echo wc_get_checkout_url(); ?>"
                        method="post" enctype='multipart/form-data'>
                        <button type="submit" name="add-to-cart" value="<?php echo esc_attr( $product->get_id() ); ?>" class="course-buy-now"> <?php echo esc_html__('Buy Now', 'skillate'); ?>
                        </button>
                    </form>
                <?php } ?>
                
                </div>
                <div clas="col-6">
                    <?php tutor_single_course_add_to_cart(); ?>
                </div>
            </div>
        </div>
    </div>
    
    <div class="skillate-tab-menu-wrap" data-isSticky="<?php echo esc_attr($single_course_tab_sticky_menu); ?>">
        <div class="container">
            <ul class="nav nav-pills single-course-item-tab">
                <li class="nav-item tab current" data-tab="tab-1">
                    <a href="#tab-1"><?php echo esc_html__('course Topics Content', 'skillate'); ?></a>
                </li>
                <li class="nav-item tab" data-tab="tab-2">
                    <a class="course-content-tab-link" href="#tab-about">
                        <?php echo esc_html__('About Course', 'skillate'); ?>
                    </a>
                    <a class="course-content-tab-link" href="#tab-learn">
                        <?php echo esc_html__('What to learn', 'skillate'); ?>
                    </a>
                    
                    <a class="course-content-tab-link" href="#tab-requirement">
                        <?php echo esc_html__('Requirement', 'skillate'); ?>
                    </a>
                    
                    <a class="course-content-tab-link" href="#tab-audience">
                        <?php echo esc_html__('Target Audience', 'skillate'); ?>
                    </a>
                    
                    <a class="course-content-tab-link" href="#tab-instructor">
                        <?php echo esc_html__('Instructor', 'skillate'); ?>
                    </a>
                    <a class="course-content-tab-link" href="#tab-review">
                        <?php echo esc_html__('Review', 'skillate'); ?>
                    </a>
                </li>
            </ul>
        </div>
    </div>
    
    <div class="container">
        <div class="row">
            <div class="col-md-8">
                <div class="skillate-tab-content-wrap" >
                    <div id="tab-1" class="skillate-tab-content current">
                        <?php tutor_course_topics(); ?>
                    </div>
                    <div id="tab-2" class="skillate-tab-content">
                        <?php do_action('tutor_course/single/before/content');?>
                        <div id="tab-about" class="tutor-course-content">   
                            <?php tutor_course_content(); ?>
                        </div>
                        <?php do_action('tutor_course/single/after/content'); ?>

                        <div id="tab-learn" class="clearfix">
                            <h4 class="course-content-head"><?php echo esc_html__('What to learn?', 'skillate');?></h4>
                            <?php 
                                echo wp_kses_post($learn_content = get_post_meta($idd, '_tutor_course_benefits', true));
                                ?>
                            <?php //tutor_course_benefits_html(); ?>
                        </div>

                        <div id="tab-requirement">
                            <?php tutor_course_requirements_html(); ?>
                        </div>
                        <div id="tab-audience">
                            <?php tutor_course_target_audience_html(); ?>
                        </div>

                        <?php 
                         $display_course_instructors = tutor_utils()->get_option('display_course_instructors');
                        if($display_course_instructors == 1) { ?>
                            <div id="tab-instructor">
                                <?php do_action('tutor_course/single/enrolled/before/instructors');
                                $instructors = tutor_utils()->get_instructors_by_course();
                                if ($instructors){
                                    ?>
                                    <h4 class="tutor-segment-title"><?php _e('Instructor', 'skillate'); ?></h4>

                                    <div class="tutor-course-instructors-wrap tutor-single-course-segment" id="single-course-ratings">
                                        <?php
                                        foreach ($instructors as $instructor){
                                            $profile_url = tutor_utils()->profile_url($instructor->ID);
                                            ?>
                                            <div class="single-instructor-wrap">
                                                <div class="single-instructor-top">
                                                    <div class="tutor-instructor-left">
                                                        <div class="instructor-avatar">
                                                            <a href="<?php echo $profile_url; ?>">
                                                                <?php 
                                                                if(function_exists('tutor_utils')){
                                                                    echo tutor_utils()->get_tutor_avatar($instructor->ID, 'skillate-squre');
                                                                }else{
                                                                    $get_avatar_url = get_avatar_url($instructor->ID, 'skillate-squre');
                                                                    echo "<img alt='' src='$get_avatar_url' />";
                                                                }
                                                                ?>
                                                            </a>
                                                            
                                                            <div class="ratings">
                                                                <i class="fas fa-star"></i>
                                                                <?php
                                                                $instructor_rating = tutor_utils()->get_instructor_ratings($instructor->ID);
                                                                echo " <span class='rating-digits'>{$instructor_rating->rating_avg}</span> ";
                                                                echo " <span class='rating-total-meta'>".__('/5', 'skillate')."</span> ";
                                                                ?>
                                                            </div>
                                                        </div>
                                                    </div>
                                                    <div class="tutor-instructor-right">
                                                        <div class="instructor-name">
                                                            <h3>
                                                                <a href="<?php echo $profile_url; ?>">
                                                                    <?php echo $instructor->display_name; ?>
                                                                </a> 
                                                            </h3>
                                            
                                                        </div>
                                                        <div class="courses">
                                                            <p>
                                                                <?php echo tutor_utils()->get_course_count_by_instructor($instructor->ID); ?> <span class="tutor-text-mute"> <?php _e('Courses', 'skillate'); ?></span>
                                                            </p>
                                                        </div>
                                                        <div class="instructor-bio">
                                                            <?php echo skillate_limit_word($instructor->tutor_profile_bio, 40); ?>
                                                        </div>
                                                        <?php
                                                            $tutor_user_social_icons = tutor_utils()->tutor_user_social_icons();
                                                            if(count($tutor_user_social_icons)){
                                                                ?>
                                                                    <div class="single-tutor-social-icons">
                                                                        <?php
                                                                            $i=0;
                                                                            foreach ($tutor_user_social_icons as $key => $social_icon){
                                                                                $icon_url = get_user_meta($instructor->ID,$key,true);
                                                                                if($icon_url){
                                                                                    echo "<a href='".esc_url($icon_url)."' target='_blank' class='".$social_icon['icon_classes']."'></a>";
                                                                                }
                                                                                $i++;
                                                                            }
                                                                        ?>
                                                                    </div>
                                                                <?php
                                                            }
                                                        ?>
                                                    </div>
                                                </div>

                                            </div>
                                            <?php
                                        }
                                        ?>
                                    </div>
                                    <?php
                                }
                                do_action('tutor_course/single/enrolled/after/instructors');?>
                            </div>
                        <?php } ?>

                        <div id="tab-review">
                        <?php tutor_course_target_reviews_html(); ?>
                        <?php tutor_course_target_review_form_html(); ?>

                        <?php do_action('tutor_course/single/after/inner-wrap'); ?>
                        </div>

                    </div>
                </div>
            </div>
            <div class="col-md-4">
                <div class="skillate-single-course-sidebar">
                    <div class="course-single-price">
                        <?php tutor_course_price(); ?>
                    </div>
                    <?php tutor_course_material_includes_html(); ?>
                    <div class="skillate-course-cart-btn <?php echo esc_attr($rcp_en_class); ?>">
                        <?php 
                        $product_id = tutor_utils()->get_course_product_id();
                        if ( class_exists( 'woocommerce' )){
                            $product = wc_get_product( $product_id );
                        }
                        if( ! class_exists( 'Easy_Digital_Downloads' ) && class_exists( 'woocommerce' ) && tutor_utils()->is_course_purchasable() ) { ?>
                            <form class="cart" action="<?php echo wc_get_checkout_url(); ?>"
                                method="post" enctype='multipart/form-data'>
                                <button type="submit" name="add-to-cart" value="<?php echo esc_attr( $product->get_id() ); ?>" class="course-buy-now"> <?php echo esc_html__('Buy Now', 'skillate'); ?>
                                </button>
                            </form>
                        <?php } ?>
                        <?php tutor_single_course_add_to_cart(); ?>
                    </div>

                        <h4 class="course-single-sidebar-title"><?php esc_html_e('Share', 'skillate') ?></h4>
                        <?php tutor_social_share(); ?>

                </div>
            </div>
        </div>
    </div>
</div>

<?php 
//do_action('tutor_course/single/after/wrap');
$related_course_slider = get_theme_mod('related_course_slider', true);
if($related_course_slider) {
    get_template_part( 'lib/single-related-post' );
}
get_footer();
