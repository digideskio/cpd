<?php

// Exit if accessed directly
defined( 'ABSPATH' ) || exit;

if( !class_exists( 'CPD_Meta_Box_Points' ) ) {

/**
 * Admin Scripts
 *
 * Load Admin Scripts
 *
 * @package    CPD
 * @subpackage CPD/admin
 * @author     Make Do <hello@makedo.in>
 */
class CPD_Meta_Box_Points {

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
														'id' 					=> 'points',
														'id_prefix' 			=> 'cpd_',
														'name' 					=> 'Points',
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
														'pages' 			=> 	array('ppd'),
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
														'id'			=> 	$this->key_prefix . 'points', 
														'name' 			=> 	__( 'Points Awarded', $this->text_domain ),
														'desc'			=>	'Enter the points that this achievement should be awarded',
														'type'			=> 	'text_small',
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
		
		$user_id 			= 	get_current_user_id();
		$user_type 			= 	get_user_meta( $user_id, 'cpd_role', TRUE );
		
		if( $user_type == 'participant' )
		{
			$metabox_args	= 	array(
									'fields' 	=> 	array(
														array( 
															'id'			=> 	$this->key_prefix . 'points', 
															'name' 			=> 	__( 'Points Awarded', $this->text_domain ),
															'desc'			=>	'Only a supervisor can award points',
															'type'			=> 	'text_small',
															'cols'			=> 	12,
															'readonly'		=>	TRUE
														),
													)
								);

			$this->args['metabox_args'] 			= 	array_merge( $this->args[ 'metabox_args'], $metabox_args );
		}

		$meta_boxes[] 							= 	$this->args['metabox_args'];
		
		return $meta_boxes;
	}

}
}