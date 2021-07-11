<?php

global $wpdb;

$course_type = tutor()->course_post_type;

if(isset($_GET['course_id'])){
    include TUTOR_REPORT()->path.'views/pages/courses/course-single.php';
} else {
    include TUTOR_REPORT()->path.'views/pages/courses/course-table.php';
}