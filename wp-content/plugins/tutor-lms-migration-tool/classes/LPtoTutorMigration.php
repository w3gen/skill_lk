<?php
if ( ! defined( 'ABSPATH' ) )
	exit;

if ( ! class_exists('LPtoTutorMigration')){
	class LPtoTutorMigration {

		public function __construct() {
			add_filter('tutor_tool_pages', array($this, 'tutor_tool_pages'));

			//add_action('wp_ajax_lp_migrate_course_to_tutor', array($this, 'lp_migrate_course_to_tutor'));

			add_action('wp_ajax_lp_migrate_all_data_to_tutor', array($this, 'lp_migrate_all_data_to_tutor'));
			add_action('wp_ajax_tlmt_reset_migrated_items_count', array($this, 'tlmt_reset_migrated_items_count'));

			add_action('wp_ajax__get_lp_live_progress_course_migrating_info', array($this, '_get_lp_live_progress_course_migrating_info'));

			add_action('tutor_action_migrate_lp_orders', array($this, 'migrate_lp_orders'));
			add_action('tutor_action_migrate_lp_reviews', array($this, 'migrate_lp_reviews'));

			add_action('tutor_action_tutor_import_from_xml', array($this, 'tutor_import_from_xml'));
			add_action('tutor_action_tutor_lp_export_xml', array($this, 'tutor_lp_export_xml'));
		}

		public function tutor_tool_pages($pages){
			$hasLPdata = get_option('learnpress_version');

			if ($hasLPdata){
				$pages['migration_lp'] = array('title' =>  __('LearnPress Migration', 'tutor-lms-migration-tool'), 'view_path' => TLMT_PATH.'views/migration_lp.php');
			}

			return $pages;
		}

		/**
		 * Delete Item Count
		 */
		public function tlmt_reset_migrated_items_count(){
			delete_option('_tutor_migrated_items_count');
		}

		public function lp_migrate_all_data_to_tutor(){
			//delete_option('_tutor_migrated_items_count');

			/*
			if (isset($_POST['import']) && is_array($_POST['import'])){
			    $i = 0;
		        foreach ($_POST['import'] as $migrate_item_key => $value ){
			        $i++;
		            update_option('_tutor_migrated_items_count', $i);
		            sleep(2);
		            switch ($migrate_item_key){
                        case 'courses':
                            //$this->lp_migrate_course_to_tutor();
                            break;
			            case 'orders':
			                //$this->migrate_lp_orders();
				            break;
			            case 'reviews':
			                //$this->migrate_lp_reviews();
				            break;
                    }
                }
            }
            */

            if (isset($_POST['migrate_type'])){
			    $migrate_type = sanitize_text_field($_POST['migrate_type']);

	            switch ($migrate_type){
		            case 'courses':
		                $this->lp_migrate_course_to_tutor();
			            break;
		            case 'orders':
		                $this->migrate_lp_orders();
			            break;
		            case 'reviews':
		                $this->migrate_lp_reviews();
			            break;
	            }
	            wp_send_json_success();
            }
            wp_send_json_error();
        }

		public function lp_migrate_course_to_tutor(){
			global $wpdb;

			$lp_courses = $wpdb->get_results("SELECT ID FROM {$wpdb->posts} WHERE post_type = 'lp_course';");
			if (tutils()->count($lp_courses)){
				$course_i = (int) get_option('_tutor_migrated_items_count');
				foreach ($lp_courses as $lp_course){
					$course_i++;
					$this->migrate_course($lp_course->ID);
					update_option('_tutor_migrated_items_count', $course_i);
				}
			}
			wp_send_json_success();
		}

		/**
		 *
		 * Get Live Update about course migrating info
		 */

		public function _get_lp_live_progress_course_migrating_info(){
			$migrated_count = (int) get_option('_tutor_migrated_items_count');
			wp_send_json_success(array('migrated_count' => $migrated_count ));
		}

		public function migrate_course($course_id){
			global $wpdb;

			$course = learn_press_get_course($course_id);

			if ( ! $course){
				return;
			}

			$curriculum = $course->get_curriculum() ;

			$lesson_post_type = tutor()->lesson_post_type;
			$course_post_type = tutor()->course_post_type;

			$tutor_course = array();
			$i = 0;
			foreach ( $curriculum as $section ) {
				$i++;

				$topic = array(
					'post_type'     => 'topics',
					'post_title'    => $section->get_title(),
					'post_content'  => $section->get_description(),
					'post_status'   => 'publish',
					'post_author'   => $course->get_author('id'),
					'post_parent'   => $course_id,
					'menu_order'    => $i,
					'items'         => array()
				);

				$lessons = $section->get_items();
				foreach ($lessons as $lesson){
					$item_post_type = learn_press_get_post_type( $lesson->get_id() );

					if ($item_post_type !== 'lp_lesson'){
						if ($item_post_type === 'lp_quiz'){
							$lesson_post_type = 'tutor_quiz';
						}
					}

					$tutor_lessons = array(
						'ID'    => $lesson->get_id(),
						'post_type'    => $lesson_post_type,
						'post_parent'  => '{topic_id}',
					);

					$topic['items'][] = $tutor_lessons;
				}

				$tutor_course[] = $topic;
			}


			if (tutils()->count($tutor_course)){
				foreach ($tutor_course as $course_topic){

					//Remove items from this topic
					$lessons = $course_topic['items'];
					unset($course_topic['items']);

					//Insert Topic post type
					$topic_id = wp_insert_post( $course_topic );

					//Update lesson from LearnPress to TutorLMS
					foreach ($lessons as $lesson){

						if ($lesson['post_type'] === 'tutor_quiz'){
							$quiz_id = tutils()->array_get('ID', $lesson);

							$questions = $wpdb->get_results("SELECT question_id, question_order, questions.ID, questions.post_content, questions.post_title, question_type_meta.meta_value as question_type, question_mark_meta.meta_value as question_mark
						FROM {$wpdb->prefix}learnpress_quiz_questions 
						LEFT JOIN {$wpdb->posts} questions on question_id = questions.ID 
						LEFT JOIN {$wpdb->postmeta} question_type_meta on question_id = question_type_meta.post_id AND question_type_meta.meta_key = '_lp_type'
						LEFT JOIN {$wpdb->postmeta} question_mark_meta on question_id = question_mark_meta.post_id AND question_mark_meta.meta_key = '_lp_mark'
						WHERE quiz_id = {$quiz_id}  ");

							if (tutils()->count($questions)){
								foreach ($questions as $question) {

									$question_type = null;
									if ($question->question_type === 'true_or_false'){
										$question_type = 'true_false';
									}
									if ($question->question_type === 'single_choice'){
										$question_type = 'single_choice';
									}
									if ($question->question_type === 'multiple_choice'){
										$question_type = 'multi_choice';
									}

									if ($question_type) {

										$new_question_data = array(
											'quiz_id'              => $quiz_id,
											'question_title'       => $question->post_title,
											'question_description' => $question->post_content,
											'question_type'        => $question_type,
											'question_mark'        => $question->question_mark,
											'question_settings'    => maybe_serialize( array() ),
											'question_order'       => $question->question_order,
										);

										$wpdb->insert($wpdb->prefix.'tutor_quiz_questions', $new_question_data);
										$question_id = $wpdb->insert_id;

										$answer_items = $wpdb->get_results("SELECT * from {$wpdb->prefix}learnpress_question_answers where question_id = {$question->question_id} ");

										if (tutils()->count($answer_items)){
											foreach ($answer_items as $answer_item){
												$answer_data = maybe_unserialize($answer_item->answer_data);

												$answer_data = array(
													'belongs_question_id'   => $question_id,
													'belongs_question_type' => $question_type,
													'answer_title'          => tutils()->array_get('text', $answer_data),
													'is_correct'            => tutils()->array_get('is_true', $answer_data) == 'yes' ? 1 : 0,
													'answer_order'          => $answer_item->answer_order,
												);

												$wpdb->insert($wpdb->prefix.'tutor_quiz_question_answers', $answer_data);
											}
										}
									}

								}

							}

						}


						$lesson['post_parent'] = $topic_id;
						wp_update_post($lesson);

						$lesson_id = tutils()->array_get('ID', $lesson);
						if ($lesson_id){
							update_post_meta( $lesson_id, '_tutor_course_id_for_lesson', $course_id );
						}

						$_lp_preview = get_post_meta($lesson_id, '_lp_preview', true);
						if ($_lp_preview === 'yes'){
							update_post_meta($lesson_id, '_is_preview', 1);
						}else{
							delete_post_meta($lesson_id, '_is_preview');
						}


					}



				}
			}

			//Migrate Course
			$tutor_course = array(
				'ID'            => $course_id,
				'post_type'     => $course_post_type,
			);
			wp_update_post($tutor_course);
			update_post_meta($course_id, '_was_lp_course', true);

			/**
			 * Create WC Product and attaching it with course
			 */

			$_lp_price = get_post_meta($course_id, '_lp_price', true);
			$_lp_sale_price = get_post_meta($course_id, '_lp_sale_price', true);

			if ($_lp_price){
				update_post_meta($course_id, '_tutor_course_price_type', 'paid');

				$product_id = wp_insert_post( array(
					'post_title' => $course->get_title().' Product',
					'post_content' => '',
					'post_status' => 'publish',
					'post_type' => "product",
				) );
				if ($product_id) {
					$product_metas = array(
						'_stock_status'      => 'instock',
						'total_sales'        => '0',
						'_regular_price'     => $_lp_price,
						'_sale_price'        => $_lp_sale_price,
						'_price'             => $_lp_price,
						'_sold_individually' => 'no',
						'_manage_stock'      => 'no',
						'_backorders'        => 'no',
						'_stock'             => '',
						'_virtual'           => 'yes',
						'_tutor_product'     => 'yes',
					);
					foreach ( $product_metas as $key => $value ) {
						update_post_meta( $product_id, $key, $value );
					}
				}

				/**
				 * Attaching product to course
				 */
				update_post_meta( $course_id, '_tutor_course_product_id', $product_id );
				$coursePostThumbnail = get_post_meta( $course_id, '_thumbnail_id', true );
				if ( $coursePostThumbnail ) {
					set_post_thumbnail( $product_id, $coursePostThumbnail );
				}
			}else{
				update_post_meta($course_id, '_tutor_course_price_type', 'free');
			}

			/**
			 * Enrollment Migration to this course
			 */
			$lp_enrollments = $wpdb->get_results( "SELECT lp_user_items.*,
        lp_order.ID as order_id,
        lp_order.post_date as order_time
          
        FROM {$wpdb->prefix}learnpress_user_items lp_user_items  
        LEFT JOIN {$wpdb->posts} lp_order ON lp_user_items.ref_id = lp_order.ID
        WHERE item_id = {$course_id} AND item_type = 'lp_course' AND status = 'enrolled'" );

			foreach ($lp_enrollments as $lp_enrollment){
				$user_id = $lp_enrollment->user_id;

				if ( ! tutils()->is_enrolled($course_id, $user_id)) {
					$order_time = strtotime($lp_enrollment->order_time);

					$title = __('Course Enrolled', 'tutor')." &ndash; ".date( get_option('date_format'), $order_time ).' @ '.date( get_option( 'time_format'), $order_time );
					$tutor_enrollment_data = array(
						'post_type'   => 'tutor_enrolled',
						'post_title'  => $title,
						'post_status' => 'completed',
						'post_author' => $user_id,
						'post_parent' => $course_id,
					);

					$isEnrolled = wp_insert_post( $tutor_enrollment_data );

					if ($isEnrolled){
						//Mark Current User as Students with user meta data
						update_user_meta( $user_id, '_is_tutor_student', $order_time );
					}
				}
			}
		}


		public function migrate_lp_orders(){
			global $wpdb;

			$lp_orders = $wpdb->get_results("SELECT * FROM {$wpdb->posts} WHERE post_type = 'lp_order' AND post_status = 'lp-completed' ;");

			$item_i = (int) get_option('_tutor_migrated_items_count');
			foreach ($lp_orders as $lp_order){
				$item_i++;
				update_option('_tutor_migrated_items_count', $item_i);

				$order_id = $lp_order->ID;
				$migrate_order_data = array(
					'ID'    => $order_id,
					'post_status'    => 'wc-completed',
					'post_type'    => 'shop_order',
				);

				wp_update_post($migrate_order_data);

				$_items = $this->get_lp_order_items($order_id);

				foreach ($_items as $item){

					$item_data = array(
						'order_item_name'   => $item->name,
						'order_item_type'   => 'line_item',
						'order_id'          => $order_id,
					);

					$wpdb->insert($wpdb->prefix.'woocommerce_order_items', $item_data);
					$order_item_id = (int) $wpdb->insert_id;

					$lp_item_metas = $wpdb->get_results("SELECT meta_key, meta_value FROM {$wpdb->prefix}learnpress_order_itemmeta WHERE learnpress_order_item_id = {$item->id} ");

					$lp_formatted_metas = array();
					foreach ($lp_item_metas as $item_meta) {
						$lp_formatted_metas[$item_meta->meta_key] = $item_meta->meta_value;
					}

					$_course_id = tutils()->array_get('_course_id', $lp_formatted_metas);
					$_quantity = tutils()->array_get('_quantity', $lp_formatted_metas);
					$_subtotal = tutils()->array_get('_subtotal', $lp_formatted_metas);
					$_total = tutils()->array_get('_total', $lp_formatted_metas);

					$wc_item_metas = array(
						'_product_id'        => $_course_id,
						'_variation_id'      => 0,
						'_qty'               => $_quantity,
						'_tax_class'         => '',
						'_line_subtotal'     => $_subtotal,
						'_line_subtotal_tax' => 0,
						'_line_total'        => $_total,
						'_line_tax'          => 0,
						'_line_tax_data'     => maybe_serialize( array( 'total' => array(), 'subtotal' => array() ) ),
					);

					foreach ($wc_item_metas as $wc_item_meta_key => $wc_item_meta_value ){
						$wc_item_metas = array(
							'order_item_id' => $order_item_id,
							'meta_key'      => $wc_item_meta_key,
							'meta_value'    => $wc_item_meta_value,
						);
						$wpdb->insert($wpdb->prefix.'woocommerce_order_itemmeta', $wc_item_metas);
					}

				}

				update_post_meta($order_id, '_customer_user', get_post_meta($order_id, '_user_id', true));
				update_post_meta($order_id, '_customer_ip_address', get_post_meta($order_id, '_user_ip_address', true));
				update_post_meta($order_id, '_customer_user_agent', get_post_meta($order_id, '_user_agent', true));

				$user_email = $wpdb->get_var("SELECT user_email from {$wpdb->users} WHERE ID = {$lp_order->post_author} ");
				update_post_meta($order_id, '_billing_address_index', $user_email );
				update_post_meta($order_id, '_billing_email', $user_email );
			}

		}

		public function migrate_lp_reviews(){
			global $wpdb;

			$lp_review_ids = $wpdb->get_col("SELECT comments.comment_ID FROM {$wpdb->comments} comments INNER JOIN {$wpdb->commentmeta} cm ON cm.comment_id = comments.comment_ID AND cm.meta_key = '_lpr_rating' WHERE comments.comment_type = 'review';");


			if (tutils()->count($lp_review_ids)){
				$item_i = (int) get_option('_tutor_migrated_items_count');
				foreach ($lp_review_ids as $lp_review_id){
					$item_i++;
					update_option('_tutor_migrated_items_count', $item_i);

					$review_migrate_data = array(
						'comment_approved'  => 'approved',
						'comment_type'      => 'tutor_course_rating',
						'comment_agent'     => 'TutorLMSPlugin',
					);

					$wpdb->update($wpdb->comments, $review_migrate_data, array( 'comment_ID' => $lp_review_id));
					$wpdb->update($wpdb->commentmeta, array('meta_key' => 'tutor_rating'), array( 'comment_id' => $lp_review_id, 'meta_key' => '_lpr_rating' ));
					$wpdb->delete($wpdb->commentmeta, array('comment_id' => $lp_review_id, 'meta_key' => '_lpr_review_title'));
				}
			}

		}


		public function get_lp_order_items($order_id){
			global $wpdb;

			$query = $wpdb->prepare( "
			SELECT order_item_id as id, order_item_name as name 
				, oim.meta_value as `course_id`
				# , oim2.meta_value as `quantity`
				# , oim3.meta_value as `total`
			FROM {$wpdb->learnpress_order_items} oi 
				INNER JOIN {$wpdb->learnpress_order_itemmeta} oim ON oi.order_item_id = oim.learnpress_order_item_id AND oim.meta_key='_course_id'
				# INNER JOIN {$wpdb->learnpress_order_itemmeta} oim2 ON oi.order_item_id = oim2.learnpress_order_item_id AND oim2.meta_key='_quantity'
				# INNER JOIN {$wpdb->learnpress_order_itemmeta} oim3 ON oi.order_item_id = oim3.learnpress_order_item_id AND oim3.meta_key='_total'
			WHERE order_id = %d ", $order_id );

			return $wpdb->get_results( $query );
		}


		/**
         *
         * Import From XML
		 */
		public function tutor_import_from_xml(){
		    global $wpdb;

			if (isset($_FILES['tutor_import_file'])){
				$course_post_type = tutor()->course_post_type;

				$xmlContent = file_get_contents($_FILES['tutor_import_file']['tmp_name']);
				$xmlContent = str_replace(array( '<![CDATA[', ']]>'),'', $xmlContent);

				$xml_data = simplexml_load_string($xmlContent);
				$courses = $xml_data->courses;

				foreach ($courses as $course){

					$course_data = array(
						'post_author'   => (string) $course->post_author,
						'post_date'   =>(string)$course->post_date,
						'post_date_gmt'   => (string) $course->post_date_gmt,
						'post_content'  => (string) $course->post_content,
						'post_title'    => (string) $course->post_title,
						'post_status'   => 'publish',
						'post_type'     =>  $course_post_type,
					);

					//Inserting Course
					$course_id = wp_insert_post($course_data);

					$course_meta = json_decode(json_encode($course->course_meta), true);
					foreach ($course_meta as $course_meta_key => $course_meta_value){
					    if ( is_array($course_meta_value)){
						    $course_meta_value = json_encode($course_meta_value);
					    }
					    $wpdb->insert($wpdb->postmeta, array('post_id' => $course_id, 'meta_key' => $course_meta_key, 'meta_value' =>$course_meta_value));
                    }

					foreach ($course->topics as $topic){
						$topic_data = array(
							'post_type'     => 'topics',
							'post_title'    => (string) $topic->post_title,
							'post_content'  => (string) $topic->post_content,
							'post_status'   => 'publish',
							'post_author'   => (string) $topic->post_author,
							'post_parent'   => $course_id,
							'menu_order'    => (string) $topic->menu_order,
						);

						//Inserting Topics
						$topic_id = wp_insert_post($topic_data);

						$item_i = 0;
						foreach ($topic->items as $item){
							$item_i++;

							$item_data = array(
								'post_type'     => (string) $item->post_type,
								'post_title'    => (string) $item->post_title,
								'post_content'  => (string) $item->post_content,
								'post_status'   => 'publish',
								'post_author'   => (string) $item->post_author,
								'post_parent'   => $topic_id,
								'menu_order'    => $item_i,
							);

							$item_id = wp_insert_post($item_data);

							$item_metas = json_decode(json_encode($item->item_meta), true);
							foreach ($item_metas as $item_meta_key => $item_meta_value){
								if ( is_array($item_meta_value)){
									$item_meta_value = json_encode($item_meta_value);
								}
								$wpdb->insert($wpdb->postmeta, array('post_id' => $item_id, 'meta_key' => $item_meta_key, 'meta_value'=> (string) $item_meta_value));
							}

							if (isset($item->questions) && is_object($item->questions) && count($item->questions)){
								foreach ($item->questions as $question) {
								    $answers = $question->answers;

									$question = (array) $question;
									$question['quiz_id'] = $item_id;
									$question['question_description'] = (string) $question['question_description'];
									unset($question['answers']);

									$wpdb->insert($wpdb->prefix.'tutor_quiz_questions', $question);
									$question_id = $wpdb->insert_id;

									foreach ($answers as $answer){
										$answer = (array) $answer;
										$answer['belongs_question_id'] = $question_id;
										$wpdb->insert($wpdb->prefix.'tutor_quiz_question_answers', $answer);
									}
								}
                            }
                        }
                    }

                    if (isset($course->reviews) && is_object($course->reviews) && count($course->reviews) ){
					    foreach ($course->reviews as $review){
						    $rating_data = array(
							    'comment_post_ID'   => $course_id,
							    'comment_approved'  => 'approved',
							    'comment_type'      => 'tutor_course_rating',
							    'comment_date'      => (string) $review->comment_date,
							    'comment_date_gmt'  => (string) $review->comment_date,
							    'comment_content'   => (string) $review->comment_content,
							    'user_id'           => (string) $review->user_id,
							    'comment_author'    => (string) $review->comment_author,
							    'comment_agent'     => 'TutorLMSPlugin',
						    );

						    $wpdb->insert($wpdb->comments, $rating_data);
						    $comment_id = (int) $wpdb->insert_id;

						    $rating_meta_data = array(
							    'comment_id' => $comment_id,
							    'meta_key' => 'tutor_rating',
							    'meta_value' => (string) $review->tutor_rating
						    );
						    $wpdb->insert( $wpdb->commentmeta,  $rating_meta_data);
                        }
                    }
				}
			}
		}


		public function tutor_lp_export_xml(){
			header('Content-Type: application/octet-stream');
			header('Content-Disposition: attachment; filename=learnpress_data_for_tutor.xml');
			header('Expires: 0');

			echo $this->generate_xml_data();
			exit;
		}


		public function generate_xml_data(){
			global $wpdb;

			$xml = '<?xml version="1.0" encoding="' . get_bloginfo( 'charset' ) . "\" ?>\n";
			$xml .= $this->start_element('channel');
			ob_start();
				?>
				<title><?php bloginfo_rss( 'name' ); ?></title>
				<link><?php bloginfo_rss( 'url' ); ?></link>
				<description><?php bloginfo_rss( 'description' ); ?></description>
				<pubDate><?php echo date( 'D, d M Y H:i:s +0000' ); ?></pubDate>
				<language><?php bloginfo_rss( 'language' ); ?></language>
				<tlmt_version><?php echo TLMT_VERSION; ?></tlmt_version>
				<?php
			$xml .= ob_get_clean();

			$lp_courses = $wpdb->get_results("SELECT ID, post_author, post_date, post_content, post_title, post_excerpt, post_status  FROM {$wpdb->posts} WHERE post_type = 'lp_course' AND post_status = 'publish';");

			//$xml .= $this->start_element('courses');

			if (tutils()->count($lp_courses)){
				$course_i = 0;
				foreach ($lp_courses as $lp_course){
					$course_i++;

					//print_r($lp_course);
					//post_type

					$course_id = $lp_course->ID;

					$xml .= $this->start_element('courses');

					$course_arr = (array) $lp_course;
					foreach ($course_arr as $course_col => $course_col_value){
						$xml .= "<{$course_col}>{$course_col_value}</{$course_col}>\n";
					}


					$course_metas = $wpdb->get_results("SELECT meta_key, meta_value from {$wpdb->postmeta} where post_id = {$course_id}");

					$xml .= $this->start_element('course_meta');
					foreach ($course_metas as $course_meta){
						$xml .= "<{$course_meta->meta_key}>{$course_meta->meta_value}</{$course_meta->meta_key}>\n";
					}
					$xml .= $this->close_element('course_meta');

					$course = learn_press_get_course($course_id);

					$lesson_post_type = tutor()->lesson_post_type;
					$course_post_type = tutor()->course_post_type;

					if ( $course) {
						$curriculum = $course->get_curriculum();

						$i            = 0;

						//$xml .= $this->start_element('topics');
						foreach ( $curriculum as $section ) {
							$i ++;

							$xml .= $this->start_element('topics');

							/**
							 * Topic
							 */
							$xml .= "<post_type>topics</post_type>\n";
							$xml .= "<post_title>{$section->get_title()}</post_title>\n";

							$topic_content = ! empty($section->get_description()) ? $this->xml_cdata($section->get_description()) : '';

							$xml .= "<post_content>{$topic_content}</post_content>\n";
							$xml .= "<post_status>publish</post_status>\n";
							$xml .= "<post_author>{$course->get_author( 'id' )}</post_author>\n";
							$xml .= "<post_parent>{$course_id}</post_parent>";
							$xml .= "<menu_order>{$i}</menu_order>\n";

							/**
							 * Lessons
							 */
							//$xml .= $this->start_element('items');

							$lessons = $this->get_lp_section_items($section->get_id());

							foreach ( $lessons as $lesson ) {
								//print_r($lesson);
								$item_post_type = $lesson->item_type;

								if ( $item_post_type !== 'lp_lesson' ) {
									if ( $item_post_type === 'lp_quiz' ) {
										$lesson_post_type = 'tutor_quiz';
									}
								}

								//Item
								$xml .= $this->start_element('items');

								$xml .= "<item_id>{$lesson->id}</item_id>\n";
								$xml .= "<post_type>{$lesson_post_type}</post_type>\n";
								$xml .= "<post_author>{$lesson->post_author}</post_author>\n";
								$xml .= "<post_date>{$lesson->post_date}</post_date>\n";
								$xml .= "<post_title>{$lesson->post_title}</post_title>\n";
								$xml .= "<post_content>{$this->xml_cdata($lesson->post_content)}</post_content>\n";
								$xml .= "<post_parent>{topic_id}</post_parent>\n";

								$xml .= $this->start_element('item_meta');

								$item_metas = $wpdb->get_results("SELECT meta_key, meta_value FROM {$wpdb->postmeta} WHERE post_id = {$lesson->id} ");

								if (is_array($item_metas) && count($item_metas)){
								    foreach ($item_metas as $item_meta){
									    $xml .= "<{$item_meta->meta_key}> {$this->xml_cdata($item_meta->meta_key)} </{$item_meta->meta_key}>\n";
								    }
                                }
								//print_r($item_metas);

								$xml .= $this->close_element('item_meta');
								//$xml .= $this->start_element('questions');

								if ($lesson_post_type === 'tutor_quiz'){
									$quiz_id = $lesson->id;

									$questions = $wpdb->get_results("SELECT question_id, question_order, questions.ID, questions.post_content, questions.post_title, question_type_meta.meta_value as question_type, question_mark_meta.meta_value as question_mark
						FROM {$wpdb->prefix}learnpress_quiz_questions 
						LEFT JOIN {$wpdb->posts} questions on question_id = questions.ID 
						LEFT JOIN {$wpdb->postmeta} question_type_meta on question_id = question_type_meta.post_id AND question_type_meta.meta_key = '_lp_type'
						LEFT JOIN {$wpdb->postmeta} question_mark_meta on question_id = question_mark_meta.post_id AND question_mark_meta.meta_key = '_lp_mark'
						WHERE quiz_id = {$quiz_id}  ");

									if (tutils()->count($questions)){

										foreach ($questions as $question) {

											$question_type = null;
											if ($question->question_type === 'true_or_false'){
												$question_type = 'true_false';
											}
											if ($question->question_type === 'single_choice'){
												$question_type = 'single_choice';
											}
											if ($question->question_type === 'multi_choice'){
												$question_type = 'multiple_choice';
											}

											if ($question_type) {
												$xml .= $this->start_element('questions');
												$new_question_data = array(
													'quiz_id'              => '{quiz_id}',
													'question_title'       => $question->post_title,
													'question_description' => $question->post_content,
													'question_type'        => $question_type,
													'question_mark'        => $question->question_mark,
													'question_settings'    => maybe_serialize( array() ),
													'question_order'       => $question->question_order,
												);

												foreach ($new_question_data as $question_key => $question_value){
													$xml .= "<{$question_key}>{$this->xml_cdata($question_value)}</{$question_key}>\n";
												}

												//$wpdb->insert($wpdb->prefix.'tutor_quiz_questions', $new_question_data);
												//$question_id = $wpdb->insert_id;
												$answer_items = $wpdb->get_results("SELECT * from {$wpdb->prefix}learnpress_question_answers where question_id = {$question->question_id} ");

												//$xml .= $this->start_element('answers');

												if (tutils()->count($answer_items)){
													foreach ($answer_items as $answer_item){
														$answer_data = maybe_unserialize($answer_item->answer_data);

														$answer_data = array(
															'belongs_question_id'   => '{question_id}',
															'belongs_question_type' => $question_type,
															'answer_title'          => tutils()->array_get('text', $answer_data),
															'is_correct'            => tutils()->array_get('is_true', $answer_data) == 'yes' ? 1 : 0,
															'answer_order'          => $answer_item->answer_order,
														);

														$xml .= $this->start_element('answers');

														foreach ($answer_data as $answers_key => $answers_value){
															$xml .= "<{$answers_key}>{$this->xml_cdata($answers_value)}</{$answers_key}>\n";
														}
														$xml .= $this->close_element('answers');

														//$wpdb->insert($wpdb->prefix.'tutor_quiz_question_answers', $answer_data);
													}
												}
												//$xml .= $this->close_element('answers');

												$xml .= $this->close_element('questions');
											}
										}
									}
								}

								//$xml .= $this->close_element('questions');

								$xml .= $this->close_element('items');
							}

							//Close Lessons Tag
							//$xml .= $this->close_element('items');

							//Close Topic Tag
							$xml .= $this->close_element('topics');
						}
						//$xml .= $this->close_element('topics');
					}

					//$xml .= $this->start_element('reviews');
					$lp_reviews = $wpdb->get_results("SELECT comments.comment_post_ID,
                    comments.comment_post_ID,
                    comments.comment_author,
                    comments.comment_author_email,
                    comments.comment_author_IP,
                    comments.comment_date,
                    comments.comment_date_gmt,
                    comments.comment_content,
                    comments.user_id,
                    cm.meta_value as tutor_rating
                     FROM {$wpdb->comments} comments INNER JOIN {$wpdb->commentmeta} cm ON cm.comment_id = comments.comment_ID AND cm.meta_key = '_lpr_rating' WHERE comments.comment_type = 'review';", ARRAY_A);

					if (tutils()->count($lp_reviews)){
						foreach ($lp_reviews as $lp_review){
							$lp_review['comment_approved'] = 'approved';
							$lp_review['comment_agent'] = 'TutorLMSPlugin';
							$lp_review['comment_type'] = 'tutor_course_rating';

							$xml .= $this->start_element('reviews');
							foreach ($lp_review as $lp_review_key => $lp_review_value){
								$xml .= "<{$lp_review_key}>{$this->xml_cdata($lp_review_value)}</{$lp_review_key}>\n";
							}
							$xml .= $this->close_element('reviews');
						}
					}

					//$xml .= $this->close_element('reviews');
					$xml .= $this->close_element('courses');
				}
			}

			//$xml .= $this->close_element('courses');
			$xml .= $this->close_element('channel');
			return $xml;
		}

		public function start_element($element = ''){
			return "\n<{$element}>\n";
		}
		public function close_element($element = ''){
			return "\n</{$element}>\n";
		}

		function xml_cdata( $str ) {
			if ( ! seems_utf8( $str ) ) {
				$str = utf8_encode( $str );
			}
			// $str = ent2ncr(esc_html($str));
			$str = '<![CDATA[' . str_replace( ']]>', ']]]]><![CDATA[>', $str ) . ']]>';

			return $str;
		}

		/**
		 * @param $section_id
		 *
		 * @return array|null|object
		 *
		 * Get items (lesson|quiz) by section ID
		 */

		public function get_lp_section_items( $section_id  ) {
			global $wpdb;

			$query = $wpdb->prepare( "
			SELECT item_id id, item_type, it.post_author, it.post_date, it.post_content, it.post_title, it.post_excerpt
			
			FROM {$wpdb->learnpress_section_items} si 
			
			INNER JOIN {$wpdb->learnpress_sections} s ON si.section_id = s.section_id
			INNER JOIN {$wpdb->posts} c ON c.ID = s.section_course_id
			INNER JOIN {$wpdb->posts} it ON it.ID = si.item_id
			
			WHERE s.section_id = %d
			AND it.post_status = %s
			ORDER BY si.item_order, si.section_item_id ASC
		", $section_id, /*'publish',*/ 'publish' );

			return $wpdb->get_results( $query );
		}
	}
}