<?php
    foreach($classroom_stream as $stream){
            
        $photo_url = $stream->creator_user_object->photoUrl;
        strpos($photo_url, '//')===0 ? $photo_url='https://'.$photo_url : 0;

        $user_name = $stream->creator_user_object->name->fullName;

        ?>
            <div class="tutor-announcement">
                <a href="<?php echo $stream->alternateLink; ?>" class="tutor-gc-google-stream">
                    <img src="<?php echo $photo_url; ?>"/>
                    <h3>
                        <?php echo $user_name; ?>
                        <small><?php echo date("j F, Y", strtotime($stream->creationTime)); ?></small>
                    </h3>
                    
                    <p><?php echo $stream->text; ?></p>
                </a>
                
                <?php 
                    if($show_stream_files){
                        $materials_array = $stream->materials ? $stream->materials : array();
                        include 'materials.php';
                    }
                ?>
            </div>
        <?php
    }
?>