<?php
/**
 * Template for displaying course reviews
 *
 * @since v.1.0.0
 *
 * @author Themeum
 * @url https://themeum.com
 *
 * @package TutorLMS/Templates
 * @version 1.4.5
 */


do_action('tutor_course/single/enrolled/before/reviews');

$disable = get_tutor_option('disable_course_review');
if ($disable){
    return;
}

$reviews = tutor_utils()->get_course_reviews();
if ( ! is_array($reviews) || ! count($reviews)){
	return;
}
?>

<div class="tutor-single-course-segment">
    <div class="row">
        <div class="col-sm-6">
            <div class="course-student-rating-title">
                <h4 class="tutor-segment-title"><?php _e('Student Reviews', 'skillate'); ?></h4>
            </div>
        </div>
        <div class="col-sm-6">
            <?php
                if(is_user_logged_in()){
                    tutor_course_target_review_form_html(); 
                }else{ ?>
                    <a class="skillate-write-review-btn" data-toggle="modal" href="#modal-login">
                        <?php echo esc_html__('Write a review', 'skillate'); ?>
                    </a>
               <?php }
            ?>
        </div>
    </div>
    
    <div class="tutor-course-reviews-wrap">

        <div class="tutor-course-reviews-list">
			<?php
			foreach ($reviews as $review){
				$profile_url = tutor_utils()->profile_url($review->user_id);
				?>
                <?php if ($profile_url){ ?>
                    <div class="tutor-review-individual-item tutor-review-<?php echo $review->comment_ID; ?>">
                        <div class="review-left">
                            <div class="review-avatar">
                                <?php if ($review->user_id){ ?>
                                    <a href="<?php echo $profile_url; ?>"> <?php echo tutor_utils()->get_tutor_avatar($review->user_id); ?> </a>
                                <?php }else { 
                                    $get_avatar_url = get_avatar_url(get_the_ID(), 'thumbnail'); ?>
                                    <img src="<?php echo $get_avatar_url ?>">
                                <?php } ?>
                            </div>

                            <div class="tutor-review-user-info">
                                <div class="review-time-name">
                                    <?php if ($review->user_id){ ?>
                                        <p> <a href="<?php echo $profile_url; ?>">  <?php echo $review->display_name; ?> </a> </p>
                                    <?php }else { ?>
                                        <p> <a href="#"><?php esc_html_e('John Deo', 'skillate'); ?> </a> </p>
                                    <?php } ?>
                                        
                                    <p class="review-meta">
                                        <?php echo sprintf(__('%s ago', 'skillate'), human_time_diff(strtotime($review->comment_date))); ?>
                                    </p>
                                </div>
                                <div class="individual-review-rating-wrap">
    								<?php tutor_utils()->star_rating_generator($review->rating); ?>
                                </div>
                            </div>

                        </div>

                        <div class="review-content review-right">
    						<?php echo wpautop(stripslashes($review->comment_content)); ?>
                        </div>
                    </div>
                <?php } ?>


				<?php
			}
			?>
        </div>
    </div>
</div>

<?php do_action('tutor_course/single/enrolled/after/reviews'); ?>
