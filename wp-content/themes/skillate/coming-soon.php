<?php
/*
* Template Name: Coming Soon
*/

get_header('alternative'); ?>

<?php
    $skillate_comingsoon_date = '';
    if ( get_theme_mod('comingsoon_date') ) {
        $skillate_comingsoon_date = esc_attr( get_theme_mod('comingsoon_date', '2019-08-09') );
    }
?>

<div class="container">
    <div class="comingsoon comingsoon-content">
        <div class="row justify-content-center">
            <div class="col-sm-12" id="skillate-comingsoon">
                
                <?php
                $skillate_logoimg   = get_theme_mod( 'logo-404', get_template_directory_uri().'/images/logo.png' );
                if( !empty($skillate_logoimg) ) { ?>
                    <img class="enter-logo img-responsive" src="<?php echo esc_url( $skillate_logoimg ); ?>" alt="<?php esc_html_e( 'Logo', 'skillate' ); ?>" title="<?php esc_html_e( 'Logo', 'skillate' ); ?>">
                <?php } ?>

                <div class="comingsoon-warper">
                    <?php $skillate_countdate = esc_url_raw(str_replace('-', '/', $skillate_comingsoon_date)); ?>
                    <h2 class="comingsoon-title"><?php echo esc_html(get_theme_mod( 'comingsoontitle', 'Coming Soon' )); ?></h2>
                    <div class="comingsoon-newslatter-descrip">
                        <?php echo esc_html(get_theme_mod( 'coming_description', 'We are come back soon' )); ?>
                    </div>

                    <?php if (get_theme_mod('countdown_en', false)): ?>
                        <div class="counter-class" data-date="'<?php echo esc_attr($skillate_countdate);?>">
                            <div id="comingsoon-countdown">
                                <div class="countdown-section"><span class="countdown-amount first-item countdown-days counter-days"></span> <span class="countdown-period"><?php esc_html_e('Days', 'skillate') ?></span></div>
                                <div class="countdown-section"><span class="countdown-amount countdown-hours counter-hours"></span> <span class="countdown-period"><?php esc_html_e('Hours', 'skillate') ?></span></div>
                                <div class="countdown-section"><span class="countdown-amount countdown-minutes counter-minutes"></span> <span class="countdown-period"><?php esc_html_e('Minutes', 'skillate') ?></span></div>
                                <div class="countdown-section"><span class="countdown-amount countdown-seconds counter-seconds"></span> <span class="countdown-period"><?php esc_html_e('Seconds', 'skillate') ?></span></div>
                            </div>
                        </div>
                    <?php endif ?>
                </div>
                
                <!-- News Latter -->
                <?php if( get_theme_mod('newsletter')): ?>
                    <div class="coming-soon-newslatter">
                        <?php echo do_shortcode(get_theme_mod( 'newsletter' )); ?>
                    </div>
                <?php endif; ?>

                <div class="comingsoon-footer">
                    <div class="social-share">
                        <ul>
                            <?php if ( get_theme_mod( 'comingsoon_facebook' ) ) { ?>
                                <li><a target="_blank" href="<?php echo esc_url(get_theme_mod( 'comingsoon_facebook' )); ?>"><i class="fab fa-facebook-f"></i></a></li>
                            <?php } ?>
                            <?php if ( get_theme_mod( 'comingsoon_twitter' ) ) { ?>
                                <li><a target="_blank" href="<?php echo esc_url( get_theme_mod( 'comingsoon_twitter' ) ); ?>"><i class="fab fa-twitter"></i></a></li>
                            <?php } ?>
                            <?php if ( get_theme_mod( 'comingsoon_google_plus' ) ) { ?>
                                <li><a target="_blank" href="<?php echo esc_url( get_theme_mod( 'comingsoon_google_plus' ) ); ?>"><i class="fab fa-google"></i></a></li>
                            <?php } ?>
                            <?php if ( get_theme_mod( 'comingsoon_pinterest' ) ) { ?>
                                <li><a target="_blank" href="<?php echo esc_url( get_theme_mod( 'comingsoon_pinterest' ) ); ?>"><i class="fab fa-pinterest"></i></a></li>
                            <?php } ?>
                            <?php if ( get_theme_mod( 'comingsoon_youtube' ) ) { ?>
                                <li><a target="_blank" href="<?php echo esc_url( get_theme_mod( 'comingsoon_youtube' ) ); ?>"><i class="fab fa-youtube"></i></a></li>
                            <?php } ?>
                            <?php if ( get_theme_mod( 'comingsoon_linkedin' ) ) { ?>
                                <li><a target="_blank" href="<?php echo esc_url( get_theme_mod( 'comingsoon_linkedin') ); ?>"><i class="fab fa-linkedin-in"></i></a></li>
                            <?php } ?>
                            <?php if ( get_theme_mod( 'comingsoon_dribbble' ) ) { ?>
                                <li><a target="_blank" href="<?php echo esc_url( get_theme_mod( 'comingsoon_dribbble' ) ); ?>"><i class="fab fa-dribbble"></i></a></li>
                            <?php } ?>
                            <?php if ( get_theme_mod( 'comingsoon_instagram' ) ) { ?>  
                                <li><a target="_blank" href="<?php echo esc_url( get_theme_mod( 'comingsoon_instagram' ) ); ?>"><i class="fab fa-instagram"></i></a></li>
                            <?php } ?>
                        </ul>
                    </div> <!--/.social-share-->
                </div><!--/.comingsoon-footer-->
            </div>
        </div>
    </div>

</div>

<?php get_footer('alternative');