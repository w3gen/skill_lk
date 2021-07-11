<?php
$user_name = sanitize_text_field(get_query_var('tutor_student_username'));
$get_user = tutor_utils()->get_user_by_login($user_name);
$user_id = $get_user->ID;

$pageposts = tutor_utils()->get_courses_by_instructor($user_id);
?>

<div class="instructor-single-all-course">
	<?php
		$i = 0;
		$max_new_post = get_theme_mod('new_course_count', 5);
		if ($pageposts):
			global $post;
			$output = '';
			foreach ($pageposts as $post):
				setup_postdata($post);
				$idd = get_the_ID();
				$best_selling = get_post_meta($idd, 'skillate_best_selling', true);

				global $authordata;
				$profile_url = tutor_utils()->profile_url($authordata->ID)
				?>
				<div class="row skillate-course-col align-items-center">
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
							
						</div>
					</div>

					<div class="col-lg-3 col-sm-4 order-1 order-sm-2">
						<div class="d-sm-none">
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
						<div class="text-md-right">
							<div class="course-single-price course-archive-price mb-lg-3 mb-0">
								<?php tutor_course_price(); ?>
							</div>
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

			<?php endforeach;
			// Author Grid Paginations

			wp_reset_postdata();

		else : ?>
		<div>
			<h2><?php _e("Not Found" , 'skillate'); ?></h2>
			<p><?php _e("Sorry, but you are looking for something that isn't here." , 'skillate'); ?></p>
		</div>
	<?php endif; ?>
</div>