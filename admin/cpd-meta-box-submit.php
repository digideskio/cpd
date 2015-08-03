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
														'name' 					=> __('Submit', $this->text_domain),
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
														'desc'			=>	__('Submit this assessment to a supervisor for review.', $this->text_domain) . ' <br/><br/><strong>'.__('Once submitted you will not be able to make edits to this assessment, unless a supervisor marks it as editable', $this->text_domain) .'</strong>.</a>',
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

		$post_id   = -1;
		$submitted = FALSE;
		$complete  = FALSE;
		if( isset( $_GET['post'] ) ) {
			$post_id   = $_GET['post'];
			$submitted = get_post_meta( $post_id, '_cpd_submit', TRUE );
			$complete  = get_post_meta( $post_id, '_cpd_complete', TRUE );
		}



		$user_id 			= 	get_current_user_id();
		$user_type 			= 	get_user_meta( $user_id, 'cpd_role', TRUE );
		
		if( $user_type == 'participant' )
		{
			$meta_boxes[] 							= 	$this->args['metabox_args'];
		}
		else if( $user_type != 'participant' ) {

			if( !$submitted && !$complete ) {
				$this->args['metabox_args']['title']     =   __('Status', $this->text_domain);
			} else if( $submitted && !$complete ) {
				$this->args['metabox_args']['title']     =   __('Submited', $this->text_domain);
			} else if( $complete ) {
				$this->args['metabox_args']['title']     =   __('Completed', $this->text_domain);
			}
			
			$this->args['metabox_args']['fields'] 	= 	array(
															array( 
																'id'			=> 	$this->key_prefix . 'submit', 
																'name' 			=> 	$submitted ? __( 'Assessment submitted', $this->text_domain ) : __( 'Assessment not submitted', $this->text_domain ),
																'desc'			=>	$submitted ? __('Uncheck the \'Assessment submitted\' box to take this assessment out of \'submitted\' status.', $this->text_domain) .'<br/><br/>'.__('Only do this if the participant has made an error and needs to ammend the submission.', $this->text_domain) : __('This assessment has not be submitted by the participant.', $this->text_domain),
																'type'			=> 	$submitted ? 'checkbox' : 'render',
																'cols'			=> 	12,
															),
															array( 
																'id'			=> 	$this->key_prefix . 'complete', 
																'name' 			=> 	$submitted ? __( 'Mark as complete', $this->text_domain ) : __( 'Not complete', $this->text_domain ),
																'desc'			=>	$submitted ? __('The supervisor should check this box to mark the assessment as complete.', $this->text_domain) .'<br/><br/>'.__('The participant will be notified.', $this->text_domain) : __('You cannot mark this assessment as complete until the participant has submitted it.', $this->text_domain),
																'type'			=> 	$submitted ? 'checkbox' : 'render',
																'cols'			=> 	12,
																'readonly'		=>	FALSE
															),
														);
			$meta_boxes[] 							= 	$this->args['metabox_args'];
		}
		
		
		return $meta_boxes;
	}

	/**
	 * Notify Supervisor
	 * 
	 * @param  int $post_id the post id
	 * @param  object $post the post
	 */
	public function notify_supervisor( $post_id, $post ) {
		
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

		remove_action( 'save_post',  array( $this, 'notify_supervisor' ), 99, 2 );

		$submitted    =  get_post_meta( $post_id, '_cpd_submit', TRUE );
		$user_id      =  get_current_user_id();
		$user_type    =  get_user_meta( $user_id, 'cpd_role', TRUE );

		// Halt if the post has been submitted
		if ( $user_type == 'participant' && $submitted ) {
			$emails = CPD_Emails::get_instance();
			$emails->send_mail_on_submit_assessment( $post_id );
			update_post_meta( $post_id, '_cpd_submitted_date', date('Y-m-d'), TRUE );
		}

		add_action( 'save_post', array( $this, 'notify_supervisor' ), 99, 2 );
	}

	/**
	 * Notify Participant
	 * 
	 * @param  int $post_id the post id
	 * @param  object $post the post
	 */
	public function notify_participant( $post_id, $post ) {
		
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

		remove_action( 'save_post',  array( $this, 'notify_participant' ), 99, 2 );

		$complete     =  get_post_meta( $post_id, '_cpd_complete', TRUE );
		$user_id      =  get_current_user_id();
		$user_type    =  get_user_meta( $user_id, 'cpd_role', TRUE );

		// Halt if the post has been submitted
		if ( $user_type != 'participant' && $complete ) {
			$emails = CPD_Emails::get_instance();
			$emails->send_mail_on_marked_assessment( $post_id );
			update_post_meta( $post_id, '_cpd_completed_date', date('Y-m-d'), TRUE );
		}

		add_action( 'save_post', array( $this, 'notify_participant' ), 99, 2 );
	}

}
}