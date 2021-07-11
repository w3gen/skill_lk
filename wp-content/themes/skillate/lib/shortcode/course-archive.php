<?php

function skillate_course_function($atts, $content, $tag) {

    global $wp_query;
    $sidebar_filter = get_theme_mod('sidebar_filter', true);
    $top_filter_bar = get_theme_mod('top_filter_bar', true);
    $course_per_page = get_theme_mod('course_per_page', 9);
    $course_pagination = get_theme_mod('course_pagination', true);
    $course_column_count = get_theme_mod('course_column_count', 3);
    $course_category_count = get_theme_mod('course_category_count', 1);
    // $course_title_length = get_theme_mod('course_title_length', 0);
    $course_sidebar_position = get_theme_mod('course_sidebar_position', 'left');
    $course_filter_title = get_theme_mod('course_filter_title', 'All Courses You <b>Can Filter</b>');

    $atts = extract(shortcode_atts( array(
        'sidebar'           => $sidebar_filter,
        'top_filter'        => $top_filter_bar,
        'count'             => $course_per_page,
        'pagination'        => $course_pagination,
        'column'            => $course_column_count,
        'category_count'    => $course_category_count,
        // 'title_length'  => $course_title_length,
        'sidebar_position'  => $course_sidebar_position
        ), $atts,  $tag
    ));

    if($sidebar === 'false' || $sidebar === 0) $sidebar = false;

    switch ($column){
        case 1:
            $column = 12;
            break;
        case 2:
            $column = 6;
            break;
        case 3:
            $column = 4;
            break;
        case 4:
            $column = 3;
            break;
        case 6:
            $column = 2;
            break;
        case 12:
            $column = 1;
            break;
        default:
            $column = 3;
    }


    $paged = (get_query_var('paged')) ? get_query_var('paged') : 1;

    $selected_cat = !empty($_GET['course_category']) ? (array) $_GET['course_category'] : array();
    $selected_cat = array_map( 'sanitize_text_field', $selected_cat );
    $selected_cat = array_map('intval', $selected_cat);
    $is_queried_object = false;
    if(isset($wp_query->queried_object->term_id)){
        $is_queried_object = true;
        $selected_cat = array($wp_query->queried_object->term_id);
    }


    $selected_tag = !empty($_GET['course_tag']) ? (array) $_GET['course_tag'] : array();
    $selected_tag = array_map( 'sanitize_text_field', $selected_tag );
    $selected_tag = array_map('intval', $selected_tag);

    $selected_level = !empty($_GET['course_level']) ? (array) $_GET['course_level'] : array('all_levels');
    $selected_level = array_map( 'sanitize_text_field', $selected_level );

    $selected_type = !empty($_GET['course_type']) ? (array) $_GET['course_type'] : array('');
    $selected_type = array_map( 'sanitize_text_field', $selected_type );

    $course_terms_cat = get_terms(array(
        'taxonomy' => 'course-category',
        'hide_empty' => true,
        'parent' => 0
    ));

    //Language Taxonomy
    $course_terms_languages = get_terms(array(
        'taxonomy' => 'course-language',
        'hide_empty' => true,
        'parent' => 0
    ));

    $course_terms_tag = get_terms(array(
        'taxonomy' => 'course-tag',
        'hide_empty' => true
    ));

    $course_levels      = tutor_utils()->course_levels();
    $course_types       = skillate_course_price_type();

    $course_level_filter = !empty($selected_level) && !in_array('all_levels', $selected_level) ? array(
        'key'       => '_tutor_course_level',
        'value'     => $selected_level,
        'compare'   => 'IN'
    ) : array();

    $course_level_type = !empty($selected_type) && !in_array('all_types', $selected_type) ? array(
        'key'       => '_tutor_course_price_type',
        'value'     => $selected_type,
        'compare'   => 'IN'
    ) : array();

    $args = array(
        'post_type'         => tutor()->course_post_type,
        'post_status'       => 'publish',
        'paged'             => $paged,
        'posts_per_page'    => $count,
        's'                 => get_search_query(),
        'meta_query'        => array(
            $course_level_filter,
        ),
        'tax_query' => array(
            'relation' => 'AND',
            array(
                'taxonomy' => 'course-category',
                'field'    => 'term_id',
                'terms'    => $selected_cat,
                'operator'  => !empty($selected_cat) ? 'IN' : 'NOT IN'
            ),
            array(
                'taxonomy' => 'course-tag',
                'field'    => 'term_id',
                'terms'    => $selected_tag,
                'operator'  => !empty($selected_tag) ? 'IN' : 'NOT IN'
            )
        )
    );

    $course_filter = 'newest_first';
    if ( ! empty($_GET['tutor_course_filter'])){
        $course_filter = sanitize_text_field($_GET['tutor_course_filter']);
    }
    switch ($course_filter){
        case 'newest_first':
            $args['orderby'] = 'ID';
            $args['order'] = 'desc';
            break;
        case 'oldest_first':
            $args['orderby'] = 'ID';
            $args['order'] = 'asc';
            break;
        case 'course_title_az':
            $args['orderby'] = 'post_title';
            $args['order'] = 'asc';
            break;
        case 'course_title_za':
            $args['orderby'] = 'post_title';
            $args['order'] = 'desc';
            break;
    }


    $q = new WP_Query($args);
    ob_start(); ?>

    <?php if ( !empty(get_search_query()) ): ?>
        <div class="skillate-search-result-wrap subtitle-cover sub-title" style="background-color:#ffffff;">
            <div class="row subtitle-border align-items-center">
                <div class="col-8 col-md-9">
                    <h2 class="page-leading"><?php printf( __( 'Search Results for: %s', 'skillate' ), '"'.get_search_query().'"' ); ?></h2>            
                </div>
                <div class="col-4 col-md-3">
                    <ol class="breadcrumb">
                        <li>
                            <a class="breadcrumb_home total-courses">
                                <?php
                                    $courseCount = tutor_utils()->get_archive_page_course_count();
                                    printf(__('%s Courses', 'skillate'), "<strong>{$q->post_count}</strong>");
                                ?>
                            </a>
                        </li>
                    </ol>
                </div>
            </div>
        </div>
    <?php endif ?>

    <?php if ( empty(get_search_query()) ): ?>
        <?php if ( $course_filter_title ): ?>
            <div class="course-archive-title">
                <h3 class="course-related-title">
                    <?php echo wp_kses_post( $course_filter_title , 'skillate'); ?>
                </h3>
            </div>
        <?php endif; ?>
    <?php endif; ?>
        
    
    <div class="row">
        <?php if($sidebar) : ?>

            <?php $course_archive_url = home_url().'/courses'; ?>

            <div class="skillate-sidebar-filter-col col-12 col-md-4 col-lg-3 mb-4 md-lg-0">
                <form class="skillate-sidebar-filter" role="search" method="get" id="course_searchform" action="javascript:void(0)">
                    
                    <input type="hidden" name="s" value="<?php echo get_search_query(); ?>">
                    <input type="hidden" name="search" value="course_search">
 
                    <!-- Course Category -->
                    <?php 
                    $category_filter  =  get_theme_mod('category_filter', true);
                    $level_filter     =  get_theme_mod('level_filter', true);
                    $price_filter     =  get_theme_mod('price_filter', true);
                    $lang_filter      =  get_theme_mod('lang_filter', true);

                    if( $category_filter && is_array($course_terms_cat) && count($course_terms_cat)) : ?>
                        <div class="single-filter">
                            <h4 class="d-inline-block"><?php esc_html_e('Category', 'skillate'); ?></h4>

                            <div class="d-inline-block float-right text-right filter-clear-btn">
                                <a href="<?php echo esc_url($course_archive_url); ?>">
                                    <?php esc_html_e('Clear', 'skillate');?>
                                </a>
                            </div>
                            
                            <?php
                            foreach ($course_terms_cat as $course_term){
                                $childern = get_categories(
                                    array(
                                        'parent'    => $course_term->term_id,
                                        'taxonomy'  => 'course-category'
                                    )
                                ); ?>

                                <div class="skillate-archive-single-cat">
                                    <label for="cat-<?php echo esc_attr($course_term->slug) ?>">
                                        <input
                                            class="course_searchword course-category"
                                            type="checkbox"
                                            name="course_category[]"
                                            value="<?php echo esc_attr($course_term->slug) ?>"
                                            id="cat-<?php echo esc_attr($course_term->slug) ?>"
                                            data-url="<?php echo get_template_directory_uri().'/lib/course-cat.php'; ?>"
                                            <?php echo in_array($course_term->term_id, $selected_cat) ? 'checked="checked"' : ''; ?>
                                        >
                                        <span class="filter-checkbox"></span>
                                        <?php echo esc_attr($course_term->name); ?>
                                    </label>
                                    <?php
                                        if(count($childern)){
                                            echo "<i class='category-toggle fas fa-plus'></i>";
                                        }
                                    ?>
                                    <?php if(count($childern)) : ?>
                                        <div class="skillate-archive-childern"  style="display: none;">
                                            <?php foreach ($childern as $child){ ?>
                                                <label for="cat-<?php echo esc_attr($child->slug) ?>">
                                                    <input
                                                            type="checkbox"
                                                            name="course_category[]"
                                                            value="<?php echo esc_attr($child->term_id) ?>"
                                                            id="cat-<?php echo esc_attr($child->slug) ?>"
                                                        <?php echo in_array($child->term_id, $selected_cat) ? 'checked="checked"' : ''; ?>
                                                    >
                                                    <span class="filter-checkbox"></span>
                                                    <?php echo esc_attr($child->name) ?>
                                                </label>
                                            <?php } ?>
                                        </div>
                                    <?php endif; ?>
                                </div>

                            <?php  } ?>
                        </div>
                    <?php endif; ?>

                    <!-- Course Level -->
                    <?php if( $level_filter ) : ?>
                    <div class="single-filter">
                        <h4><?php esc_html_e('Level', 'skillate'); ?></h4>
                        <?php

                        foreach ($course_levels as $key => $course_level){
                            if($key == 'all_levels') continue; ?>

                                <label for="<?php echo esc_attr($key); ?>">
                                    <input
                                        class="course_searchword course-level"
                                        type="checkbox"
                                        name="course_level[]"
                                        value="<?php echo esc_attr($key); ?>"
                                        id="<?php echo esc_attr($key); ?>"
                                        data-url="<?php echo get_template_directory_uri().'/lib/course-cat.php'; ?>"
                                        <?php echo in_array($key, $selected_level) ? 'checked="checked"' : ''; ?>
                                    >
                                    <span class="filter-checkbox"></span>
                                    <?php echo esc_html($course_level); ?>
                                </label>

                            <?php
                            }
                        ?>
                    </div>
                    <?php endif; ?>
                    <!-- Course Level -->

                    <!-- Course Price -->
                    <?php if( $price_filter ) : ?>
                    <div class="single-filter">
                        <h4><?php esc_html_e('Price', 'skillate'); ?></h4>
                        <?php
                        foreach ($course_types as $key => $course_type){
                            if($key == 'all_levels') continue;
                            ?>
                            <label for="<?php echo esc_attr($key); ?>">
                                <input
                                    class="course_searchword course-price"
                                    type="checkbox"
                                    name="course_type[]"
                                    value="<?php echo esc_attr($key); ?>"
                                    id="<?php echo esc_attr($key); ?>"
                                    data-url="<?php echo get_template_directory_uri().'/lib/course-cat.php'; ?>"
                                    <?php echo in_array($key, $selected_type) ? 'checked="checked"' : ''; ?>
                                >
                                <span class="filter-checkbox"></span>
                                <?php echo esc_html($course_type); ?>
                            </label>
                            <?php
                        }
                        ?>
                    </div>
                    <?php endif; ?>
                    <!-- Course Price -->


                    <!-- Course Language -->
                    <?php if( $lang_filter && is_array($course_terms_languages) && count($course_terms_languages)) : ?>
                        <div class="single-filter">
                            <h4><?php esc_html_e('Language', 'skillate'); ?></h4>
                            <?php
                            foreach ($course_terms_languages as $course_language){
                                $childern = get_categories(
                                    array(
                                        'parent'    => $course_language->term_id,
                                        'taxonomy'  => 'course-language'
                                    )
                                ); ?>

                                <div class="skillate-archive-single-cat">
                                    <label for="cat-<?php echo esc_attr($course_language->slug) ?>">
                                        <input
                                            class="course_searchword course-tag"
                                            type="checkbox"
                                            name="course_category[]"
                                            value="<?php echo esc_attr($course_language->slug) ?>"
                                            id="cat-<?php echo esc_attr($course_language->slug) ?>"
                                            data-url="<?php echo get_template_directory_uri().'/lib/course-cat.php'; ?>"
                                            <?php echo in_array($course_language->term_id, $selected_cat) ? 'checked="checked"' : ''; ?>
                                        >
                                        <span class="filter-checkbox"></span>
                                        <?php echo esc_attr($course_language->name); ?>
                                    </label>
                                    <?php
                                        if(count($childern)){
                                            echo "<i class='category-toggle fas fa-plus'></i>";
                                        }
                                    ?>
                                    <?php if(count($childern)) : ?>
                                        <div class="skillate-archive-childern"  style="display: none;">
                                            <?php foreach ($childern as $child){ ?>
                                                <label for="cat-<?php echo esc_attr($child->slug) ?>">
                                                    <input
                                                            type="checkbox"
                                                            name="course_category[]"
                                                            value="<?php echo esc_attr($child->term_id) ?>"
                                                            id="cat-<?php echo esc_attr($child->slug) ?>"
                                                        <?php echo in_array($child->term_id, $selected_cat) ? 'checked="checked"' : ''; ?>
                                                    >
                                                    <span class="filter-checkbox"></span>
                                                    <?php echo esc_attr($child->name) ?>
                                                </label>
                                            <?php } ?>
                                        </div>
                                    <?php endif; ?>
                                </div>

                            <?php  } ?>
                        </div>
                    <?php endif; ?>
                </form>
            </div>
        <?php endif; ?>

        <div class="col">

            <?php if ( empty(get_search_query()) ): ?>
                <?php if($top_filter) { ?>
                    <div class="skillate-course-filter-wrap row align-items-center">
                        <div class="skillate-course-archive-filters-wrap col-auto">
                            <form class="skillate-course-filter-form" method="get">
                                <select name="tutor_course_filter" class="small">
                                    <option value="newest_first" <?php if (isset($_GET["tutor_course_filter"]) ? selected("newest_first",$_GET["tutor_course_filter"]) : "" ); ?> ><?php _e("Newest Item", 'skillate'); ?></option>
                                    <option value="oldest_first" <?php if (isset($_GET["tutor_course_filter"]) ? selected("oldest_first",$_GET["tutor_course_filter"]) : "" ); ?>><?php _e("Oldest Item", 'skillate'); ?></option>
                                    <option value="course_title_az" <?php if (isset($_GET["tutor_course_filter"]) ? selected("course_title_az",$_GET["tutor_course_filter"]) : "" ); ?>><?php _e("Course Title (a-z)", 'skillate'); ?></option>
                                    <option value="course_title_za" <?php if (isset($_GET["tutor_course_filter"]) ? selected("course_title_za",$_GET["tutor_course_filter"]) : "" ); ?>><?php _e("Course Title (z-a)", 'skillate'); ?></option>
                                </select>
                            </form>
                        </div>
                        <div class="skillate-course-archive-results-wrap ml-auto col-auto total-courses">
                            <div class="d-none d-md-block">
                            <?php
                                $courseCount = tutor_utils()->get_archive_page_course_count();
                                printf(__('%s Courses', 'skillate'), "<strong>{$q->post_count}</strong>");
                            ?>
                            </div>
                            <div class="d-md-none">
                                <div class="courses-mobile-filter">
                                    <i class="fas fa-filter"></i>
                                    <span><?php echo esc_html__('Filter', 'skillate'); ?></span>
                                </div>
                            </div>
                        </div>
                    </div>
                <?php } ?>
            <?php endif ?>

            <!-- Course Result -->
            <div class="course-search-results">
                <div class="course-spinner"><div class="spinner-cls"></div></div>
            </div> 

            <div class="skillate-courses-wrap course-archive">
                <?php
                    $i = 0;
                    $max_new_post = get_theme_mod('new_course_count', 5);
                    if($q->have_posts()){
                        while ($q->have_posts()) {
                            $i++;
                            $q->the_post();
                            $idd = get_the_ID();
                            $best_selling = get_post_meta($idd, 'skillate_best_selling', true);

                            global $authordata;
                            $profile_url = tutor_utils()->profile_url($authordata->ID)
                            ?>
                            <div class="row skillate-course-col align-items-lg-center">
                                <div class="col-lg-4 col-sm-12">
                                    <div class="skillate-course-media mb-lg-0 mb-4">
                                        <a href="<?php the_permalink(); ?>">
                                            <?php the_post_thumbnail('skillate-courses');?>
                                        </a>
                                        <div class="tutor-course-loop-header-meta">
                                            <?php
                                            $is_wishlisted = tutor_utils()->is_wishlisted($idd);
                                            $has_wish_list = '';
                                            if ($is_wishlisted){
                                                $has_wish_list = 'has-wish-listed';
                                            }
                                            if(is_user_logged_in()){
                                            echo '<span class="tutor-course-wishlist"><a href="javascript:;" class="tutor-icon-fav-line tutor-course-wishlist-btn '.$has_wish_list.' " data-course-id="'.$idd.'"></a> </span>';
                                            }else{
                                                echo '<span class="tutor-course-wishlist"><a class="tutor-icon-fav-line" data-toggle="modal" href="#modal-login"></a></span>';
                                            }
                                            ?>
                                        </div>
                                        <div class="course-media-hover">
                                            <a class="archive-course-view" href="<?php the_permalink(); ?>">
                                                <?php echo esc_html__('View Course', 'skillate'); ?>
                                            </a>
                                            <div class="skillate-course-enroll-btn">
                                                <?php
                                                $course_id = get_the_ID();
                                                if (tutor_utils()->is_enrolled()) {
                                                    $enroll_btn = '<div class="tutor-loop-cart-btn-wrap"><a href="'. get_the_permalink(). '">'.__('Enrolled', 'skillate'). '</a></div>';
                                                } else {
                                                    $enroll_btn = '<div class="tutor-loop-cart-btn-wrap"><a href="'. get_the_permalink(). '">'.__('Enroll Now', 'skillate'). '</a></div>';
                                                }
                                                
                                                if (tutor_utils()->is_course_purchasable()) {
                                                    $enroll_btn = tutor_course_loop_add_to_cart(false);

                                                    $product_id = tutor_utils()->get_course_product_id($course_id);
                                                    // $product    = wc_get_product( $product_id );
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
                                        <div class="d-none d-sm-block">
                                        <?php if($best_selling == !false) {?>
                                        <span class="best-sell-tag">
                                            <?php echo esc_html__('Featured', 'skillate'); ?>
                                        </span>
                                        <?php }else if($i <= $max_new_post){?>
                                        <span class="best-sell-tag new-tag">
                                            <?php echo esc_html__('New', 'skillate'); ?>
                                        </span>
                                        <?php }?>
                                        </div>

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
                                    <div class="d-block d-sm-none">
                                        <?php if($best_selling == !false) {?>
                                        <span class="best-sell-tag">
                                            <?php echo esc_html__('Featured', 'skillate'); ?>
                                        </span>
                                        <?php }else if($i <= $max_new_post){?>
                                        <span class="best-sell-tag new-tag">
                                            <?php echo esc_html__('New', 'skillate'); ?>
                                        </span>
                                        <?php }?>
                                    </div>
                                    <div class="text-sm-right ">
                                        <?php if(tutor_utils()->is_enrolled() ) { ?>
                                            <div class="course-single-price course-archive-price mb-lg-3 mb-0">
                                                <div class="price"><a href="<?php the_permalink();?>"><?php echo esc_html__('Enrolled', 'skillate'); ?></a></div>
                                            </div>
                                        <?php } else { ?>
                                            <div class="course-single-price course-archive-price mb-lg-3 mb-0">
                                                <?php tutor_course_price(); ?>
                                            </div>
                                        <?php } ?>
                                        <div class="tutor-single-course-rating">
                                            <?php
                                            $skillate_course_rating = tutor_utils()->get_course_rating();
                                            tutor_utils()->star_rating_generator($skillate_course_rating->rating_avg);
                                            $product_id = tutor_utils()->get_course_product_id();
                                            if ( class_exists( 'woocommerce' )){
                                                $product = wc_get_product( $product_id );
                                            }
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
                    }else{
                        ?>

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


            <?php if($pagination) { ?>
                <div class="archive-course-pagination">
                    <?php
                    $page_numb = max( 1, get_query_var('paged') );
                    $max_page = $q->max_num_pages;
                    echo skillate_pagination( $page_numb, $max_page );
                    ?>
                </div>
            <?php } ?>
        </div>
    </div>
    <?php

    wp_reset_query();
    $output = ob_get_contents();
    ob_end_clean();
    echo $output;
}

add_shortcode('skillate-course', 'skillate_course_function');