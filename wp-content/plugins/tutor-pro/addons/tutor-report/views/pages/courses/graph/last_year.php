<?php
global $wpdb;

$lastYear = date('Y', strtotime('-1 year'));


$enrolledQuery = $wpdb->get_results( 
    "SELECT SUM(meta.meta_value) as total_sales, DATE(posts.post_date) as date_format from {$wpdb->posts} AS posts
    LEFT JOIN {$wpdb->postmeta} AS meta2 ON posts.ID = meta2.post_id
    LEFT JOIN {$wpdb->postmeta} AS meta ON posts.ID = meta.post_id
    WHERE posts.post_type = 'shop_order'
    AND meta.meta_key = '_order_total'
    AND meta2.meta_key = '_tutor_order_for_course_id_{$current_id}'
    AND YEAR(posts.post_date) = {$lastYear} 
    AND posts.post_status = 'wc-completed'
    GROUP BY MONTH (posts.post_date)
    ORDER BY MONTH(posts.post_date) ASC;"
);


$total_sales = wp_list_pluck($enrolledQuery, 'total_sales');
$months = wp_list_pluck($enrolledQuery, 'month_name');
$monthWiseEnrolled = array_combine($months, $total_sales);

$emptyMonths = array();
for ($m=1; $m<=12; $m++) {
	$emptyMonths[date('F', mktime(0,0,0,$m, 1, date('Y')))] = 0;
}
$chartData = array_merge($emptyMonths, $monthWiseEnrolled);

include TUTOR_REPORT()->path.'views/pages/courses/graph/body.php';
?>