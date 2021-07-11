<?php get_header(); ?>

<section id="main" class="single-download-page">
    <?php if ( have_posts() ) :  ?> 
        <?php while ( have_posts() ) : the_post(); ?>
			<div id="post-<?php the_ID(); ?>" <?php post_class('skillate-post skillate-single-post single-content-flat'); ?>>
		        <div class="container">
		            <div class="row">
		                <div class="col-md-6">
		                    <div class="single-blog-info">
		                    	<?php if ( has_post_thumbnail() ) { ?>
							        <div class="featured-wrap">
						    	        <?php the_post_thumbnail('full', array('class' => 'img-responsive'));?>
						            </div>
						        <?php } ?>
		                        <?php the_content();; ?>
		                    </div> 
		                </div>
		                <div class="col-md-6">
		                    <div class="post-single-sidebar">
	                            <?php the_title( '<h2 class="content-item-title">', '</h2>' ); ?>
		                    	<?php  the_content(); ?>
		                    	<?php if ( get_theme_mod( 'blog_category_single', true ) ): ?>
	                                <span class="post-category">
	                                    <?php echo wp_kses_post(get_the_category_list(', ')); ?>
	                                </span>
	                            <?php endif; ?>
		                    </div>
		                </div>
		            </div>
		        </div>
			</div><!--/#post-->
        <?php endwhile; ?> 
    <?php endif; ?>
</section> <!-- #main -->
<?php get_footer();