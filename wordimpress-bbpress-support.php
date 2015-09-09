<?php
/**
 * Plugin Name:       WordImpress bbPress Support
 * Plugin URI:        https://github.com/WordImpress/give-bbpress-support/
 * Description:       Extends bbPress to provide a Support dashboard interface
 * Version:           1.0
 * Author:            WordImpress
 * Author URI:        https://wordimpress.com/
 */


// Exit if accessed directly
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}


if ( ! class_exists( 'WordImpress_bbSupport' ) ) {

	/**
	 * Main GIVE_BBP class
	 *
	 * @since        1.0
	 */
	class WordImpress_bbSupport {

		/**
		 * @var            WordImpress_bbSupport $instance The one true WordImpress_bbSupport
		 * @since        1.0
		 */
		private static $instance;

		/**
		 * Settings Object
		 *
		 * @var object
		 * @since 1.0
		 */
		public $settings;

		/**
		 * Get active instance
		 *
		 * @access        public
		 * @since         1.0
		 * @return        object self::$instance The one true WordImpress_bbSupport
		 */
		public static function instance() {

			if ( ! self::$instance ) {
				self::$instance = new WordImpress_bbSupport();
				self::$instance->setup_constants();
				self::$instance->hooks();

				self::$instance->includes();
				self::$instance->settings = new WordImpress_bbSupport_Settings();

			}

			return self::$instance;
		}


		/**
		 * Setup plugin constants
		 *
		 * @access        private
		 * @since         1.0
		 * @return        void
		 */
		private function setup_constants() {
			// Plugin path
			if ( ! defined( 'BB_SUPPORT_DIR' ) ) {
				define( 'BB_SUPPORT_DIR', plugin_dir_path( __FILE__ ) );
			}
			// Plugin URL
			if ( ! defined( 'BB_SUPPORT_URL' ) ) {
				define( 'BB_SUPPORT_URL', plugin_dir_url( __FILE__ ) );
			}
		}


		/**
		 * Include necessary files
		 *
		 * @access        private
		 * @since         1.0
		 * @return        void
		 */
		private function includes() {
			require_once BB_SUPPORT_DIR . 'includes/actions.php';
			require_once BB_SUPPORT_DIR . 'includes/functions.php';
			require_once BB_SUPPORT_DIR . 'includes/shortcodes.php';
			require_once BB_SUPPORT_DIR . 'includes/support-functions.php';

			if ( is_admin() ) {
				require_once BB_SUPPORT_DIR . 'includes/admin/functions.php';
				require_once BB_SUPPORT_DIR . 'includes/admin/bbps-admin.php';
				require_once BB_SUPPORT_DIR . 'includes/admin/register-settings.php';
			}
		}


		/**
		 * Run action and filter hooks
		 *
		 * @access        private
		 * @since         1.0
		 * @return        void
		 */
		private function hooks() {
			// Initial activation
			register_activation_hook( __FILE__, array( $this, 'activate' ) );

			// Tweak subforum paging
			add_filter( 'bbp_after_forum_get_subforums_parse_args', array( $this, 'subforum_args' ) );
			add_filter( 'bbp_topic_admin_links', array( $this, 'admin_links' ), 10, 2 );
			add_filter( 'bbp_reply_admin_links', array( $this, 'admin_links' ), 10, 2 );
		}


		/**
		 * Plugin activation
		 *
		 * @access        public
		 * @since         1.0
		 * @return        void
		 */
		function activate() {
			do_action( 'wi_bbp_activation' );
		}


		/**
		 * Tweak args for subforums
		 *
		 * @access        public
		 * @since         1.0
		 *
		 * @param        array $args The current arguments
		 *
		 * @return        array $args The modified arguments
		 */
		function subforum_args( $args ) {
			$args['nopaging'] = true;

			return $args;
		}

		/**
		 * Remove unused admin links
		 *
		 * @access        public
		 * @since         1.0
		 *
		 * @param        array $links The default links
		 *
		 * @return        array $id The Topic or Reply ID
		 */
		function admin_links( $links, $id ) {

			if ( isset( $links['stick'] ) ) {

				unset( $links['stick'] );

			}

			unset( $links['reply'] );

			return $links;
		}
	}
}

return WordImpress_bbSupport::instance();
