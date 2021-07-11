<?php 
    global $post;
    $cat_list = [];
    $cat_items = wp_get_post_terms(get_the_ID(), 'course-category', array("fields" => "all"));
    foreach ( $cat_items as $cat_item ) {           
        $cat_list[] = $cat_item->term_id;
    } 
    $tag_list = [];
    $tag_items = wp_get_post_terms(get_the_ID(), 'course-tag', array("fields" => "all"));
    foreach ( $tag_items as $tag_item ) {
        $tag_list[] = $tag_item->term_id;
    } 
    $course_args = array(
        'post_type' 		=> 'courses',
        'post_status' 		=> 'publish',
        'post__not_in' => array($post->ID),
    ); 
    $course_args['posts_per_page'] = get_theme_mod('related_course_slider_total_item', 20);
    $course_args['tax_query'] = array (
        'relation' => 'OR',
        array(
            'taxonomy' => 'course-category',
            'field' => 'term_id',
            'terms' => $cat_list,
        ),
        array(
            'taxonomy' => 'course-tag',
            'field' => 'term_id',
            'terms' => $tag_list,
        )
    );
    $related_posts = get_posts($course_args);
    $i = 0;
    $max_new_post = get_theme_mod('new_course_count', 5);
    $related_course_title = get_theme_mod('related_course_title', 'More Courses to get <b>You Started</b>');
    $slide_opacity_en = get_theme_mod('slider_opacity_en', true) ? '' : ' opacity-disable';

    $output = '';
    $output .= '<div class="skillate-related-course">';
        $output .= '<div class="container">';
            $output .= '<h3 class="course-related-title">'.__( $related_course_title,'skillate' ).'</h3>';
        $output .= '</div>';
        $output .= '<div dir="rtl" data-columns="7" class="skillate-related-course-items skillate-related-course-slide '.esc_attr($slide_opacity_en).'">';
            foreach ($related_posts as $related_post){ 
                $i++;
                setup_postdata($related_post);
                $best_selling = get_post_meta($related_post->ID, 'skillate_best_selling', true);
                $price = apply_filters('get_tutor_course_price', null, $related_post->ID);
                $src = wp_get_attachment_image_src(get_post_thumbnail_id($related_post->ID), 'skillate-medium');
               
                $output .= '<div class="tutor-course-grid-item">'; 
                    $output .= '<div class="tutor-course-grid-content">';
                        $output .= '<div class="tutor-course-overlay">';

                            if($best_selling == !false) {
                                $output .= '<span class="best-sell-tag">';
                                    $output .= __('Featured', 'skillate');
                                $output .= '</span>';
                            }else if($i <= $max_new_post){
                                $output .= '<span class="best-sell-tag new-tag">';
                                    $output .= __('New', 'skillate');
                                $output .= '</span>';
                            }

                            $output .= '<div class="course-related-price">';
                                ob_start();
                                if($price == !null){
                                    $output .= $price;
                                }else{
                                    $output .= '<span class="woocommerce-Price-amount amount">'.esc_html__('Free', 'skillate').'</span>';
                                }
                                $output .= ob_get_clean();
                            $output .= '</div>';   
                            $output .= '<img src="' . esc_url($src[0]) . '" class="img-responsive" />';  

                            $is_wishlisted = tutor_utils()->is_wishlisted($related_post->ID);
                            $has_wish_list = '';
                            if ($is_wishlisted){
                                $has_wish_list = 'has-wish-listed';
                            }
                            
                            $output .= '<div class="tutor-course-grid-level-wishlist">';
                                $output .= '<span class="tutor-course-grid-wishlist tutor-course-wishlist">';
                                    if(is_user_logged_in()){
                                        $output .= '<a href="javascript:;" class="tutor-icon-fav-line tutor-course-wishlist-btn '.esc_attr($has_wish_list).' " data-course-id="'.get_the_ID().'"></a>';
                                        }else{
                                        $output .= '<a class="tutor-icon-fav-line" data-toggle="modal" href="#modal-login"></a>';
                                    }
                                $output .= '</span>';
                            $output .= '</div>';//tutor-course-grid-level-wishlis
                            $output .= '<div class="tutor-course-grid-enroll">';
                                $output .= '<div class="course-related-hover-price">';
                                    ob_start();
                                    if($price == !null){
                                        $output .= $price;
                                    }else{
                                        $output .= '<span class="woocommerce-Price-amount amount">'.esc_html__('Free', 'skillate').'</span>';
                                    }
                                    $output .= ob_get_clean();
                                $output .= '</div>';
                                $output .= '<span class="tutor-course-grid-level">'.get_tutor_course_level($related_post->ID).'</span>';
                                $output .= '<span class="tutor-course-duration">'.get_tutor_course_duration_context().'</span>';

                                if (tutor_utils()->is_course_purchasable($related_post->ID)) {
                                    $product_id = tutor_utils()->get_course_product_id($related_post->ID);
                                   // $product    = wc_get_product( $product_id );
                                    $output .= tutor_course_loop_add_to_cart(false);
                                } else {
                                    if (tutor_utils()->is_enrolled($related_post->ID)) {
                                    $output .= '<a href="'.esc_url(get_the_permalink($related_post->ID)).'" class="btn btn-classic btn-no-fill">'.__('Enrolled','skillate').'</a>';
                                    } else {
                                        $output .= '<a href="'.esc_url(get_the_permalink($related_post->ID)).'" class="btn btn-classic btn-no-fill">'.__('Enroll Now','skillate').'</a>';
                                    }
                                }
                                
                                

                            $output .= '</div>';
                        $output .= '</div>';//tutor-course-overlay
                        $output .= '<h4 class="tutor-courses-grid-title"><a href="'.esc_url(get_the_permalink($related_post->ID)).'">'.get_the_title($related_post->ID).'</a></h4>';
                    $output .= '</div>';//tutor-course-grid-content
                $output .= '</div>';//tutor-course-grid-item
            }
        $output .= '</div>';//thm-grid-items
        wp_reset_query();
    $output .= '</div>';//thm-grid-items
    echo $output;
?>