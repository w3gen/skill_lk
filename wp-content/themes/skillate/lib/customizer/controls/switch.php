<?php

if( class_exists( 'WP_Customize_Control' ) && !class_exists( 'SKILLATE_THMC_Switch_Button_Control' ) ):
	/**
	* 
	*/
	class SKILLATE_THMC_Switch_Button_Control extends WP_Customize_Control
	{
		public $type = 'switch';
		public function render_content() {
			$name = '_customize-switch-button-' . $this->id;
			if ( ! empty( $this->label ) ) : ?>
				<span class="customize-control-title"><?php echo esc_html( $this->label ); ?></span>
			<?php endif;
			if ( ! empty( $this->description ) ) : ?>
				<span class="description customize-control-description"><?php echo esc_html($this->description) ; ?></span>
			<?php endif; ?>
			<div class="thmc-switch-button clearfix">
				<input type="radio" id="on-<?php echo $this->id;?>" name="<?php echo esc_attr( $name ); ?>" value="on" <?php $this->link(); checked( $this->value(), true ); ?>>
				<!-- <div class="thmc-switch-ui"></div> -->
				<label for="on-<?php echo $this->id;?>"><?php esc_html_e('on', 'skillate'); ?></label>
				<input type="radio" id="off-<?php echo $this->id;?>" name="<?php echo esc_attr( $name ); ?>" value="off" <?php $this->link(); checked( $this->value(), false ); ?>>
				<label for="off-<?php echo $this->id;?>"><?php esc_html_e('off', 'skillate'); ?></label>
			</div>
			<?php
		}
	}
endif;