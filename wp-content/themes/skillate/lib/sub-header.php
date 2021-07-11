<?php
    global $post;
    $skillate_output = $subtext = ''; 

    $attachment_id = attachment_url_to_postid(get_header_image());
    $skillate_banner_img = wp_get_attachment_image_url($attachment_id, 'skillate-large');
    $skillate_banner_color = get_theme_mod( 'sub_header_banner_color', '#fff' );

    $skillate_output = ( $skillate_banner_img ) ? ( 'style="background-image:url('.esc_url( $skillate_banner_img ).');background-size: cover;background-position: 50% 50%;"' ) : ( 'style="background-color:'.esc_attr( $skillate_banner_color ).';"' );
?>

<div class="subtitle-cover sub-title" <?php print wp_kses_post($skillate_output);?>>
    <div class="container">
        <div class="row subtitle-border align-items-center">
            <div class="col-md-6">
                <?php
                    global $wp_query;
                    if(isset($wp_query->queried_object->name)){
                        if (get_theme_mod( 'header_title_enable', true )) {
                            if($wp_query->queried_object->name != ''){
                                if($wp_query->queried_object->name == 'product' ){
                                    echo '<h2 class="page-leading">'.esc_html__('Shop','skillate').'</h2>';
                                }else{
                                    echo '<h2 class="page-leading">'.$wp_query->queried_object->name.'</h2>'; 
                                }
                            }else{
                                echo '<h2 class="page-leading">'.get_the_title().'</h2>';
                            }
                        }
                    }else{
                        

                        if( is_search() ){
                            if (get_theme_mod( 'subtitle_enable', true )) {
                                if (get_theme_mod( 'header_subtitle_text', '' )){
                                    echo '<h3 class="page-subleading">'. get_theme_mod( 'header_subtitle_text','' ).'</h3>';
                                }
                            }

                            if (get_theme_mod( 'header_title_enable', true )) {
                                $text = '';
                                $first_char = esc_html__('Search','skillate');
                                if( isset($_GET['s'])){ $text = $_GET['s']; }
                                echo '<h2 class="page-leading">'.$first_char.':'.$text.'</h2>';
                            }
                        }
                        else if( is_home() ){
                            if (get_theme_mod( 'subtitle_enable', true )) {
                                if (get_theme_mod( 'header_subtitle_text', '' )){
                                    echo '<h3 class="page-subleading">'. get_theme_mod( 'header_subtitle_text','' ).'</h3>';
                                }
                            }
                            if (get_theme_mod( 'header_title_enable', true )) {
                                if (get_theme_mod( 'header_title_text', 'Blog' )){
                                    echo '<h2 class="page-leading">'. get_theme_mod( 'header_title_text','Blog' ).'</h2>';
                                }
                            }
                        }
                        else if( is_single()){

                            if (get_theme_mod( 'subtitle_enable', true )) {
                                if (get_theme_mod( 'header_subtitle_text', '' )){
                                    echo '<h3 class="page-subleading">'. get_theme_mod( 'header_subtitle_text','' ).'</h3>';
                                }
                            }
                            if (get_theme_mod( 'header_title_enable', true )) {
                                if (get_post_type() == 'gallery') {
                                    echo '<h2 class="page-leading">'. esc_html__( 'Gallery','skillate' ).'</h2>';
                                } elseif (get_post_type() == 'product'){
                                    echo '<h2 class="page-leading">'.esc_html__('Product Details','skillate').'</h2>';
                                }else {
                                    if (get_theme_mod( 'header_title_text', 'Latest Blog' )){
                                        echo '<h2 class="page-leading">'. get_theme_mod( 'header_title_text','Latest Blog' ).'</h2>';
                                    }

                                }
                            }

                        }
                        else if(is_archive()){
                            if (get_theme_mod( 'header_title_enable', true )) {
                                echo '<h2 class="page-leading">'.get_the_archive_title().'</h2>';
                                if ( $subtext != ""){
                                    echo '<h3 class="page-subleading">'. $subtext .'</h3>';
                                }
                            }
                        }
                        else{
                            if (get_theme_mod( 'header_title_enable', true )) {
                                echo '<h2 class="page-leading">'.get_the_title().'</h2>';
                                if ( $subtext != ""){
                                    echo '<h3 class="page-subleading">'. $subtext .'</h3>';
                                }
                            }
                        }
                    }
                ?>
            </div>
            <div class="col-md-6">
                <?php skillate_breadcrumb(); ?>
            </div>
        </div>
    </div>
</div><!--/.sub-title-->

