<?php

// Exit if accessed directly
defined( 'ABSPATH' ) || exit;

if( !class_exists( 'CPD_Meta_Box_Criteria' ) ) {

/**
 * Admin Scripts
 *
 * Load Admin Scripts
 *
 * @package    CPD
 * @subpackage CPD/admin
 * @author     Make Do <hello@makedo.in>
 */
class CPD_Meta_Box_Criteria {

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
														'id' 					=> 'criteria',
														'id_prefix' 			=> 'cpd_',
														'name' 					=> 'Criteria',
														'context' 				=> 'normal',
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
														'id'			=> 	$this->key_prefix . 'group', 
														// 'name' 			=> 	__( 'Evidence', $this->text_domain ),
														'desc'			=>	'The criteria for this assessment. Criteria can only be added by a supervisor.',
														'type'			=> 	'group',
														'cols'			=> 	12,
														'fields'		=> 	array(
																				array( 
																					'id'			=> 	$this->key_prefix . 'criteria_title', 
																					'name' 			=> 	__( 'Criteria Item', $this->text_domain ),
																					'desc'			=>	'The fields below define an item in a set of criteria.',
																					'type'			=> 	'title',
																					'cols'			=> 	12
																				),
																				array( 
																					'id'			=> 	$this->key_prefix . 'criteria_guidance', 
																					'name' 			=> 	__( 'Guidance', $this->text_domain ),
																					'desc'			=>	'Guidance about this criteria (only editable by the supervisor).',
																					'type'			=> 	'wysiwyg',
																					'cols'			=> 	12
																				),
																				array( 
																					'id'			=> 	$this->key_prefix . 'criteria_response', 
																					'name' 			=> 	__( 'Response', $this->text_domain ),
																					'desc'			=>	'Response to the critera (editable by the participant)',
																					'type'			=> 	'wysiwyg',
																					'cols'			=> 	12
																				),
																				array( 
																					'id'			=> 	$this->key_prefix . 'criteria_max_score', 
																					'name' 			=> 	__( 'Maximum score', $this->text_domain ),
																					'desc'			=>	'The maximum score available for this criteria (only editable by the supervisor)',
																					'type'			=> 	'text',
																					'cols'			=> 	12
																				),
																				array( 
																					'id'			=> 	$this->key_prefix . 'criteria_participants_score', 
																					'name' 			=> 	__( 'Participants score', $this->text_domain ),
																					'desc'			=>	'Self assessement score (editable by the participant)',
																					'type'			=> 	'text',
																					'cols'			=> 	12
																				),
																				array( 
																					'id'			=> 	$this->key_prefix . 'criteria_supervisors_score', 
																					'name' 			=> 	__( 'Supervisors score', $this->text_domain ),
																					'desc'			=>	'Actual score (only editable by the supervisor)',
																					'type'			=> 	'text',
																					'cols'			=> 	12
																				),
																			),
														'repeatable'	=> true,
														'string-repeat-field' => 'Add Criteria',
														'string-delete-field' => 'Remove Criteria',
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
		
		$user_id 			= 	get_current_user_id();
		$user_type 			= 	get_user_meta( $user_id, 'cpd_role', TRUE );
		
		if( $user_type == 'participant' )
		{

			$this->args['metabox_args']['fields'][0]['repeatable'] = FALSE;

			foreach( $this->args['metabox_args']['fields'][0]['fields'] as $key=>&$field ) {
				if( $field['id'] == '_cpd_criteria_guidance' ) {
					$this->args['metabox_args']['fields'][0]['fields'][$key]['type'] = 	'render';
				}

				// if( $field['id'] == '_cpd_criteria_response' ) {
				// 	$this->args['metabox_args']['fields'][0]['fields'][$key]['type'] = 	'wysiwyg';
				// }

				if( $field['id'] == '_cpd_criteria_max_score' ) {
					$this->args['metabox_args']['fields'][0]['fields'][$key]['readonly'] = 	TRUE;
				}

				if( $field['id'] == '_cpd_criteria_supervisors_score' ) {
					$this->args['metabox_args']['fields'][0]['fields'][$key]['readonly'] = 	TRUE;
				}
			}
		}

		$meta_boxes[] 							= 	$this->args['metabox_args'];
		
		return $meta_boxes;
	}

}
}