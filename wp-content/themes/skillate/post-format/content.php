<?php 
$has_thumb = has_post_thumbnail() ? 'has-post-thumb' : 'no-post-thumb';
if( is_single() ): ?>
    <article id="post-<?php the_ID(); ?>" <?php post_class('skillate-post skillate-single-post single-content-flat'); ?>>
<?php else: ?>
    <article id="post-<?php the_ID(); ?>" <?php post_class('skillate-post row align-items-center skillate-index-post'); ?>>
<?php endif; ?>

    <?php if (is_single()) { ?>

        <?php if ( has_post_thumbnail() ) { ?>
	        <div class="featured-wrap">
                <?php the_post_thumbnail('full'); 
                ?>
            </div>
        <?php } ?>
        
        <div class="container <?php echo esc_attr($has_thumb); ?>">
            <div class="row">
                <div class="col-md-8">
                    <div class="single-blog-info <?php echo esc_attr($has_thumb); ?>">
                        <div class="single-blog-post-top">
                            <?php if ( get_theme_mod( 'blog_category_single', true ) ): ?>
                                <span class="post-category">
                                    <?php echo wp_kses_post(get_the_category_list(', ')); ?>
                                </span>
                            <?php endif; ?>
                            <?php the_title( '<h2 class="content-item-title">', '</h2>' ); ?>
                            <div class="row">
                                <div class="col-lg-6 col-md-5">
                                    <div class="blog-post-meta-wrap">
                                        <ul class="blog-post-meta clearfix"> 
                                            <?php if ( get_theme_mod( 'blog_author_single', false ) ): ?>
                                                <li class="meta-author">
                                                    <span class="img-author"><i class="far fa-user"></i>
                                                        <a href="<?php echo esc_url(get_author_posts_url( get_the_author_meta( 'ID' ) )); ?>"> <?php esc_html_e('By ', 'skillate'); ?><?php echo wp_kses_post(get_the_author_meta('display_name')); ?></a>
                                                    </span>
                                                </li>
                                            <?php endif; ?>
                                            <?php if ( get_theme_mod( 'blog_date_single', true ) ) { ?>
                                                <li>
                                                    <div class="blog-date-wrapper">
                                                    <?php echo get_the_date(); ?>  
                                                    </div>
                                                </li>
                                            <?php } ?> 

                                            <?php if ( get_theme_mod( 'blog_tags_single', false ) ) { ?>
                                                <li><?php echo wp_kses_post(get_the_tag_list('',', ','')); ?></li> 
                                            <?php } ?>

                                            <?php if ( get_theme_mod( 'blog_comment_single', false ) ) { ?>
                                                    <li><i class="far fa-comment"></i>
                                                        <span><?php comments_number( '0', '1', '%' ); ?><?php esc_html_e(' Comments', 'skillate') ?></span>
                                                    </li>
                                            <?php } ?>  
                                        </ul>
                                    </div>
                                </div>
                                <div class="col-lg-6 col-md-7">
                                    <?php if(get_theme_mod('blog_social_share', true)) {
                                        get_template_part('lib/social-share');
                                    }?>
                                </div>
                            </div>
                        </div>
                        <?php get_template_part( 'post-format/entry-content-blog' ); ?>
                    </div> 
                </div>
                <div class="col-md-4">
                    <div class="post-single-sidebar">
                    <?php if(is_single()) {?>
                        <div class="single-post-author <?php echo esc_attr($has_thumb); ?>">
                            <?php 
                            global $post;
                            $post_user_id = get_post_field( 'post_author', $post->ID );
                            $post_user_data = get_user_meta( $post_user_id );
                            ?>
                            <a href="<?php echo get_author_posts_url($post_user_id); ?>">
                                <?php echo get_avatar( get_the_author_meta( 'ID' ) , 74 ); ?>
                            </a>
                            <?php if($post_user_data['first_name'][0] || $post_user_data['last_name'][0]) {?>
                            <h3>
                                <a href="<?php echo get_author_posts_url($post_user_id); ?>">
                                <?php echo $post_user_data['first_name'][0].' '.$post_user_data['last_name'][0]; ?>
                                </a>
                            </h3>
                            <?php }?>
                            <p><?php echo $post_user_data['description'][0]; ?></p>
                        </div>
                    <?php }?>
                    <?php get_sidebar(); ?>
                    </div>
                </div>
            </div>
        </div>
    <?php } ?> 

    <?php if ( has_post_thumbnail() && !is_single()){ ?>
        <div class="blog-details-img <?php if(!is_single()) { echo 'col-sm-5';} ?>">
            <a href="<?php the_permalink(); ?>">
                <?php the_post_thumbnail('skillate-squre', array('class' => 'img-fluid')); ?>
            </a>
        </div>
    <?php }  ?>

    <div class="skillate-blog-title <?php if(!is_single() && has_post_thumbnail()) { echo 'col-sm-7';} else{ echo 'col-md-12';}?>"> 
        <?php
            if (! is_single()) { ?>
        
            <?php if (has_post_thumbnail()){ ?>
                <ul class="blog-post-meta clearfix"> 
            <?php }else { ?>
                <ul class="blog-post-meta not-thumb clearfix"> 
            <?php } ?>
            
                <?php if ( get_theme_mod( 'blog_date', false ) ) { ?>
                    <li>
                        <div class="blog-date-wrapper">
                            <a href="<?php the_permalink(); ?>"><time datetime="<?php echo get_the_date('Y-m-d') ?>"><i class="far fa-calendar"></i> <?php echo get_the_date(); ?></time></a>
                        </div>
                    </li>
                <?php } ?> 

                <?php if ( get_theme_mod( 'blog_category', true ) ): ?>
                    <li class="meta-category">
                        <i class="far fa-folder"></i>
                        <?php echo wp_kses_post(get_the_category_list(', ')); ?>
                    </li>
                <?php endif; ?>

                <?php if ( get_theme_mod( 'blog_tags', false ) ) { ?>
                    <li><?php echo wp_kses_post(get_the_tag_list('<i class="far fa-tags"></i> ',', ','')); ?></li> 
                <?php } ?>

                <?php if ( get_theme_mod( 'blog_comment', false ) ) { ?>
                    <li><i class="far fa-comment"></i><?php comments_number( '0', '1', '%' ); ?></li>
                <?php } ?>  

            </ul>
        <?php the_title( '<h3 class="content-item-title"><a href="'.esc_url(get_the_permalink()).'">', '</a></h3>' ); ?>
        <?php } ?>

        <div class="entry-blog">
            <?php
                if (!is_single()) {
                    get_template_part( 'post-format/entry-content' );
                }
            ?> 
        </div> <!--/.entry-meta -->
    </div>
</article><!--/#post-->