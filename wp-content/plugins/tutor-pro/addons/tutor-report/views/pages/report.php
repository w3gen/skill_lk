<?php
$currentSubPage = 'overview';
$currentName = 'Overview';
$subPages = array(
	'overview' => __('Overview', 'tutor-pro'),
	'courses' => __('Courses', 'tutor-pro'),
	'reviews' => __('Reviews', 'tutor-pro'),
	'sales' => __('Sales', 'tutor-pro'),
	'students' => __('Students', 'tutor-pro'),
);

if ( ! empty($_GET['sub_page'])){
    $currentSubPage = sanitize_text_field($_GET['sub_page']);
    $currentName = isset($subPages[$currentSubPage]) ? $subPages[$currentSubPage] : '';
}
?>

<div class="wrap">
    <div class="report-main-wrap">
        <div class="tutor-report-left-menus">
            <div class="tutor-report-title">
                <strong><?php _e('LMS Report', 'tutor-pro'); ?></strong>
                <span>/ <?php echo $currentName; ?></span>
            </div>
            <div class="tutor-report-menu">
                <ul>
                    <?php
                    foreach ($subPages as $pageKey => $pageName){
                        $activeClass = ($pageKey === $currentSubPage) ? 'active' : '';
                        echo "<li class='{$activeClass}'><a href='".add_query_arg(array('page'=>'tutor_report', 'sub_page' => $pageKey), admin_url('admin.php'))."'>{$pageName}</a></li>";
                    }
                    ?>
                </ul>
            </div>
        </div>

        <div class="tutor-report-content">
            <?php
            $page = 'overview';
            if ( ! empty($_GET['sub_page'])){
	            $page = sanitize_text_field($_GET['sub_page']);
            }
            $view_page = TUTOR_REPORT()->path.'views/pages/';

            if (file_exists($view_page.$page."/{$page}.php")){
                include $view_page.$page."/{$page}.php";
            }elseif(file_exists($view_page."{$page}.php")){
                include $view_page."{$page}.php";
            }
            ?>
        </div>
    </div>
</div>