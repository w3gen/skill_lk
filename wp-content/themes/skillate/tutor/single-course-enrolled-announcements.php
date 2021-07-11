<?php
/**
 * Template for displaying single course
 *
 * @since v.1.0.0
 *
 * @author Themeum
 * @url https://themeum.com
 */

get_header();

$course_details_rating = get_theme_mod('course_details_rating', true);

do_action('tutor_course/single/enrolled/before/wrap');
?>

<div <?php tutor_post_class('tutor-single-anouncement-wrap tutor-page-wrap'); ?>>
    <div class="container course-single-title-top">
        <div class="row">
            <div class="col-md-8">
                <h1 class="tutor-course-header-h1"><?php the_title(); ?></h1>
            </div>
            <div class="col-md-4 ml-auto text-md-right">
                <div class="course-single-price">
                    <?php tutor_course_price(); ?>
                </div>
                <?php
                if($course_details_rating) { ?>
                    <div class="tutor-single-course-rating">
                        <?php
                        $skillate_course_rating = tutor_utils()->get_course_rating();
                        tutor_utils()->star_rating_generator($skillate_course_rating->rating_avg);
                        ?>
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
                <?php } ?>
            </div>
        </div>
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
            <?php }}?>

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
                <div class="course-attribute-single">  
                    <span><?php echo esc_html__('Video Tutorials', 'skillate'); ?></span>
                    <h3><?php echo esc_html($skillate_pro_tutor_lesson_count);?></h3>
                </div>
            </div>
            <?php } ?>
            
            <div class="col-lg-6 col-sm-12 ml-auto text-left text-lg-right mt-lg-0 mt-3">
                <?php
                    $idd = get_the_ID();
                    $is_wishlisted = tutor_utils()->is_wishlisted($idd);
                    $has_wish_list = '';
                    if ($is_wishlisted){
                        $has_wish_list = 'has-wish-listed';
                    }
                ?>

                <div class="skillate-course-cart-btn">
                    <?php
                    if ( $wp_query->query['post_type'] !== 'lesson') {
                        $lesson_url = tutor_utils()->get_course_first_lesson();
                        $completed_lessons = tutor_utils()->get_completed_lesson_count_by_course();
                        if ( $lesson_url ) { ?>
                            <a href="<?php echo esc_url($lesson_url); ?>" class="tutor-button tutor-success">
                                <?php
                                    if($completed_lessons){
                                        esc_attr_e( 'Continue to lesson', 'skillate' );
                                    }else{
                                        esc_attr_e( 'Start Course', 'skillate' );
                                    }
                                ?>
                            </a>
                        <?php }
                    }
                    ?>
                    <?php tutor_course_mark_complete_html(); ?>
                    <?php do_action('tutor_enrolled_box_after') ?>
                </div>
            </div>
        </div>
    </div>

    <div class="tutor-container">
        <div class="row">  
            <div class="col-lg-12">
                <div class="tutor-price-thumbnail">
                    <?php
                    if(tutor_utils()->has_video_in_single()){
                        tutor_course_video();
                    } else{
                        get_tutor_course_thumbnail();
                    }
                    ?>
                </div>
            </div>
        </div><!---->      

        <div class="tutor-row">
            <div class="tutor-col-8  tutor-col-md-100">
                <?php do_action('tutor_course/single/enrolled/before/inner-wrap'); ?>


                    <div class="tutor-course-status">
                        <?php $completed_count = tutor_utils()->get_course_completed_percent(); ?>
                        <h4 class="tutor-segment-title"><?php esc_attr_e('Course Status', 'skillate'); ?></h4>
                        <div class="tutor-progress-bar-wrap">
                            <div class="tutor-progress-bar">
                                <div class="tutor-progress-filled" style="--tutor-progress-left: <?php echo esc_attr($completed_count).'%;'; ?>"></div>
                            </div>
                            <span class="tutor-progress-percent"><?php echo esc_attr($completed_count); ?>% <?php esc_attr_e(' Complete', 'skillate')?></span>
                        </div>
                    </div>


                <?php //tutor_course_enrolled_lead_info(); ?>
                <?php tutor_course_enrolled_nav(); ?>
                <?php tutor_course_announcements(); ?>
                <?php do_action('tutor_course/single/enrolled/after/inner-wrap'); ?>
            </div>
            <div class="tutor-col-4">
                <?php do_action('tutor_course/single/enrolled/before/sidebar'); ?>
                <div class="skillate-single-course-sidebar">
                    <div class="course-single-price">
                        <?php tutor_course_price(); ?>
                    </div>
                    <?php tutor_course_material_includes_html(); ?>
                        <h4 class="course-single-sidebar-title"><?php esc_html_e('Share', 'skillate') ?></h4>
                        <?php tutor_social_share(); ?>
                </div>
                <?php do_action('tutor_course/single/enrolled/after/sidebar'); ?>
            </div>
        </div>
    </div>
</div>
<?php
do_action('tutor_course/single/enrolled/after/wrap');
$related_course_slider = get_theme_mod('related_course_slider', true);
if($related_course_slider) {
    get_template_part( 'lib/single-related-post' );
}
get_footer();
