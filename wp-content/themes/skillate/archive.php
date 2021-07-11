<?php get_header(); ?>
<?php get_template_part('lib/sub-header'); ?>

<section id="main">
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
                            $skillate_index++;   
                        }  
                    endwhile;
                else:
                    get_template_part( 'post-format/content', 'none' );
                endif;
                if($skillate_index !=  1 ){ ?>
                   </div><!--/row-->
                <?php } ?>
                <?php
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