<?php

// Exit if accessed directly
defined( 'ABSPATH' ) || exit;

if( !class_exists( 'CPD_Meta_Box_Privacy' ) ) {

/**
 * Admin Scripts
 *
 * Load Admin Scripts
 *
 * @package    CPD
 * @subpackage CPD/admin
 * @author     Make Do <hello@makedo.in>
 */
class CPD_Meta_Box_Privacy {

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
														'id' 					=> 'privacy',
														'id_prefix' 			=> 'cpd_',
														'name' 					=> '<span class="cpd-dashboard-widget-title dashicons-before dashicons-shield"></span> Privacy',
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
														'pages' 			=> 	array(),
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
														'id'			=> 	$this->key_prefix . 'private_content',
														'name' 			=> 	__( 'Private Content', $this->text_domain ),
														'desc'			=>	'Check the box below to mark this content as private. <a href="admin.php?page=cpd_settings_privacy#private-content-who">Who can view private content?</a>',
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

		$cpd_login_to_view = get_option( 'cpd_login_to_view', NULL );
		$post_types = get_post_types(
			array(
                'public' => true
			)
		);

		unset( $post_types['attachment'] );

        $this->args['metabox_args']['pages'] = $post_types;

		if( current_user_can( 'manage_privacy' ) && $cpd_login_to_view == 'false' ) {
            $meta_boxes[] = $this->args['metabox_args'];
		}

		return $meta_boxes;
	}

	/**
	 * Change post status
	 *
	 * @param  int $post_id the post id
	 * @param  object $post the post
	 */
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

		$private_content = get_post_meta( $post_id, '_cpd_private_content', TRUE );

		if( $private_content ) {
			if( $post->post_status == 'publish' ) {
				wp_update_post(
					array(
						'ID'          => $post_id,
						'post_status' => 'private'
					)
				);
			}
		} else {
			if( $post->post_status == 'private' ) {
				wp_update_post(
					array(
						'ID'          => $post_id,
						'post_status' => 'publish'
					)
				);
			}
		}

		add_action( 'save_post', array( $this, 'change_post_status' ), 99, 2 );
	}

}
}
