<?php

global $wpdb;

$reviewsCount = (int) $wpdb->get_var(
	"SELECT COUNT({$wpdb->comments}.comment_ID) 
	FROM {$wpdb->comments}
	INNER JOIN {$wpdb->commentmeta} 
	ON {$wpdb->comments}.comment_ID = {$wpdb->commentmeta}.comment_id
	INNER  JOIN {$wpdb->users}
	ON {$wpdb->comments}.user_id = {$wpdb->users}.ID
	AND meta_key = 'tutor_rating'"
);

$per_page = 50;
$total_items = $reviewsCount;
$current_page = isset( $_GET['paged'] ) ? $_GET['paged'] : 0;
$start =  max( 0,($current_page-1)*$per_page );

$course_query  = '';
if ( ! empty($_GET['course_id'])){
	$course_id = sanitize_text_field($_GET['course_id']);
	$course_query = "AND {$wpdb->comments}.comment_post_ID =".$course_id;
}
$user_query  = '';
if ( ! empty($_GET['user_id'])){
	$user_id = sanitize_text_field($_GET['user_id']);
	$user_query = "AND {$wpdb->comments}.user_id =".$user_id;
}

$reviews = $wpdb->get_results(
	"SELECT {$wpdb->comments}.comment_ID, 
	{$wpdb->comments}.comment_post_ID, 
	{$wpdb->comments}.comment_author, 
	{$wpdb->comments}.comment_author_email, 
	{$wpdb->comments}.comment_date, 
	{$wpdb->comments}.comment_content, 
	{$wpdb->comments}.user_id, 
	{$wpdb->commentmeta}.meta_value as rating,
	{$wpdb->users}.display_name 
	FROM {$wpdb->comments}
	INNER JOIN {$wpdb->commentmeta} 
	ON {$wpdb->comments}.comment_ID = {$wpdb->commentmeta}.comment_id {$course_query} {$user_query}
	INNER  JOIN {$wpdb->users}
	ON {$wpdb->comments}.user_id = {$wpdb->users}.ID
	AND meta_key = 'tutor_rating' ORDER BY comment_ID DESC LIMIT {$start},{$per_page} ;"
);
?>

<div class="tutor-list-wrap tutor-report-review">
	<div class="tutor-list-header">
		<div class="heading">
			<?php _e('Reviews', 'tutor-pro'); ?>
		</div>
		<p><?php echo sprintf(__('Total reviews %d', 'tutor-pro'), $reviewsCount) ?></p>
	</div>
	
	<?php if(!empty($reviews)) { ?>
		<table class="tutor-list-table">
			<thead>
				<tr>
					<th width="150"><?php _e('User', 'tutor-pro'); ?> </th>
					<th><?php _e('Course', 'tutor-pro'); ?> </th>
					<th><?php _e('Rating', 'tutor-pro'); ?> </th>
					<th><?php _e('Reviews', 'tutor-pro'); ?> </th>
					<th><?php _e('Time', 'tutor-pro'); ?> </th>
					<th><?php _e('Action', 'tutor-pro'); ?> </th>
				</tr>
			</thead>
			<?php
			if (is_array($reviews) && count($reviews)){
				foreach ($reviews as $review){
					?>
					
						<tr>
							<td>
								<div class="instructor">
									<div class="instructor-thumb">
										<span class="instructor-icon"><?php echo get_avatar($review->user_id, 50); ?></span>
									</div>
									<div class="instructor-meta">
										<span class="instructor-name">
											<span><?php echo $review->display_name; ?></span> <a target="_blank" href="<?php echo admin_url('admin.php?page=tutor_report&sub_page=students&student_id='.$review->user_id); ?>"><i class="tutor-icon-detail-link"></i></a>
										</span>
									</div>
								</div>
							</td>
							<td><?php echo get_the_title($review->comment_post_ID); ?></td>
							<td><?php tutor_utils()->star_rating_generator($review->rating, true); ?></td>
							<td><?php echo wpautop($review->comment_content); ?></td>
							<td><?php echo human_time_diff(strtotime($review->comment_date)).' '.__('ago', 'tutor-pro'); ?></td>
							<td>
								<div class="details-button">
									<button type="button" class="tutor-delete-link tutor-rating-delete-link tutor-report-btn default" data-rating-id="<?php echo $review->comment_ID; ?>">
										<i class="tutor-icon-trash"></i> <?php _e('Delete', 'tutor-pro'); ?>
									</button>
									<a target="_blank" href="<?php echo get_permalink($review->comment_post_ID); ?>"><i class="tutor-icon-detail-link"></i></a>
								</div>
							</td>
						</tr>
					
					<?php
				}
			}
			?>
		</table>
	<?php } else { ?>
		<div class="no-data-found">
			<img src="<?php echo tutor_pro()->url."addons/tutor-report/assets/images/empty-data.svg"?>" alt="">
			<span><?php _e('No Review Data Found!', 'tutor-pro'); ?></span>
		</div>
	<?php } ?>

	<div class="tutor-list-footer">
		<div class="tutor-report-count"></div>
		<div class="tutor-pagination" >
			<?php
			echo paginate_links( array(
				'base' => str_replace( $current_page, '%#%', "admin.php?page=tutor_report&sub_page=reviews&paged=%#%" ),
				'current' => max( 1, $current_page ),
				'total' => ceil($total_items/$per_page)
			) );
			?>
		</div>
    </div>
</div>