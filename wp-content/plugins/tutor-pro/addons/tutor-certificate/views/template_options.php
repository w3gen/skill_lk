<?php
if ( ! defined( 'ABSPATH' ) )
	exit;

	$selected_template = tutor_utils()->get_option('certificate_template');
?>

<div class="tutor-option-field-row">
    <div class="tutor-option-field-label">
        <label for=""><?php _e('Select Certificate Template', 'tutor-pro'); ?></label>
    </div>
    <div class="tutor-option-field">


        <div class="certificate-templates-fields">
			<?php
			if (tutor_utils()->count($templates)){
				foreach ($templates as $template_key => $template){
				    if ( $template['orientation'] !== 'landscape')
				        continue;
					?>
                    <label class="certificate-template <?php echo ($template_key === $selected_template) ? 'selected-template' : '' ?> ">
                        <img src="<?php echo $template['preview_src']; ?>" />
                        <input type="radio" name="tutor_option[certificate_template]" value="<?php echo $template_key; ?>" <?php checked($template_key,
                            $selected_template) ?> style="display: none;" >
                    </label>
					<?php
				}
			}
			?>
        </div>


        <div class="certificate-templates-fields">
		    <?php
		    if (tutor_utils()->count($templates)){
			    foreach ($templates as $template_key => $template){
				    if ( $template['orientation'] !== 'portrait')
					    continue;
				    ?>
                    <label class="certificate-template <?php echo ($template_key === $selected_template) ? 'selected-template' : '' ?> ">
                        <img src="<?php echo $template['preview_src']; ?>" />
                        <input type="radio" name="tutor_option[certificate_template]" value="<?php echo $template_key; ?>" <?php checked($template_key,
						    $selected_template) ?> style="display: none;" >
                    </label>
				    <?php
			    }
		    }
		    ?>
        </div>


    </div>
</div>