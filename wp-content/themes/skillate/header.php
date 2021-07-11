<!DOCTYPE html> 
<html <?php language_attributes(); ?>> 
<head>
	<meta charset="<?php bloginfo( 'charset' ); ?>">
	<meta name="viewport" content="width=device-width, initial-scale=1.0">
	<link rel="profile" href="http://gmpg.org/xfn/11">
	<?php wp_head(); ?>
</head>

<body <?php body_class(); ?>> 
    <?php
	$layout = get_theme_mod( 'boxfull_en', 'fullwidth' );

    $headerlayout = get_theme_mod( 'head_style', 'solid' );
    $header_style = get_post_meta( get_the_ID(), "skillate_header_style", true );
    if($header_style){
        if($header_style == 'transparent_header'){
            $headerlayout =  'transparent';
		}elseif($header_style == 'white_header'){
			$headerlayout =  'white';
		}else{
            $headerlayout =  'solid';
        }
	}
	
    $sticky_class = get_theme_mod( 'header_fixed', ' disable-sticky ' );
    $sticky_style = get_post_meta( get_the_ID(), "skillate_sticky_header", true );
    if($sticky_style){
        if($sticky_style == 'enable'){
            $sticky_class =  'enable-sticky';
		}else{
            $sticky_class =  ' disable-sticky ';
        }
	}

	$en_search = false; $en_search_class = '';
	if(get_post_meta( get_the_ID(), 'skillate_disable_header_search' , true)){
		$en_search = false;
	}else{
		$en_search = get_theme_mod( 'en_header_search', false );
	}

	if($en_search){
		$en_search_class = ' ';
	}else{
		$en_search_class = ' ml-auto ';
	}

	$hide_header = ' ';
	if(get_post_meta( get_the_ID(), 'skillate_hide_header_footer' , true)){
		$hide_header = 'hide-header-footer';
	}

	
    //wp_body_open hook from WordPress 5.2
    if ( function_exists( 'wp_body_open' ) ) {
        wp_body_open();
    }?>
	<div id="page" class="hfeed site <?php echo esc_attr($hide_header); ?>">
		<a class="skip-link screen-reader-text" href="#content"><?php esc_html_e( 'Skip to content', 'skillate' ); ?></a>

		<?php if(!is_user_logged_in() && get_post_meta(get_the_ID(), 'skillate_en_topbar', true) == '1'){ ?>
		<div class="skillate-topbar-wrap text-center">
			<div class="container">
				<p>
					<?php echo esc_html__('Not sure? Watch a FREE webclass: ', 'skillate'); ?>
					<a data-toggle="modal" href="#modal-registration"><?php echo esc_html__('Sign up here!', 'skillate'); ?></a>
				</p>
			</div>
		</div>
		<?php }?>

	    <header id="masthead" class="site-header header-<?php echo esc_attr($headerlayout).' '.esc_attr($sticky_class); ?>">  	
		<div class="container-fluid">
			<div class="row">
				<?php if( ! class_exists('wp_megamenu_initial_setup')) { ?>
					<div class="col-md-12">
						<div class="primary-menu">
							<div class="row align-items-center">
								<div class="col-auto">
									<div class="skillate-navbar-header">
										<div class="logo-wrapper">
											<a class="skillate-navbar-brand" href="<?php echo esc_url(site_url()); ?>">
												<?php
												$logoimg   = get_theme_mod( 'logo', get_parent_theme_file_uri().'/images/logo.svg' );
												$logotext  = get_theme_mod( 'logo_text', 'skillate' );
												$logotype  = get_theme_mod( 'logo_style', 'logoimg' );

												$header_logo = wp_get_attachment_image(get_post_meta( get_the_ID(), "skillate_individual_page_logo", true ));

												if(!empty($header_logo)){
													echo '<a href="'.esc_url(home_url()).'">'.$header_logo.'</a>';
												}else if($logotype == 'logoimg') {
													if(!empty($logoimg)){
														echo '<img 
															class="enter-logo img-responsive" 
															src="'.esc_url($logoimg).'" 
															alt="'.esc_html('Logo', 'skillate').'"
															title="'.esc_html('Logo', 'skillate').'"
														/>';
													}else{
														echo get_bloginfo('name');
													}
												}else{
													if(!empty($logotext)){
														echo esc_html($logotext);
													}else{
														echo get_bloginfo('name');
													}
												}
												?>
											</a>
											
											
										</div>   
									</div> <!--/#skillate-navbar-header-->   
								</div> <!--/.col-sm-2-->
								<div class="col-auto thm-cat-col d-none d-lg-block">
									<?php
										$en_header_cat_menu = get_theme_mod('en_header_cat_menu', false);
										$category_menu_label = get_theme_mod('category_menu_label', 'Category');
										if($en_header_cat_menu && taxonomy_exists('course-category')) :
										
										$category_count = get_theme_mod( 'category_count', 8 );
										$cat_orderby = get_theme_mod( 'cat_orderby', 'name' );
										$cat_order = get_theme_mod( 'cat_order', 'ASC' );
										
										$course_cats = get_terms( 'course-category', array(
											'orderby'    => $cat_orderby,
											'order'      => $cat_order,
											'number'     => $category_count,
											'hide_empty' => true
										));
									?>
									
									<div class="header-cat-menu">
										<?php if ($category_menu_label): ?>
											<div class="header-cat-text">
												<?php echo $category_menu_label; ?>
												<i class="fas fa-caret-down"></i>
											</div>
										<?php endif ?>
										<ul class="skillate-course-category">
											<?php 
											if( function_exists('get_term_meta') ){
												foreach( $course_cats as $course_cat ) :
													$cat_thumb_id = get_term_meta( $course_cat->term_id, 'thumbnail_id', true );
													$shop_catalog_img = wp_get_attachment_image_src( $cat_thumb_id, 'full' );
													$term_link = get_term_link( $course_cat, 'course-category' );?>
													<li>
														<a href="<?php echo $term_link; ?>">
															<div class="media">
																<div class="cat-media-img">
																	<img src="<?php echo $shop_catalog_img[0]; ?>" alt="<?php echo $course_cat->name; ?>" />
																</div>
																<div class="media-body align-middle">
																	<h4><?php echo $course_cat->name; ?></h4>
																	<span><?php echo $course_cat->description; ?></span>
																</div>
															</div>
														</a>
													</li>
												<?php endforeach; 
											}
											wp_reset_query(); ?>
										</ul>
									</div>
									<?php endif; ?>
								</div><!--/.thm-cat-col-->
								
								<!-- Primary Menu -->
								<div class="col col-lg-auto common-menu space-wrap">
									<div class="header-common-menu">
										<?php if ( has_nav_menu( 'primary' ) ) { ?>
											<div id="main-menu" class="common-menu-wrap">
												<?php 
													wp_nav_menu(  
														array(
															'theme_location'  => 'primary',
															'container'       => '', 
															'menu_class'      => 'nav',
															'fallback_cb'     => 'wp_page_menu',
															'depth'            => 4,
															//'walker'          => new Megamenu_Walker()
														)
													); 
												?>  
											</div><!--/.col-sm-9--> 
										<?php } else{ ?>
										<div class="no-menu-action">
											<a href="<?php echo home_url()?>/wp-admin/nav-menus.php">
											<i class="fas fa-bars"></i>
											<?php echo esc_html__('Set a Menu', 'skillate'); ?></a>
										</div>
										<?php }?>
									</div><!-- header-common-menu -->
								</div><!-- common-menu -->
								
								<?php if($en_search) {?>
								<div class="col ml-auto col-auto skillate-header-search">
									<?php
										if(shortcode_exists('course_search')) echo do_shortcode('[course_search]');
									?>
									<!-- <a href="#" class="skillate-search search-open-icon">
										<i class="fas fa-search"></i>
									</a> -->
								</div>
								<?php }?>

									<?php 
									if(function_exists('tutor_utils')) : ?>
									<div class="col-md-auto <?php echo esc_attr($en_search_class);?> col-auto header-login-cart-wrap">
										<?php
										if(get_theme_mod('en_header_shopping_cart', true) ){ 
											if ( class_exists( 'WooCommerce' ) && !is_cart() && !is_checkout() ) {
										?>
										<div class="skillate-header-cart mr-lg-2 d-none d-lg-inline-block">
											<?php echo skillate_header_cart(); ?>
										</div>
										<?php }?>
										
										<?php if( class_exists( 'Easy_Digital_Downloads' ) ) {?>
											<div class="skillate-header-cart mr-lg-2 d-none d-lg-inline-block">
													<div class="site-header-cars-edd">
														<span class="cart-icons">
															
															<a class="cart-content-edd" href="<?php echo edd_get_checkout_uri(); ?>" title="<?php esc_attr_e( 'View your shopping cart', 'skillate' ); ?>">
																<img src="<?php echo get_template_directory_uri(); ?>/images/cart-icon.svg" alt="<?php esc_attr_e( 'View your shopping cart', 'skillate' );?>">		
																<span class="count"><?php echo edd_get_cart_quantity();?></span>
															</a>
													</span>
												</div>
											</div>
										<?php } ?>
									<?php } ?>

										<?php if(get_theme_mod('header_login_btn', true ) && !is_user_logged_in()){ 
											$header_login_btn_text = get_theme_mod('header_login_btn_text', 'Login');
											$header_reg_btn_text = get_theme_mod('header_reg_btn_text', 'Sign Up');
											?>
										<div class="skillate-header-login d-inline-block ml-4">
											<div class="header-login-wrap">
												<a data-toggle="modal" href="#modal-login">
													<?php echo esc_html($header_login_btn_text); ?>
												</a>
												<a data-toggle="modal" href="#modal-registration">
													<?php echo esc_html($header_reg_btn_text); ?>
												</a>
											</div>
										</div>
										<?php } ?>

										<?php if(is_user_logged_in()){ ?>
											<div class="skillate-header-login d-inline-block ml-4">
												<div class="header_profile_menu">
													<div class="skillate_header_profile_photo">
														<?php
															if(function_exists('tutor_utils')){
																echo tutor_utils()->get_tutor_avatar(get_current_user_id(), 'thumbnail');
															}else{
																$get_avatar_url = get_avatar_url(get_current_user_id(), 'thumbnail');
																echo "<img alt='' src='$get_avatar_url' />";
															}
														?>
													</div>
													<ul>
														<?php
															if(function_exists('tutor_utils')) {
																$dashboard_page_id = tutor_utils()->get_option('tutor_dashboard_page_id');
																$dashboard_pages = tutor_utils()->tutor_dashboard_pages();
										
																foreach ($dashboard_pages as $dashboard_key => $dashboard_page){
																	$menu_title = $dashboard_page;
																	$menu_link = tutils()->get_tutor_dashboard_page_permalink($dashboard_key);
																	$separator = false;
																	if (is_array($dashboard_page)){
																		if(!current_user_can(tutor()->instructor_role)) continue;
																		$menu_title = tutor_utils()->array_get('title', $dashboard_page);
																		/**
																		 * Add new menu item property "url" for custom link
																		 */
																		if (isset($dashboard_page['url'])){
																			$menu_link = $dashboard_page['url'];
																		}
																		if (isset($dashboard_page['type']) && $dashboard_page['type'] == 'separator'){
																			$separator = true;
																		}
																	}
																	if ($separator) {
																		echo '<li class="tutor-dashboard-menu-divider"></li>';
																		if ($menu_title) {
																			echo "<li class='tutor-dashboard-menu-divider-header'>{$menu_title}</li>";
																		}
																	} else {
																		if ($dashboard_key === 'index') $dashboard_key = '';
																		echo "<li><a href='".esc_url($menu_link)."'>".esc_html($menu_title)." </a> </li>";
																	}
																}
															}
														?>
													</ul>
												</div>
											</div>
										<?php }?>
									</div>
									<?php endif; ?>

							</div>
						</div>
					</div>
				<?php } ?>

				<!-- For Megamenu -->
				<?php if( class_exists('wp_megamenu_initial_setup') ) { ?>
					<div class="col-auto common-menu common-main-menu">
						<div class="header-common-menu">
							<?php if ( has_nav_menu( 'primary' ) ) { ?>
								<div id="main-menu" class="common-menu-wrap">
									<?php 
										wp_nav_menu(  
											array(
												'theme_location'  => 'primary',
												'container'       => '', 
												'menu_class'      => 'nav',
												'fallback_cb'     => 'wp_page_menu',
												'depth'            => 4,
												//'walker'          => new Megamenu_Walker()
											)
										); 
									?>  
								</div><!--/.col-sm-9--> 
							<?php } else{ ?>
							<div class="no-menu-action">
								<a href="<?php echo home_url()?>/wp-admin/nav-menus.php">
									<i class="fas fa-bars"></i>
									<?php echo esc_html__('Set a Menu', 'skillate'); ?>
								</a>
							</div>
							<?php }?>
						</div><!-- header-common-menu -->
					</div><!-- common-menu -->
				<?php } ?>
			</div><!--row-->  
		</div><!--/.container--> 
	</header> <!-- header -->

	<!-- Splash Screen -->
	<?php if(!is_user_logged_in() && function_exists('tutor_utils') && get_theme_mod('splash_enable', true)) {?>

	<div class="skillate-splash-screen d-lg-none">
		<div class="skillate-splash-content">
			<div class="skillate-splash-content-inner">
				<div class="skillate-splash-logo">
					<?php 
						$splash_logo  = get_theme_mod('splash_logo', get_template_directory_uri().'/images/logo.svg');
					?>
					<img src="<?php echo esc_url($splash_logo);?>" alt="">
				</div>
				<h2 class="skillate-splash-title"><?php echo get_theme_mod('splash_title', 'Welcome') ?></h2>
				<h4 class="splash-content"><?php echo get_theme_mod('splash_content', 'Labore et dolore magna aliqua. Ut enim ad minim veniam') ?></h4>
				<div class="splash-login-btn">
					<a data-toggle="modal" href="#modal-login" ><?php echo esc_html__('Login Now','skillate'); ?></a>
					<span class="skillate-skip-login">
						<?php echo esc_html__('Skip Login', 'skillate'); ?>
					</span>
				</div>
			</div>
		</div>
	</div>	
	<?php }?>	
	<!-- Splash Screen -->

	<!-- Mobile Bottom Menu Start-->
	<?php if(function_exists('tutor_utils') && get_theme_mod('mobile_menu_en', true)) {?>
	<div class="skillate-mobile-menu-bottom d-lg-none">
		<div class="skillate-menu-bottom-inner">
			<div class="skillate-single-menu-bottom active">
				<a href="<?php echo get_home_url(); ?>">
					<img src="<?php echo esc_url(get_template_directory_uri()); ?>/images/mobile-nav-icon/home.svg">
					<div class="d-block"><?php echo esc_html__('Home', 'skillate'); ?></div>
				</a>
			</div>
			<?php if(get_theme_mod('mobile_menu_cat', true)) {?>
			<div class="skillate-single-menu-bottom">
				<a href="#" class="skillate-single-bottom-category">
					<img src="<?php echo esc_url(get_template_directory_uri()); ?>/images/mobile-nav-icon/category.svg">
					<div class="d-block"><?php echo esc_html__('Category', 'skillate'); ?></div>
				</a>
			</div>
			<?php }?>
			<?php if(get_theme_mod('mobile_menu_search', true)) {?>
			<div class="skillate-single-menu-bottom">
				<a href="#" class="skillate-search search-open-icon">
					<img src="<?php echo esc_url(get_template_directory_uri()); ?>/images/mobile-nav-icon/search.svg">
					<div class="d-block"><?php echo esc_html__('Search', 'skillate'); ?></div>
				</a>
			</div>
			<?php }?>
			<?php if(get_theme_mod('mobile_menu_cart', true)) {?>
			<div class="skillate-single-menu-bottom">
				<a class="skillate-single-bottom-cart" data-toggle="modal" href="#modal-cart">
					<img src="<?php echo esc_url(get_template_directory_uri()); ?>/images/mobile-nav-icon/cart.svg">
					<div class="d-block"><?php echo esc_html__('Cart', 'skillate'); ?></div>
				</a>
			</div>
			<?php }?>
			<div class="skillate-single-menu-bottom">
				<!-- Mobile Monu -->
				<div class="navbar-toggle" data-toggle="collapse" data-target=".navbar-collapse">
					<img src="<?php echo esc_url(get_template_directory_uri()); ?>/images/mobile-nav-icon/menu.svg">
					<a href="javascript:void(0)">
						<?php echo esc_html__('Menu', 'skillate'); ?>
					</a>
				</div>
				<!-- thm-mobile-menu -->
			</div>
		</div>
	</div>
	<?php }?>
	<!-- Mobile Bottom Menu End -->


	<!-- Mobile Search Start -->
	<div class="top-search-input-wrap">
		<!-- <div class="search-close-icon">
			<span>×</span>
		</div> -->
		<?php 
		$action = function_exists('tutor_utils') ? tutor_utils()->course_archive_page_url() : site_url('/');
		?>
		<form action="<?php echo esc_url($action); ?>" method="get">
			<div class="search skillate-top-search">
				<h3 class="text-center"><?php echo esc_html__('Find your desired course', 'skillate'); ?></h3>
				<div class="skillate_search_input">
					<span class="search-icon-wrap">
						<img src="<?php echo get_template_directory_uri(); ?>/images/search-icon.svg" alt="">
					</span>
					<input type="text" value="<?php echo get_search_query(); ?>" name="s" class="form-control" placeholder="<?php esc_html_e('What do you want to learn?','skillate'); ?>"/>
				</div>
				<input type="submit" value="Search">
			</div>
		</form>
	</div>
	<!-- Mobile Search End -->



	<div id="mobile-menu" class="thm-mobile-menu collapse navbar-collapse"> 
		<?php if(is_user_logged_in()) {?>
		<div class="mobile-menu-author-wrap">
			<div class="media">
				<?php
					if(function_exists('tutor_utils')){
						echo tutor_utils()->get_tutor_avatar(get_current_user_id(), 'thumbnail');
					}else{
						$get_avatar_url = get_avatar_url(get_current_user_id(), 'thumbnail');
						echo "<img alt='' src='$get_avatar_url' />";
					}
				?>
				<?php if(wp_get_current_user()) {
				$current_user = wp_get_current_user();
				?>
				<div class="media-body">
					<h3 class="mb-0"><?php echo $current_user->display_name; ?></h3>
					<p><?php echo $current_user->user_email; ?></p>
				</div>
				<?php }?>
			</div>
		</div>
		<?php }?>
		<?php 
			if ( has_nav_menu( 'primary' ) ) {
				wp_nav_menu( 
					array(
						'theme_location'    => 'primary',
						'container'         => false,
						'menu_class'        => 'nav navbar-nav',
						'fallback_cb'       => 'wp_page_menu',
						'depth'             => 3,
						'walker'            => new wp_bootstrap_mobile_navwalker()
					)
				); 
			} 
		?>
		<div class="skillate-mobile-sign-out">
			<?php if(is_user_logged_in()) {?>
			<a href="<?php echo wp_logout_url(home_url()); ?>">
				<img src="<?php echo get_template_directory_uri();?>/images/mobile-nav-icon/sign-out.svg">
				<?php echo esc_html__('Sign Out', 'skillate'); ?>
			</a>
			<?php } else{?>
				<a data-toggle="modal" href="#modal-login">
					<img src="<?php echo get_template_directory_uri();?>/images/mobile-nav-icon/sign-out.svg">
					<?php echo esc_html__('Sign In', 'skillate'); ?>
				</a>
			<?php }?>
		</div>
	</div> 
	<!-- Mobile Menu End-->
	
	<div class="skillate-mobile-category-menu">
		<?php
			if(taxonomy_exists('course-category')) :
			$category_count = get_theme_mod( 'category_count', 8 );
			$cat_orderby = get_theme_mod( 'cat_orderby', 'name' );
			$cat_order = get_theme_mod( 'cat_order', 'ASC' );
			
			$course_cats = get_terms( 'course-category', array(
				'orderby'    => $cat_orderby,
				'order'      => $cat_order,
				'number'     => $category_count,
				'hide_empty' => true
			));
		?>
		<h3 class="mobile-category-title">
			<?php echo __('Select Your Favourite<br> <strong>Category</strong> And Start Learning.', 'skillate'); ?>
		</h3>
		<div class="header-cat-menu">
			<ul class="skillate-course-category">
				<?php 
				if( function_exists('get_term_meta') ){
					foreach( $course_cats as $course_cat ) :
						$cat_thumb_id = get_term_meta( $course_cat->term_id, 'thumbnail_id', true );
						$shop_catalog_img = wp_get_attachment_image_src( $cat_thumb_id, 'full' );
						$term_link = get_term_link( $course_cat, 'course-category' );?>
						<li>
							<a href="<?php echo $term_link; ?>">
								<div class="media">
									<img src="<?php echo $shop_catalog_img[0]; ?>" alt="<?php echo $course_cat->name; ?>" />
									<div class="media-body align-middle">
										<h4><?php echo $course_cat->name; ?></h4>
										<span><?php echo $course_cat->description; ?></span>
									</div>
								</div>
							</a>
						</li>
					<?php endforeach; 
				}
				wp_reset_query(); ?>
			</ul>
		</div>
		<?php endif; ?>
	</div>