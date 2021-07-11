<div class="tutor-lesson-attachments-metabox">
    <div class="tutor-added-attachments-wrap tutor-course-builder-attachements">
        <?php
            $attachments = tutor_utils()->get_attachments();
            if (is_array($attachments) && count($attachments)) {
                foreach ($attachments as $attachment) { ?>
                <div class="tutor-added-attachment">
                    <i class="tutor-icon-archive"></i>
                    <a href="javascript:;" class="tutor-delete-attachment tutor-icon-line-cross"></a>
                    <span><a href="<?php echo $attachment->url; ?>"><?php echo $attachment->name; ?></a></span>
                    <input type="hidden" name="tutor_attachments[]" value="<?php echo $attachment->id; ?>">
                </div> 
                <?php 
                }
            }
        ?>
        <input type="hidden" name="_tutor_attachments_main_edit" value="true" />    
    </div>
    <button type="button" class="tutor-btn tutorUploadAttachmentBtn bordered-btn"><i class="tutor-icon-attach"></i><?php _e('Upload Attachment', 'tutor-pro'); ?></button>
</div>