<?php

/**
 * CMB2 Plugin Options
 * @since 1.0
 */
class WordImpress_bbSupport_Settings {

	/**
	 * Option key, and option page slug
	 * @var string
	 */
	private $key = 'wi_bbp_options';

	/**
	 * Options Page title
	 * @var string
	 */
	protected $title = '';
	/**
	 * Options Page hook
	 * @var string
	 */
	protected $options_page = '';

	/**
	 * Constructor
	 * @since 1.0
	 */
	public function __construct() {
		// Set our title
		$this->title = __( 'Support', 'wi_bbp' );

	}

	/**
	 * Initiate our hooks
	 * @since 1.0
	 */
	public function hooks() {
		add_action( 'admin_init', array( $this, 'init' ) );
		add_action( 'admin_menu', array( $this, 'add_options_page' ) );

		//Custom CMB2 Settings Fields
		add_action( 'cmb2_render_bbp_title', 'bbp_title_callback', 10, 5 );

	}

	/**
	 * Register our setting to WP
	 * @since  1.0
	 */
	public function init() {
		register_setting( $this->key, $this->key );
	}

	/**
	 * Add menu options page
	 * @since 1.0
	 */
	public function add_options_page() {
		$this->options_page = add_menu_page( $this->title, $this->title, 'manage_options', $this->key, array(
			$this,
			'admin_page_display'
		), 'dashicons-sos' );

		// Include CMB CSS in the head to avoid FOUC
		add_action( "admin_print_styles-{$this->options_page}", array( 'CMB2_hookup', 'enqueue_cmb_css' ) );
	}


	/**
	 * Retrieve settings tabs
	 *
	 * @since 1.0
	 * @return array $tabs
	 */
	public function get_settings_tabs() {

		$settings = $this->plugin_settings( null );

		$tabs            = array();
		$tabs['general'] = __( 'General', 'wi_bbp' );
		$tabs['topics']  = __( 'Topics', 'wi_bbp' );

		return apply_filters( 'bbps_settings_tabs', $tabs );
	}


	/**
	 * Admin page markup. Mostly handled by CMB2
	 * @since  1.0
	 */
	public function admin_page_display() {
		$active_tab = isset( $_GET['tab'] ) && array_key_exists( $_GET['tab'], $this->get_settings_tabs() ) ? $_GET['tab'] : 'general';

		?>

		<div class="wrap give_settings_page cmb2_options_page <?php echo $this->key; ?>">
			<h1 class="nav-tab-wrapper">
				<?php
				foreach ( $this->get_settings_tabs() as $tab_id => $tab_name ) {

					$tab_url = esc_url( add_query_arg( array(
						'settings-updated' => false,
						'tab'              => $tab_id
					) ) );

					$active = $active_tab == $tab_id ? ' nav-tab-active' : '';

					echo '<a href="' . esc_url( $tab_url ) . '" title="' . esc_attr( $tab_name ) . '" class="nav-tab' . $active . '">';
					echo esc_html( $tab_name );

					echo '</a>';
				}
				?>
			</h1>

			<?php cmb2_metabox_form( $this->plugin_settings( $active_tab ), $this->key ); ?>

		</div><!-- .wrap -->

		<?php
	}


	/**
	 * Define General Settings Metabox and field configurations.
	 *
	 * Filters are provided for each settings section to allow add-ons and other plugins to add their own settings
	 *
	 * @param $active_tab active tab settings; null returns full array
	 *
	 * @return array
	 */
	function plugin_settings( $active_tab ) {

		$plugin_settings['general'] = new_cmb2_box( array(
			'id'         => 'general',
			'hookup'     => false,
			'cmb_styles' => false,
			'show_on'    => array(
				// These are important, don't remove
				'key'   => 'options-page',
				'value' => array( $this->key, )
			),
		) );

		//		$plugin_settings['general']->add_field( array(
		//			'name'    => __( 'Test Text', 'myprefix' ),
		//			'desc'    => __( 'field description (optional)', 'myprefix' ),
		//			'id'      => 'test_text',
		//			'type'    => 'text',
		//			'default' => 'Default Text',
		//		) );

		//Topic Flags
		$plugin_settings['topics'] = new_cmb2_box( array(
			'id'         => 'topics',
			'hookup'     => false,
			'cmb_styles' => false,
			'show_on'    => array(
				// These are important, don't remove
				'key'   => 'options-page',
				'value' => array( $this->key, )
			),
		) );


		$plugin_settings['topics']->add_field( array(
			'name' => __( 'Topic Options', 'wi_bbp' ),
			'id'   => 'bbp_title',
			'type' => 'bbp_title'
		) );

		$support_status = $plugin_settings['topics']->add_field( array(
			'id'      => 'support_status',
			'type'    => 'group',
			'options' => array(
				'group_title'   => __( 'Status {#}', 'wi_bbp' ), // since version 1.1.4, {#} gets replaced by row number
				'add_button'    => __( 'Add Status', 'wi_bbp' ),
				'remove_button' => __( 'Remove Status', 'wi_bbp' ),
				'sortable'      => true, // beta
				// 'closed'     => true, // true to have the groups closed by default
			),
		) );


		$plugin_settings['topics']->add_field( array(
			'name' => __( 'Topic Title Flags', 'wi_bbp' ),
			'desc' => __( 'When a user types any of the words you flag a message will display and they will not be able to submit a support topic with the word contained in the topic heading input. The message prompts them to perform an alternative action such as how to properly request a refund or refer to documentation.', 'wi_bbp' ),
			'id'   => 'enable_topic_flags',
			'type' => 'checkbox'
		) );

		$flag_topic_words = $plugin_settings['topics']->add_field( array(
			'id'      => 'flag_topics_words_group',
			'type'    => 'group',
			'options' => array(
				'group_title'   => __( 'Flag {#}', 'wi_bbp' ), // since version 1.1.4, {#} gets replaced by row number
				'add_button'    => __( 'Add Flag', 'wi_bbp' ),
				'remove_button' => __( 'Remove Flag', 'wi_bbp' ),
				'sortable'      => true, // beta
				// 'closed'     => true, // true to have the groups closed by default
			),
		) );

		$plugin_settings['topics']->add_group_field( $flag_topic_words, array(
			'name' => 'Flag Word(s)',
			'desc' => 'Enter in one or more words you would like to flag. Multiple words need to be separated by commas. ',
			'id'   => 'flag_words',
			'type' => 'text',
		) );

		$plugin_settings['topics']->add_group_field( $flag_topic_words, array(
			'name' => 'Message',
			'desc' => __( 'This message will display when one of the words above is detected.' ),
			'id'   => 'flag_message',
			'type' => 'wysiwyg',
		) );


		// Return all settings array if necessary
		if ( $active_tab === null || ! isset( $plugin_settings[ $active_tab ] ) ) {
			return apply_filters( 'wi_bbp_registered_settings', $plugin_settings );
		}

		// Add other tabs and settings fields as needed
		return apply_filters( 'wi_bbp_registered_settings', $plugin_settings[ $active_tab ] );

	}

	/**
	 * Public getter method for retrieving protected/private variables
	 * @since  1.0
	 *
	 * @param  string $field Field to retrieve
	 *
	 * @return mixed          Field value or exception is thrown
	 */
	public function __get( $field ) {
		// Allowed fields to retrieve
		if ( in_array( $field, array( 'key', 'metabox_id', 'title', 'options_page' ), true ) ) {
			return $this->{$field};
		}
		throw new Exception( 'Invalid property: ' . $field );
	}
} //End Class


/**
 * Modify CMB2 Default Form Output
 *
 * @param string @args
 *
 * @since 1.0
 */

add_filter( 'cmb2_get_metabox_form_format', 'wi_bbp_modify_cmb2_form_output', 10, 3 );

function wi_bbp_modify_cmb2_form_output( $form_format, $object_id, $cmb ) {

	//only modify the give settings form
	if ( 'wi_bbp_options' == $object_id ) {

		return '<form class="cmb-form" method="post" id="%1$s" enctype="multipart/form-data" encoding="multipart/form-data"><input type="hidden" name="object_id" value="%2$s">%3$s<div class="wi-bbp-submit-wrap"><input type="submit" name="submit-cmb" value="' . __( 'Save Settings', 'wi_bbp' ) . '" class="button-primary"></div></form>';
	}

	return $form_format;

}


/**
 * Give Title
 *
 * Renders custom section titles output; Really only an <hr> because CMB2's output is a bit funky
 *
 * @since 1.0
 *
 * @param       $field_object , $escaped_value, $object_id, $object_type, $field_type_object
 *
 * @return void
 */
function bbp_title_callback( $field_object, $escaped_value, $object_id, $object_type, $field_type_object ) {

	$id                = $field_type_object->field->args['id'];
	$title             = $field_type_object->field->args['name'];
	$field_description = $field_type_object->field->args['desc'];

	echo '<hr>';

}

/**
 * Helper function to get/return the WordImpress_bbSupport_Settings object
 * @since  1.0
 * @return WordImpress_bbSupport_Settings object
 */
function wi_bbp_admin() {
	static $object = null;
	if ( is_null( $object ) ) {
		$object = new WordImpress_bbSupport_Settings();
		$object->hooks();
	}

	return $object;
}

/**
 * Wrapper function around cmb2_get_option
 * @since  1.0
 *
 * @param  string $key Options array key
 *
 * @return mixed        Option value
 */
function wi_bbp_get_option( $key = '' ) {
	return cmb2_get_option( wi_bbp_admin()->key, $key );
}


/**
 * Get the CMB2 bootstrap!
 *
 * Super important!
 */
if ( file_exists( BB_SUPPORT_DIR . 'includes/libraries/cmb2/init.php' ) ) {
	require_once BB_SUPPORT_DIR . 'includes/libraries/cmb2/init.php';
} elseif ( file_exists( BB_SUPPORT_DIR . 'includes/libraries/CMB2/init.php' ) ) {
	require_once BB_SUPPORT_DIR . 'includes/libraries/CMB2/init.php';
}

//Show Settings
wi_bbp_admin();

