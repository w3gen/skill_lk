<?php 
    $bottom_layout = get_theme_mod('bottom_style', 'layout_one');;
    $skillate_col = get_theme_mod( 'bottom_column', 3 );
    $bottom_class = $bottom_layout == 'layout_one' ? '6' : esc_attr($skillate_col);
    $client_logo_title = get_theme_mod('client_logo_title', 'As Featured In');
    $client_col = $client_logo_title == !'' ? '9' : '12';

    //$slide_count      = get_theme_mod('client_slide_count', 3) ? 3 : 3;
    $slide_autoplay   = get_theme_mod('client_slide_autoplay', true) ? 'true' : 'false';
    //$course_layout = get_theme_mod('course_single_layout', 'layout_one');

    // $course_layout_class = '';
    // if( $course_layout == 'layout_two' ){
    //     $course_layout_class = 'course-single-2';
    // }

 ?>
 <?php if (is_active_sidebar('footer_top') ) { ?>
<div class="footer-top-section">
    <div class="container">
        <div class="row align-items-center">
            <?php if($client_logo_title) {?>
            <div class="col-md-3">
                <h4 class="client-logo-title">
                    <?php echo $client_logo_title; ?>
                </h4>
            </div>
            <?php }?>
            <div class="col-md-<?php echo esc_attr($client_col); ?>">
                <div dir="rtl" class="client-logo-carousel" data-autoplay="<?php echo esc_attr($slide_autoplay); ?>">
                <?php if (is_active_sidebar('footer_top')) {
                        dynamic_sidebar('footer_top');
                    } ?>
                </div>
            </div>
        </div>
    </div>
</div>
<?php }?>
<?php 
if (is_active_sidebar('bottom1') || is_active_sidebar('bottom2') || is_active_sidebar('bottom3') || is_active_sidebar('bottom4') ) {?>
<div id="bottom-wrap"  class="footer"> 
    <div class="container">
        <div class="row clearfix border-wrap">
            <?php if (is_active_sidebar('bottom1')):?>
                <div class="col-sm-6 col-lg-<?php echo $bottom_layout == 'layout_one' ? '6' : esc_attr($skillate_col);?>">
                    <?php dynamic_sidebar('bottom1'); ?>
                </div>
            <?php endif; ?> 
            <?php if (is_active_sidebar('bottom2')):?>
                <div class="col-6 col-lg-<?php echo $bottom_layout == 'layout_one' ? '2' : esc_attr($skillate_col);?>">
                    <?php dynamic_sidebar('bottom2'); ?>
                </div>
            <?php endif; ?>
            <?php if (is_active_sidebar('bottom3')):?>
                <div class="col-6 col-lg-<?php echo $bottom_layout == 'layout_one' ? '2' : esc_attr($skillate_col);?>">
                    <?php dynamic_sidebar('bottom3'); ?>
                </div>  
            <?php endif; ?>  
            <?php if (is_active_sidebar('bottom4')):?>                 
                <div class="col-6 col-lg-<?php echo $bottom_layout == 'layout_one' ? '2' : esc_attr($skillate_col);?>">
                    <?php dynamic_sidebar('bottom4'); ?>
                </div>
            <?php endif; ?>  
        </div>
    </div>
</div><!--/#bottom-wrap-->
<?php } ?>


    <?php if ( get_theme_mod( 'footer_en', true )) { ?>
        <footer id="footer-wrap"> 
            <div class="container">
                <div class="row clearfix">
                    <?php if ( get_theme_mod( 'copyright_en', true )) { ?>

                        <?php if (get_theme_mod( 'bottom_footer_menu', true ) ) { ?>
                        <div class="col-sm-12 order-2 order-md-1 text-md-left col-md-auto">
                        <?php } else{?>
                        <div class="col-sm-12 order-2 order-md-1 col-md-12 text-center">
                        <?php }?>
                            <div class="footer-copyright">
                                <?php $skillate_footer_logo = get_theme_mod( 'footer_logo', false );
                                    if( !empty($skillate_footer_logo) ) { ?>
                                        <img class="enter-logo img-responsive" src="<?php echo esc_url( $skillate_footer_logo ); ?>" alt="<?php esc_html_e( 'Logo', 'skillate' ); ?>" title="<?php esc_html_e( 'Logo', 'skillate' ); ?>"> 
                                <?php } ?>

                                <?php if( get_theme_mod( 'copyright_en', true ) ) { ?>
                                    <?php echo wp_kses_post( get_theme_mod( 'copyright_text', '2020 skillate. All Rights Reserved.')); ?>
                                <?php } ?>
                            </div> <!-- col-md-6 -->
                        </div> <!-- end footer-copyright -->
                    <?php } ?>   

                    <?php if ( is_active_sidebar('footer_bottom')) { ?>
                        <?php if( get_theme_mod( 'copyright_en', true ) ) { ?>
                        <div class="col-auto order-1 order-md-2 ml-lg-auto text-lg-right">
                        <?php }else{ ?>
                        <div class="col-sm-12 order-1 order-md-2 col-md-12 text-center">
                        <?php } ?>
                            <div class="footer-payment-method-widget row align-items-center">

                                <?php if(get_theme_mod('payment_method_title', 'Secure payment:')) {?>
                                <div class="col-auto">
                                    <h5><?php echo esc_html(get_theme_mod('payment_method_title', 'Secure payment:')) ?></h5>
                                </div>
                                <?php }?>

                                <div class="col-auto">
                                    <?php 
                                    if (is_active_sidebar('footer_bottom')) {
                                        dynamic_sidebar('footer_bottom');
                                    }?>
                                </div>

                            </div>
                        </div>
                    <?php } ?>   
                    
                </div><!--/.row clearfix-->    
            </div><!--/.container-->    
        </footer><!--/#footer-wrap-->    
    <?php } ?>

    </div> <!-- #page -->

<?php wp_footer(); ?>
</body>
</html>
