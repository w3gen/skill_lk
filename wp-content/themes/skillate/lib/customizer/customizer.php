<?php

/**
 * Themeum Customizer
 */


if (!class_exists('SKILLATE_THMC_Framework')):

	class SKILLATE_THMC_Framework
	{
		/**
		 * Instance of WP_Customize_Manager class
		 */
		public $wp_customize;


		private $skillate_fields_class = array();

		private $google_fonts = array();

		/**
		 * Constructor of 'SKILLATE_THMC_Framework' class
		 *
		 * @wp_customize (WP_Customize_Manager) Instance of 'WP_Customize_Manager' class
		 */
		function __construct( $wp_customize )
		{
			$this->wp_customize = $wp_customize;

			$this->fields_class = array(
				'text'            => 'WP_Customize_Control',
				'checkbox'        => 'WP_Customize_Control',
				'textarea'        => 'WP_Customize_Control',
				'radio'           => 'WP_Customize_Control',
				'select'          => 'WP_Customize_Control',
				'email'           => 'WP_Customize_Control',
				'url'             => 'WP_Customize_Control',
				'number'          => 'WP_Customize_Control',
				'range'           => 'WP_Customize_Control',
				'hidden'          => 'WP_Customize_Control',
				'date'            => 'SKILLATE_THMC_Date_Control',
				'color'           => 'WP_Customize_Color_Control',
				'upload'          => 'WP_Customize_Upload_Control',
				'image'           => 'WP_Customize_Image_Control',
				'radio_button'    => 'SKILLATE_THMC_Radio_Button_Control',
				'checkbox_button' => 'SKILLATE_THMC_Checkbox_Button_Control',
				'switch'          => 'SKILLATE_THMC_Switch_Button_Control',
				'multi_select'    => 'SKILLATE_THMC_Multi_Select_Control',
				'radio_image'     => 'SKILLATE_THMC_Radio_Image_Control',
				'checkbox_image'  => 'SKILLATE_THMC_Checkbox_Image_Control',
				'color_palette'   => 'SKILLATE_THMC_Color_Palette_Control',
				'rgba'            => 'SKILLATE_THMC_Rgba_Color_Picker_Control',
				'title'           => 'SKILLATE_THMC_Switch_Title_Control',
			);

			$this->load_custom_controls();

			add_action( 'customize_controls_enqueue_scripts', array( $this, 'customizer_scripts' ), 100 );
		}

		public function customizer_scripts()
		{
			wp_enqueue_style( 'wp-color-picker' );
			wp_enqueue_style( 'thmc-select2', SKILLATE_URI.'lib/customizer/assets/select2/css/select2.min.css' );
			wp_enqueue_style( 'thmc-customizer', SKILLATE_URI.'lib/customizer/assets/css/customizer.css' );

			wp_enqueue_script( 'jquery-ui-datepicker' );
			wp_enqueue_script( 'wp-color-picker' );
			wp_enqueue_script( 'thmc-select2', SKILLATE_URI.'lib/customizer/assets/select2/js/select2.min.js', array('jquery'), '1.0', true );
			wp_enqueue_script( 'thmc-rgba-colorpicker', SKILLATE_URI.'lib/customizer/assets/js/thmc-rgba-colorpicker.js', array('jquery', 'wp-color-picker'), '1.0', true );
			wp_enqueue_script( 'thmc-customizer', SKILLATE_URI.'lib/customizer/assets/js/customizer.js', array('jquery', 'jquery-ui-datepicker'), '1.0', true );

			wp_localize_script( 'thmc-customizer', 'thm_customizer', array(
				'ajax_url' => admin_url( 'admin-ajax.php' ),
				'import_success' => esc_html__('Success! Your theme data successfully imported. Page will be reloaded within 2 sec.', 'skillate'),
				'import_error' => esc_html__('Error! Your theme data importing failed.', 'skillate'),
				'file_error' => esc_html__('Error! Please upload a file.', 'skillate')
			) );
		}

		private function load_custom_controls(){
			get_template_part('lib/customizer/controls/radio-button');
            get_template_part('lib/customizer/controls/radio-image');
            get_template_part('lib/customizer/controls/checkbox-button');
            get_template_part('lib/customizer/controls/checkbox-image');
            get_template_part('lib/customizer/controls/switch');
            get_template_part('lib/customizer/controls/date');
            get_template_part('lib/customizer/controls/multi-select');
            get_template_part('lib/customizer/controls/color-palette');
            get_template_part('lib/customizer/controls/rgba-colorpicker');
            get_template_part('lib/customizer/controls/title');

            // Load Sanitize class
            get_template_part('lib/customizer/libs/sanitize');
		}

		public function add_option( $options ){
			if (isset($options['sections'])) {
				$this->panel_to_section($options);
			}
		}



		private function panel_to_section( $options )
		{
			$panel = $options;
			$panel_id = $options['id'];

			unset($panel['sections']);
			unset($panel['id']);

			// Register this panel
			$this->add_panel($panel, $panel_id);

			$sections = $options['sections'];

			if (!empty($sections)) {
				foreach ($sections as $section) {
					$skillate_fields = $section['fields'];
					$section_id = $section['id'];

					unset($section['fields']);
					unset($section['id']);

					$section['panel'] = $panel_id;

					$this->add_section($section, $section_id);

					if (!empty($skillate_fields)) {
						foreach ($skillate_fields as $field) {
							if (!isset($field['settings'])) {
								var_dump($field);
							}
							$field_id = $field['settings'];

							$this->add_field($field, $field_id, $section_id);
						}
					}
				}
			}
		}

		private function add_panel($panel, $panel_id){
			$this->wp_customize->add_panel( $panel_id, $panel );
		}

		private function add_section($section, $section_id)
		{
			$this->wp_customize->add_section( $section_id, $section );
		}

		private function add_field($field, $field_id, $section_id){



			$setting_args = array(
				'default'        => isset($field['default']) ? $field['default'] : '',
				'type'           => isset($field['setting_type']) ? $field['setting_type'] : 'theme_mod',
				'transport'     => isset($field['transport']) ? $field['transport'] : 'refresh',
				'capability'     => isset($field['capability']) ? $field['capability'] : 'edit_theme_options',
			);

			if (isset($field['type']) && $field['type'] == 'switch') {
				$setting_args['sanitize_callback'] = array('SKILLATE_THMC_Sanitize', 'switch_sntz');
			} elseif (isset($field['type']) && ($field['type'] == 'checkbox_button' || $field['type'] == 'checkbox_image')) {
				$setting_args['sanitize_callback'] = array('SKILLATE_THMC_Sanitize', 'multi_checkbox');
			} elseif (isset($field['type']) && $field['type'] == 'multi_select') {
				$setting_args['sanitize_callback'] = array('SKILLATE_THMC_Sanitize', 'multi_select');
				$setting_args['sanitize_js_callback'] = array('SKILLATE_THMC_Sanitize', 'multi_select_js');
			}

			$control_args = array(
				'label'       => isset($field['label']) ? $field['label'] : '',
				'section'     => $section_id,
				'settings'    => $field_id,
				'type'        => isset($field['type']) ? $field['type'] : 'text',
				'priority'    => isset($field['priority']) ? $field['priority'] : 10,
			);

			if (isset($field['choices'])) {
				$control_args['choices'] = $field['choices'];
			}

			// Register the settings
			$this->wp_customize->add_setting( $field_id, $setting_args );
			$control_class = isset($this->fields_class[$field['type']]) ? $this->fields_class[$field['type']] : 'WP_Customize_Control';
			// Add the controls
			$this->wp_customize->add_control( new $control_class( $this->wp_customize, $field_id, $control_args ) );
		}
	}

endif;

/**
*
*/
class Skillate_Customize
{
	public $google_fonts = array();

	function __construct( $options )
	{
		$this->options = $options;

		add_action('customize_register', array($this, 'customize_register'));
		add_action('wp_enqueue_scripts', array($this, 'get_google_fonts_data'));

		add_action('wp_ajax_thm_export_data', array($this, 'export_data_cb'));
		add_action('wp_ajax_thm_import_data', array($this, 'import_data_cb'));
	}

	public function customize_register( $wp_customize )
	{
		$skillate_framework = new SKILLATE_THMC_Framework( $wp_customize );

		$skillate_framework->add_option( $this->options );

		$this->import_export_ui( $wp_customize );
	}

	public function import_export_ui( $wp_customize )
	{

		get_template_part( 'lib/customizer/controls/export' );
        get_template_part( 'lib/customizer/controls/import' );

		$wp_customize->add_section( 'thm_import_export', array(
			'title'           => esc_html__( 'Import/Export', 'skillate' ),
			'description'     => esc_html__( 'Import Export Option Data', 'skillate' ),
			'priority'        => 1000,
		) );

		$wp_customize->add_setting( 'thm_export', array(
			'default'        => '',
			'transport'      => 'postMessage',
            'capability'     => 'edit_theme_options',
            'sanitize_callback'  => 'esc_attr',
		) );

		$wp_customize->add_control( new SKILLATE_THMC_Export_Control( $wp_customize, 'thm_export_ctrl', array(
			'label'       => 'Export Theme Data',
			'section'     => 'thm_import_export',
			'settings'    => 'thm_export',
			'type'        => 'export',
			'priority'    => 10,
		) ) );

		$wp_customize->add_setting( 'thm_import', array(
			'default'        => '',
			'transport'      => 'postMessage',
            'capability'     => 'edit_theme_options',
            'sanitize_callback'  => 'esc_attr',
		) );

		$wp_customize->add_control( new SKILLATE_THMC_Import_Control( $wp_customize, 'thm_import_ctrl', array(
			'label'       => 'Import Theme Data',
			'section'     => 'thm_import_export',
			'settings'    => 'thm_import',
			'type'        => 'export',
			'priority'    => 10,
		) ) );
	}

	public function export_data_cb()
	{
		$theme_slug = get_option( 'stylesheet' );
		$mods = get_option( "theme_mods_$theme_slug" );

		header( "Content-Description: File Transfer" );
		header( "Content-Disposition: attachment; filename=theme_data.json" );
		header( "Content-Type: application/octet-stream" );
		echo json_encode($mods);
		exit;
	}

	public function import_data_cb()
	{

        global $wp_filesystem;
		$theme_data = $wp_filesystem->put_contents($_FILES['file']['tmp_name']);

		if (empty($theme_data)) {
			echo 0;
			exit();
		}

		$theme_data = json_decode($theme_data, true);

		if (empty($theme_data)) {
			echo 0;
			exit();
		}

		unset($theme_data['nav_menu_locations']);

		$theme_slug = get_option( 'stylesheet' );
		$mods = get_option( "theme_mods_$theme_slug" );

		if ($mods  === false) {
			$status = add_option( "theme_mods_$theme_slug", $theme_data );
			if ($status) {
				echo 1;
			} else {
				echo 0;
			}
		} else {
			$theme_data['nav_menu_locations'] = $mods['nav_menu_locations'];
			$status = update_option( "theme_mods_$theme_slug", $theme_data );

			if ($status) {
				echo 1;
			} else {
				echo 0;
			}
		}

		exit();
	}

	public function get_google_fonts_data()
	{
		if (isset($this->options['sections']) && !empty($this->options['sections'])) {
			foreach ($this->options['sections'] as $section) {
				if (isset($section['fields']) && !empty($section['fields'])) {
					foreach ($section['fields'] as $field) {
						if (isset($field['google_font']) && $field['google_font'] == true) {
							$this->google_fonts[$field['settings']] = array();

							if (isset($field['default']) && !empty($field['default'])) {
								$this->google_fonts[$field['settings']]["default"] = $field['default'];
							}

							if (isset($field['google_font_weight']) && !empty($field['google_font_weight'])) {
								$this->google_fonts[$field['settings']]["weight"] = $field['google_font_weight'];
							}

							if (isset($field['google_font_weight_default']) && !empty($field['google_font_weight_default'])) {
								$this->google_fonts[$field['settings']]["weight_default"] = $field['google_font_weight_default'];
							}
						}
					}
				}
			}
		}

		$all_fonts = array();

		if (!empty($this->google_fonts)) {
			foreach ($this->google_fonts as $font_id => $font_data) {
				$font_family_default = isset($font_data['default']) ? $font_data['default'] : '';
				$font_family = get_theme_mod( $font_id, $font_family_default );

				if (!isset($all_fonts[$font_family])) {
					$all_fonts[$font_family] = array();
				}

				if (isset($font_data['weight']) && !empty($font_data['weight'])) {
					$font_weight_default = isset($font_data['weight_default']) ? $font_data['weight_default'] : '';

					$font_weight = get_theme_mod( $font_data['weight'], $font_weight_default );

					$all_fonts[$font_family][] = $font_weight;
				}

			}
		}

		$font_url = "//fonts.googleapis.com/css?family=";

		if (!empty($all_fonts)) {

			$i = 0;

			foreach ($all_fonts as $font => $weights) {

				if ($i) {
					$font_url .= "%7C";
				}

				$font_url .= str_replace(" ", "+", $font);

				if (!empty($weights)) {
					$font_url .= ":";
					$font_url .= implode(",", $weights);
				}

				$i++;
			}

			wp_enqueue_style( "tm-google-font", $font_url );
		}
	}
}


// Customizer Section
$skillate_panel_to_section = array(
	'id'           => 'skillate_panel_options',
	'title'        => esc_html( 'Skillate Options', 'skillate' ),
	'description'  => esc_html__( 'Skillate Theme Options', 'skillate' ),
	'priority'     => 10,
	
	'sections'     => array(
		array(
			'id'              => 'header_setting',
			'title'           => esc_html__( 'Header Settings', 'skillate' ),
			'description'     => esc_html__( 'Header Settings', 'skillate' ),
			'priority'        => 10,
			'fields'         => array(
				array(
					'settings' => 'head_style',
					'label'    => esc_html__( 'Select Header Style', 'skillate' ),
					'type'     => 'select',
					'priority' => 10,
					'default'  => 'solid',
					'choices'  => array(
						'transparent' => esc_html__( 'Header Transparent', 'skillate' ),
						'solid' => esc_html__( 'Header Solid', 'skillate' ),
					)
				),
				array(
					'settings' => 'header_color',
					'label'    => esc_html__( 'Header background Color', 'skillate' ),
					'type'     => 'rgba',
					'priority' => 10,
					'default'  => '#0d0e12',
					'dependency' => array(
						'id' => 'head_style',
						'comp' => '!=',
						'value' => 'transparent',
					),
				),
				array(
					'settings' => 'header_padding_top',
					'label'    => esc_html__( 'Header Top Padding', 'skillate' ),
					'type'     => 'number',
					'priority' => 10,
					'default'  => 20,
				),
				array(
					'settings' => 'header_padding_bottom',
					'label'    => esc_html__( 'Header Bottom Padding', 'skillate' ),
					'type'     => 'number',
					'priority' => 10,
					'default'  => 20,
				),

				array(
					'settings' => 'header_fixed',
					'label'    => esc_html__( 'Sticky Header', 'skillate' ),
					'type'     => 'select',
					'priority' => 10,
					'default'  => 'disable-sticky',
					'choices'  => array(
						'enable-sticky'  => esc_html__( 'Sticky Enable', 'skillate' ),
						'disable-sticky' => esc_html__( 'Sticky Disable', 'skillate' ),
					)
				),
				array(
					'settings' => 'sticky_header_color',
					'label'    => esc_html__( 'Sticky background Color', 'skillate' ),
					'type'     => 'rgba',
					'priority' => 10,
					'default'  => '#0d0e12',
				),
				#category menu
                array(
                    'settings' => 'en_header_cat_menu',
                    'label'    => esc_html__( 'Header Category Menu', 'skillate' ),
                    'type'     => 'switch',
                    'priority' => 10,
                    'default'  => false,
                ),
                array(
					'settings' => 'category_count',
					'label'    => esc_html__( 'Number of category', 'skillate' ),
					'type'     => 'number',
					'priority' => 10,
					'default'  => 8,
				),	
				array(
					'settings' => 'cat_orderby',
					'label'    => esc_html__( 'Order By', 'skillate' ),
					'type'     => 'select',
					'priority' => 10,
					'default'  => 'name',
					'choices'  => array(
						'name' => esc_html( 'name', 'skillate' ),
						'id' => esc_html( 'ID', 'skillate' ),
						'slug' => esc_html( 'Slug', 'skillate' ),
					)
				),				
				array(
					'settings' => 'cat_order',
					'label'    => esc_html__( 'Order', 'skillate' ),
					'type'     => 'select',
					'priority' => 10,
					'default'  => 'ASC',
					'choices'  => array(
						'ASC' => esc_html( 'ASC', 'skillate' ),
						'DESC' => esc_html( 'DESC', 'skillate' ),
					)
				),
                array(
                    'settings' => 'category_menu_label',
                    'label'    => esc_html__( 'Category Menu Label Text', 'skillate' ),
                    'type'     => 'text',
                    'priority' => 10,
                    'default'  => 'Browse Course'
				),
                array(
                    'settings' => 'category_triangle_position',
                    'label'    => esc_html__( 'Category Triangle Position', 'skillate' ),
                    'type'     => 'text',
                    'priority' => 10,
                    'default'  => 150
				),
				array(
					'settings' => 'en_header_search',
					'label'    => esc_html__( 'Header Search', 'skillate' ),
					'type'     => 'switch',
					'priority' => 10,
					'default'  => true,
				),
				array(
                    'settings' => 'en_header_shopping_cart',
                    'label'    => esc_html__( 'Enable Header Shopping Cart', 'skillate' ),
                    'type'     => 'switch',
                    'priority' => 10,
                    'default'  => true
                ),
                array(
                    'settings' => 'header_login_btn',
                    'label'    => esc_html__( 'Header Login/Signup Button', 'skillate' ),
                    'type'     => 'switch',
                    'priority' => 10,
                    'default'  => true
                ),
                array(
                    'settings' => 'header_login_btn_text',
                    'label'    => esc_html__( 'Header login button text', 'skillate' ),
                    'type'     => 'text',
                    'priority' => 10,
                    'default'  => 'Login'
                ),
                array(
                    'settings' => 'header_reg_btn_text',
                    'label'    => esc_html__( 'Header signup button text', 'skillate' ),
                    'type'     => 'text',
                    'priority' => 10,
                    'default'  => 'Sign Up'
                ),
		
				
			)//fields
		),//header_setting


		array(
            'id'              => 'skillate_login_options',
            'title'           => esc_html__( 'Social login', 'skillate' ),
            'description'     => esc_html__( 'Social login', 'skillate' ),
            'priority'        => 10,
            // 'active_callback' => 'is_front_page',
            'fields'         => array(
                array(
                    'settings' => 'en_social_login',
                    'label'    => esc_html__( 'Enable Social Login', 'skillate' ),
                    'type'     => 'switch',
                    'priority' => 10,
                    'default'  => true,
                ),
                array(
                    'settings' => 'google_client_ID',
                    'label'    => esc_html__( 'Google Login Client ID* (Leave empty to disable), ', 'skillate' ),
                    'type'     => 'text',
                    'priority' => 10,
                    'default'  => ''
                ),
                array(
                    'settings' => 'facebook_app_ID',
                    'label'    => esc_html__( 'Facebook login App ID* (Leave empty to disable), ', 'skillate' ),
                    'type'     => 'text',
                    'priority' => 10,
                    'default'  => ''
                ),
                array(
                    'settings' => 'twitter_consumer_key',
                    'label'    => esc_html__( 'Twitter Login Consumer Key* (Leave empty to disable), ', 'skillate' ),
                    'type'     => 'text',
                    'priority' => 10,
                    'default'  => ''
                ),
                array(
                    'settings' => 'twitter_consumer_secreat',
                    'label'    => esc_html__( 'Twitter Login Consumer Secret* ', 'skillate' ),
                    'type'     => 'text',
                    'priority' => 10,
                    'default'  => ''
                ),
                array(
                    'settings' => 'twitter_auth_callback_url',
                    'label'    => esc_html__( 'Twitter Login auth redirect URL* ', 'skillate' ),
                    'type'     => 'text',
                    'priority' => 10,
                    'default'  => ''
				),
				array(
					'settings' => 'login_reg_screen_bg_color',
					'label'    => esc_html__( 'Login reg screen bg color', 'skillate' ),
					'type'     => 'rgba',
					'priority' => 10,
					'default'  => '#ffffff',
				),
				array(
					'settings' => 'login_reg_screen_text_color',
					'label'    => esc_html__( 'Login reg screen text color', 'skillate' ),
					'type'     => 'rgba',
					'priority' => 10,
					'default'  => '#1f2949',
				),
            )//fields
		),//Login setting
		

		array(
            'id'              => 'skillate_tutor_options',
            'title'           => esc_html__( 'Course Listing', 'skillate' ),
            'description'     => esc_html__( 'You can customize your course archive page from here.', 'skillate' ),
            'priority'        => 10,
            'fields'         => array(
                array(
                    'settings' => 'featured_slide_en',
                    'label'    => esc_html__( 'Featured Slide', 'skillate' ),
                    'type'     => 'switch',
                    'priority' => 10,
                    'default'  => true
				),
				array(
                    'settings' => 'feature_slider_title',
                    'label'    => esc_html__( 'Feature Slider Title', 'skillate' ),
                    'type'     => 'text',
                    'priority' => 10,
                    'default'  => 'More Courses to get <b>You Started</b>'
				),
				array(
                    'settings' => 'featured_slider_total_item',
                    'label'    => esc_html__( 'Featured Slider Total Item', 'skillate' ),
                    'type'     => 'number',
                    'priority' => 10,
                    'default'  => 20
				),
				array(
                    'settings' => 'top_bottom_slide_count',
                    'label'    => esc_html__( 'Featured Slider Column', 'skillate' ),
					'type'     => 'select',
					'priority' => 10,
					'default'  => '6',
					'choices'  => array(
						'4'    => esc_html__( '4 Column', 'skillate' ),
						'5'    => esc_html__( '5 Column', 'skillate' ),
						'6'    => esc_html__( '6 Column', 'skillate' ),
						'7'    => esc_html__( '7 Column', 'skillate' ),
					)
				),
				array(
                    'settings' => 'slide_center_mod',
                    'label'    => esc_html__( 'Slide Center Mode', 'skillate' ),
                    'type'     => 'switch',
                    'priority' => 10,
                    'default'  => true
				),
				array(
                    'settings' => 'slider_opacity_en',
                    'label'    => esc_html__( 'Slider Opacity Disable', 'skillate' ),
                    'type'     => 'switch',
                    'priority' => 10,
                    'default'  => false
                ),
				array(
                    'settings' => 'course_filter_title',
                    'label'    => esc_html__( 'Course Filter Title', 'skillate' ),
                    'type'     => 'text',
                    'priority' => 10,
                    'default'  => 'All Courses You <b>Can Filter</b>'
				),
				array(
                    'settings' => 'sidebar_filter',
                    'label'    => esc_html__( 'Sidebar filter', 'skillate' ),
                    'type'     => 'switch',
                    'priority' => 10,
                    'default'  => true
                ),

                array(
                    'settings' => 'top_filter_bar',
                    'label'    => esc_html__( 'Course Sorting Bar', 'skillate' ),
                    'type'     => 'switch',
					'priority' => 10,
                    'default'  => true
				),
				array(
                    'settings' => 'category_filter',
                    'label'    => esc_html__( 'Category Filter', 'skillate' ),
                    'type'     => 'switch',
					'priority' => 10,
					'default'  => true
				),
				array(
                    'settings' => 'level_filter',
                    'label'    => esc_html__( 'Level Filter', 'skillate' ),
                    'type'     => 'switch',
					'priority' => 10,
					'default'  => true
				),
				array(
                    'settings' => 'price_filter',
                    'label'    => esc_html__( 'Price Filter', 'skillate' ),
                    'type'     => 'switch',
                    'priority' => 10,
                    'default'  => true
				),
				array(
                    'settings' => 'lang_filter',
                    'label'    => esc_html__( 'Language Filter', 'skillate' ),
                    'type'     => 'switch',
                    'priority' => 10,
					'default'  => true
                ),
                array(
                    'settings' => 'new_course_count',
                    'label'    => esc_html__( 'Latest Items with "NEW" Tag', 'skillate' ),
                    'type'     => 'number',
                    'priority' => 10,
					'default'  => 5
                ),
                array(
                    'settings' => 'course_per_page',
                    'label'    => esc_html__( 'Course Per Page', 'skillate' ),
                    'type'     => 'number',
                    'priority' => 10,
                    'default'  => 9
                ),
                array(
                    'settings' => 'course_pagination',
                    'label'    => esc_html__( 'Course Pagination', 'skillate' ),
                    'type'     => 'switch',
                    'priority' => 10,
                    'default'  => true
				),
				array(	
                    'settings' => 'instructor_slider_title',
                    'label'    => esc_html__( 'Instructor Slider Title', 'skillate' ),
                    'type'     => 'text',
                    'priority' => 10,
                    'default'  => 'Most favourite <b>Instructor.</b>'
				),
				array(
                    'settings' => 'instructor_slider_title_link_text',
                    'label'    => esc_html__( 'Instructor Slider Title Link Text', 'skillate' ),
                    'type'     => 'text',
                    'priority' => 10,
                    'default'  => 'View More'
				),
                array(
                    'settings' => 'instructor_slide',
                    'label'    => esc_html__( 'Instructor Slide', 'skillate' ),
                    'type'     => 'switch',
                    'priority' => 10,
                    'default'  => true
				),
				array(	
                    'settings' => 'instructor_slider_title',
                    'label'    => esc_html__( 'Instructor Slider Title', 'skillate' ),
                    'type'     => 'text',
                    'priority' => 10,
                    'default'  => 'Most favourite <b>Instructor.</b>'
				),
				array(
                    'settings' => 'instructor_slider_title_link_text',
                    'label'    => esc_html__( 'Instructor Slider Title Link Text', 'skillate' ),
                    'type'     => 'text',
                    'priority' => 10,
                    'default'  => 'View More'
				),
                array(
                    'settings' => 'instructor_page_link',
                    'label'    => esc_html__( 'Instructor page link', 'skillate' ),
                    'type'     => 'text',
                    'priority' => 10,
                    'default'  => ''
				),
				array(
					'settings' => 'instructor_slide_query',
					'label'    => esc_html__( 'Instructor Slide Query', 'skillate' ),
					'type'     => 'select',
					'priority' => 10,
					'default'  => 'all_instructor',
					'choices'  => array(
						'all_instructor' => esc_html__( 'All Instructor', 'skillate' ),
						'fav_instructor' => esc_html__( 'Favourite Instructor', 'skillate' ),
					)
				),
				
            )//fields
		),//archive setting

		array(
            'id'              => 'skillate_tutor_related_course_options',
            'title'           => esc_html__( 'Course Details', 'skillate' ),
            'description'     => esc_html__( 'You can customize your course archive page from here.', 'skillate' ),
            'priority'        => 10,
            'fields'          => array(
				array(
					'settings' => 'course_details_best_sell_tag',
					'label'    => esc_html__('Best Sell Tag'),
					'type'     => 'switch',
					'priority' => 10,
					'default'  => true
				),
				array(
					'settings' => 'course_details_rating',
					'label'    => esc_html__('Course Details Rating'),
					'type'     => 'switch',
					'priority' => '10',
					'default'  => true
				),
				array(
					'settings' => 'single_course_tab_sticky_menu',
					'label'    => esc_html__('Single Course Sticky Menu'),
					'type'     => 'switch',
					'priority' => '10',
					'default'  => true
				),
                array(
					'settings' => 'related_course_slider',
                    'label'    => esc_html__( 'Related Course Slider', 'skillate' ),
                    'type'     => 'switch',
                    'priority' => 10,
                    'default'  => true
				),
				array(
					'settings' => 'related_course_slider_total_item',
                    'label'    => esc_html__( 'Related Course Slider Total Item', 'skillate' ),
                    'type'     => 'number',
                    'priority' => 10,
                    'default'  => 6
				),
                array(
                    'settings' => 'related_course_title',
                    'label'    => esc_html__( 'Related Course Title', 'skillate' ),
                    'type'     => 'text',
                    'priority' => 10,
                    'default'  => 'More Courses to get <b>You Started</b>'
				),
            )//fields
		),//course details setting		

		array(
            'id'              => 'skillate_instructor_single',
            'title'           => esc_html__( 'Instructor Single', 'skillate' ),
            'priority'        => 10,
            'fields'         => array(
				array(
                    'settings' => 'instructor_single_top',
                    'label'    => esc_html__( 'Upload Top Background Image', 'skillate' ),
                    'type'     => 'upload',
                    'priority' => 10,
                    'default' => get_template_directory_uri().'/images/instructor_single.jpg',
                ),
                array(
                    'settings' => 'instructor_batch',
                    'label'    => esc_html__( 'Intructor Batch Area', 'skillate' ),
                    'type'     => 'switch',
                    'priority' => 10,
                    'default'  => false
				),
                array(
                    'settings' => 'gamipress_achievement',
                    'label'    => esc_html__( 'Gamipress Achievement', 'skillate' ),
                    'type'     => 'switch',
                    'priority' => 10,
                    'default'  => false
				),
				
            )//fields
		),//archive setting
		
		array(
            'id'              => 'skillate_splash_screen',
            'title'           => esc_html__( 'Mobile Splash Screen', 'skillate' ),
            'priority'        => 10,
            'fields'         => array(
				array(
                    'settings' => 'splash_enable',
                    'label'    => esc_html__( 'Enable', 'skillate' ),
                    'type'     => 'switch',
                    'priority' => 10,
                    'default'  => true
                ),
				array(
                    'settings' => 'splash_bg',
                    'label'    => esc_html__( 'Upload Splash Background Image', 'skillate' ),
                    'type'     => 'upload',
                    'priority' => 10,
                    //'default' => get_template_directory_uri().'/images/mobile-screen-bg.png',
				),
				array(
                    'settings' => 'splash_bg_color',
                    'label'    => esc_html__( 'Background Color', 'skillate' ),
                    'type'     => 'rgba',
                    'priority' => 10,
                    'default' => '#fff',
                ),
				array(
                    'settings' => 'splash_logo',
                    'label'    => esc_html__( 'Upload Logo', 'skillate' ),
                    'type'     => 'upload',
                    'priority' => 10,
                    //'default' => get_template_directory_uri().'/images/splash-logo.svg',
                ),
                array(
                    'settings' => 'splash_title',
                    'label'    => esc_html__( 'Title', 'skillate' ),
                    'type'     => 'text',
                    'priority' => 10,
                    'default'  => ''
				),
                array(
                    'settings' => 'splash_content',
                    'label'    => esc_html__( 'Content', 'skillate' ),
                    'type'     => 'text',
                    'priority' => 10,
                    'default'  => ''
				),
				
            )//fields
		),//archive setting
		

		array(
            'id'              => 'skillate_mobile_menu',
            'title'           => esc_html__( 'Mobile Menu Settings', 'skillate' ),
            'priority'        => 10,
            'fields'         => array(
				array(
                    'settings' => 'mobile_menu_en',
                    'label'    => esc_html__( 'Mobile Menu Enable', 'skillate' ),
                    'type'     => 'switch',
                    'priority' => 10,
                    'default'  => true
				),
				array(
                    'settings' => 'mobile_menu_cat',
                    'label'    => esc_html__( 'Category Enable', 'skillate' ),
                    'type'     => 'switch',
                    'priority' => 10,
                    'default'  => true
				),
				array(
                    'settings' => 'mobile_menu_search',
                    'label'    => esc_html__( 'Search Enable', 'skillate' ),
                    'type'     => 'switch',
                    'priority' => 10,
                    'default'  => true
                ),
				array(
                    'settings' => 'mobile_menu_cart',
                    'label'    => esc_html__( 'Cart Enable', 'skillate' ),
                    'type'     => 'switch',
                    'priority' => 10,
                    'default'  => true
				),
				
			
				
            )//fields
        ),//archive setting

		

		array(
			'id'              => 'logo_setting',
			'title'           => esc_html__( 'Logo Settings', 'skillate' ),
			'description'     => esc_html__( 'Logo Settings', 'skillate' ),
			'priority'        => 10,
			// 'active_callback' => 'is_front_page',
			'fields'         => array(	
				array(
                    'settings' => 'logo',
                    'label'    => esc_html__( 'Upload Logo', 'skillate' ),
                    'type'     => 'upload',
                    'priority' => 10,
                    'default' => get_template_directory_uri().'/images/logo.svg',
                ),
				array(
					'settings' => 'logo_style',
					'label'    => esc_html__( 'Select Logo Style', 'skillate' ),
					'type'     => 'select',
					'priority' => 10,
					'default'  => 'logoimg',
					'choices'  => array(
						'logoimg' => esc_html( 'Logo image', 'skillate' ),
						'logotext' => esc_html( 'Logo text', 'skillate' ),
					)
				),	
				array(
					'settings' => 'logo_text',
					'label'    => esc_html__( 'Custom Logo Text', 'skillate' ),
					'type'     => 'text',
					'priority' => 10,
					'default'  => 'skillate',
				),	
				array(
					'settings' => 'logo_width',
					'label'    => esc_html__( 'Logo Width', 'skillate' ),
					'type'     => 'text',
					'priority' => 10,
					'default'  => 120,
				),
				array(
					'settings' => 'logo_height',
					'label'    => esc_html__( 'Logo Height', 'skillate' ),
					'type'     => 'text',
					'priority' => 10,
				),
			)//fields
		),//topbar_setting
		
		array(
			'id'              => 'sub_header_banner',
			'title'           => esc_html__( 'Sub Header Banner', 'skillate' ),
			'description'     => esc_html__( 'sub header banner', 'skillate' ),
			'priority'        => 10,
			// 'active_callback' => 'is_front_page',
			'fields'         => array(

				array(
					'settings' => 'sub_header_padding_top',
					'label'    => esc_html__( 'Sub-Header Padding Top', 'skillate' ),
					'type'     => 'number',
					'priority' => 10,
					'default'  => 60,
				),
				array(
					'settings' => 'sub_header_padding_bottom',
					'label'    => esc_html__( 'Sub-Header Padding Bottom', 'skillate' ),
					'type'     => 'number',
					'priority' => 10,
					'default'  => 0,
				),
				array(
					'settings' => 'sub_header_banner_color',
					'label'    => esc_html__( 'Sub-Header BG Color', 'skillate' ),
					'type'     => 'color',
					'priority' => 10,
					'default' 	=> '#fff',
				),
				array(
					'settings' => 'sub_header_title',
					'label'    => esc_html__( 'Title Settings', 'skillate' ),
					'type'     => 'title',
					'priority' => 10,
				),
				array(
					'settings' => 'sub_header_title_color',
					'label'    => esc_html__( 'Header Title Color', 'skillate' ),
					'type'     => 'color',
					'priority' => 10,
					'default'  => '#1f2949',
				),
				array(
					'settings' => 'sub_header_title_size',
					'label'    => esc_html__( 'Header Title Font Size', 'skillate' ),
					'type'     => 'text',
					'priority' => 10,
					'default'  => 40,
				),
			)//fields
		),//sub_header_banner


		array(
			'id'              => 'typo_setting',
			'title'           => esc_html__( 'Typography Setting', 'skillate' ),
			'description'     => esc_html__( 'Typography Setting', 'skillate' ),
			'priority'        => 10,
			'fields'         => array(

				array(
					'settings' => 'font_title_body',
					'label'    => esc_html__( 'Body Font Options', 'skillate' ),
					'type'     => 'title',
					'priority' => 10,
				),
				//body font
				array(
					'settings' => 'body_google_font',
					'label'    => esc_html__( 'Select Google Font', 'skillate' ),
					'type'     => 'select',
					'default'  => 'Open Sans',
					'choices'  => skillate_get_google_fonts(),
					'google_font' 					=> true,
					'google_font_weight' 			=> 'body_font_weight',
					'google_font_weight_default' 	=> '400'
				),
				array(
					'settings' => 'body_font_size',
					'label'    => esc_html__( 'Body Font Size', 'skillate' ),
					'type'     => 'number',
					'default'  => '14',
				),
				array(
					'settings' => 'body_font_height',
					'label'    => esc_html__( 'Body Font Line Height', 'skillate' ),
					'type'     => 'number',
					'default'  => '24',
				),
				array(
					'settings' => 'body_font_weight',
					'label'    => esc_html__( 'Body Font Weight', 'skillate' ),
					'type'     => 'select',
					'priority' => 10,
					'default'  => '400',
					'choices'  => array(
						'' => esc_html__( 'Select', 'skillate' ),
						'100' => esc_html__( '100', 'skillate' ),
						'200' => esc_html__( '200', 'skillate' ),
						'300' => esc_html__( '300', 'skillate' ),
						'400' => esc_html__( '400', 'skillate' ),
						'500' => esc_html__( '500', 'skillate' ),
						'600' => esc_html__( '600', 'skillate' ),
						'700' => esc_html__( '700', 'skillate' ),
						'800' => esc_html__( '800', 'skillate' ),
						'900' => esc_html__( '900', 'skillate' ),
					)
				),
				array(
					'settings' => 'body_font_color',
					'label'    => esc_html__( 'Body Font Color', 'skillate' ),
					'type'     => 'color',
					'priority' => 10,
					'default'  => '#7e879a',
				),
				array(
					'settings' => 'font_title_menu',
					'label'    => esc_html__( 'Menu Font Options', 'skillate' ),
					'type'     => 'title',
					'priority' => 10,
				),
				//Menu font
				array(
					'settings' => 'menu_google_font',
					'label'    => esc_html__( 'Select Google Font', 'skillate' ),
					'type'     => 'select',
					'default'  => 'Open Sans',
					'choices'  => skillate_get_google_fonts(),
					'google_font' => true,
					'google_font_weight' => 'menu_font_weight',
					'google_font_weight_default' => '700'
				),
				array(
					'settings' => 'menu_font_size',
					'label'    => esc_html__( 'Menu Font Size', 'skillate' ),
					'type'     => 'number',
					'default'  => '16',
				),
				array(
					'settings' => 'menu_font_height',
					'label'    => esc_html__( 'Menu Font Line Height', 'skillate' ),
					'type'     => 'number',
					'default'  => '20',
				),
				array(
					'settings' => 'menu_font_weight',
					'label'    => esc_html__( 'Menu Font Weight', 'skillate' ),
					'type'     => 'select',
					'priority' => 10,
					'default'  => '400',
					'choices'  => array(
						'' => esc_html( 'Select', 'skillate' ),
						'100' => esc_html( '100', 'skillate' ),
						'200' => esc_html( '200', 'skillate' ),
						'300' => esc_html( '300', 'skillate' ),
						'400' => esc_html( '400', 'skillate' ),
						'500' => esc_html( '500', 'skillate' ),
						'600' => esc_html( '600', 'skillate' ),
						'700' => esc_html( '700', 'skillate' ),
						'800' => esc_html( '800', 'skillate' ),
						'900' => esc_html( '900', 'skillate' ),
					)
				),
				array(
					'settings' => 'font_title_h1',
					'label'    => esc_html__( 'Heading 1 Font Options', 'skillate' ),
					'type'     => 'title',
					'priority' => 10,
				),
				//Heading 1
				array(
					'settings' => 'h1_google_font',
					'label'    => esc_html__( 'Google Font', 'skillate' ),
					'type'     => 'select',
					'default'  => 'Open Sans',
					'choices'  => skillate_get_google_fonts(),
					'google_font' => true,
					'google_font_weight' => 'menu_font_weight',
					'google_font_weight_default' => '700'
				),
				array(
					'settings' => 'h1_font_size',
					'label'    => esc_html__( 'Font Size', 'skillate' ),
					'type'     => 'number',
					'default'  => '44',
				),
				array(
					'settings' => 'h1_font_height',
					'label'    => esc_html__( 'Font Line Height', 'skillate' ),
					'type'     => 'number',
					'default'  => '48',
				),
				array(
					'settings' => 'h1_font_weight',
					'label'    => esc_html__( 'Font Weight', 'skillate' ),
					'type'     => 'select',
					'priority' => 10,
					'default'  => '700',
					'choices'  => array(
						'' => esc_html__( 'Select', 'skillate' ),
						'100' => esc_html__( '100', 'skillate' ),
						'200' => esc_html__( '200', 'skillate' ),
						'300' => esc_html__( '300', 'skillate' ),
						'400' => esc_html__( '400', 'skillate' ),
						'500' => esc_html__( '500', 'skillate' ),
						'600' => esc_html__( '600', 'skillate' ),
						'700' => esc_html__( '700', 'skillate' ),
						'800' => esc_html__( '800', 'skillate' ),
						'900' => esc_html__( '900', 'skillate' ),
					)
				),

				array(
					'settings' => 'font_title_h2',
					'label'    => esc_html__( 'Heading 2 Font Options', 'skillate' ),
					'type'     => 'title',
					'priority' => 10,
				),
				//Heading 2
				array(
					'settings' => 'h2_google_font',
					'label'    => esc_html__( 'Google Font', 'skillate' ),
					'type'     => 'select',
					'default'  => 'Open Sans',
					'choices'  => skillate_get_google_fonts(),
					'google_font' => true,
					'google_font_weight' => 'menu_font_weight',
					'google_font_weight_default' => '600'
				),
				array(
					'settings' => 'h2_font_size',
					'label'    => esc_html__( 'Font Size', 'skillate' ),
					'type'     => 'number',
					'default'  => '30',
				),
				array(
					'settings' => 'h2_font_height',
					'label'    => esc_html__( 'Font Line Height', 'skillate' ),
					'type'     => 'number',
					'default'  => '36',
				),
				array(
					'settings' => 'h2_font_weight',
					'label'    => esc_html__( 'Font Weight', 'skillate' ),
					'type'     => 'select',
					'priority' => 10,
					'default'  => '600',
					'choices'  => array(
						'' => esc_html__( 'Select', 'skillate' ),
						'100' => esc_html__( '100', 'skillate' ),
						'200' => esc_html__( '200', 'skillate' ),
						'300' => esc_html__( '300', 'skillate' ),
						'400' => esc_html__( '400', 'skillate' ),
						'500' => esc_html__( '500', 'skillate' ),
						'600' => esc_html__( '600', 'skillate' ),
						'700' => esc_html__( '700', 'skillate' ),
						'800' => esc_html__( '800', 'skillate' ),
						'900' => esc_html__( '900', 'skillate' ),
					)
				),

				array(
					'settings' => 'font_title_h3',
					'label'    => esc_html__( 'Heading 3 Font Options', 'skillate' ),
					'type'     => 'title',
					'priority' => 10,
				),
				//Heading 3
				array(
					'settings' => 'h3_google_font',
					'label'    => esc_html__( 'Google Font', 'skillate' ),
					'type'     => 'select',
					'default'  => 'Open Sans',
					'choices'  => skillate_get_google_fonts(),
					'google_font' => true,
					'google_font_weight' => 'menu_font_weight',
					'google_font_weight_default' => '600'
				),
				array(
					'settings' => 'h3_font_size',
					'label'    => esc_html__( 'Font Size', 'skillate' ),
					'type'     => 'number',
					'default'  => '22',
				),
				array(
					'settings' => 'h3_font_height',
					'label'    => esc_html__( 'Font Line Height', 'skillate' ),
					'type'     => 'number',
					'default'  => '28',
				),
				array(
					'settings' => 'h3_font_weight',
					'label'    => esc_html__( 'Font Weight', 'skillate' ),
					'type'     => 'select',
					'priority' => 10,
					'default'  => '600',
					'choices'  => array(
						'' => esc_html__( 'Select', 'skillate' ),
						'100' => esc_html__( '100', 'skillate' ),
						'200' => esc_html__( '200', 'skillate' ),
						'300' => esc_html__( '300', 'skillate' ),
						'400' => esc_html__( '400', 'skillate' ),
						'500' => esc_html__( '500', 'skillate' ),
						'600' => esc_html__( '600', 'skillate' ),
						'700' => esc_html__( '700', 'skillate' ),
						'800' => esc_html__( '800', 'skillate' ),
						'900' => esc_html__( '900', 'skillate' ),
					)
				),

				array(
					'settings' => 'font_title_h4',
					'label'    => esc_html__( 'Heading 4 Font Options', 'skillate' ),
					'type'     => 'title',
					'priority' => 10,
				),
				//Heading 4
				array(
					'settings' => 'h4_google_font',
					'label'    => esc_html__( 'Heading4 Google Font', 'skillate' ),
					'type'     => 'select',
					'default'  => 'Open Sans',
					'choices'  => skillate_get_google_fonts(),
					'google_font' => true,
					'google_font_weight' => 'menu_font_weight',
					'google_font_weight_default' => '600'
				),
				array(
					'settings' => 'h4_font_size',
					'label'    => esc_html__( 'Heading4 Font Size', 'skillate' ),
					'type'     => 'number',
					'default'  => '17',
				),
				array(
					'settings' => 'h4_font_height',
					'label'    => esc_html__( 'Heading4 Font Line Height', 'skillate' ),
					'type'     => 'number',
					'default'  => '22',
				),
				array(
					'settings' => 'h4_font_weight',
					'label'    => esc_html__( 'Heading4 Font Weight', 'skillate' ),
					'type'     => 'select',
					'priority' => 10,
					'default'  => '600',
					'choices'  => array(
						'' => esc_html__( 'Select', 'skillate' ),
						'100' => esc_html__( '100', 'skillate' ),
						'200' => esc_html__( '200', 'skillate' ),
						'300' => esc_html__( '300', 'skillate' ),
						'400' => esc_html__( '400', 'skillate' ),
						'500' => esc_html__( '500', 'skillate' ),
						'600' => esc_html__( '600', 'skillate' ),
						'700' => esc_html__( '700', 'skillate' ),
						'800' => esc_html__( '800', 'skillate' ),
						'900' => esc_html__( '900', 'skillate' ),
					)
				),

				array(
					'settings' => 'font_title_h5',
					'label'    => esc_html__( 'Heading 5 Font Options', 'skillate' ),
					'type'     => 'title',
					'priority' => 10,
				),

				//Heading 5
				array(
					'settings' => 'h5_google_font',
					'label'    => esc_html__( 'Heading5 Google Font', 'skillate' ),
					'type'     => 'select',
					'default'  => 'Open Sans',
					'choices'  => skillate_get_google_fonts(),
					'google_font' => true,
					'google_font_weight' => 'menu_font_weight',
					'google_font_weight_default' => '600'
				),
				array(
					'settings' => 'h5_font_size',
					'label'    => esc_html__( 'Heading5 Font Size', 'skillate' ),
					'type'     => 'number',
					'default'  => '14',
				),
				array(
					'settings' => 'h5_font_height',
					'label'    => esc_html__( 'Heading5 Font Line Height', 'skillate' ),
					'type'     => 'number',
					'default'  => '24',
				),
				array(
					'settings' => 'h5_font_weight',
					'label'    => esc_html__( 'Heading5 Font Weight', 'skillate' ),
					'type'     => 'select',
					'priority' => 10,
					'default'  => '600',
					'choices'  => array(
						'' => esc_html__( 'Select', 'skillate' ),
						'100' => esc_html__( '100', 'skillate' ),
						'200' => esc_html__( '200', 'skillate' ),
						'300' => esc_html__( '300', 'skillate' ),
						'400' => esc_html__( '400', 'skillate' ),
						'500' => esc_html__( '500', 'skillate' ),
						'600' => esc_html__( '600', 'skillate' ),
						'700' => esc_html__( '700', 'skillate' ),
						'800' => esc_html__( '800', 'skillate' ),
						'900' => esc_html__( '900', 'skillate' ),
					)
				),

			)//fields
		),//typo_setting

		array(
			'id'              => 'layout_styling',
			'title'           => esc_html__( 'Layout & Styling', 'skillate' ),
			'description'     => esc_html__( 'Layout & Styling', 'skillate' ),
			'priority'        => 10,
			'fields'         => array(
				
				array(
					'settings' => 'custom_preset_en',
					'label'    => esc_html__( 'Set Custom Color', 'skillate' ),
					'type'     => 'switch',
					'priority' => 10,
					'default'  => true,
				),
				array(
					'settings' => 'major_color',
					'label'    => esc_html__( 'Major Color', 'skillate' ),
					'type'     => 'color',
					'priority' => 10,
					'default'  => '#ff5248',
				),
				array(
					'settings' => 'hover_color',
					'label'    => esc_html__( 'Hover Color', 'skillate' ),
					'type'     => 'color',
					'priority' => 10,
					'default'  => '#1f2949',
				),
			
				# navbar color section start.
				array(
					'settings' => 'menu_color_title',
					'label'    => esc_html__( 'Menu Color Settings', 'skillate' ),
					'type'     => 'title',
					'priority' => 10,
				),
				array(
					'settings' => 'menu_font_color',
					'label'    => esc_html__( 'Text Color', 'skillate' ),
					'type'     => 'color',
					'priority' => 10,
					'default'  => '#fff',
				),

				// array(
				// 	'settings' => 'navbar_hover_text_color',
				// 	'label'    => esc_html__( 'Hover Text Color', 'skillate' ),
				// 	'type'     => 'color',
				// 	'priority' => 10,
				// 	'default'  => '#ff5248',
				// ),

				// array(
				// 	'settings' => 'navbar_active_text_color',
				// 	'label'    => esc_html__( 'Active Text Color', 'skillate' ),
				// 	'type'     => 'color',
				// 	'priority' => 10,
				// 	'default'  => '#ff5248',
				// ),

				# Submenu
				array(
					'settings' => 'sub_menu_color_title',
					'label'    => esc_html__( 'Sub-Menu Color Settings', 'skillate' ),
					'type'     => 'title',
					'priority' => 10,
				),
				array(
					'settings' => 'sub_menu_bg',
					'label'    => esc_html__( 'Background Color', 'skillate' ),
					'type'     => 'color',
					'priority' => 10,
					'default'  => '#ffffff',
				),
				array(
					'settings' => 'sub_menu_text_color',
					'label'    => esc_html__( 'Text Color', 'skillate' ),
					'type'     => 'color',
					'priority' => 10,
					'default'  => '#1f2949',
				),
				// array(
				// 	'settings' => 'sub_menu_text_color_hover',
				// 	'label'    => esc_html__( 'Hover Text Color', 'skillate' ),
				// 	'type'     => 'color',
				// 	'priority' => 10,
				// 	'default'  => '#ff5248',
				// ),
				#End of the navbar color section
			)//fields
		),//Layout & Styling

		# 404 Page.
		array(
			'id'              => '404_settings',
			'title'           => esc_html__( '404 Page', 'skillate' ),
			'description'     => esc_html__( '404 page background and text settings', 'skillate' ),
			'priority'        => 10,
			'fields'         => array(
				array(
					'settings' => 'logo_404',
					'label'    => esc_html__( 'Upload Image', 'skillate' ),
					'type'     => 'upload',
					'priority' => 10,
					'default'  => '',
				),
				array(
					'settings' => '404_title',
					'label'    => esc_html__( '404 Page Title', 'skillate' ),
					'type'     => 'text',
					'priority' => 10,
					'default'  => esc_html__('404', 'skillate')
				),
				array(
					'settings' => '404_description',
					'label'    => esc_html__( 'The page you are looking for does not exit.', 'skillate' ),
					'type'     => 'textarea',
					'priority' => 10,
					'default'  => ''
				),
				array(
					'settings' => '404_btn_text',
					'label'    => esc_html__( 'Button Text', 'skillate' ),
					'type'     => 'text',
					'priority' => 10,
					'default'  => 'Go to Homepage',
				),
			)
		),

		# Blog Settings.
		array(
			'id'              => 'blog_setting',
			'title'           => esc_html__( 'Blog Setting', 'skillate' ),
			'description'     => esc_html__( 'Set up Blog page', 'skillate' ),
			'priority'        => 10,
			'fields'         => array(

				array(
					'settings' => 'blog_date',
					'label'    => esc_html__( 'Show Blog Date', 'skillate' ),
					'type'     => 'switch',
					'priority' => 10,
					'default'  => false,
				),
				array(
					'settings' => 'blog_author',
					'label'    => esc_html__( 'Show Blog Author', 'skillate' ),
					'type'     => 'switch',
					'priority' => 10,
					'default'  => false,
				),
				array(
					'settings' => 'blog_category',
					'label'    => esc_html__( 'Show Blog Category', 'skillate' ),
					'type'     => 'switch',
					'priority' => 10,
					'default'  => true,
				),
				array(
					'settings' => 'blog_comment',
					'label'    => esc_html__( 'Show Comment', 'skillate' ),
					'type'     => 'switch',
					'priority' => 10,
					'default'  => false,
				),
				array(
					'settings' => 'blog_intro_en',
					'label'    => esc_html__( 'Show Archive Content', 'skillate' ),
					'type'     => 'switch',
					'priority' => 10,
					'default'  => false,
				),
				array(
					'settings' => 'blog_continue',
					'label'    => esc_html__( 'Show Read More', 'skillate' ),
					'type'     => 'switch',
					'priority' => 10,
					'default'  => false,
				),

				array(
					'settings' => 'blog_post_text_limit',
					'label'    => esc_html__( 'Excerpt Charlength Limit', 'skillate' ),
					'type'     => 'text',
					'priority' => 10,
					'default'  => '220',
				),
			)//fields
		),//blog_setting

		array(
			'id'              => 'blog_single_setting',
			'title'           => esc_html__( 'Blog Single Page Setting', 'skillate' ),
			'description'     => esc_html__( 'Setup blog single post', 'skillate' ),
			'priority'        => 10,
			'fields'         => array(
				
				array(
					'settings' => 'blog_related_post',
					'label'    => esc_html__( 'Show Related Post', 'skillate' ),
					'type'     => 'switch',
					'priority' => 10,
					'default'  => true,
				),
				array(
					'settings' => 'blog_social_share',
					'label'    => esc_html__( 'Show blog Social Share', 'skillate' ),
					'type'     => 'switch',
					'priority' => 10,
					'default'  => true,
				),
				array(
					'settings' => 'blog_date_single',
					'label'    => esc_html__( 'Show blog single date', 'skillate' ),
					'type'     => 'switch',
					'priority' => 10,
					'default'  => true,
				),
				array(
					'settings' => 'blog_author_single',
					'label'    => esc_html__( 'Show blog single author', 'skillate' ),
					'type'     => 'switch',
					'priority' => 10,
					'default'  => false,
				),
				array(
					'settings' => 'blog_category_single',
					'label'    => esc_html__( 'Show blog single category', 'skillate' ),
					'type'     => 'switch',
					'priority' => 10,
					'default'  => true,
				),
				array(
					'settings' => 'blog_comment_single',
					'label'    => esc_html__( 'Show blog single comment', 'skillate' ),
					'type'     => 'switch',
					'priority' => 10,
					'default'  => false,
				),
							
			) #fields
		), 
		#blog_single_page_setting

		array(
			'id'              => 'client_logo_area',
			'title'           => esc_html__( 'Client Logo Option', 'skillate' ),
			'priority'        => 10,
			'fields'         => array(
				array(
					'settings' => 'client_logo_title',
					'label'    => esc_html__( 'Client Logo Title', 'skillate' ),
					'type'     => 'text',
					'priority' => 10,
					'default'  => 'As Featured In',
				),
				array(
                    'settings' => 'client_slide_autoplay',
                    'label'    => esc_html__( 'Slide Autoplay', 'skillate' ),
                    'type'     => 'switch',
                    'priority' => 10,
                    'default'  => true
				),
				// array(
                //     'settings' => 'client_slide_count',
                //     'label'    => esc_html__( 'Slide Count', 'skillate' ),
                //     'type'     => 'text',
                //     'priority' => 10,
                //     'default'  => true
				// ),
				
							
			) #fields
		), 
		#blog_single_page_setting

		array(
			'id'              => 'bottom_setting',
			'title'           => esc_html__( 'Bottom Setting', 'skillate' ),
			'description'     => esc_html__( 'Bottom Setting', 'skillate' ),
			'priority'        => 10,
			'fields'         => array(
				array(
					'settings' => 'bottom_style',
					'label'    => esc_html__( 'Select Bottom Layout', 'skillate' ),
					'type'     => 'select',
					'priority' => 10,
					'default'  => 'layout_one',
					'choices'  => array(
						'layout_one' 	=> esc_html__( 'Layout One', 'skillate' ),
						'layout_two' 	=> esc_html__( 'Layout Two', 'skillate' ),
					)
				),
				array(
					'settings' => 'bottom_column',
					'label'    => esc_html__( 'Select Bottom Column (For Layout Two)', 'skillate' ),
					'type'     => 'select',
					'priority' => 10,
					'default'  => '4',
					'choices'  => array(
						'12' 	=> esc_html__( 'Column 1', 'skillate' ),
						'6' 	=> esc_html__( 'Column 2', 'skillate' ),
						'4' 	=> esc_html__( 'Column 3', 'skillate' ),
						'3' 	=> esc_html__( 'Column 4', 'skillate' ),
					),
					'dependency' => array(
						'id' => 'bottom_style',
						'comp' => '!=',
						'value' => 'layout_one',
					),
				),
				array(
					'settings' => 'bottom_color',
					'label'    => esc_html__( 'Bottom background Color', 'skillate' ),
					'type'     => 'color',
					'priority' => 10,
					'default'  => '#fff',
				),
				array(
					'settings' => 'bottom_title_color',
					'label'    => esc_html__( 'Bottom Title Color', 'skillate' ),
					'type'     => 'color',
					'priority' => 10,
					'default'  => '#1f2949',
				),	
				array(
					'settings' => 'bottom_link_color',
					'label'    => esc_html__( 'Bottom Link Color', 'skillate' ),
					'type'     => 'color',
					'priority' => 10,
					'default'  => '#797c7f',
				),				
				array(
					'settings' => 'bottom_hover_color',
					'label'    => esc_html__( 'Bottom link hover color', 'skillate' ),
					'type'     => 'color',
					'priority' => 10,
					'default'  => '#ff5248',
				),
				array(
					'settings' => 'bottom_text_color',
					'label'    => esc_html__( 'Bottom Text color', 'skillate' ),
					'type'     => 'color',
					'priority' => 10,
					'default'  => '#797c7f',
				),
				array(
					'settings' => 'bottom_padding_top',
					'label'    => esc_html__( 'Bottom Top Padding', 'skillate' ),
					'type'     => 'number',
					'priority' => 10,
					'default'  => 80,
				),	
				array(
					'settings' => 'bottom_padding_bottom',
					'label'    => esc_html__( 'Bottom Padding Bottom', 'skillate' ),
					'type'     => 'number',
					'priority' => 10,
					'default'  => 80,
				),					
			)//fields
		),//bottom_setting		
		array(
			'id'              => 'footer_setting',
			'title'           => esc_html__( 'Footer Setting', 'skillate' ),
			'description'     => esc_html__( 'Footer Setting', 'skillate' ),
			'priority'        => 10,
			'fields'         => array(
				array(
					'settings' => 'footer_en',
					'label'    => esc_html__( 'Disable Copyright Area', 'skillate' ),
					'type'     => 'switch',
					'priority' => 10,
					'default'  => true,
				),
				array(
					'settings' => 'copyright_en',
					'label'    => esc_html__( 'Disable copyright text', 'skillate' ),
					'type'     => 'switch',
					'priority' => 10,
					'default'  => true,
				),
				array(
					'settings' => 'copyright_text',
					'label'    => esc_html__( 'Copyright Text', 'skillate' ),
					'type'     => 'textarea',
					'priority' => 10,
					'default'  => esc_html__( '2019 skillate. All Rights Reserved.', 'skillate' ),
				),
				array(
					'settings' => 'payment_method_title',
					'label'    => esc_html__( 'Secure payment title', 'skillate' ),
					'type'     => 'text',
					'priority' => 10,
					'default'  => esc_html__( 'Secure payment:', 'skillate' ),
				),

				array(
					'settings' => 'copyright_text_color',
					'label'    => esc_html__( 'Footer Text Color', 'skillate' ),
					'type'     => 'color',
					'priority' => 10,
					'default'  => '#797c7f',
				),				

				array(
					'settings' => 'copyright_bg_color',
					'label'    => esc_html__( 'Footer background color', 'skillate' ),
					'type'     => 'color',
					'priority' => 10,
					'default'  => '#f3f4f7',
				),
				array(
					'settings' => 'copyright_padding_top',
					'label'    => esc_html__( 'Footer Top Padding', 'skillate' ),
					'type'     => 'number',
					'priority' => 10,
					'default'  => 25,
				),	
				array(
					'settings' => 'copyright_padding_bottom',
					'label'    => esc_html__( 'Footer Bottom Padding', 'skillate' ),
					'type'     => 'number',
					'priority' => 10,
					'default'  => 25,
				),					
			)//fields
		),//footer_setting
		
	),
);//wpestate-core_panel_options

$skillate_framework = new Skillate_Customize( $skillate_panel_to_section );

