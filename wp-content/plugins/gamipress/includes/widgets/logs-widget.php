<?php
/**
 * Logs Widget
 *
 * @package     GamiPress\Widgets\Widget\Logs
 * @author      GamiPress <contact@gamipress.com>, Ruben Garcia <rubengcdev@gmail.com>
 * @since       1.0.0
 */
// Exit if accessed directly
if( !defined( 'ABSPATH' ) ) exit;

class GamiPress_Logs_Widget extends GamiPress_Widget {

    /**
     * Shortcode for this widget.
     *
     * @var string
     */
    protected $shortcode = 'gamipress_logs';

    public function __construct() {
        parent::__construct(
            $this->shortcode . '_widget',
            __( 'GamiPress: Logs', 'gamipress' ),
            __( 'Display a list of logs.', 'gamipress' )
        );
    }

    public function get_fields() {
        return GamiPress()->shortcodes[$this->shortcode]->fields;
    }

    public function get_widget( $args, $instance ) {
        // Build shortcode attributes from widget instance
        $atts = gamipress_build_shortcode_atts( $this->shortcode, $instance );

        echo gamipress_do_shortcode( $this->shortcode, $atts );
    }
}