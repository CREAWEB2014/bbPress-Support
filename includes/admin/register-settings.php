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
	 * Options page metabox id
	 * @var string
	 */
	private $metabox_id = 'wi_bbp_option_metabox';

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
		add_action( 'cmb2_init', array( $this, 'add_options_page_metabox' ) );
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
	 * Admin page markup. Mostly handled by CMB2
	 * @since  1.0
	 */
	public function admin_page_display() {
		?>
		<div class="wrap cmb2-options-page <?php echo $this->key; ?>">
			<h1><?php echo esc_html( get_admin_page_title() ); ?></h1>
			<?php cmb2_metabox_form( $this->metabox_id, $this->key ); ?>
		</div>
		<?php
	}

	/**
	 * Add the options metabox to the array of metaboxes
	 * @since  1.0
	 */
	function add_options_page_metabox() {
		$cmb = new_cmb2_box( array(
			'id'         => $this->metabox_id,
			'show_names'   => true, // Show field names on the left
			'show_on'    => array(
				// These are important, don't remove
				'key'   => 'options-page',
				'value' => array( $this->key, )
			),
		) );

		//Topic Flags
		$cmb->add_field( array(
		    'name' => __('Topic Title Flags', 'wi_bbp'),
		    'desc' => __( 'When a user types any of the words you flag a message will display and they will not be able to submit a support topic with the word contained in the topic heading input. The message prompts them to perform an alternative action such as how to properly request a refund or refer to documentation.', 'wi_bbp' ),
		    'id'   => 'enable_topic_flags',
		    'type' => 'checkbox'
		) );

		$flag_topic_words = $cmb->add_field( array(
			'id'          => 'flag_topics_words_group',
			'type'        => 'group',
			'options'     => array(
				'group_title'   => __( 'Flag {#}', 'wi_bbp' ), // since version 1.1.4, {#} gets replaced by row number
				'add_button'    => __( 'Add Another Entry', 'wi_bbp' ),
				'remove_button' => __( 'Remove Entry', 'wi_bbp' ),
				'sortable'      => true, // beta
				// 'closed'     => true, // true to have the groups closed by default
			),
		) );

		$cmb->add_group_field( $flag_topic_words, array(
			'name' => 'Flag Word(s)',
			'desc' => 'Enter in one or more words you would like to flag. Multiple words need to be separated by commas. ',
			'id'   => 'flag_words',
			'type' => 'text',
		) );

		$cmb->add_group_field( $flag_topic_words, array(
			'name' => 'Message',
			'desc' => 'This message will display when one of the words above is detected.',
			'id'   => 'flag_message',
			'type' => 'wysiwyg',
		) );


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

