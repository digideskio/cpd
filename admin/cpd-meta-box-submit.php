<?php

// Exit if accessed directly
defined( 'ABSPATH' ) || exit;

if( !class_exists( 'CPD_Meta_Box_Submit' ) ) {

/**
 * Admin Scripts
 *
 * Load Admin Scripts
 *
 * @package    CPD
 * @subpackage CPD/admin
 * @author     Make Do <hello@makedo.in>
 */
class CPD_Meta_Box_Submit {

	private static $instance = null;
	private $text_domain;
	private $name;
	private $metabox_id;
	private $key_prefix;
	private $id_prefix;
	private $id;
	private $context;
	private $priority;
	private $args;

	/**
	 * Creates or returns an instance of this class.
	 */
	public static function get_instance() {
		/**
		 * If an instance hasn't been created and set to $instance create an instance 
		 * and set it to $instance.
		 */
		if ( null == self::$instance ) {
			self::$instance = new self;
		}
		return self::$instance;
	}

	/**
	 * Initialize the class and set its properties.
	 */
	public function __construct() {
		
		$this->args 							= 	array(
														'id' 					=> 'submit',
														'id_prefix' 			=> 'cpd_',
														'name' 					=> '<span class="cpd-dashboard-widget-title dashicons-before dashicons-yes"></span> Submit',
														'context' 				=> 'side',
														'priority'				=> 'high',
														'metabox_id' 			=> '',
														'key_prefix' 			=> '',
														'metabox_args'			=> array()
													);

		$this->id 								= 	$this->args[ 'id'			];
		$this->id_prefix						= 	$this->args[ 'id_prefix'	];
		$this->name 							= 	$this->args[ 'name'			];
		$this->context 							= 	$this->args[ 'context'		];
		$this->priority 						=	$this->args[ 'priority'		];

		if( empty( $this->args['metabox_id'] ) )
		{
			$this->args['metabox_id'] 			=	$this->id_prefix . 'meta_box_' . $this->id;
		}

		if( empty( $this->args['key_prefix'] ) )
		{
			$this->args['key_prefix'] 			=	'_' . $this->id_prefix;
		}

		$this->metabox_id						=	$this->args['metabox_id'];
		$this->key_prefix						=	$this->args['key_prefix'];


		$metabox_args							=	array(
														'id' 				=> 	$this->metabox_id,
														'title' 			=> 	$this->name,
														'pages' 			=> 	array('assessment'),
														'context' 			=> 	$this->context,
														'priority' 			=> 	$this->priority,
														'show_on'			=>	array(),
														'hide_on'			=>	array(),
														'fields' 			=> 	array()
													);

		$this->args['metabox_args'] 			= 	array_merge( $metabox_args, $this->args[ 'metabox_args'] );

		$metabox_args	= 	array(
								'fields' 	=> 	array(
													array( 
														'id'			=> 	$this->key_prefix . 'submit', 
														'name' 			=> 	__( 'Submit to Supervisor', $this->text_domain ),
														'desc'			=>	'Submit this assessment to a supervisor for review. <br/><br/><strong>Once submitted you will not be able to make edits to this assessment, unless a supervisor marks it as editable</strong>.</a>',
														'type'			=> 	'checkbox',
														'cols'			=> 	12,
														'readonly'		=>	FALSE
													),
												)
							);
		
		$this->args['metabox_args'] 			= 	array_merge( $this->args[ 'metabox_args'], $metabox_args );
	}

	/**
	 * Set the text domain
	 *
	 * @param      string    $text_domain       The text domain of the plugin.
	 */
	public function set_text_domain( $text_domain ) { 
		$this->text_domain = $text_domain;
	}

	/**
	 * Creates the custom meta boxes for the associated post edit screens.
	 *
	 * @param 	array 	$meta_boxes 	The existing metaboxes array
	 * @return	array 	$meta_boxes 	The modified metaboxes array
	 */
	function register_metabox( $meta_boxes ) {

		$post_id = -1;
		$submitted = FALSE;
		if( isset( $_GET['post'] ) ) {
			$post_id   = $_GET['post'];
			$submitted = get_post_meta( $post_id, '_cpd_submit', TRUE );
		}



		$user_id 			= 	get_current_user_id();
		$user_type 			= 	get_user_meta( $user_id, 'cpd_role', TRUE );
		
		if( $user_type == 'participant' )
		{
			$meta_boxes[] 							= 	$this->args['metabox_args'];
		}
		else if( $user_type != 'participant' && $submitted ) {

			$this->args['metabox_args']['title']     =   '<span class="cpd-dashboard-widget-title dashicons-before dashicons-yes"></span> Submited';
			$this->args['metabox_args']['fields'] 	= 	array(
															array( 
																'id'			=> 	$this->key_prefix . 'submit', 
																'name' 			=> 	__( 'Mark as not submitted', $this->text_domain ),
																'desc'			=>	'Uncheck this box to take this assessment out of \'submitted\' status.<br/><br/>Only do this if the participant has made an error and needs to ammend the submission.',
																'type'			=> 	'checkbox',
																'cols'			=> 	12,
																'readonly'		=>	FALSE
															),
														);
			$meta_boxes[] 							= 	$this->args['metabox_args'];
		}
		
		
		return $meta_boxes;
	}

	public function change_post_status( $post_id, $post ) {
		
		if( $post == NULL ) {
			return;
		}

		// If it is just a revision don't worry about it
		if ( wp_is_post_revision( $post_id ) ) {
			return $post_id;
		}

		// Check it's not an auto save routine
		if ( defined('DOING_AUTOSAVE') && DOING_AUTOSAVE ) {
			return $post_id;
		}

		// Check that the current user has permission to edit the post
		if ( !current_user_can( 'edit_post', $post_id ) ) {
			return $post_id;
		}

		remove_action( 'save_post',  array( $this, 'change_post_status' ), 99, 2 );

		// Send email to supervisors here.

		add_action( 'save_post', array( $this, 'change_post_status' ), 99, 2 );
	}

}
}