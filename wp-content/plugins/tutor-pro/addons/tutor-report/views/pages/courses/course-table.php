<?php
if ( ! defined( 'ABSPATH' ) )
exit;


// Pagination
$per_page = 10;
$current_page = isset( $_GET['paged'] ) ? $_GET['paged'] : 0;
$start =  max( 0,($current_page-1)*$per_page );

// Order Filter
$order_filter = isset($_GET['order']) ? $_GET['order'] : 'DESC';

// Date Filter
$date_filter = '';
$_date = isset($_GET['date']) ? $_GET['date'] : ''; 
if($_date){
    $date_filter = DateTime::createFromFormat('Y-m-d', $_date);
    $date_filter = "AND (post_date BETWEEN '{$date_filter->modify('-1 day')->format('Y-m-d')}' AND '{$date_filter->modify('+2 day')->format('Y-m-d')}')";
}

// Search Filter
$search_sql = '';
$_search = isset($_GET['search']) ? $_GET['search'] : ''; 
if($_search){
    $search_sql = "AND {$wpdb->posts}.post_title LIKE '%{$_search}%' ";
}

// Category Filter
$category_sql = '';
$_cat = isset($_GET['cat']) ? $_GET['cat'] : ''; 
if($_cat){
    $category_sql = "SELECT {$wpdb->posts}.ID
    FROM {$wpdb->posts}, {$wpdb->term_relationships}, {$wpdb->terms}
    WHERE {$wpdb->posts}.ID = {$wpdb->term_relationships}.object_id
    AND {$wpdb->terms}.term_id = {$wpdb->term_relationships}.term_taxonomy_id
    AND {$wpdb->terms}.term_id = {$_cat}";
    $category_sql = "AND {$wpdb->posts}.ID IN ({$category_sql}) ";
}

$all_data = $wpdb->get_results(
    "SELECT ID, post_title FROM {$wpdb->posts} 
    WHERE post_type ='{$course_type}' 
    AND post_status = 'publish'
    {$search_sql}
    {$category_sql}
    {$date_filter}
    ORDER BY ID {$order_filter} LIMIT {$start},{$per_page};");

$total_items = count($wpdb->get_results("SELECT ID, post_title FROM {$wpdb->posts} WHERE post_type ='{$course_type}' AND post_status = 'publish' {$search_sql} {$category_sql} {$date_filter};"));

function quiz_number($current_id){
    global $wpdb;
    $quiz_number = $wpdb->get_var($wpdb->prepare(
        "SELECT COUNT(ID) FROM {$wpdb->posts}
        WHERE post_parent IN (SELECT ID FROM {$wpdb->posts} WHERE post_type ='topics' AND post_parent = %d AND post_status = 'publish')
        AND post_type ='tutor_quiz' 
        AND post_status = 'publish'", $current_id));

        return $quiz_number;
}

$complete_data = 0;
$course_single = array();
if(is_array($all_data) && !empty($all_data)){
    $complete = 0;
    foreach ($all_data as $data) {
        $var = array();
        $var['id'] = $data->ID;
        $var['link'] = get_permalink($data->ID);
        $var['course'] = $data->post_title;
        $var['lesson'] = tutor_utils()->get_lesson_count_by_course($data->ID);
        $var['quiz'] = quiz_number($data->ID);
        $var['assignment'] = tutor_utils()->get_assignments_by_course($data->ID)->count;
        $var['students'] = tutor_utils()->count_enrolled_users_by_course($data->ID);

        $total_sales = 0;
        $product_id = get_post_meta($data->ID, '_tutor_course_product_id', true);
        if($product_id){
            $total_sales = $wpdb->get_var( "SELECT SUM( order_item_meta__line_total.meta_value) as order_item_amount 
            FROM {$wpdb->posts} AS posts
            INNER JOIN {$wpdb->prefix}woocommerce_order_items AS order_items ON posts.ID = order_items.order_id
            INNER JOIN {$wpdb->prefix}woocommerce_order_itemmeta AS order_item_meta__line_total ON (order_items.order_item_id = order_item_meta__line_total.order_item_id)
                AND (order_item_meta__line_total.meta_key = '_line_total')
            INNER JOIN {$wpdb->prefix}woocommerce_order_itemmeta AS order_item_meta__product_id_array ON order_items.order_item_id = order_item_meta__product_id_array.order_item_id 
            WHERE posts.post_type IN ( 'shop_order' )
            AND posts.post_status IN ( 'wc-completed' ) AND ( ( order_item_meta__product_id_array.meta_key IN ('_product_id','_variation_id') 
            AND order_item_meta__product_id_array.meta_value IN ('{$product_id}') ) );" );
        }
        if(function_exists('wc_price')){
            $total_sales = wc_price($total_sales);
        }
        $var['earnings'] = $total_sales;
        $course_single[] = $var;
    }
} else {
    $complete_data = 0;
}
?>
<div class="tutor-admin-search-box-container">
    <div>
        <div class="menu-label"><?php _e('Search', 'tutor-pro'); ?></div>
        <div>
            <input type="text" class="tutor-report-search" value="<?php echo $_search; ?>" autocomplete="off" placeholder="<?php _e('Search in here.', 'tutor-pro'); ?>" />
            <button class="tutor-report-search-btn"><i class="tutor-icon-magnifying-glass-1"></i></button>
        </div>
    </div>

    <div>
        <div class="menu-label"><?php _e('Category', 'tutor-pro'); ?></div>
        <div>
            <select class="tutor-report-category">
                <?php
                    $terms = get_terms( 'course-category', array( 'hide_empty' => true) );
                    if (!empty($terms)) {
                        array_unshift($terms, (object)['term_id' => '', 'name' => 'All']);
                        foreach ($terms as $key => $val) {
                            echo '<option '.($_cat == $val->term_id ? "selected" : "").' value="'.$val->term_id.'">'.$val->name.'</option>';
                        }
                    }
                ?>
            </select>
        </div>
    </div>

    <div>
        <div class="menu-label"><?php _e('Sort By', 'tutor-pro'); ?></div>
        <div>
            <select class="tutor-report-sort">
                <option <?php selected( $order_filter, 'ASC' ); ?>>ASC</option>
                <option <?php selected( $order_filter, 'DESC' ); ?>>DESC</option>
            </select>
        </div>
    </div>

    <div>
        <div class="menu-label"><?php _e('Date', 'tutor-pro'); ?></div>
        <div class="date-range-input">
            <input type="text" class="tutor_report_datepicker tutor-report-date" value="<?php echo $_date; ?>" autocomplete="off" placeholder="<?php echo date("Y-m-d", strtotime("last sunday midnight")); ?>" />
            <i class="tutor-icon-calendar"></i>
        </div>
    </div>
</div>

<div class="tutor-list-wrap tutor-report-course-list">
    <div class="tutor-list-header">
        <div class="heading"><?php _e('Course List', 'tutor-pro'); ?></div>
    </div>
    <?php if(!empty($course_single)) { ?>
        <table class="tutor-list-table">
            <thead>
                <tr>
                    <th><?php _e('Course', 'tutor-pro'); ?></th>
                    <th><?php _e('Lesson', 'tutor-pro'); ?></th>
                    <th><?php _e('Quiz', 'tutor-pro'); ?></th>
                    <th><?php _e('Assignment', 'tutor-pro'); ?></th>
                    <th><?php _e('Total Students', 'tutor-pro'); ?></th>
                    <th><?php _e('Earnings', 'tutor-pro'); ?></th>
                    <th></th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($course_single as $key => $course) { ?>
                    <tr>
                        <td><a href="<?php echo $course['link']; ?>" target="_blank"><?php echo $course['course']; ?></a></td>
                        <td><?php echo $course['lesson']; ?></td>
                        <td><?php echo $course['quiz']; ?></td>
                        <td><?php echo $course['assignment']; ?></td>
                        <td><?php echo $course['students']; ?></td>
                        <td><?php echo $course['earnings']; ?></td>
                        <td>
                            <div class="details-button">
                                <a class="tutor-report-btn default" href="<?php echo admin_url('admin.php?page=tutor_report&sub_page=courses&course_id='.$course['id']); ?>"><?php _e('Details', 'tutor-pro') ?></a>
                                <a href="<?php echo $course['link']; ?>" target="_blank"><i class="tutor-icon-detail-link"></i></a>
                            </div>
                        </td>
                    </tr>
                <?php } ?>
            </tbody>
        </table>
    <?php } else { ?>
        <div class="no-data-found">
			<img src="<?php echo tutor_pro()->url."addons/tutor-report/assets/images/empty-data.svg"?>" alt="">
			<span><?php _e('No Course Data Found!', 'tutor-pro'); ?></span>
		</div>
    <?php } ?>

    <div class="tutor-list-footer">
        <div class="tutor-report-count">
            <?php 
                if($total_items > 0){
                    printf( __('Items <strong> %s </strong> of <strong> %s </strong> total'), $per_page,  $total_items );
                }
            ?>
        </div>
        <div class="tutor-pagination">
            <?php
            echo paginate_links( array(
                'base' => str_replace( 1, '%#%', "admin.php?page=tutor_report&sub_page=courses&paged=%#%" ),
                'current' => max( 1, $current_page ),
                'total' => ceil($total_items/$per_page)
            ) );
            ?>
        </div>
    </div>

</div>