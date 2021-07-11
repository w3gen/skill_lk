<?php 
$instructor_slider_title = get_theme_mod('instructor_slider_title','Most favourite <b>Instructor.</b>');
$instructor_slider_title_link_text = get_theme_mod('instructor_slider_title_link_text','View More');
$instructor_page_link = get_theme_mod('instructor_page_link', '#');
$slide_opacity_en = get_theme_mod('slider_opacity_en', true) ? '' : ' opacity-disable';
if(get_theme_mod('instructor_slide', true)){ ?>
<div class="skillate-instructor-slide-wrap">
    <div class="container">
        <h3 class="course-related-title d-inline-block">
            <?php echo wp_kses_post( $instructor_slider_title, 'skillate'); ?>
        </h3>
        <?php if($instructor_page_link) { ?>
        <a class="view-more-link" href="<?php echo esc_url($instructor_page_link); ?>">
            <?php echo esc_html__( $instructor_slider_title_link_text , 'skillate'); ?>
        </a>
        <?php } ?>
    </div>

    <div dir="rtl" class="skillate-related-course-slide <?php echo esc_attr($slide_opacity_en); ?> ">
        <?php 
            $instructor_slide_query = get_theme_mod('instructor_slide_query', 'fav_instructor');
            if($instructor_slide_query == 'fav_instructor'){
                $user_id = get_users( array(
                    "meta_key"      => "favourite_instructor",
                    "meta_value"    => 'yes',
                    "fields"        => "ID"
                ));
            }else{
                $user_id = get_users(array(
                    'role'    => 'tutor_instructor',
                    'fields'  => 'ID'
                ));
            }

            $user_id_count = count($user_id);
        ?>
        <?php for ($i=0; $i < $user_id_count; $i++) { 
            if(tutor_utils()->is_instructor($user_id[$i])){ 
                $user = get_userdata($user_id[$i]);  
                $instructor_rating = tutor_utils()->get_instructor_ratings($user_id[$i]); ?>
                <div class="skillate-instructor-content">
                    <div class="skillate-instructor-thumb">
                        <?php global $post;
                            $author_id = $user_id[$i]; 
                            $user_photo = '';
                            $tutor_user = tutor_utils()->get_tutor_user($author_id);
                            if ($tutor_user->tutor_profile_photo){
                                $user_photo = wp_get_attachment_image_url($tutor_user->tutor_profile_photo, 'skillate-courses');
                            }
                            if($user_photo){
                                echo '<a href='.tutor_utils()->profile_url($author_id).'><img src='.$user_photo.' /></a>';
                            }else{
                                echo get_avatar($author_id, 255);
                            }
                        ?>
                        <span class="rating-avg">
                            <i class="fas fa-star"></i>
                            <strong> <?php echo $instructor_rating->rating_avg; ?></strong>
                            /<?php esc_html_e('5', 'skillate'); ?>
                        </span>
                    </div>
                    <div class="upskil-instructor-content"> 
                        <a href="<?php echo tutor_utils()->profile_url($author_id); ?>">                                   
                            <h3 class="instructor-name"><?php echo $user->display_name; ?></h3>
                        </a>
                        <p class="instructor-course-count">
                            <strong><?php echo tutor_utils()->get_course_count_by_instructor($user_id[$i]); ?></strong> 
                            <?php echo esc_html__('Courses', 'skillate'); ?>
                        </p>
                    </div>
                </div>
            <?php }?>
        <?php } ?>

    </div>
</div>
<?php } ?>