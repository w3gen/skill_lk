<?php
namespace ElementorPro\Modules\GlobalWidget\Documents;

use Elementor\Modules\Library\Documents\Library_Document;
use Elementor\User;
use ElementorPro\Modules\GlobalWidget\Module;

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}

class Widget extends Library_Document {

	public static function get_properties() {
		$properties = parent::get_properties();

		$properties['admin_tab_group'] = 'library';
		$properties['show_in_library'] = false;
		$properties['is_editable'] = false;

		return $properties;
	}

	public function get_name() {
		return 'widget';
	}

	public static function get_title() {
		return __( 'Global Widget', 'elementor-pro' );
	}

	public function is_editable_by_current_user() {
		return User::is_current_user_can_edit( $this->get_main_id() );
	}

	public function import( array $data ) {
		parent::import( $data );

		$this->update_main_meta( Module::WIDGET_TYPE_META_KEY, $data['content'][0]['widgetType'] );
	}
}
