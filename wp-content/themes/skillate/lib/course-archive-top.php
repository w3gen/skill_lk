<?php 
    global $post;
    $course_args = array(
        'post_type'         => 'courses',
        'post_status'       => 'publish',
        'posts_per_page'    => get_theme_mod('featured_slider_total_item', 20),
        'meta_query' => array(
            array(
                'key' => 'skillate_best_selling',
                'value' => 1
            )
        )
    ); 

    $featured_posts = get_posts($course_args);
    $course_search = '';

    if(isset($_GET['search'])) {
        $course_search =  $_GET['search'];
    }
    $i = 0;
    $max_new_post = get_theme_mod('new_course_count', 5);
    $top_bottom_slide_count = get_theme_mod('top_bottom_slide_count', 6);
    $slide_center_mod = get_theme_mod('slide_center_mod', true) ? 'true' : 'false';
    $slide_opacity_en = get_theme_mod('slider_opacity_en', true) ? '' : ' opacity-disable';
    $feature_slider_title = get_theme_mod('feature_slider_title', 'More Courses to get <b>You Started</b>');
    
    $output = '';
    if(count($featured_posts) > 0){
        $output .= '<div class="skillate-related-course course-archive-top-slide-wrap yo ' . count($featured_posts) . ' ">';
            $output .= '<div class="container">';
                $output .= '<h3 class="course-related-title">'.wp_kses_post( $feature_slider_title ,'skillate').'</h3>';
            $output .= '</div>';
            $output .= '<div dir="rtl" data-slidemode="'.esc_attr($slide_center_mod).'" data-columns="'.esc_attr($top_bottom_slide_count).'" class="skillate-related-course-items skillate-related-course-slide '.esc_attr($slide_opacity_en).'">';
                foreach ($featured_posts as $featured_post){ 
                    $i ++;
                    $best_selling = get_post_meta($featured_post->ID, 'skillate_best_selling', true);
                    $price = apply_filters('get_tutor_course_price', null, $featured_post->ID);
                    $output .= '<div class="tutor-course-grid-item">';
                        $output .= '<div class="tutor-course-grid-content">';
                            $output .= '<div class="tutor-course-overlay">';
                                $thumb  = wp_get_attachment_image_src( get_post_thumbnail_id($featured_post->ID), 'skillate-courses');
                                if($thumb[0]){ $output .= '<img src="'. $thumb[0] .'" class="img-responsive">'; }
                                
                                $output .= '<div class="tutor-course-overlay-element">';
                                    $output .= '<div class="level-tag">';
                                        $best_selling = get_post_meta(get_the_ID(), 'skillate_best_selling', true);
                                        if ($best_selling != false) {
                                            $output .= '<span class="tag intermediate">'.__('Featured', 'skillate').'</span>';
                                        }
                                    $output .= '</div>';
                            
                                    $is_wishlisted = tutor_utils()->is_wishlisted($featured_post->ID);
                                    $has_wish_list = '';
                                    if ($is_wishlisted){
                                        $has_wish_list = 'has-wish-listed';
                                    }
                                    $output .= '<div class="bookmark">';
                                        $output .= '<span class="tutor-course-grid-wishlist tutor-course-wishlist">';
                                            if(is_user_logged_in()){
                                            $output .= '<a href="javascript:;" class="tutor-icon-fav-line tutor-course-wishlist-btn '.esc_attr($has_wish_list).' " data-course-id="'.get_the_ID().'"></a>';
                                            }else{
                                                $output .= '<a class="tutor-icon-fav-line" data-toggle="modal" href="#modal-login"></a>';
                                            }
                                        $output .= '</span>';
                                    $output .= '</div>';
                            
                                    $output .= '<div class="price">';
                                        ob_start();
                                        if($price == !null){
                                            $output .= $price;
                                        }else{
                                            $output .= '<span class="woocommerce-Price-amount amount">Free</span>';
                                        }
                                        $output .= ob_get_clean();
                                    $output .= '</div>'; 
                                $output .= '</div>';

                                
                                $output .= '<div class="tutor-course-grid-enroll">';
                                    $output .= '<div class="course-related-hover-price">';
                                        
                                        ob_start();
                                        if($price == !null){
                                            $output .= $price;
                                        }else{
                                            $output .= '<span class="woocommerce-Price-amount amount">Free</span>';
                                        }
                                        $output .= ob_get_clean();

                                    $output .= '</div>';
                                    $output .= '<span class="tutor-course-grid-level">'.get_tutor_course_level($featured_post->ID).'</span>';
                                    $output .= '<span class="tutor-course-duration">'.get_tutor_course_duration_context($featured_post->ID).'</span>';
                                    
                                    if (tutor_utils()->is_course_purchasable($featured_post->ID)) {
                                        $product_id = tutor_utils()->get_course_product_id($featured_post->ID);
                                        //$product    = wc_get_product( $product_id );
                                        $output .= tutor_course_loop_add_to_cart(false);
                                    } else {
                                        if (tutor_utils()->is_enrolled($featured_post->ID)) {
                                            $output .= '<a href="'.esc_url(get_the_permalink($featured_post->ID)).'" class="btn btn-classic btn-no-fill">'.__('Enrolled','skillate').'</a>';
                                        } else {
                                            $output .= '<a href="'.esc_url(get_the_permalink($featured_post->ID)).'" class="btn btn-classic btn-no-fill">'.__('Enroll Now','skillate').'</a>';
                                        }
                                    }
                                $output .= '</div>';
                            $output .= '</div>';
                            
                            $output .= '<h3 class="tutor-courses-grid-title"><a href="'.esc_url(get_the_permalink($featured_post->ID)).'">'.get_the_title($featured_post->ID).'</a></h3>';
                            $output .= '<div class="course-price-mobile d-lg-none">';
                                ob_start();
                                $output .= tutor_course_price();
                                $output .= ob_get_clean();
                            $output .= '</div>';
                            
                        $output .= '</div>';
                    $output .= '</div>'; 


                }
            $output .= '</div>';//thm-grid-items
            wp_reset_query();
        $output .= '</div>';//thm-grid-items
    }
    if($course_search == !'course_search'){
        echo $output;
    }
?>