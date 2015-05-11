<?php

// Exit if accessed directly
defined( 'ABSPATH' ) || exit;

if( !class_exists( 'CPD_Meta_Box_Evidence' ) ) {

/**
 * Admin Scripts
 *
 * Load Admin Scripts
 *
 * @package    CPD
 * @subpackage CPD/admin
 * @author     Make Do <hello@makedo.in>
 */
class CPD_Meta_Box_Evidence {

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
														'id' 					=> 'evidence',
														'id_prefix' 			=> 'cpd_',
														'name' 					=> 'Evidence / Verification',
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
			$this->args['key_prefix'] 			=	'_' . $this->id_prefix . '_';
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


		$journal_entries 						= 	array();

		$journal_posts 							= 	get_posts(
														array(
															'post_type'			=> 	'post',
															'posts_per_page'	=>	-1
														)
													);

		if( is_array( $journal_posts ) ) {
			foreach( $journal_posts as $journal_post ) {
				$journal_entries[ $journal_post->ID ]	=	$journal_post->post_title;
			}
		}

		$metabox_args	= 	array(
								'fields' 	=> 	array(
													array( 
														'id'			=> 	$this->key_prefix . 'group', 
														// 'name' 			=> 	__( 'Evidence', $this->text_domain ),
														'desc'			=>	'Eg. certificate of achivement, certificate of attendance, line manager or self certification.<br/><br/>Select \'Add New Group\' to start adding supporting evidence. You can add as much evidence as you need by adding more groups.',
														'type'			=> 	'group',
														'cols'			=> 	12,
														'fields'		=> 	array(
																				array( 
																					'id'			=> 	$this->key_prefix . 'evidence_type', 
																					'name' 			=> 	__( 'Evidence Type', $this->text_domain ),
																					'desc'			=>	'Choose the type of evidence you wish to add.',
																					'type'			=> 	'radio',
																					'cols'			=> 	12,
																					'options'		=> 	array(
																											'upload' 	=>	'File Upload',
																											'journal' 	=>	'Journal Entry',
																											'url' 		=>	'URL',
																										)
																				),
																				array( 
																					'id'			=> 	$this->key_prefix . 'evidence_file', 
																					'name' 			=> 	__( 'File Upload', $this->text_domain ),
																					'desc'			=>	'Upload your evidence:',
																					'type'			=> 	'file',
																					'cols'			=> 	12
																				),
																				array( 
																					'id'			=> 	$this->key_prefix . 'evidence_journal', 
																					'name' 			=> 	__( 'Journal Entry', $this->text_domain ),
																					'desc'			=>	'Please select the Journal Entry:',
																					'type'			=> 	'select',
																					'cols'			=> 	12,
																					'options'		=>  $journal_entries,
																					'allow_none'	=>	TRUE
																				),
																				array( 
																					'id'			=> 	$this->key_prefix . 'evidence_url', 
																					'name' 			=> 	__( 'URL', $this->text_domain ),
																					'desc'			=>	'Cut and paste the URL of your evidence into the field provided:',
																					'type'			=> 	'text_url',
																					'cols'			=> 	12
																				),
																			),
														'repeatable'	=> true
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