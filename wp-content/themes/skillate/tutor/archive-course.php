<?php
/**
 * Template for displaying courses
 *
 * @since v.1.0.0
 *
 * @author Themeum
 * @url https://themeum.com
 */

get_header(); ?>

<?php if ( empty(get_search_query()) ): ?>
	<?php get_template_part('lib/sub-header'); ?>
<?php endif ?>

<div class="generic-padding">
    <?php
    if(get_theme_mod('featured_slide_en', true)){
        get_template_part('lib/course-archive-top-carousel'); 
        }
    ?>
    <div class="container archive-container">
        <?php do_shortcode('[skillate-course]'); ?>
    </div>
    <?php get_template_part('lib/course-archive-instructor'); ?>
</div>

<?php get_footer();
