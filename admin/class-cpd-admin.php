<?php
/**
 * The dashboard-specific functionality of the plugin.
 *
 * @link       http://makedo.in
 * @since      1.0.0
 *
 * @package    CPD
 * @subpackage CPD/admin
 */

/**
 * The dashboard-specific functionality of the plugin.
 *
 * @package    CPD
 * @subpackage CPD/admin
 * @author     Make Do <hello@makedo.in>
 */
class CPD_Admin extends MKDO_Class {

	/**
	 * Initialize the class and set its properties.
	 *
	 * @since    1.0.0
	 * @var      string    $instance       The name of this plugin.
	 * @var      string    $version    The version of this plugin.
	 */
	public function __construct( $instance, $version ) {
		parent::__construct( $instance, $version );

		
	}

	public function add_admin_menus() {

		if( is_super_admin() ) {

			add_menu_page( 
				'Network', 
				'Network', 
				'manage_network',
				'network/index.php',
				'',
				'dashicons-admin-site',
				2
			);

			add_menu_page( 
				'Sites', 
				'Sites', 
				'manage_network', 
				'network/sites.php',
				'',
				'dashicons-admin-network',
				3
			);


		}
		else {
			add_menu_page( 
				'My Sites', 
				'My Sites', 
				'edit_pages', 
				'my-sites.php',
				'',
				'dashicons-admin-network',
				2
			);
		}
	}

	public function add_network_admin_menus() {

		if( is_super_admin() ) {

			global $menu;

			foreach( $menu as $key=>&$menu_item ) {
				if( $menu_item[0] == 'Dashboard' ) {
					$menu_item[0] = 'Network';
					$menu_item[6] = 'dashicons-admin-site';
				}

				if( $menu_item[0] == 'Sites' ) {
					$tmp = $menu[4];
					$menu[4] = $menu[$key];
					$menu[5] = $tmp;
				}
			}

			add_menu_page( 
				'Dashboard', 
				'Dashboard', 
				'manage_network',
				'../admin.php?page=mkdo_dashboard',
				'',
				'dashicons-dashboard',
				0
			);
			
		}
	}

	public function multisite_my_sites_use_custom_dash( $actions ) {
		$actions = str_replace( 'wp-admin/', 'wp-admin/admin.php?page=mkdo_dashboard', $actions );
		return $actions;
	}

	
}