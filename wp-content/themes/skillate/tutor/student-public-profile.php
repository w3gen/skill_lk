<?php
/**
 * Template for displaying student Public Profile
 *
 * @since v.1.0.0
 *
 * @author Themeum
 * @url https://themeum.com
 *
 * @package TutorLMS/Templates
 * @version 1.4.3
 */

get_header();

$user_name = sanitize_text_field(get_query_var('tutor_student_username'));
$sub_page = sanitize_text_field(get_query_var('profile_sub_page'));
$get_user = tutor_utils()->get_user_by_login($user_name);
$user_id = $get_user->ID;

$is_instructor = tutor_utils()->is_instructor($user_id);

$profile_layout = tutor_utils()->get_option(($is_instructor ? 'instructor' : 'student').'_public_profile_layout', 'pp-circle');
!$is_instructor ? $profile_layout='pp-circle' : 0; // For now

global $wp_query;

$profile_sub_page = '';
if (isset($wp_query->query_vars['profile_sub_page']) && $wp_query->query_vars['profile_sub_page']) {
    $profile_sub_page = $wp_query->query_vars['profile_sub_page'];
}
$top_bg = get_theme_mod('instructor_single_top', get_template_directory_uri().'/images/instructor_single.jpg');
$top_bg_tutor = tutor_utils()->get_cover_photo_url($user_id);

?>

<?php do_action('tutor_student/before/wrap'); ?>

    <div class="instructor-single-wrap">
        <?php if ( 'no-cp' === $profile_layout ) : ?>
            <div style="height: <?php echo wp_is_mobile() ? '80px' : '200px'; ?>; background-color: #333333"></div>
        <?php else : ?>
            <div class="instructor-single-top" style="background:url(<?php echo $top_bg_tutor ? $top_bg_tutor : $top_bg; ?>)"></div>
        <?php endif; ?>
        <div class="tutor-container">
            <div class="tutor-row instructor-single-content">
                <div class="tutor-col-3 col-sm-3">
                    <div class="instructor-single-avatar skillate-avatar-<?php echo $profile_layout; ?>">
                        <?php 
                            if(function_exists('tutor_utils')){
                                echo tutor_utils()->get_tutor_avatar($user_id, 'skillate-squre');
                            }else{
                                $get_avatar_url = get_avatar_url($user_id, 'skillate-squre');
                                echo "<img alt='' src='$get_avatar_url' />";
                            }
                        ?>
                    </div>
                    <?php
                    $tutor_user_social_icons = tutor_utils()->tutor_user_social_icons();
                    if(count($tutor_user_social_icons)){
                        ?>
                        <div class="instructor-single-social text-center">
                            <?php
                                $i=0;
                                foreach ($tutor_user_social_icons as $key => $social_icon){
                                    $icon_url = get_user_meta($user_id,$key,true);
                                    if($icon_url){
                                        if($i==0){
                                            ?>
                                            <?php
                                        }
                                        echo "<a href='".esc_url($icon_url)."' target='_blank' class='".$social_icon['icon_classes']."'></a>";
                                    }
                                    $i++;
                                }
                            ?>
                        </div>
                        <?php
                    }
                    ?>
                    <div class="sennd-msg-to-instructor">
                        <a class="d-block" href="mailto:<?php echo get_userdata( $user_id )->user_email; ?>">
                            <?php echo esc_html__('Send Message', 'skillate'); ?>
                        </a>
                    </div>
                    <?php 
                    if(get_theme_mod('instructor_batch', true) && class_exists('GamiPress')){ ?>
                    <div class="skillate-instructor-achivement">
                        <?php
                            if(get_theme_mod('gamipress_achievement', true)){
                                echo do_shortcode('[gamipress_achievements current_user="no" filter="no" search="no" limit="10" user_id="'.$user_id.'"]');
                            }
                        ?>
                        <?php if (is_active_sidebar('skillate_gamipress_widget')):?>                 
                            <div class="skillate-gamipress-widget-area">
                                <?php dynamic_sidebar('skillate_gamipress_widget'); ?>
                            </div>
                        <?php endif; ?>  
                    </div>
                    <?php } ?>
                </div>
                <div class="tutor-col-9 col-sm-9">
                    <div class="row align-items-md-center mt-md-0 mt-3">
                        <div class="col-sm-7 col-7">
                            <div class="instructor-single-name">
                                <h3><?php echo $get_user->display_name; ?></h3>
                            </div>
                            <div class="instructor-single-asset">
                                <span>
                                    <strong><?php echo tutor_utils()->get_course_count_by_instructor($user_id); ?></strong>
                                    <?php echo esc_html__('Courses', 'skillate'); ?>
                                </span>
                                <span>
                                    <?php echo tutor_utils()->get_total_students_by_instructor($user_id); ?>
                                    <?php echo esc_html__('Students', 'skillate'); ?>
                                </span>
                            </div>
                        </div>
                        <div class="col-sm-5 col-5">
                            <?php
                            if (user_can($user_id, tutor()->instructor_role)){
                                $instructor_rating = tutor_utils()->get_instructor_ratings($get_user->ID);
                                ?>
                                <div class="tutor-dashboard-header-stats">
                                    <div class="instructor-single-review">
                                        <?php tutor_utils()->star_rating_generator($instructor_rating->rating_avg); ?>
                                        <span> (<?php echo sprintf(__('%d Reviews', 'skillate'), $instructor_rating->rating_count); ?>) </span>
                                    </div>
                                </div>
                            <?php } ?>
                        </div>
                    </div>
                    <div class="instructor-single-course-content">
                        <?php
                        tutor_load_template('profile.bio');
                        tutor_load_template('profile.courses_taken');
                        ?>
                    </div>
                </div> <!-- .tutor-col-8 -->
            </div>

        </div> <!-- .tutor-row -->
    </div> <!-- .tutor-container -->
<?php do_action('tutor_student/after/wrap'); ?>

<?php
get_footer();
