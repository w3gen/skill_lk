<div class="tutor-leadinfo-top-meta etlms-rating">
    <span class="tutor-single-course-rating">
        <?php
        $course_rating = tutor_utils()->get_course_rating();
        tutor_utils()->star_rating_generator($course_rating->rating_avg);
        ?>
        <span class="tutor-single-rating-count">
            <?php
            echo $course_rating->rating_avg;
            echo '<i>(' . $course_rating->rating_count .' '. __('Ratings') . ')</i>';
            ?>
        </span>
    </span>
</div>