<?php
$permalink 	= get_permalink(get_the_ID());
$titleget 	= get_the_title();
$media_url 	= '';
if( has_post_thumbnail( get_the_ID() ) ){
    $thumb_src =  wp_get_attachment_image_src( get_post_thumbnail_id( get_the_ID() ), 'full' ); 
    $media_url = $thumb_src[0];
}
?>
<?php if ( get_theme_mod( 'blog_social_share', true ) ) { ?>
	<div class="social-share-wrap">
        <span><?php esc_html_e('Share', 'skillate') ?></span>
		<ul>
			<li>
				<a href="#" data-type="facebook" data-url="<?php echo esc_url($permalink); ?>" data-title="<?php echo esc_html($titleget); ?>" data-description="<?php echo esc_html($titleget) ?>" data-media="<?php echo esc_url( $media_url ); ?>" class="prettySocial fab fa-facebook"></a>
			</li>
			<li>
				<a href="#" data-type="twitter" data-url="<?php echo esc_url($permalink); ?>" data-description="<?php echo esc_html($titleget); ?>" class="prettySocial fab fa-twitter"></a>
			</li>
			<li>
				<a href="#" data-type="googleplus" data-url="<?php echo esc_url($permalink); ?>" data-description="<?php echo esc_html($titleget); ?>" class="prettySocial fab fa-google-plus"></a>
			</li>
			<li>
				<a href="#" data-type="pinterest" data-url="<?php echo esc_url($permalink); ?>" data-description="<?php echo esc_html($titleget); ?>" data-media="<?php echo esc_url( $media_url ); ?>" class="prettySocial fab fa-pinterest"></a>
			</li>
			<li>
				<a href="#" data-type="linkedin" data-url="<?php echo esc_url($permalink); ?>" data-title="<?php echo esc_html($titleget); ?>" data-description="<?php echo esc_html($titleget) ?>" data-via="<?php echo get_theme_mod( 'wp_linkedin_user' ); ?>" data-media="<?php echo esc_url( $media_url ); ?>" class="prettySocial fab fa-linkedin"></a>
			</li>
		</ul>
	</div>
<?php } ?>