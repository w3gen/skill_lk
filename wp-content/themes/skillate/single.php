<?php get_header(); ?>

<section id="main">
    <?php if ( have_posts() ) :  ?> 
        <?php while ( have_posts() ) : the_post(); ?>
            <?php get_template_part( 'post-format/content', get_post_format() ); ?>  
        <?php endwhile; ?> 
    <?php else: ?>
        <?php get_template_part( 'post-format/content', 'none' ); ?>
    <?php endif; ?>
    <div class="clearfix"></div>

    <!-- Related post -->
    <div class="container">
        <?php get_template_part( 'lib/related-post', 'none' ); ?>
    </div>
	
</section> <!-- #main -->
<?php get_footer();