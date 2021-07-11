<div class="entry-summary clearfix">
    <?php 
        if ( is_single() ) {
            the_content();
        } else {
            if ( get_theme_mod( 'blog_intro_en', true ) ) { 
                if ( get_theme_mod( 'blog_post_text_limit', 160 ) ) {
                    $skillate_textlimit = get_theme_mod( 'blog_post_text_limit', 160 );
                    if (get_theme_mod( 'blog_intro_text_en', true )) {
                        echo wp_kses_post(skillate_excerpt_max_charlength($skillate_textlimit));
                    }
                } else {
                    the_content();
                }
                if ( get_theme_mod( 'blog_continue_en', false ) ) { 
                    if ( get_theme_mod( 'blog_continue', 'Read More' ) ) {
                        $skillate_continue = esc_html( get_theme_mod( 'blog_continue', 'Read More' ) );
                        echo '<p class="wrap-btn-style"><a class="btn btn-style" href="'.esc_url(get_permalink()).'">'. esc_html($skillate_continue) .' <i class="fas fa-long-arrow-alt-right"></i></a></p>';
                    } 
                }
                
            }
            if ( get_theme_mod( 'blog_author', true ) ){?>
                <div class="meta-author">
                    <h4>
                        <a href="<?php echo esc_url(get_author_posts_url( get_the_author_meta( 'ID' ) )); ?>">
                        <?php
                            $post_author_id = get_post_field( 'post_author', get_the_ID() );
                            if(function_exists('tutor_utils')){
                                echo tutor_utils()->get_tutor_avatar($post_author_id, 'thumbnail');
                            }else{
                                $get_avatar_url = get_avatar_url($post_author_id, 'thumbnail');
                                echo "<img alt='' src='$get_avatar_url' />";
                            }
                        ?>
                            <?php echo wp_kses_post(get_the_author_meta('display_name')); ?>
                        </a>
                    </h4>
                </div>
            <?php } 
        } 
    ?>
</div> <!-- //.entry-summary -->