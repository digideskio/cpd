<?php

// Exit if accessed directly
defined( 'ABSPATH' ) || exit;

if( !class_exists( 'CPD_CPT_PPD' ) ) {

/**
 * Admin Scripts
 *
 * Load Admin Scripts
 *
 * @package    CPD
 * @subpackage CPD/admin
 * @author     Make Do <hello@makedo.in>
 */
class CPD_CPT_PPD {

	private static $instance = null;
	private $text_domain;

	private $dash_icon;
	private $name_singular;
	private $name_plural;
	private $cpt_name;
	private $args;
	private $slug;
	private $image_metabox_title;
	private $menu_postition;
	private $show_in_menu;

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
														'cpt_name' 				=> 'ppd',
														'dash_icon' 			=> 'dashicons-index-card',
														'name_singular' 		=> 'Activity Log',
														'name_plural' 			=> 'Activity Logs',
														'slug' 					=> 'ppd',
														'image_metabox_title'	=> '',
														'menu_postition'		=> 20,
														'post_type_args'		=> array(),
														'show_in_menu'			=> TRUE,
													);

		$this->cpt_name 						= 	$this->args[ 'cpt_name'				];
		$this->dash_icon 						= 	$this->args[ 'dash_icon'			];
		$this->name_singular 					= 	$this->args[ 'name_singular'		];
		$this->name_plural 						= 	$this->args[ 'name_plural'			];
		$this->slug 							= 	$this->args[ 'slug'					];
		$this->image_metabox_title 				= 	$this->args[ 'image_metabox_title'	];
		$this->menu_postition 					= 	$this->args[ 'menu_postition'		];
		$this->show_in_menu 					= 	$this->args[ 'show_in_menu'			];

		// Lets check whether our custom content menu has been created
		if( $this->show_in_menu && class_exists( 'MKDO_Admin' ) && $this->use_mkdo_menu ) {
			// Set this post type to show in the custom content menu
			$this->show_in_menu  				= 'mkdo_content_menu';
		}

		$post_type_args 						=	array(
														'description'			=> 	'',
														'public'				=> 	TRUE,
														'publicly_queryable'	=> 	TRUE,
														'show_in_nav_menus'		=> 	TRUE,
														'show_in_admin_bar'		=> 	TRUE,
														'exclude_from_search'	=> 	FALSE,
														'show_ui'				=> 	TRUE,
														'show_in_menu'			=> 	'cpd_content_menu',
														'can_export'			=> 	TRUE,
														'delete_with_user'		=> 	TRUE,
														'hierarchical'			=> 	FALSE,
														'has_archive'			=> 	TRUE,
														'menu_icon'				=> 	$this->dash_icon,
														'query_var'				=> 	$this->cpt_name,
														'menu_position'			=> 	$this->menu_postition,

														'rewrite' 				=> 	array(
																						'slug' => $this->slug 
																					),

														'supports' 				=> 	array(
																						'title',
																						// 'editor',
																						// 'author',
																						'thumbnail',
																						// 'excerpt',
																						// 'trackbacks',
																						// 'custom-fields',
																						// 'comments',
																						// 'revisions',
																						'page-attributes',
																						// 'post-formats'
																					),
													
														'label'					=> __( $this->name_plural, $this->text_domain  ),
														'labels' 				=> array(
																						'name'					=> __( $this->name_plural, 														$this->text_domain  ),
																						'singular_name'			=> __( $this->name_singular, 													$this->text_domain  ),
																						'menu_name'				=> __( $this->name_plural, 														$this->text_domain  ),
																						'name_admin_bar'		=> __( $this->name_plural, 														$this->text_domain  ),
																						'add_new'				=> __( 'Add New', 																$this->text_domain  ),
																						'add_new_item'			=> __( 'Add New ' 				. $this->name_singular, 						$this->text_domain  ),
																						'edit_item'				=> __( 'Edit ' 					. $this->name_singular, 						$this->text_domain  ),
																						'new_item'				=> __( 'New ' 					. $this->name_singular, 						$this->text_domain  ),
																						'view_item'				=> __( 'View ' 					. $this->name_singular, 						$this->text_domain  ),
																						'search_items'			=> __( 'Search '				. $this->name_plural, 							$this->text_domain  ),
																						'not_found'				=> __( 'No ' 					. $this->name_plural 	. 	' found', 			$this->text_domain  ),
																						'not_found_in_trash'	=> __( 'No ' 					. $this->name_plural 	. 	' found in trash', 	$this->text_domain  ),
																					)
													);
		
		$this->args['post_type_args'] 			= 	array_merge( $post_type_args, $this->args[ 'post_type_args'] );
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
	 * Register the CPT
	 */
	public function register_post_type() {
		
		register_post_type( $this->cpt_name, $this->args['post_type_args'] );
	}

	/**
	 * Move advanced metaboxes above the editor
	 */
	public function move_advanced_metaboxes_above_editor() {
		
		global $post, $wp_meta_boxes;

		$screen = get_current_screen();
		
		if( $screen->id == $this->cpt_name )
		{
			echo '<br/>';
			do_meta_boxes( get_current_screen(), 'advanced', $post );
			unset( $wp_meta_boxes[get_post_type($post)]['advanced'] );
		}
	}

		/**
	 * Change the name of the featured image meta box so it is more relevent to the plugin
	 */
	public function set_featured_image_metabox_title() {
	
		remove_meta_box( 'postimagediv', $this->cpt_name, 'side' );

		if( empty( $this->image_metabox_title ) )
		{
			add_meta_box('postimagediv', __( $this->name_singular . ' Image' ), 'post_thumbnail_meta_box', $this->cpt_name, 'side', 'default');
		}
		else
		{
			add_meta_box('postimagediv', __( $this->image_metabox_title ), 'post_thumbnail_meta_box', $this->cpt_name, 'side', 'default');
		}
	}

	/**
	 * Insert text
	 *
	 * @since    	1.0.0
	 */
	public function add_title_helper_text() {
		
		global $post, $wp_meta_boxes;

		$html = '<h3>Activity Name</h3>';
		$html .= '<p>The title should be the name of the activity.</p>';

		if( !empty( $html ) )
		{
			$screen = get_current_screen();
		
			if( $screen->id == $this->cpt_name ) {
				echo $html;
			}
		}
	}
}
}