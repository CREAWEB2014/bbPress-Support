<?php

/**
 *  wordimpress.dev - widget-user-status.php
 *
 * @description:
 * @copyright  : http://opensource.org/licenses/gpl-2.0.php GNU Public License
 * @since      : 1.0.0
 * @created    : 9/30/2015
 */
class WordImpress_bbSupport_User_Status extends WP_Widget {

	/**
	 * Register widget with WordPress.
	 */
	function __construct() {
		parent::__construct(
			'wi_bbp_user_status', // Base ID
			__( '(bbPress Support) User Support Status', 'wi_bbp' ), // Name
			array( 'description' => __( 'Displays a user\'s EDD purchases and whether their license is active. This should be placed in your forum sidebar and will only display for admins.', 'wi_bbp' ), ) // Args
		);
	}

	/**
	 * Front-end display of widget.
	 *
	 * @see WP_Widget::widget()
	 *
	 * @param array $args     Widget arguments.
	 * @param array $instance Saved values from database.
	 */
	public function widget( $args, $instance ) {

		//No EDD? Bail
		if ( ! class_exists( 'Easy_Digital_Downloads' ) ) {
			return false;
		}

		//Not EDD admin? Bail
		if ( ! current_user_can( 'view_shop_sensitive_data' ) ) {
			return false;
		}

		//Handle before_widget args
		echo $args['before_widget'];
		if ( ! empty( $instance['title'] ) ) {
			echo $args['before_title'] . apply_filters( 'widget_title', $instance['title'] ) . $args['after_title'];
		}

		$user_id   = get_the_author_meta( 'ID' );
		$user_data = get_userdata( $user_id );

		?>
		<div class="box">

			<?php do_action( 'wi_bbp_sidebar' ); ?>

			<h3><?php echo get_the_author_meta( 'first_name' ) . '  ' . get_the_author_meta( 'last_name' ); ?></h3>

			<p class="bbp-user-forum-role"><?php printf( 'Forum Role: %s', bbp_get_user_display_role( $user_id ) ); ?></p>

			<p class="bbp-user-topic-count"><?php printf( 'Topics Started: %s', bbp_get_user_topic_count_raw( $user_id ) ); ?></p>

			<p class="bbp-user-reply-count"><?php printf( 'Replies Created: %s', bbp_get_user_reply_count_raw( $user_id ) ); ?></p>


			<div class="wi_users_purchases">
				<h3><?php _e( 'User\'s Purchases:', 'wi_bbp' ); ?></h3>
				<?php
				$purchases = edd_get_users_purchases( $user_data->user_email, 100, false, 'any' );
				if ( $purchases ) :
					echo '<ul>';
					foreach ( $purchases as $purchase ) {

						echo '<li>';

						echo '<strong><a href="' . admin_url( 'edit.php?post_type=download&page=give-payment-history&view=view-order-details&id=' . $purchase->ID ) . '">#' . $purchase->ID . ' - ' . edd_get_payment_status( $purchase, true ) . '</a></strong><br/>';

						$downloads = edd_get_payment_meta_downloads( $purchase->ID );
						foreach ( $downloads as $download ) {
							echo get_the_title( $download['id'] ) . ' - ' . date( 'F j, Y', strtotime( $purchase->post_date ) ) . '<br/>';
						}

						//Check license key
						if ( function_exists( 'edd_software_licensing' ) ) {
							$licenses = edd_software_licensing()->get_licenses_of_purchase( $purchase->ID );
							if ( $licenses ) {
								echo '<strong>' . __( 'Licenses:', 'edd' ) . '</strong><br/>';
								foreach ( $licenses as $license ) {
									$key = edd_software_licensing()->get_license_key( $license->ID );

									$download_id = edd_software_licensing()->get_download_by_license( $key );

									$title = get_the_title( $download_id );

									//output license URL
									echo $title . ' - <a href="' . admin_url( 'edit.php?post_type=download&page=give-licenses&s=' . $key ) . '">' . $key . '</a>';
									echo ' - ' . edd_software_licensing()->get_license_status( $license->ID );
									echo '<br/>';
								}
							}
							echo '<hr/>';
						}
						echo '</li>';
					}
					echo '</ul>';
				else :
					echo '<p>' . __( 'This user has never purchased anything.', 'wi_bbp' ) . '</p>';
				endif; ?>
			</div>
		</div>
		<?php
		//After widget args
		echo $args['after_widget'];

		return false;
	}

	/**
	 * Back-end widget form.
	 *
	 * @see WP_Widget::form()
	 *
	 * @param array $instance Previously saved values from database.
	 *
	 * @return void
	 */
	public function form( $instance ) {
		$title = ! empty( $instance['title'] ) ? $instance['title'] : __( 'New title', 'wi_bbp' );
		?>
		<p>
			<label for="<?php echo $this->get_field_id( 'title' ); ?>"><?php _e( 'Title:' ); ?></label>
			<input class="widefat" id="<?php echo $this->get_field_id( 'title' ); ?>" name="<?php echo $this->get_field_name( 'title' ); ?>" type="text" value="<?php echo esc_attr( $title ); ?>">
		</p>
		<?php
	}

	/**
	 * Sanitize widget form values as they are saved.
	 *
	 * @see WP_Widget::update()
	 *
	 * @param array $new_instance Values just sent to be saved.
	 * @param array $old_instance Previously saved values from database.
	 *
	 * @return array Updated safe values to be saved.
	 */
	public function update( $new_instance, $old_instance ) {
		$instance          = array();
		$instance['title'] = ( ! empty( $new_instance['title'] ) ) ? strip_tags( $new_instance['title'] ) : '';

		return $instance;
	}

}