<?php
/**
 * The menu-specific functionality of the plugin.
 *
 * @link       http://makedo.in
 * @since      1.0.0
 *
 * @package    CPD
 * @subpackage CPD/admin
 */

/**
 * The menu-specific functionality of the plugin.
 *
 * @package    CPD
 * @subpackage CPD/admin
 * @author     Make Do <hello@makedo.in>
 */
class CPD_Journal_Menus extends MKDO_Class {

	/**
	 * Initialize the class and set its properties.
	 *
	 * @since    1.0.0
	 * @var      string    $instance       The name of this plugin.
	 * @var      string    $version    The version of this plugin.
	 */
	public function __construct( $instance, $version ) {
		parent::__construct( $instance, $version );

		add_action( 'parent_file', 	array( $this, 'correct_menu_hierarchy'), 9999 );
	}

	/**
	 * Add admin menus
	 */
	public function add_admin_menus() {

		// If the user is a super admin (network manager)
		if( is_super_admin() ) {

			if( !MKDO_Helper_User::is_mkdo_user() ) 
			{
				add_menu_page( 
					'Global Settings', 
					'Global Settings', 
					'manage_network',
					'network/index.php',
					'',
					'dashicons-admin-site',
					9999
				);
			}

			add_menu_page( 
				'Journals', 
				'Journals', 
				'manage_network', 
				'network/sites.php',
				'',
				'dashicons-book-alt',
				3
			);

		}
		// If the user is not a super admin (network manager)
		else {

			// If the user belogs to more than one blog (journal)
			$user_id 	= get_current_user_id();
			$user_blogs = get_blogs_of_user( $user_id );

			if ( count( $user_blogs ) > 1 ) {

				add_menu_page( 
					'My Journals', 
					'My Journals', 
					'edit_pages', 
					'my-sites.php',
					'',
					'dashicons-book-alt',
					2
				);

			}
		}
	}

	/**
	 * Add admin sub menus
	 */
	public function add_admin_sub_menus() {

		if( is_super_admin() ) {

			add_submenu_page(
				'options-general.php',
				'Global Settings',
				'Global Settings',
				'manage_network',
				'network/index.php',
				''
			);

		}
	}

	/**
	 * Add network menus
	 */
	public function add_network_admin_menus() {

		if( is_super_admin() ) {
			
			add_menu_page( 
				'Dashboard', 
				'Dashboard', 
				'manage_network',
				'../admin.php?page=mkdo_dashboard',
				'',
				'dashicons-dashboard',
				1
			);
		}
	}

	/**
	 * Add network sub menus
	 */
	public function add_network_admin_sub_menus() {

		if( is_super_admin() ) {
			
			add_submenu_page(
				'index.php',
				'Journals',
				'Journals',
				'manage_network',
				'sites.php',
				''
			);

			add_submenu_page(
				'index.php',
				'Users',
				'Users',
				'manage_network',
				'users.php',
				''
			);

			add_submenu_page(
				'index.php',
				'Themes',
				'Themes',
				'manage_network',
				'themes.php',
				''
			);

			add_submenu_page(
				'index.php',
				'Plugins',
				'Plugins',
				'manage_network',
				'plugins.php',
				''
			);

			add_submenu_page(
				'index.php',
				'Settings',
				'Settings',
				'manage_network',
				'settings.php',
				''
			);

			add_submenu_page(
				'index.php',
				'Updates',
				'Updates',
				'manage_network',
				'update-core.php',
				''
			);
		}
	}

	/**
	 * Rename / reorder network menus
	 */
	public function rename_network_admin_menus() {

		if( is_super_admin() ) {

			global $menu;

			// Rename menu items
			foreach( $menu as $key=>&$menu_item ) {
				if( $menu_item[0] == 'Dashboard' || $menu_item[0] == 'Global Settings' ) {

					$menu_item[0] 	= 'Global Settings';
					$menu_item[6] 	= 'dashicons-admin-site';
					$network		= $menu[$key];
					unset( $menu[$key] );
					$menu[6] 	= $network; 
				}
				// It may have already been renamed
				// else if( $menu_item[0] == 'Sites' || $menu_item[0] == 'Journals' ) {

				// 	$menu_item[0] 	= 'Journals';
				// 	$menu_item[6] 	= 'dashicons-book-alt';

				// 	// Swap it with position 4 (separator)
				// 	$separator		= $menu[4];
				// 	$menu[4] 		= $menu[$key];
				// 	$menu[$key] 	= $separator;
				// }
			}
		}
	}

	/**
	 * Rename network sub menus
	 */
	public function rename_network_admin_sub_menus() {

		if( is_super_admin() ) {

			global $submenu;

			// Rename submenu items
			foreach( $submenu as $key=>&$menu_item ) {
				if( $key == 'sites.php') {

					foreach( $menu_item as &$submenu_item ) {
						if( $submenu_item[0] == 'All Sites' ) {
							$submenu_item[0] = 'All Journals';
							break;
						}
					}
					break;
				}
			}
		}
	}

	/**
	 * Remove sub menus
	 */
	public function remove_admin_sub_menus() {
		
		$user_id 	= get_current_user_id();
		$user_type 	= get_user_meta( $user_id, 'cpd_role', true );

		if( $user_type == 'participant' )
		{
			remove_submenu_page( 'users.php', 'users.php' );
			remove_submenu_page( 'users.php', 'user-new.php' );
			remove_submenu_page( 'tools.php', 'ms-delete-site.php' );
			remove_submenu_page( 'options-general.php', 'options-discussion.php' );
		}
	}

	/**
	 * Remove network menus menus
	 */
	public function remove_network_admin_menus() {
		
		if( is_super_admin() ) {
			remove_menu_page( 'sites.php' );
			remove_menu_page( 'users.php' );
			remove_menu_page( 'themes.php' );
			remove_menu_page( 'plugins.php' );
			remove_menu_page( 'settings.php' );
			remove_menu_page( 'update-core.php' );
		}
	}

	/**
	 * Correct the heirachy
	 */
	public function correct_menu_hierarchy( $parent_file ) {
	
		global $current_screen;
		global $submenu;

		$pages 		= array();
		$this->slug = 'mkdo_content_menu';
		$parent 	= $this->slug;
		
		if ( is_array( $submenu ) && isset( $submenu[$parent] ) ) {

			foreach ( (array) $submenu[$parent] as $item) {

				if ( current_user_can($item[1]) ) {
					$menu_file = $item[2];
					if ( false !== ( $pos = strpos( $menu_file, '?' ) ) ) {
						$menu_file = substr( $menu_file, 0, $pos );
					}
	
					if( $item[2] == 'edit.php' ) {
						$pages[] = 'Journal Entries';
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
		if( ( $screenbase == 'edit' && in_array( $post_type, $pages ) )|| ( $screenbase == 'post' && in_array( $post_type, $pages ) ) ) {

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
	 * Fix the menu hierarchy
	 */
	public function correct_sub_menu_hierarchy() {

		global $submenu;
		$screen = get_current_screen();
		//print_r($submenu);
		if( strpos( $screen->base, '-network' ) ) {
	
			foreach( $submenu as $path=>&$submenu_item ) {
				if ( 
						$path == 'sites.php' 		||
						$path == 'users.php' 		|| 
						$path == 'themes.php' 		|| 
						$path == 'plugins.php' 		|| 
						$path == 'settings.php' 	|| 
						$path == 'update-core.php'
					) {
					foreach( $submenu_item as $key=>&$smenu ) {
						$submenu_item[$key][2] = 'index.php';
					}
				}
			}
		}
		// else {
		// 	foreach( $submenu as $path=>&$submenu_item ) {
		// 		foreach( $submenu_item as $key=>&$smenu ) {
		// 			if( $submenu_item[$key][0] == 'Journal Entries' ) {
		// 				$submenu_item[$key][2] = 'mkdo_content_menu';
		// 			}
		// 		}
		// 	}
		// }
	}

}