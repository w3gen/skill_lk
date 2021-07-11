<?php get_header();
/*
*Template Name: 404 Page Template
*/
?>
 
<?php $skillate_logo_404   = get_theme_mod( 'logo_404', false ); ?>

<div class="skillate-error">
	<div class="skillate-error-wrapper">
		<div class="container">
			<div class="row">
				<div class="col-md-7">
					<?php if ($skillate_logo_404){ ?>
						<img class="enter-logo img-responsive" src="<?php echo esc_url( $skillate_logo_404 ); ?>" alt="<?php  esc_html_e( 'Logo', 'skillate' ); ?>" title="<?php esc_html_e( 'Logo', 'skillate' ); ?>">
					<?php }else { ?>
						<h1> <?php echo esc_html(get_bloginfo('name')); ?> </h1>
					<?php } ?>
				</div>
				<div class="col-md-5">
					<h1 class="error-title"><?php echo esc_html(get_theme_mod( '404_title', '' )); ?></h1>
					<h2 class="error-message-title"><?php echo esc_html(get_theme_mod( '404_description', '' )); ?></h2>
					
					<a href="<?php echo esc_url( home_url('/') ); ?>" class="btn btn-secondary">
						<?php echo esc_html(get_theme_mod( '404_btn_text', 'Back To Home' )); ?>
					</a>
				</div>
			</div>
		</div>
	</div>
</div>
<?php get_footer();
