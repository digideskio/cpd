<?php

// Exit if accessed directly
defined( 'ABSPATH' ) || exit;

if( !class_exists( 'CPD_Meta_Box_Description' ) ) {

/**
 * Admin Scripts
 *
 * Load Admin Scripts
 *
 * @package    CPD
 * @subpackage CPD/admin
 * @author     Make Do <hello@makedo.in>
 */
class CPD_Meta_Box_Description {

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
														'id' 					=> 'description',
														'id_prefix' 			=> 'cpd_',
														'name' 					=> __('Description', $this->text_domain),
														'context' 				=> 'advanced',
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
														'id'			=> 	$this->key_prefix . 'description', 
														'name' 			=> 	__( 'Brief Description', $this->text_domain ),
														'desc'			=>	__('Brief Description of activity or course, including provider, if applicable.', $this->text_domain),
														'type'			=> 	'textarea',
														'cols'			=> 	12,
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
		
		$meta_boxes[] 							= 	$this->args['metabox_args'];
		
		return $meta_boxes;
	}

}
}