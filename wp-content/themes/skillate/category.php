<?php get_header(); ?>

<section id="main">
    <?php get_template_part('lib/sub-header'); ?>
    <div class="container blog-full-container">
        <div class="row">
            <div id="content" class="site-content col-sm-8" role="main">
                <?php
                $skillate_index = 1;
                $skillate_col = get_theme_mod( 'blog_column', 12 );     
                if ( have_posts() ) :
                    while ( have_posts() ) : the_post();
                        if ( $skillate_index == '1' ) { ?>
                            <div class="row">
                        <?php }?>
                            <div class="col-md-<?php echo esc_attr($skillate_col);?>">
                                <?php get_template_part( 'post-format/content', get_post_format() ); ?>
                            </div>
                        <?php if ( $skillate_index == (12/esc_attr($skillate_col) )) { ?>
                            </div><!--/row-->
                        <?php $skillate_index = 1;
                        }else{
                            $index++;   
                        }  
                    endwhile;
                else:
                    get_template_part( 'post-format/content', 'none' );
                endif;
                wp_reset_postdata();
                if($skillate_index !=  1 ){ ?>
                   </div><!--/row-->
                <?php } ?>

                <?php global $wp_query;
                    $skillate_page_numb = max( 1, get_query_var('paged') );
                    $skillate_max_page = $wp_query->max_num_pages;
                    if($skillate_max_page>=2){
                    echo wp_kses_post(skillate_pagination( $skillate_page_numb, $skillate_max_page ));  
                    }
                ?>
            </div>
            
            <!-- sidebar -->
            <div class="col-md-3 ml-lg-auto">
                <?php get_sidebar();?>
            </div>
            <!-- sidebar -->
            
        </div> <!-- .row -->
    </div><!-- .container -->
</section> 
<?php get_footer();