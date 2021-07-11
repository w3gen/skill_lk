<?php
global $wpdb;


/**
 * Getting the last week
 */
$start_week = date("Y-m-01");
$end_week = date("Y-m-t");

/**
 * Format Date Name
 */
$begin = new DateTime($start_week);
$end = new DateTime($end_week.' + 1 day');
$interval = DateInterval::createFromDateString('1 day');
$period = new DatePeriod($begin, $interval, $end);

$datesPeriod = array();
foreach ($period as $dt) {
	$datesPeriod[$dt->format("Y-m-d")] = 0;
}

/**
 * Query last week
 */

$enrolledQuery = $wpdb->get_results( 
    "SELECT SUM(meta.meta_value) as total_sales, DATE(posts.post_date) as date_format from {$wpdb->posts} AS posts
    LEFT JOIN {$wpdb->postmeta} AS meta2 ON posts.ID = meta2.post_id
    LEFT JOIN {$wpdb->postmeta} AS meta ON posts.ID = meta.post_id
    WHERE posts.post_type = 'shop_order'
    AND meta.meta_key = '_order_total'
    AND meta2.meta_key = '_tutor_order_for_course_id_{$current_id}'
    AND (posts.post_date BETWEEN '{$start_week}' AND '{$end_week}')
    AND posts.post_status = 'wc-completed'
    GROUP BY date_format
    ORDER BY posts.post_date ASC;"
);

              


$total_sales = wp_list_pluck($enrolledQuery, 'total_sales');
$queried_date = wp_list_pluck($enrolledQuery, 'date_format');
$dateWiseEnrolled = array_combine($queried_date, $total_sales);

$chartData = array_merge($datesPeriod, $dateWiseEnrolled);
foreach ($chartData as $key => $enrolledCount){
    unset($chartData[$key]);
    $formatDate = date('d M', strtotime($key));
	$chartData[$formatDate] = $enrolledCount;
}

include TUTOR_REPORT()->path.'views/pages/courses/graph/body.php';