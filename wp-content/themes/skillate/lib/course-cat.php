<?php
/* skillate search Result. */ 
define('WP_USE_THEMES', false);
$parse_uri = explode('wp-content', $_SERVER['SCRIPT_FILENAME']);
$wp_load = $parse_uri[0].'wp-load.php';
require_once($wp_load);

$paged = (get_query_var('paged')) ? get_query_var('paged') : 1;

if( isset($_POST['data_level']) || isset($_POST['data_category']) || isset($_POST['data_price']) || isset($_POST['data_language']) ){

    # Course Level
    if (!empty($_POST['data_level'])) {
        $selected_level = $_POST['data_level'];
    }
    $course_level_filter = !empty($selected_level) ? array(
            'key'       => '_tutor_course_level',
            'value'     => $selected_level,
             'compare'   => '='
        ) : array();


    # Course Price
    if (!empty($_POST['data_price'])) {
        $selected_price = $_POST['data_price'];
    } 
    $course_price_filter = !empty($selected_price) ? array(
            'key'       => '_tutor_course_price_type',
            'value'     => $selected_price,
             'compare'   => '='
        ) : array();


    # Category
    $data_category = $data_language = array();
    if (isset($_POST['data_category']) && !empty($_POST['data_category'])) {
        $data_category = $_POST['data_category'];
    }
    
    # Language
    if (!empty($_POST['data_language'])) {
        $data_language = $_POST['data_language'];
    } 
}


$args = array(
    'post_type'         => tutor()->course_post_type,
    'post_status'       => 'publish',
    'paged'             => $paged,
    'posts_per_page'    => -1,
);


if (!empty($course_level_filter)) {
    $args['meta_query']['relation'] = 'AND';
    $args['meta_query'][] = $course_level_filter;
}

if (!empty($course_price_filter)) {
    $args['meta_query'][] = $course_price_filter;
}


if (!empty($data_category)) {
    $args['tax_query']['relation'] = 'OR';
    $args['tax_query'][] = array(
        'taxonomy' => 'course-category',
        'field'    => 'slug',
        'terms'    => $data_category,
        'operator' => !empty($data_category) ? 'IN' : ''
    );
}

if (!empty($data_language)) {
    $args['tax_query']['relation'] = 'OR';
    $args['tax_query'][] = array(
        'taxonomy' => 'course-language',
        'field'    => 'slug',
        'terms'    => $data_language,
        'operator' => 'IN'
    );
}

?>



<div class="skillate-courses-wrap successfully">
    <?php
        $search_data = new WP_Query($args);
        $i = 0;
        $max_new_post = get_theme_mod('new_course_count', 5);
        if($search_data->have_posts()){
            while ($search_data->have_posts()) {
                $i++;
                $search_data->the_post();
                $idd = get_the_ID();
                $best_selling = get_post_meta($idd, 'skillate_best_selling', true);

                global $authordata;
                $profile_url = tutor_utils()->profile_url( $authordata->ID )
                ?>
                <div class="row skillate-course-col align-items-lg-center">
                    <div class="col-lg-4 col-sm-12">
                        <div class="skillate-course-media mb-lg-0 mb-4">
                            <a href="<?php the_permalink(); ?>">
                                <?php the_post_thumbnail('skillate-medium');?>
                            </a>
                            <div class="tutor-course-loop-header-meta">
                                <?php
                                $is_wishlisted = tutor_utils()->is_wishlisted($idd);
                                $has_wish_list = '';
                                if ($is_wishlisted){
                                    $has_wish_list = 'has-wish-listed';
                                }
                                echo '<span class="tutor-course-wishlist"><a href="javascript:;" class="tutor-icon-fav-line tutor-course-wishlist-btn '.$has_wish_list.' " data-course-id="'.$idd.'"></a> </span>';
                                ?>
                            </div>
                            <div class="course-media-hover">
                                <a class="archive-course-view" href="<?php the_permalink(); ?>">
                                    <?php echo esc_html__('View Course', 'skillate'); ?>
                                </a>
                                <div class="skillate-course-enroll-btn">
                                    <?php
                                    $course_id = get_the_ID();
                                    $enroll_btn = '<div  class="tutor-loop-cart-btn-wrap"><a href="'. get_the_permalink(). '">'.__('Enroll Now', 'skillate'). '</a></div>';
                                    
                                    if (tutor_utils()->is_course_purchasable()) {
                                        $enroll_btn = tutor_course_loop_add_to_cart(false);

                                        $product_id = tutor_utils()->get_course_product_id($course_id);
                                 
                                    } else {
                                        $price_html = '<div class="price"> '.$enroll_btn.' </div>';
                                    }
                                    $price_html = '<div class="price"> '.$enroll_btn. '</div>';
                                    echo $price_html;
                                    ?>
                                </div>

                            </div>
                        </div>
                    </div>

                    <div class="col-lg-5 col-sm-8 order-2 order-sm-1">
                        <div class="skillate-course-body mb-sm-0 mb-3">
                            <?php if($best_selling == !false) {?>
                            <span class="best-sell-tag">
                                <?php echo esc_html__('Featured', 'skillate'); ?>
                            </span>
                            <?php }else if($i <= $max_new_post){?>
                            <span class="best-sell-tag new-tag">
                                <?php echo esc_html__('New', 'skillate'); ?>
                            </span>
                            <?php }?>
                            <h3>
                                <a href="<?php the_permalink(); ?>">
                                    <?php echo get_the_title(); ?>
                                </a>
                            </h3>
                            <div class="course-archive-single-meta">
                                <span><?php echo esc_html(get_tutor_course_level()); ?></span>
                                <span><?php echo esc_html(get_tutor_course_duration_context()); ?></span>
                                <?php $skillate_pro_tutor_lesson_count = tutor_utils()->get_lesson_count_by_course(get_the_ID());
                                if($skillate_pro_tutor_lesson_count) {?>
                                <span>
                                    <?php echo esc_html($skillate_pro_tutor_lesson_count);?>
                                    <?php echo esc_html__('Lesson', 'skillate'); ?>
                                </span>
                                <?php }?>
                            </div>

                            <div class="course-archive-author">
                                <?php global $post;
                                    $author_id=$post->post_author;
                                    if(function_exists('tutor_utils')){ ?>
                                        <a href="<?php echo tutor_utils()->profile_url($author_id); ?>">
                                            <?php echo tutor_utils()->get_tutor_avatar($author_id, 'thumbnail'); ?>
                                            <h4><?php echo the_author_meta( 'display_name' , $author_id ); ?></h4>
                                        </a>
                                    <?php }else{
                                        $get_avatar_url = get_avatar_url($author_id, 'thumbnail');
                                        echo "<img alt='' src='$get_avatar_url' />";
                                    }
                                ?>
                            </div>
                            
                        </div>
                    </div>

                    <div class="col-lg-3 col-sm-4 order-1 order-sm-2">
                        <div class="text-sm-right ">
                            <div class="course-single-price course-archive-price mb-lg-3 mb-0">
                                <?php tutor_course_price(); ?>
                            </div>
                            <div class="tutor-single-course-rating">
                                <?php
                                $skillate_course_rating = tutor_utils()->get_course_rating();
                                tutor_utils()->star_rating_generator($skillate_course_rating->rating_avg);
                                $product_id = tutor_utils()->get_course_product_id();
                                //$product = wc_get_product( $product_id );
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
                        </div>
                    </div>
                </div>
            <?php }
        } else { ?>

            <div class="col-12">
                <?php
                    echo "<h2>".__('Nothing found!', 'skillate')."</h2>";
                    echo "<div>".__('Sorry, but nothing matched your search terms. Please try again with different terms.', 'skillate')."</div>";
                ?>
            </div>

            <?php
        }
    ?>
</div>







