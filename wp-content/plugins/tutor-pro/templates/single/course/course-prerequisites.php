
<h4><?php _e('Course Prerequisite(s)', 'tutor-pro'); ?></h4>

<div class="course-prerequisites-lists-wrap">
    <ul class="prerequisites-course-lists">
        <li class="prerequisites-warning">
            <span>
                <svg width="22" height="20" xmlns="http://www.w3.org/2000/svg">
                <path d="M21.543 15.021l-7.701-13.34c-1.258-2.203-4.44-2.203-5.693 0L.443 15.022c-1.258 2.204.308 4.941 2.847 4.941h15.38c2.538 0 4.13-2.764 2.873-4.94zM10.99 17.003c-.67 0-1.226-.556-1.226-1.226 0-.67.556-1.226 1.226-1.226.67 0 1.226.556 1.199 1.258.032.638-.557 1.194-1.2 1.194zm1.118-7.928c-.055.95-.114 1.896-.168 2.847-.027.307-.027.588-.027.891a.924.924 0 0 1-.923.891.903.903 0 0 1-.923-.864c-.082-1.48-.168-2.932-.249-4.412-.027-.389-.054-.782-.086-1.172 0-.642.362-1.171.95-1.339a1.23 1.23 0 0 1 1.426.697c.086.195.113.39.113.615-.027.62-.086 1.236-.113 1.846z" fill="#E5AE3F" fill-rule="nonzero"/>
            </svg>
            </span>
	        <?php _e('Please note that this course has the following prerequisites which must be completed before it can be accessed', 'tutor-pro'); ?>
        </li>
		<?php
		$savedPrerequisitesIDS = maybe_unserialize(get_post_meta(get_the_ID(), '_tutor_course_prerequisites_ids', true));
		if (is_array($savedPrerequisitesIDS) && count($savedPrerequisitesIDS)){
			foreach ($savedPrerequisitesIDS as $courseID){
				?>
                <li>
                    <a href="<?php echo get_the_permalink($courseID); ?>" class="prerequisites-course-item">
                        <span class="prerequisites-course-feature-image">
                            <?php echo get_the_post_thumbnail($courseID); ?>
                        </span>

                        <span class="prerequisites-course-title">
                            <?php echo get_the_title($courseID); ?>
                        </span>

						<?php if (tutor_utils()->is_completed_course($courseID)){
							?>
                            <div class="is-complete-prerequisites-course"><i class="tutor-icon-mark"></i></div>
							<?php
						} ?>
                    </a>
                </li>
				<?php
			}
		}
		?>
    </ul>
</div>
