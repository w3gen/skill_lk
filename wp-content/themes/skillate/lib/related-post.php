<!-- Related Post -->
<?php if(get_theme_mod('blog_related_post', false)) {?>
	<div class="related-entries">
		<div class="row">
			<?php 
			global $post;
			$skillate_categories = get_the_category($post->ID);
			if ($skillate_categories) { ?>
				<!-- Title -->
				<div class="col-md-12">
					<h3 class="related-post-title"><?php esc_html_e('Related Posts', 'skillate') ?></h3>
				</div>

				<?php 
				$skillate_category_ids = array();
				foreach($skillate_categories as $skillate_individual_category) $skillate_category_ids[] = $skillate_individual_category->term_id;
				$skillate_args=array(
					'category__in' 		=> $skillate_category_ids,
					'post__not_in' 		=> array($post->ID),
					'posts_per_page'	=> 3,
					'ignore_sticky_posts'	=>1
				);
				$skillate_thequery = new wp_query( $skillate_args );
				if( $skillate_thequery->have_posts() ) { ?>

					<?php while( $skillate_thequery->have_posts() ) {
						$skillate_thequery->the_post();?>

						<div class="col-sm-4">
							<div class="relatedthumb">
								<a href="<?php echo esc_url(get_permalink()); ?>" class="img-wrapper">
									<?php echo get_the_post_thumbnail(get_the_ID(), 'skillate-squre', array('class' => 'img-responsive')); ?>
								</a>
							</div>
							<div class="relatedcontent">
								<h3>
									<a href="<?php echo esc_url(get_permalink()); ?>" rel="bookmark" title="<?php the_title_attribute(); ?>"><?php the_title_attribute(); ?></a>
								</h3>
								<span class="meta-category">
									<?php echo wp_kses_post(get_the_category_list(', ')); ?>
								</span>
							</div>
						</div>
					<?php } ?>

				<?php } }
				wp_reset_postdata();  
			?>
		</div> <!-- Row end -->

	</div>

<?php }?>