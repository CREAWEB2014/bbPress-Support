<?php
/**
 * Scripts
 *
 * @package     bbPress Support
 * @subpackage  Functions
 * @copyright   Copyright (c) 2015, WordImpress
 * @license     http://opensource.org/licenses/gpl-2.0.php GNU Public License
 * @since       1.0
 */

// Exit if accessed directly
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Load Scripts
 *
 * Enqueues the required scripts.
 *
 * @since 1.0
 * @global $give_options
 * @global $post
 * @return mixed|void
 */
function bbps_load_scripts() {

	$localize_bbps = array(
		'enable_topic_flags' => wi_bbp_get_option( 'enable_topic_flags' ),
		'topic_title_flags'  => wi_bbp_get_option( 'flag_topics_words_group' )
	);

	wp_register_script( 'bbpress-support-js', BB_SUPPORT_URL . 'assets/js/bbps-scripts.js', array( 'jquery' ), null, true );
	wp_enqueue_script( 'bbpress-support-js' );

	wp_localize_script( 'bbpress-support-js', 'bbps_vars', $localize_bbps );

}

add_action( 'wp_enqueue_scripts', 'bbps_load_scripts' );

/**
 * Register Styles
 *
 * Checks the styles option and hooks the required filter.
 *
 * @since 1.0
 * @return void
 */
function give_register_styles() {


}

//add_action( 'wp_enqueue_scripts', 'give_register_styles' );

/**
 * Load Admin Scripts
 *
 * Enqueues the required admin scripts.
 *
 * @since 1.0
 * @global       $post
 *
 * @param string $hook Page hook
 *
 * @return void
 */
function give_load_admin_scripts( $hook ) {

	//Settings scripts
	if($hook == 'toplevel_page_wi_bbp_options'){
		wp_register_style( 'bbp-support-settings-css', BB_SUPPORT_URL . 'assets/css/bbps-admin.css' );
		wp_enqueue_style( 'bbp-support-settings-css' );
	}


}

add_action( 'admin_enqueue_scripts', 'give_load_admin_scripts', 100 );