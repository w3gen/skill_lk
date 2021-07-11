<div class="container-fluid" style="max-width:1920px">
    <div class="row">
        <?php
            foreach($google_classes as $class){
                
                ?>
                    <div class="<?php echo $column_class; ?> tutor-gc-class-shortcode-container">
                        <a href="<?php echo get_permalink($class->ID); ?>" class="tutor-gc-class-shortcode">
                            <div class="class-header" style="background-image:url(<?php echo $class->post_thumbnail_url; ?>)">

                            </div>
                            <div class="class-content">
                                <div>
                                    <img src="https:<?php echo $class->remote_class_owner->photoUrl; ?>"/>
                                    <span>by</span> <b><?php echo $class->remote_class_owner->name->fullName; ?></b>
                                </div>

                                <h4><?php echo $class->post_title; ?></h4>
                                <small><?php echo $class->remote_class->room_and_section; ?></small>

                                <?php
                                    if($is_class_restricted && !tutor_utils()->is_enrolled($class->ID, get_current_user_id())){
                                        ?>
                                            <div class="restriction-notice">
                                                <div>
                                                    <img src="<?php echo TUTOR_GC()->url.'/assets/images/info.svg'; ?>"/>
                                                </div>
                                                <div>
                                                    <?php echo __('Only logged in students in a specific classrooom can join.', 'tutor-pro'); ?>
                                                </div>
                                            </div>
                                        <?php
                                    }
                                    else {
                                        ?>
                                            <div class="code-in-content">
                                                <span><?php echo __('Code', 'tutor-pro'); ?>: </span>
                                                <span><?php echo $class->remote_class->enrollmentCode; ?></span>
                                                <span class="tutor-icon-copy tutor-gc-copy-text" data-text="<?php echo $class->remote_class->enrollmentCode; ?>"></span>
                                            </div>
                                        <?php
                                    }
                                ?>
                            </div>
                        </a>
                    </div>
                <?php
            }
            
            $has_classes = count($google_classes);
            if(!$has_classes){
                ?>
                    <div class="col-12" style="text-align:center">
                        <?php echo __('No Class', 'tutor-pro'); ?>
                    </div>
                <?php
            }
            
            $page = isset($_GET['class_page']) ? $_GET['class_page'] : 1;
            (!is_numeric($page) || $page<1) ? $page=1 : 0;
    
            ?>
                <div class="col-12" style="text-align:center">
                    <div class="tutor-pagination-wrap">
                        <?php
                            if($page>1){
                                ?>
                                    <a class="previous page-numbers" href="?class_page=<?php echo $page-1; ?>">
                                        « <?php echo __('Previous', 'tutor-pro'); ?>
                                    </a>
                                <?php
                            }

                            if($has_classes){
                                ?>
                                    <a class="next page-numbers" href="?class_page=<?php echo $page+1; ?>">
                                        <?php echo __('Next', 'tutor-pro'); ?> »
                                    </a>
                                <?php
                            }
                        ?>
                    </div>
                </div>
            <?php
        ?>
    </div>
</div>
