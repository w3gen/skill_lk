<?php
$attempt = tutor_utils()->get_attempt($attempt_id);

$quiz_attempt_info = tutor_utils()->quiz_attempt_info($attempt->attempt_info);
$answers = tutor_utils()->get_quiz_answers_by_attempt_id($attempt->attempt_id);

$user_id = tutor_utils()->avalue_dot('user_id', $attempt);
$user = get_userdata($user_id);
?>

<div class="tutor-quiz-attempt-review-wrap">

	<?php
	if (is_array($answers) && count($answers)){

		?>
		<div class="quiz-attempt-answers-wrap">

			<div class="attempt-answers-header">
				<h3><?php _e('View Attempts', 'tutor-pro'); ?> 
					<a href="<?php echo remove_query_arg('view_quiz_attempt_id') ?>" class="tutor-button tutor-button-primary"> <i class="tutor-icon-angle-left"></i>
						<?php _e('Back to quiz',	'tutor-pro'); ?></a>
				</h3>
			</div>

			<table class="wp-list-table">
				<tr>
					<th><?php _e('Type', 'tutor-pro'); ?></th>
					<th><?php _e('No.', 'tutor-pro'); ?></th>
					<th><?php _e('Question', 'tutor-pro'); ?></th>
					<th><?php _e('Given Answers', 'tutor-pro'); ?></th>
					<th><?php _e('Correct/Incorrect', 'tutor-pro'); ?></th>
				</tr>
				<?php
				$answer_i = 0;
				foreach ($answers as $answer){
					$answer_i++;
					$question_type = tutor_utils()->get_question_types($answer->question_type);
					?>
					<tr>
						<td><?php echo $question_type['icon']; ?></td>
						<td><?php echo $answer_i; ?></td>
						<td><?php echo stripslashes($answer->question_title); ?></td>
						<td>
							<?php
							if ($answer->question_type === 'true_false' || $answer->question_type === 'single_choice' ){
								$get_answers = tutor_utils()->get_answer_by_id($answer->given_answer);
								$answer_titles = wp_list_pluck($get_answers, 'answer_title');
								$answer_titles = array_map('stripslashes', $answer_titles);
								echo '<p>'.implode('</p><p>', $answer_titles).'</p>';
							}elseif ($answer->question_type === 'multiple_choice'){
								$get_answers = tutor_utils()->get_answer_by_id(maybe_unserialize($answer->given_answer));
								$answer_titles = wp_list_pluck($get_answers, 'answer_title');
								$answer_titles = array_map('stripslashes', $answer_titles);
								echo '<p>'.implode('</p><p>', $answer_titles).'</p>';
							}elseif ($answer->question_type === 'fill_in_the_blank'){
								$answer_titles = maybe_unserialize($answer->given_answer);
								$get_db_answers_by_question = tutor_utils()->get_answers_by_quiz_question($answer->question_id);
								foreach ($get_db_answers_by_question as $db_answer);
								$count_dash_fields = substr_count($db_answer->answer_title, '{dash}');
								if ($count_dash_fields){
									$dash_string = array();
									$input_data = array();
									for($i=0; $i<$count_dash_fields; $i++){
										//$dash_string[] = '{dash}';
										$input_data[] =  isset($answer_titles[$i]) ? "<span class='filled_dash_unser'>{$answer_titles[$i]}</span>" : "______";
									}
									$answer_title = $db_answer->answer_title;
									foreach($input_data as $replace){
										$answer_title = preg_replace('/{dash}/i', $replace, $answer_title, 1);
									}
									echo str_replace('{dash}', '_____', $answer_title);
								}

							}elseif ($answer->question_type === 'open_ended' || $answer->question_type === 'short_answer'){

								if ($answer->given_answer){
									echo wpautop(stripslashes($answer->given_answer));
								}

							}elseif ($answer->question_type === 'ordering'){

								$ordering_ids = maybe_unserialize($answer->given_answer);
								foreach ($ordering_ids as $ordering_id){
									$get_answers = tutor_utils()->get_answer_by_id($ordering_id);
									$answer_titles = wp_list_pluck($get_answers, 'answer_title');
									$answer_titles = array_map('stripslashes', $answer_titles);
									echo '<p>'.implode('</p><p>', $answer_titles).'</p>';
								}

							}elseif ($answer->question_type === 'matching'){

								$ordering_ids = maybe_unserialize($answer->given_answer);
								$original_saved_answers = tutor_utils()->get_answers_by_quiz_question($answer->question_id);

								foreach ($original_saved_answers as $key => $original_saved_answer){
									$provided_answer_order_id = isset($ordering_ids[$key]) ? $ordering_ids[$key] : 0;
									$provided_answer_order = tutor_utils()->get_answer_by_id($provided_answer_order_id);
									if(tutils()->count($provided_answer_order)){
										foreach ($provided_answer_order as $provided_answer_order);
										echo $original_saved_answer->answer_title  ." - {$provided_answer_order->answer_two_gap_match} <br />";
									}
								}

							}elseif ($answer->question_type === 'image_matching'){

								$ordering_ids = maybe_unserialize($answer->given_answer);
								$original_saved_answers = tutor_utils()->get_answers_by_quiz_question($answer->question_id);

								echo '<div class="answer-image-matched-wrap">';
								foreach ($original_saved_answers as $key => $original_saved_answer){
									$provided_answer_order_id = isset($ordering_ids[$key]) ? $ordering_ids[$key] : 0;
									$provided_answer_order = tutor_utils()->get_answer_by_id($provided_answer_order_id);
									foreach ($provided_answer_order as $provided_answer_order);
									?>
									<div class="image-matching-item">
										<p class="dragged-img-rap"><img src="<?php echo wp_get_attachment_image_url( $original_saved_answer->image_id); ?>" /> </p>
										<p class="dragged-caption"><?php echo $provided_answer_order->answer_title; ?></p>
									</div>
									<?php
								}
								echo '</div>';
							}elseif ($answer->question_type === 'image_answering'){

								$ordering_ids = maybe_unserialize($answer->given_answer);

								echo '<div class="answer-image-matched-wrap">';
								foreach ($ordering_ids as $answer_id => $image_answer){
									$db_answers = tutor_utils()->get_answer_by_id($answer_id);
									foreach ($db_answers as $db_answer);
									?>
									<div class="image-matching-item">
										<p class="dragged-img-rap"><img src="<?php echo wp_get_attachment_image_url( $db_answer->image_id); ?>" /> </p>
										<p class="dragged-caption"><?php echo $image_answer; ?></p>
									</div>
									<?php
								}
								echo '</div>';
							}

							?>
						</td>

						<td>
							<?php

							if ( (bool) isset( $answer->is_correct ) ? $answer->is_correct : '' ) {
								echo '<span class="quiz-correct-answer-text"><i class="tutor-icon-mark"></i> '.__('Correct', 'tutor-pro').'</span>';
							} else {
								if ($answer->question_type === 'open_ended' || $answer->question_type === 'short_answer'){
									echo '<p style="color: #878A8F;"><span style="color: #ff282a;">&ast;</span> '.__('Review Required', 'tutor-pro').'</p>';
								}else {
									echo '<span class="quiz-incorrect-answer-text"><i class="tutor-icon-line-cross"></i> '.__('Incorrect', 'tutor-pro').'</span>';
								}
							}
							?>
						</td>

					</tr>
					<?php
				}
				?>
			</table>
		</div>

		<?php
	}
	?>
</div>