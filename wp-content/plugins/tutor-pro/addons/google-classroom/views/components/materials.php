<div class="tutor-attachments-wrap">
    <?php
        $title_length = 18;

        foreach($materials_array as $attachment){

            $public_resource = $attachment->youtubeVideo ? $attachment->youtubeVideo : $attachment->link;
            $drive_file = $attachment->driveFile ? $attachment->driveFile : null;
            ($drive_file && $drive_file->driveFile) ? $drive_file=$drive_file->driveFile : 0;

            $content = $drive_file ? $drive_file : $public_resource;

            if(!$content){
                continue;
            }

            ?>
                <a href="<?php echo ($content->alternateLink ? $content->alternateLink : $content->url); ?>" target="_blank" class="tutor-gc-material">
                    <div style="background-image:url(<?php echo TUTOR_GC()->url; ?>/assets/images/attachment-icon.svg)" data-thumbnail_url="<?php echo $content->thumbnailUrl; ?>" class="tutor-gc-google-thumbnail">
                    
                    </div>
                    <div>
                        <span>
                            <?php 

                                $title = $content->title; 
                                $cutted_title = substr($title, 0, $title_length);
                                
                                echo $cutted_title, (strlen($title)>$title_length ? '..' : '');
                            ?>
                        </span>
                    </div>
                </a>
            <?php
        }
    ?>
</div>