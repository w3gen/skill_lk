<?php get_header(); ?>

<section id="main">
    <?php get_template_part('lib/sub-header'); ?>
    <div class="container blog-full-container">
    <?php
            $args = array( 
                'post_type'         => 'post',
                'meta_key'          => 'skillate_post_featured',
                'meta_value'        => 1,
                'order'             => 'DESC',
                'posts_per_page'    => 3,
            );
            $feature_post = get_posts($args);

        ?>
         <?php if(count($feature_post) > 0)  {?>
        <div class="row feature-blog">
            <?php 
            foreach($feature_post as $post){ setup_postdata( $post );  ?>
        
                    <div class="col-sm-4 skillate-image-wrap">
                        <div class="skillate-blog addon-article leading-item">
                            <div class="article-image-wrap">
                                <?php  if ( has_post_thumbnail()) { ?>
                                    <a class="item-image"  href="<?php the_permalink(); ?>">
                                        <?php echo get_the_post_thumbnail(get_the_ID(), 'skillate-squre', array('class' => 'img-responsive')); ?>     
                                    </a>
                                <?php } ?>
                            </div>

                            <div class="article-details">
                                <?php if ( get_theme_mod( 'blog_date', false ) ) { ?>
                                <div class="article-meta">
                                    <span class="meta-date"><?php echo get_the_date(); ?></span>
                                </div>
                                <?php } ?>
                                <h3 class="article-title">
                                    <a href="<?php the_permalink( ); ?>"><?php the_title(); ?></a>
                                </h3>
                                <span class="meta-category">
                                    <?php echo wp_kses_post(get_the_category_list(', ')); ?>
                                </span>
                            </div>
                        </div>
                    </div><!-- end col-8 -->
  
            <?php }  wp_reset_postdata(); ?>
        </div> <!-- row end -->
        <?php } ?>

        <div class="row">
            <div id="content" class="site-content col-md-8" role="main">
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
                        <?php if( $skillate_index == (12/esc_attr($skillate_col) )) { ?>
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