<?php get_header(); ?>
<section id="main" class="generic-padding">
    <?php get_template_part('lib/sub-header')?>
    <div class="container">
        <div class="row">
            <div id="content" class="site-content col-sm-12" role="main">
                <?php query_posts(array( 'post_type' => 'post', 's' => get_search_query() )); ?>
                <div class="row">
                    <?php if ( have_posts() ){
                        while ( have_posts() ) : the_post(); ?>
                            <div class="separator-wrapper col-md-4">
                                <?php get_template_part( 'post-format/content', get_post_format() ); ?>
                            </div>
                        <?php endwhile;
                        } else { ?>
                            <div class="col-md-12">
                                <div class="error-log">
                                    <h2 class="search-error-title"><?php esc_html_e( 'Nothing Found', 'skillate' ); ?></h2>
                                    <p class="search-error-text"><?php esc_html_e( 'Sorry, but nothing matched your search terms. Please try again with different keywords.', 'skillate' ); ?></p>
                                    <?php get_search_form();?>
                                </div>
                            </div>
                        <?php } ?>
                </div><!--/row--> 
                <?php 
                    $skillate_page_numb = max( 1, get_query_var('paged') );
                    $skillate_max_page = $wp_query->max_num_pages;
                    if($skillate_max_page>=2){
                        echo wp_kses_post(skillate_pagination( $skillate_page_numb, $skillate_max_page ));  
                    }
                ?>
            </div><!-- content -->
        </div>
    </div> <!-- .container --> 
</section> <!-- #main -->
<?php get_footer();