<div class="tutor-gc-stream-classroom-info">
    <h3><?php echo $classroom_info->descriptionHeading; ?></h3>
    <p><?php echo $classroom_info->room_and_section; ?></p>
    <div>
        <span class="tutor-gc-class-code">
            <span><?php echo __('Code', 'tutor-pro'); ?>: </span>
            <span><?php echo $classroom_info->enrollmentCode; ?></span>
            <span class="tutor-icon-copy tutor-gc-copy-text" data-text="<?php echo $classroom_info->enrollmentCode; ?>"></span>
        </span>

        <a class="tutor-gc-class-go-to" href="<?php echo $classroom_info->alternateLink; ?>">
            <?php echo __('Go to Classroom', 'tutor-pro'); ?>
        </a>
    </div>
</div>

<div class="tutor-announcements-wrap">
    <?php
        if(!count($classroom_stream)){
            // echo '<br/><div style="text-align:center">'.__('No Post Found', 'tutor-pro').'</div>';
        }

        include 'stream-individual.php';

        if($stream_next_token){
            ?>
                <div style="text-align:center" id="tutor_gc_stream_loader" data-next_token="<?php echo $stream_next_token; ?>" data-course_id="<?php echo $course_id; ?>">
                    <br/>
                    <br/>
                    <a href="#"><?php echo __('Load More', 'tutor-prop'); ?></a>
                    <span style="display:inline-block;">
                        <img src="<?php echo get_admin_url().'images/loading.gif'; ?>" style="display:none;"/>
                    </span>
                </div>
            <?php
        }
    ?>    
</div>