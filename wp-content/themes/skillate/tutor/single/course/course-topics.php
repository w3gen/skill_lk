<?php
/**
 * Template for displaying single course
 *
 * @since v.1.0.0
 *
 * @author Themeum
 * @url https://themeum.com
 *
 * @package TutorLMS/Templates
 * @version 1.4.3
 */

if ( ! defined( 'ABSPATH' ) )
	exit;

$topics = tutor_utils()->get_topics();
$course_id = get_the_ID();
$is_enrolled = tutor_utils()->is_enrolled($course_id);
?>
<?php do_action('tutor_course/single/before/topics'); ?>
<?php if($topics->have_posts()) { ?>
    <div class="tutor-single-course-segment  tutor-course-topics-wrap">
        <div class="tutor-course-topics-header">
            <div class="tutor-course-topics-header-left">
                <h4 class="tutor-segment-title"><?php _e('Course content', 'skillate'); ?></h4>
            </div>
            <div class="tutor-course-topics-header-right">
				<?php
				$tutor_lesson_count = tutor_utils()->get_lesson_count_by_course($course_id);
				$tutor_course_duration = get_tutor_course_duration_context($course_id);

				if($tutor_lesson_count) {
					echo "<span> $tutor_lesson_count";
					_e(' Lessons', 'skillate');
					echo "</span>";
				}
				if($tutor_course_duration){
					echo "<span>$tutor_course_duration</span>";
				}
				?>
            </div>
        </div>
        <div class="tutor-course-topics-contents">
			<?php
			$index = 0;
			if ($topics->have_posts()){
				while ($topics->have_posts()){ $topics->the_post();
					$topic_summery = get_the_content();
					$index++;
					?>
                    <div class="tutor-course-topic <?php if($index == 1) echo "tutor-active"; ?>">
                        <div class="tutor-course-title">
                            <h4><i class="tutor-icon-plus"></i> <?php the_title(); ?></h4>
                        </div>
                        <div class="tutor-course-lessons" style="<?php echo $index > 1 ? 'display: none' : ''; ?>">
							<?php if ($topic_summery){ ?>
								<div class="tutor-topics-summery">
									<?php echo $topic_summery; ?>
								</div>
							<?php } ?>
							<?php
							$lessons = tutor_utils()->get_course_contents_by_topic(get_the_ID(), -1);
							if ($lessons->have_posts()){
								while ($lessons->have_posts()){ $lessons->the_post();
								global $post;
									$_is_preview = get_post_meta(get_the_ID(), '_is_preview', true);
									$video = tutor_utils()->get_video_info();
									$thumbURL 		= get_the_post_thumbnail_url();
									if($thumbURL == ''){
										$thumbURL = get_template_directory_uri().'/images/lesson-thumb.jpg';
									}
									$play_time = false;
									if ($video){
										$play_time = $video->playtime;
									}
									$is_completed_lesson = tutor_utils()->is_completed_lesson();
									if($is_completed_lesson) {
										$lesson_icon = $play_time ? 'tutor-icon-youtube' : 'tutor-icon-document-alt';
									} else {
										$lesson_icon = $play_time ? 'tutor-icon-lock' : 'tutor-icon-document-alt';
									}
									if ($post->post_type === 'tutor_quiz'){
										$lesson_icon = 'tutor-icon-doubt';
                                    }
									if ($post->post_type === 'tutor_assignments'){
										$lesson_icon = 'tutor-icon-clipboard';
									}
									?>
									<?php if($_is_preview) {?>
                                    <div class="tutor-course-lesson preview-enabled-lesson">
									<?php } else { ?>
										<div class="tutor-course-lesson">
									<?php } ?>
                                        <h5>
											<?php
                                                $lesson_title = "<i style='background:url(".esc_url($thumbURL).")' class='$lesson_icon'></i>";
                                                if ($is_enrolled){
	                                                $lesson_title .= "<div class='tutor-course-lesson-content'><a href='".get_the_permalink()."'> ".get_the_title()." </a>";
													$lesson_title .= $play_time ? "<span class='tutor-lesson-duration'>$play_time</span></div>" : '';
													if($is_completed_lesson){
														$lesson_title .= '<div class="lesson-completed-text"><i class="fa fa-check"></i>';
															$lesson_title .= '<span>'.esc_html__('Viewed', 'skillate').'</span>';
														$lesson_title .= '</div>';
													}
	                                                echo $lesson_title;
                                                }else{
	                                                $lesson_title .= '<div class="tutor-course-lesson-content">';
	                                                	$lesson_title .= '<div class="course-lesson-title-inner">'.get_the_title().'</div>';
														$lesson_title .= $play_time ? "<span class='tutor-lesson-duration'>$play_time</span>" : '';
													$lesson_title .= '</div>';
													//echo $lesson_title;
													echo apply_filters('tutor_course/contents/lesson/title', $lesson_title, get_the_ID());
                                                }
											?>
                                        </h5>
                                    </div>
									<?php
								}
								$lessons->reset_postdata();
							}
							?>
                        </div>
                    </div>
					<?php
				}
				$topics->reset_postdata();
			}
			?>
        </div>
    </div>
<?php } ?>
<?php do_action('tutor_course/single/after/topics'); ?>