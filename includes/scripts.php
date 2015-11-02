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
 * @return void
 */
function bbps_load_scripts() {

	//Only enqueue on bbPress pages
	if ( ! is_bbpress() ) {
		return false;
	}

	wp_register_script( 'bbpress-support-js', BB_SUPPORT_URL . 'assets/js/bbps-scripts.js', array( 'jquery' ) );
	wp_enqueue_script( 'bbpress-support-js' );


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


}

add_action( 'admin_enqueue_scripts', 'give_load_admin_scripts', 100 );