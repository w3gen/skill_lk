<?php
/**
 * Template for displaying certificate
 *
 * @since v.1.5.1
 *
 * @author Themeum
 * @url https://themeum.com
 *
 * @package TutorLMS/Certificate
 * @version 1.5.1
 */

get_header(); ?>

    <link rel="stylesheet" href="<?php echo TUTOR_CERT()->url . 'assets/css/certificate-page.css'; ?>">

    <div class="<?php tutor_container_classes(); ?>">
		<?php do_action('tutor_certificate/before_content'); ?>

        <div class="tutor-certificate-container">
            <div class="tutor-certificate-img-container">
                <img id="tutor-pro-certificate-preview" src="<?php echo $cert_img; ?>" data-is_generated="<?php echo $cert_file ? 'yes' : 'no'; ?>"/>
            </div>

            <div class="tutor-certificate-sidebar">
                <div class="tutor-certificate-sidebar-btn-container">
                    <div class="tutor-dropdown">
                        <button class="tutor-dropbtn tutor-btn tutor-button-block download-btn"><?php _e('Download Certificate', 'tutor-pro'); ?> <i class="tutor-icon-download"></i></button>
                        <div class="tutor-dropdown-content">
                            <ul>
                                <li>
                                    <a id="tutor-pro-certificate-download-pdf" data-cert_hash="<?php echo $cert_hash; ?>" data-course_id="<?php echo $course->ID; ?>">
                                        <i class="tutor-icon-pdf"></i> <?php _e('PDF', 'tutor-pro'); ?>
                                    </a>
                                </li>
                                <li>
                                    <a href="#" id="tutor-pro-certificate-download-image">
                                        <i class="tutor-icon-jpg"></i> <?php _e('JPG', 'tutor-pro'); ?>
                                    </a>
                                </li>
                            </ul>
                        </div>
                    </div>

                    <div class="tutor-certificate-btn-group tutor-dropdown">
                        <button class="tutor-copy-link tutor-btn bordered-btn tutor-button-block"><i class="tutor-icon-copy"></i> <?php _e('Copy Link', 'tutor-pro'); ?></button>
                        <div class="tutor-share-btn">
                            <button class="tutor-dropbtn tutor-btn bordered-btn tutor-button-block"><i class="tutor-icon-share"></i></button>
                            <div class="tutor-dropdown-content">
								<?php tutor_social_share(); ?>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="tutor-certificate-sidebar-course">
                    <h3><?php _e('About Course', 'tutor-pro'); ?></h3>
                    <div class="tutor-course-loop-level"><?php echo get_tutor_course_level($course->ID); ?></div>
					<?php
					$course_rating = tutor_utils()->get_course_rating($course->ID);
					tutor_utils()->star_rating_generator($course_rating->rating_avg);
					?>

                    <h1 class="course-name"><a href="<?php echo $course->guid; ?>" class="tutor-sidebar-course-title"><?php echo $course->post_title;
                    ?></a></h1>
                    <div class="tutor-sidebar-course-author">
                        <img src="<?php echo get_avatar_url($course->post_author); ?>"/>
                        <span>
                            <?php _e('by', 'tutor-pro'); ?>
                            <a href="<?php echo tutor_utils()->profile_url($course->post_author); ?>">
                                <strong><?php echo get_the_author_meta('display_name', $course->post_author); ?></strong>
                            </a>
                        </span>
                    </div>
                </div>
            </div>
        </div>
    </div><!-- .wrap -->

<?php get_footer();