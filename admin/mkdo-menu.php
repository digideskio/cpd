<?php
/**
 * The menus
 *
 * @link       http://makedo.in
 * @since      1.0.0
 *
 * @package    MKDO_Admin
 * @subpackage MKDO_Admin/admin
 */

/**
 * The menus
 *
 * Creates the MKDO menu items
 *
 * @package    MKDO_Admin
 * @subpackage MKDO_Admin/admin
 * @author     Make Do <hello@makedo.in>
 */
class MKDO_Menu{

	/**
	 * The arguments for the CPT
	 *
	 * @since    1.0.0
	 * @access   protected
	 * @var      array    $args    The arguments for the CPT.
	 */
	protected $args;
	protected $page_title;
	protected $menu_title;
	protected $capibility;
	protected $slug;
	protected $function;
	protected $icon;
	protected $position;

	/**
	 * Initialize the class and set its properties.
	 *
	 * @since    1.0.0
	 * @var      string    $instance       The name of this plugin.
	 * @var      string    $version    The version of this plugin.
	 */
	public function __construct( $args = array() ) {
		

		$defaults 								= 	array(
														'page_title' 			=> 	'Menu',
														'menu_title' 			=> 	'Menu',
														'capibility' 			=> 	'edit_posts',
														'slug' 					=> 	'mkdo_menu',
														'function'				=> 	'', // eg. array( $this, 'mkdo_content_output'),
														'icon' 					=> 	'dashicons-admin-page',
														'position' 				=> 	'26',
														'add_menus'				=> 	array(),
														'remove_menus'			=> 	array(),
														'remove_sub_menus'		=> 	array(),
													);

		$this->args 							= 	array_merge( $defaults, $args );

		$this->page_title 						= 	$this->args[ 'page_title'	];
		$this->menu_title						= 	$this->args[ 'menu_title'	];
		$this->capibility 						= 	$this->args[ 'capibility'	];
		$this->slug 							= 	$this->args[ 'slug'			];
		$this->function 						=	$this->args[ 'function'		];
		$this->icon 							=	$this->args[ 'icon'			];
		$this->position 						=	$this->args[ 'position'		];

	}

	/**
	 * Add admin menu
	 */
	public function add_menu() {
	
		add_object_page(
			$this->page_title,
			$this->menu_title,
			$this->capibility,
			$this->slug,
			$this->function,
			$this->icon
			//$this->position
		);
	}

	/**
	 * Remove admin menus
	 */
	public function remove_admin_menus() {

		$admin_menu 	= 	$this->args['remove_menus'];
		$is_mkdo_user 	=	MKDO_Helper_User::is_mkdo_user();
		$is_admin 		=	current_user_can('manage_options');

		$remove_menu_items = apply_filters(
			$this->slug . '_remove_admin_menus',
			$admin_menu
		);

		foreach( $remove_menu_items as $remove_menu_item ) {

			if( $is_mkdo_user && $remove_menu_item['mkdo_remove'] ) {
				remove_menu_page( $remove_menu_item['menu'] );
			}
			else if( !$is_mkdo_user && $is_admin && $remove_menu_item['admin_remove'] ) {
				remove_menu_page( $remove_menu_item['menu'] );
			}
			else if( !$is_mkdo_user && !$is_admin ) {
				remove_menu_page( $remove_menu_item['menu'] );
			}
		}
	}

	/**
	 * Remove admin sub menus
	 */
	public function remove_admin_sub_menus() {

		$admin_sub_menu 	= 	$this->args['remove_sub_menus'];
		$is_mkdo_user 		=	MKDO_Helper_User::is_mkdo_user();
		$is_admin 			=	current_user_can('manage_options');

		$remove_menu_items 	= 	apply_filters(
			$this->slug . '_remove_admin_sub_menus',
			$admin_sub_menu
		);

		foreach( $remove_menu_items as $remove_menu_item ) {

			if( $is_mkdo_user && $remove_menu_item['mkdo_remove'] ) {
				remove_submenu_page( $remove_menu_item[ 'parent' ], $remove_menu_item[ 'child' ] );
			}
			else if( !$is_mkdo_user && $is_admin && $remove_menu_item['admin_remove'] ) {
				remove_submenu_page( $remove_menu_item[ 'parent' ], $remove_menu_item[ 'child' ] );
			}
			else if( !$is_mkdo_user && !$is_admin ) {
				remove_submenu_page( $remove_menu_item[ 'parent' ], $remove_menu_item[ 'child' ] );
			}
			
		}

	}

	/**
	 * Add menu items to the menu
	 */
	public function add_menu_items() {

		$mkdo_content_menus 	= 	$this->args['add_menus'];
		$is_mkdo_user 			=	MKDO_Helper_User::is_mkdo_user();
		$is_admin 				=	current_user_can('manage_options');
		
		$add_mkdo_content_menu_items = apply_filters(
			$this->slug . '_add_menu_items',
			$mkdo_content_menus
		);

		foreach( $add_mkdo_content_menu_items as $add_menu_item ) {

			if( $add_menu_item['remove_original_menu'] ) {

				add_filter( $this->slug . '_remove_admin_menus', function( $admin_menu ) use ( $add_menu_item ){
					$admin_menu[] = array(
									'menu' 			=> 		$add_menu_item['function'],
									'admin_remove'	=>		$add_menu_item['admin_remove'],
									'mkdo_remove'	=> 		$add_menu_item['mkdo_remove'],
								);
					return $admin_menu;
				});
			}

			if( $add_menu_item['remove_original_sub_menu'] ) {

				add_filter( $this->slug . '_remove_admin_sub_menus', function( $admin_menu ) use ( $add_menu_item ){
					$admin_menu[] = array(
									'parent' 		=> 		$add_menu_item['remove_original_sub_menu_parent'],
									'child' 		=> 		$add_menu_item['function'],
									'admin_remove'	=>		$add_menu_item['admin_remove'],
									'mkdo_remove'	=> 		$add_menu_item['mkdo_remove'],
								);
					return $admin_menu;
				});
			}

			if( $add_menu_item['add_to_dashboard'] ) {

				add_filter( $add_menu_item['add_to_dashboard_slug'] . '_blocks', function( $blocks ) use ( $add_menu_item ){
					
					$post_type = get_post_type_object( $add_menu_item['post_type'] );

					if( !array_key_exists( 'add_to_dashboard_block', $add_menu_item ) || !is_array( $add_menu_item['add_to_dashboard_block'] ) ) {

						if( empty( $post_type->menu_icon ) ) {
							if( $post_type->name == 'page' ) {
								$post_type->menu_icon = 'dashicons-admin-page';
							}
							else if( $post_type->name == 'post' ) {
								$post_type->menu_icon = 'dashicons-admin-post';
							}
						}

						$blocks[] = array(
										'title' 				=> $add_menu_item['menu_name'],
										'dashicon' 				=> $post_type->menu_icon,
										'desc' 					=> '<p>This content type is for managing ' . $post_type->labels->menu_name . '.</p>',
										'post_type' 			=> $post_type->name,
										'button_label' 			=> 'Edit / Manage ' . $post_type->labels->menu_name,
										'css_class' 			=> $post_type->name,
										'show_tax' 				=> TRUE,
										'link' 					=> admin_url( 'edit.php?post_type=' . $post_type->name ),
										'call_to_action_text'	=> 'Add New',
										'call_to_action_link' 	=> admin_url( 'post-new.php?post_type=' . $post_type->name )
									);

					}
					else {
						$block = array(
									'title' 				=> array_key_exists( 'title', 				$add_menu_item['add_to_dashboard_block'] ) ? $add_menu_item['add_to_dashboard_block']['title'] 					: null,
									'dashicon' 				=> array_key_exists( 'dashicon', 			$add_menu_item['add_to_dashboard_block'] ) ? $add_menu_item['add_to_dashboard_block']['dashicon'] 				: null,
									'desc' 					=> array_key_exists( 'desc', 				$add_menu_item['add_to_dashboard_block'] ) ? $add_menu_item['add_to_dashboard_block']['desc'] 					: null,
									'post_type' 			=> array_key_exists( 'post_type', 			$add_menu_item['add_to_dashboard_block'] ) ? $add_menu_item['add_to_dashboard_block']['desc'] 					: null,
									'button_label' 			=> array_key_exists( 'button_label', 		$add_menu_item['add_to_dashboard_block'] ) ? $add_menu_item['add_to_dashboard_block']['button_label'] 			: null,
									'css_class' 			=> array_key_exists( 'css_class', 			$add_menu_item['add_to_dashboard_block'] ) ? $add_menu_item['add_to_dashboard_block']['css_class'] 				: null,
									'show_tax' 				=> array_key_exists( 'show_tax', 			$add_menu_item['add_to_dashboard_block'] ) ? $add_menu_item['add_to_dashboard_block']['show_tax'] 				: null,
									'link' 					=> array_key_exists( 'link', 				$add_menu_item['add_to_dashboard_block'] ) ? $add_menu_item['add_to_dashboard_block']['link'] 					: null,
									'call_to_action_text' 	=> array_key_exists( 'call_to_action_text', $add_menu_item['add_to_dashboard_block'] ) ? $add_menu_item['add_to_dashboard_block']['call_to_action_text'] 	: null,
									'call_to_action_link' 	=> array_key_exists( 'call_to_action_link', $add_menu_item['add_to_dashboard_block'] ) ? $add_menu_item['add_to_dashboard_block']['call_to_action_link'] 	: null
								);

						if( !empty( $post_type ) ) {

							if( empty( $block['title'] ) ) {
								$block['title'] = $add_menu_item['menu_name'];
							}

							if( empty( $block['dashicon'] ) ) {
								$block['dashicon'] = $post_type->menu_icon;
							}

							if( empty( $block['desc'] ) ) {
								$block['desc'] = '<p>This content type is for managing ' . $post_type->labels->menu_name . '.</p>';
							}

							if( empty( $block['post_type'] ) ) {
								$block['post_type'] = $post_type->name;
							}

							if( empty( $block['button_label'] ) ) {
								$block['button_label'] = 'Edit / Manage ' . $post_type->labels->menu_name;
							}

							if( empty( $block['css_class'] ) ) {
								$block['css_class'] = $post_type->name;
							}

							if( empty( $block['show_tax'] ) ) {
								$block['show_tax'] = TRUE;
							}

							if( empty( $block['link'] ) ) {
								$block['link'] = admin_url( 'edit.php?post_type=' . $post_type->name );
							}

							if( empty( $block['call_to_action_text'] ) ) {
								$block['call_to_action_text'] = 'Add New';
							}

							if( empty( $block['call_to_action_link'] ) ) {
								$block['call_to_action_link'] = admin_url( 'post-new.php?post_type=' . $post_type->name );
							}
						}
						

						$blocks[] = $block;
					}
					return $blocks;
				});
			}
			
			if( $is_mkdo_user && $add_menu_item['mkdo_add'] ) {
				add_submenu_page(
					$this->slug,
					$add_menu_item['post_name'],
					$add_menu_item['menu_name'],
					$add_menu_item['capability'],
					$add_menu_item['function']
				);
			}
			else if( !$is_mkdo_user && $is_admin && $add_menu_item['admin_add'] ) {
				add_submenu_page(
					$this->slug,
					$add_menu_item['post_name'],
					$add_menu_item['menu_name'],
					$add_menu_item['capability'],
					$add_menu_item['function']
				);
			}
			else if( !$add_menu_item['mkdo_add'] && !$add_menu_item['admin_add'] ) {
				add_submenu_page(
					$this->slug,
					$add_menu_item['post_name'],
					$add_menu_item['menu_name'],
					$add_menu_item['capability'],
					$add_menu_item['function']
				);
			}
		}
	}

	/**
	 * Correct the heirachy
	 */
	public function correct_menu_hierarchy( $parent_file ) {
	
		global $current_screen;
		global $submenu;

		$pages 	= array();
		$parent = $this->slug ;
		
		if ( is_array( $submenu ) && isset( $submenu[$parent] ) ) {

			foreach ( (array) $submenu[$parent] as $item) {

				if ( current_user_can($item[1]) ) {
					$menu_file = $item[2];
					if ( false !== ( $pos = strpos( $menu_file, '?' ) ) ) {
						$menu_file = substr( $menu_file, 0, $pos );
					}
	
					if( $item[2] == 'edit.php' ) {
						$pages[] = 'Posts';
					}
					else if( $item[2] == 'edit.php?post_type=page' || $item[2] == 'edit.php?post_type=page&page=cms-tpv-page-page' ){
						$pages[] = 'Pages';
					}
					else {
						$pages[] = $item[0];
					}
						
				}
			}
		}

		$post_type = get_post_type_object( $current_screen->post_type );
		
		if( isset( $post_type->labels ) && isset( $post_type->labels->name ) ) {
			$post_type = $post_type->labels->name;
		}
		
		/* get the base of the current screen */
		$screenbase = $current_screen->base;

		/* if this is the edit.php base */
		if( ( $screenbase == 'edit' && in_array( $post_type, $pages ) ) || ( $screenbase == 'post' && in_array( $post_type, $pages ) ) ) {

			/* set the parent file slug to the custom content page */
			$parent_file = $this->slug;
		}

		if( defined('CMS_TPV_URL') && $screenbase == 'pages_page_cms-tpv-page-page' && in_array( $post_type, $pages ) ) {
			$parent_file = $this->slug;
		}

		/* return the new parent file */	
		return $parent_file;
	}

	/**
	 * Correct the heirachy
	 */
	public function correct_sub_menu_hierarchy() {
		global $submenu;
		
		if( array_key_exists( 'edit.php?post_type=page', $submenu ) ) {

			foreach( $submenu['edit.php?post_type=page'] as $key=>$smenu ) {
				$submenu['edit.php?post_type=page'][$key][2] = $smenu[2] . '&post_type=page';
			}
		}

	}
}
