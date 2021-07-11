<?php
/**
 * Template for displaying course instructors/ instructor
 *
 * @since v.1.0.0
 *
 * @author Themeum
 * @url https://themeum.com
 */



do_action('tutor_course/single/enrolled/before/instructors');

$instructors = tutor_utils()->get_instructors_by_course();
if ($instructors){
	?>
	<h4 class="tutor-segment-title"><?php esc_attr_e('About the instructors', 'skillate'); ?></h4>
	<div class="tutor-course-instructors-wrap tutor-single-course-segment" id="single-course-ratings">
		<?php
		foreach ($instructors as $instructor){
		    $profile_url = tutor_utils()->profile_url($instructor->ID);
			?>
			<div class="single-instructor-wrap">
				<div class="single-instructor-top">
                    <div class="instructor-avatar">
                        <a href="<?php echo esc_url($profile_url); ?>">
                            <?php echo tutor_utils()->get_tutor_avatar($instructor->ID); ?>
                        </a>
                    </div>
                    <div class="instructor-name">
                        <div class="instructor-title-rating">
                            <h3><a href="<?php echo esc_url($profile_url); ?>"><?php echo esc_attr($instructor->display_name); ?></a> </h3>
                            <div class="ratings">
                                <?php $instructor_rating = tutor_utils()->get_instructor_ratings($instructor->ID); ?>
                                <span class="rating-generated">
                                    <?php tutor_utils()->star_rating_generator($instructor_rating->rating_avg); ?>
                                </span>

                                <?php
                                echo " <span class='rating-digits'>".esc_attr($instructor_rating->rating_avg)."</span> ";
                                echo " <span class='rating-total-meta'>(".esc_attr($instructor_rating->rating_count)." ".esc_attr_e('ratings', 'skillate').")</span> ";
                                ?>
                            </div>
                        </div>
                        
                        <?php
                        if ( ! empty($instructor->tutor_profile_job_title)){
                            echo "<span>".esc_attr($instructor->tutor_profile_job_title)."</span>";
                        }
                        ?>
                        <div class="instructor-bio">
                            <?php echo esc_attr($instructor->tutor_profile_bio); ?>
                        </div>
                        <div class="instructor-bio-inner">
                            <div class="courses">
                                <span>
                                    <i class='tutor-icon-mortarboard'></i>
                                    <?php echo tutor_utils()->get_course_count_by_instructor($instructor->ID); ?> <span class="tutor-text-mute"> <?php esc_attr_e('Courses', 'skillate'); ?></span>
                                </span>
                            </div>

                            <div class="students">
                                <?php
                                $total_students = tutor_utils()->get_total_students_by_instructor($instructor->ID);
                                ?>
                                <span>
                                    <i class='tutor-icon-user'></i>
                                    <?php echo esc_attr($total_students); ?>
                                    <span class="tutor-text-mute">  <?php esc_attr_e('students', 'skillate'); ?></span>
                                </span>
                            </div>
                        </div>
                    </div>
				</div>
			</div>
		<?php } ?>
	</div>
	<?php
}

do_action('tutor_course/single/enrolled/after/instructors');
