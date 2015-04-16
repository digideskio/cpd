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
class MKDO_Admin_MU_Menus{

	/**
	 * The name of the network dashboard
	 *
	 * @since    1.0.0
	 * @access   protected
	 * @var      string    $instance    The name of the network dashboard
	 */
	protected $network_dash_title;

	/**
	 * Initialize the class and set its properties.
	 *
	 * @since    1.0.0
	 * @var      string    $instance       The name of this plugin.
	 * @var      string    $version    The version of this plugin.
	 */
	public function __construct() {
		

		$network_dash_title = apply_filters(
			'mkdo_admin_network_dash_title_filter',
			'Network Settings'
		);

		$this->network_dash_title = $network_dash_title;

	}

	/**
	 * Add admin sub menus
	 */
	public function add_admin_sub_menus() {

		if( is_super_admin() ) {

			add_submenu_page(
				'index.php',
				$this->network_dash_title,
				$this->network_dash_title,
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
			
			$user_id 		= get_current_user_id();
			$user_role 		= get_user_meta( $user_id , 'mkdo_admin_role', TRUE );

			add_menu_page( 
				'Dashboard', 
				'Dashboard', 
				'manage_network',
				'../',
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

		$menus = array(
					array (
						'parent_slug' 	=> 	'index.php',
						'page_title' 	=> 	'Sites',
						'menu_title' 	=> 	'Sites',
						'capability' 	=> 	'manage_network',
						'menu_slug'		=> 	'sites.php',
						'function' 		=> 	''
					),
					array (
						'parent_slug' 	=> 	'index.php',
						'page_title' 	=> 	'Users',
						'menu_title' 	=> 	'Users',
						'capability' 	=> 	'manage_network',
						'menu_slug'		=> 	'users.php',
						'function' 		=> 	''
					),
					array (
						'parent_slug' 	=> 	'index.php',
						'page_title' 	=> 	'Themes',
						'menu_title' 	=> 	'Themes',
						'capability' 	=> 	'manage_network',
						'menu_slug'		=> 	'themes.php',
						'function' 		=> 	''
					),
					array (
						'parent_slug' 	=> 	'index.php',
						'page_title' 	=> 	'Plugins',
						'menu_title' 	=> 	'Plugins',
						'capability' 	=> 	'manage_network',
						'menu_slug'		=> 	'plugins.php',
						'function' 		=> 	''
					),
					array (
						'parent_slug' 	=> 	'index.php',
						'page_title' 	=> 	'Settings',
						'menu_title' 	=> 	'Settings',
						'capability' 	=> 	'manage_network',
						'menu_slug'		=> 	'settings.php',
						'function' 		=> 	''
					),
					array (
						'parent_slug' 	=> 	'index.php',
						'page_title' 	=> 	'Updates',
						'menu_title' 	=> 	'Updates',
						'capability' 	=> 	'manage_network',
						'menu_slug'		=> 	'update-core.php',
						'function' 		=> 	''
					),
				);

		$network_admin_sub_menus = apply_filters(
			'mkdo_admin_add_network_admin_sub_menus_filter',
			$menus
		);

		if( is_super_admin() ) {
			foreach( $network_admin_sub_menus as $menu ) {
				add_submenu_page(
					$menu['parent_slug'],
					$menu['page_title'],
					$menu['menu_title'],
					$menu['capability'],
					$menu['menu_slug'],
					$menu['function']
				);
			}
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
				if( $menu_item[0] == 'Dashboard' || $menu_item[0] == 'Network Settings' ) {

					$menu_item[0] 	= $this->network_dash_title;
					$menu_item[6] 	= 'dashicons-admin-site';
					$network		= $menu[$key];
					unset( $menu[$key] );
					$menu[6] 	= $network; 
				}
			}
		}
	}

	/**
	 * Remove network menus menus
	 */
	public function remove_network_admin_menus() {
		
		$menus = array(
					'sites.php',
					'users.php',
					'themes.php',
					'plugins.php',
					'settings.php',
					'update-core.php',
				);

		$network_admin_menus = apply_filters(
			'mkdo_admin_remove_network_admin_menus_filter',
			$menus
		);

		if( is_super_admin() ) {
			foreach( $network_admin_menus as $menu ) {
				remove_menu_page( $menu );
			}
		}
	}

	/** 
	 * Fix the menu hierarchy
	 */
	public function correct_sub_menu_hierarchy() {

		global $submenu;
		$screen = get_current_screen();

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
	}

}
