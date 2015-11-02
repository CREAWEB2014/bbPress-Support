<?php
/**
 * Admin Functions
 *
 */


// Exit if accessed directly
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}


/**
 * The support forum checkbox will add resolved / not resolved status to all forums.
 *
 * @since        1.0
 *
 * @param        int $forum_id The ID of this forum
 *
 * @return        void
 */
function wi_bbp_extend_forum_attributes_mb( $forum_id ) {

	$support_forum = wi_bbp_is_support_forum( $forum_id );

	if ( $support_forum ) {
		$checked1 = 'checked';
	} else {
		$checked1 = '';
	}
	?>
	<hr />

	<p>
		<strong><?php _e('Support Forum:', 'wi_bbp'); ?></strong>
		<input type="checkbox" name="bbps-support-forum" value="1" <?php echo $checked1; ?>/>
		<br />
	</p>
	<?php
}

add_action( 'bbp_forum_metabox', 'bbps_extend_forum_attributes_mb' );


/**
 * Save the metabox
 *
 * @since        1.0
 *
 * @param        int $forum_id The ID of this forum
 *
 * @return        int $forum_id The ID of this forum
 */
function wi_bbp_forum_attributes_mb_save( $forum_id ) {
	
	// Support options
	if ( ! empty( $_POST['bbps-support-forum'] ) ) {
		update_post_meta( $forum_id, '_bbps_is_support', $_POST['bbps-support-forum'] );
	}

	return $forum_id;
}

add_action( 'bbp_forum_attributes_metabox_save', 'bbps_forum_attributes_mb_save' );


/**
 * Checkbox validation callback
 *
 * @since        1.0
 *
 * @param        array $input The field input
 *
 * @return        array $newoptions The sanitized input
 */
function wi_bbp_validate_checkbox_group( $input ) {
	// Update only the needed options
	foreach ( $input as $key => $value ) {
		$newoptions[ $key ] = $value;
	}

	// Return all options
	return $newoptions;
}


/**
 * General validation callback
 *
 * @since        1.0
 *
 * @param        array $input The field input
 *
 * @return        array $newoptions The sanitized input
 */
function wi_bbp_validate_options( $input ) {
	$options = get_option( '_bbps_reply_count' );

	$i = 1;

	foreach ( $input as $array ) {
		foreach ( $array as $key => $value ) {
			$options[ $i ][ $key ] = $value;
		}
		$i ++;
	}

	return $options;
}
