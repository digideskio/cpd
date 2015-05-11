<?php

// Exit if accessed directly
defined( 'ABSPATH' ) || exit;

if( !class_exists( 'CPD_Taxonomy_Development_Category' ) ) {

/**
 * Admin Scripts
 *
 * Load Admin Scripts
 *
 * @package    CPD
 * @subpackage CPD/admin
 * @author     Make Do <hello@makedo.in>
 */
class CPD_Taxonomy_Development_Category {

	private static $instance = null;
	private $text_domain;
	private $name_singular;
	private $name_plural;
	private $taxonomy_name;
	private $slug;
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
		
	}

	/**
	 * Set the text domain
	 *
	 * @param      string    $text_domain       The text domain of the plugin.
	 */
	public function set_text_domain( $text_domain ) { 
		$this->args 							= 	array(
														'name_singular' 		=> 'Development Category',
														'name_plural' 			=> 'Development Categories',
														'taxonomy_name' 		=> 'development-category',
														'slug' 					=> 'development-category',
														'register_pages' 		=> array(),
														'taxonomy_args'			=> array(),
														'taxonomy_terms'		=> array(),
														'metabox_remove_pages'	=> array()
													);


		$this->name_singular 					= 	$this->args[ 'name_singular'		];
		$this->name_plural 						= 	$this->args[ 'name_plural'			];
		$this->taxonomy_name 					= 	$this->args[ 'taxonomy_name'		];
		$this->slug 							= 	$this->args[ 'slug'					];

		$taxonomy_args 							=	array(
														'label'				=> __( $this->name_plural),
														'labels' 			=> 	array(
																					'name' 				=> _x( $this->name_singular, 		'taxonomy general name' 			),
																					'singular_name' 	=> _x( $this->name_singular, 		'taxonomy singular name' 			),
																					'search_items' 		=> __( 'Search ' 					. $this->name_plural 				),
																					'all_items' 		=> __( 'All ' 						. $this->name_plural 				),
																					'parent_item'		=> __( 'Parent ' 					. $this->name_plural 				),
																					'parent_item_colon' => __( 'Parent ' 					. $this->name_plural 	. ':' 		),
																					'edit_item' 		=> __( 'Edit ' 						. $this->name_plural 				), 
																					'update_item' 		=> __( 'Update ' 					. $this->name_plural 				),
																					'add_new_item' 		=> __( 'Add New ' 					. $this->name_singular 				),
																					'new_item_name' 	=> __( 'New ' 						. $this->name_singular 	. ' Name' 	),
																					'menu_name' 		=> __( $this->name_plural 												),
																				),
														'show_in_nav_menus' => 	FALSE,
														'show_ui' 			=> 	TRUE,
														'hierarchical' 		=> 	FALSE,
														'sort' 				=> 	TRUE,
														'args' 				=> 	array(
																					'orderby' => 'term_order'
																				),
														'rewrite' 			=> 	array(
																					'slug' => $this->slug
																				),
														'show_admin_column' => 	TRUE,
													);

		$this->args['taxonomy_args'] 			= 	array_merge( $taxonomy_args, $this->args[ 'taxonomy_args'] );
	}

	/**
	 * Register the taxonomy.
	 */
	public function register_taxonomy() {
		
		register_taxonomy( $this->taxonomy_name, array('ppd'), $this->args['taxonomy_args'] );
	}
}
}