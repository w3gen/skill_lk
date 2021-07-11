<?php
$check_api = tutor_zoom_check_api_connection();
$currentSubPage = ($check_api) ? 'meetings' : 'set_api';
$currentName = ($check_api) ? 'All Meetings' : 'Set Api';
$subPages = array(
    'meetings' => __('All Meetings', 'tutor-pro'),
    'set_api' => __('Set API', 'tutor-pro'),
    'settings' => __('Settings', 'tutor-pro'),
    'help' => __('Help', 'tutor-pro'),
);

$error_msg = '';
if (!empty($_GET['sub_page'])) {
    $currentSubPage = sanitize_text_field($_GET['sub_page']);
    if(!$check_api && ($currentSubPage == 'meetings' || $currentSubPage == 'settings')) {
        $error_msg = __('Please set your API Credentials. Without valid credentials, Zoom integration will not work', 'tutor-pro');
        $currentSubPage = 'set_api';
    }
    $currentName = isset($subPages[$currentSubPage]) ? $subPages[$currentSubPage] : '';
}
?>

<div class="wrap">
    <div class="report-main-wrap">
        <div class="tutor-report-left-menus">
            <div class="tutor-report-title">
                <strong><?php _e('Zoom', 'tutor-pro'); ?></strong>
                <span>/ <?php echo $currentName; ?></span>
            </div>
            <div class="tutor-report-menu">
                <ul>
                    <?php
                    foreach ($subPages as $pageKey => $pageName) {
                        $activeClass = ($pageKey === $currentSubPage) ? 'active' : '';
                        echo "<li class='{$activeClass}'><a href='" . add_query_arg(array('page' => 'tutor_zoom', 'sub_page' => $pageKey), admin_url('admin.php')) . "'>{$pageName}</a></li>";
                    }
                    ?>
                </ul>
            </div>
        </div>

        <?php if ($error_msg) {
            echo "<div class='tutor-alert zoom-api-error'>{$error_msg}</div>";
        } ?>

        <div class="tutor-zoom-content">
            <?php
            $page = sanitize_text_field($currentSubPage);
            $view_page = TUTOR_ZOOM()->path . 'views/pages/';

            if (file_exists($view_page . "/{$page}.php")) {
                include $view_page . "/{$page}.php";
            }
            ?>
        </div>
    </div>
</div>