<?php
/**
 * Plugin Name:           GamiPress - Tutor LMS integration
 * Plugin URI:            https://wordpress.org/plugins/gamipress-tutor-integration/
 * Description:           Connect GamiPress with Tutor LMS.
 * Version:               1.0.4
 * Author:                GamiPress
 * Author URI:            https://gamipress.com/
 * Text Domain:           gamipress-tutor-integration
 * Domain Path:           /languages/
 * Requires at least:     4.4
 * Tested up to:          5.7
 * License:               GNU AGPL v3.0 (http://www.gnu.org/licenses/agpl.txt)
 *
 * @package               GamiPress\Tutor
 * @author                GamiPress
 * @copyright             Copyright (c) GamiPress
 */

final class GamiPress_Tutor {

    /**
     * @var         GamiPress_Tutor $instance The one true GamiPress_Tutor
     * @since       1.0.0
     */
    private static $instance;

    /**
     * Get active instance
     *
     * @access      public
     * @since       1.0.0
     * @return      GamiPress_Tutor self::$instance The one true GamiPress_Tutor
     */
    public static function instance() {

        if( !self::$instance ) {
            self::$instance = new GamiPress_Tutor();
            self::$instance->constants();
            self::$instance->includes();
            self::$instance->hooks();
            self::$instance->load_textdomain();
        }

        return self::$instance;
    }

    /**
     * Setup plugin constants
     *
     * @access      private
     * @since       1.0.0
     * @return      void
     */
    private function constants() {
        // Plugin version
        define( 'GAMIPRESS_TUTOR_VER', '1.0.4' );

        // Plugin path
        define( 'GAMIPRESS_TUTOR_DIR', plugin_dir_path( __FILE__ ) );

        // Plugin URL
        define( 'GAMIPRESS_TUTOR_URL', plugin_dir_url( __FILE__ ) );
    }

    /**
     * Include plugin files
     *
     * @access      private
     * @since       1.0.0
     * @return      void
     */
    private function includes() {

        if( $this->meets_requirements() ) {

            require_once GAMIPRESS_TUTOR_DIR . 'includes/admin.php';
            require_once GAMIPRESS_TUTOR_DIR . 'includes/listeners.php';
            require_once GAMIPRESS_TUTOR_DIR . 'includes/triggers.php';

        }
    }

    /**
     * Setup plugin hooks
     *
     * @access      private
     * @since       1.0.0
     * @return      void
     */
    private function hooks() {
        // Setup our activation and deactivation hooks
        register_activation_hook( __FILE__, array( $this, 'activate' ) );
        register_deactivation_hook( __FILE__, array( $this, 'deactivate' ) );

        add_action( 'admin_notices', array( $this, 'admin_notices' ) );
    }

    /**
     * Activation hook for the plugin.
     *
     * @since  1.0.0
     */
    function activate() {

        if( $this->meets_requirements() ) {

        }

    }

    /**
     * Deactivation hook for the plugin.
     *
     * @since  1.0.0
     */
    function deactivate() {

    }

    /**
     * Plugin admin notices.
     *
     * @since  1.0.0
     */
    public function admin_notices() {

        if ( ! $this->meets_requirements() && ! defined( 'GAMIPRESS_ADMIN_NOTICES' ) ) : ?>

            <div id="message" class="notice notice-error is-dismissible">
                <p>
                    <?php printf(
                        __( 'GamiPress - Tutor LMS integration requires %s and %s in order to work. Please install and activate them.', 'gamipress-tutor-integration' ),
                        '<a href="https://wordpress.org/plugins/gamipress/" target="_blank">GamiPress</a>',
                        '<a href="https://wordpress.org/plugins/tutor/" target="_blank">Tutor LMS</a>'
                    ); ?>
                </p>
            </div>

            <?php define( 'GAMIPRESS_ADMIN_NOTICES', true ); ?>

        <?php endif;

    }

    /**
     * Check if there are all plugin requirements
     *
     * @since  1.0.0
     *
     * @return bool True if installation meets all requirements
     */
    private function meets_requirements() {

        if ( ! class_exists( 'GamiPress' ) )
            return false;

        // Requirements on multisite install
        if( is_multisite() && gamipress_is_network_wide_active() && is_main_site() ) {

            // On main site, need to check if integrated plugin is installed on any sub site to load all configuration files
            if( gamipress_is_plugin_active_on_network( 'tutor/tutor.php' ) ) {
                return true;
            }

        }

        if ( ! function_exists( 'tutor' ) )
            return false;

        return true;

    }

    /**
     * Internationalization
     *
     * @access      public
     * @since       1.0.0
     * @return      void
     */
    public function load_textdomain() {

        // Set filter for language directory
        $lang_dir = GAMIPRESS_TUTOR_DIR . '/languages/';
        $lang_dir = apply_filters( 'gamipress_tutor_languages_directory', $lang_dir );

        // Traditional WordPress plugin locale filter
        $locale = apply_filters( 'plugin_locale', get_locale(), 'gamipress-tutor-integration' );
        $mofile = sprintf( '%1$s-%2$s.mo', 'gamipress-tutor-integration', $locale );

        // Setup paths to current locale file
        $mofile_local   = $lang_dir . $mofile;
        $mofile_global  = WP_LANG_DIR . '/gamipress-tutor-integration/' . $mofile;

        if( file_exists( $mofile_global ) ) {
            // Look in global /wp-content/languages/gamipress-tutor-integration/ folder
            load_textdomain( 'gamipress-tutor-integration', $mofile_global );
        } elseif( file_exists( $mofile_local ) ) {
            // Look in local /wp-content/plugins/gamipress-tutor-integration/languages/ folder
            load_textdomain( 'gamipress-tutor-integration', $mofile_local );
        } else {
            // Load the default language files
            load_plugin_textdomain( 'gamipress-tutor-integration', false, $lang_dir );
        }

    }

}

/**
 * The main function responsible for returning the one true GamiPress_Tutor instance to functions everywhere
 *
 * @since       1.0.0
 * @return      \GamiPress_Tutor The one true GamiPress_Tutor
 */
function GamiPress_Tutor() {
    return GamiPress_Tutor::instance();
}
add_action( 'plugins_loaded', 'GamiPress_Tutor' );
