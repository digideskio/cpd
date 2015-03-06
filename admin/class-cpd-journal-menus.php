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
	}

	/**
	 * Add admin menus
	 */
	public function add_admin_menus() {

		// If the user is a super admin (network manager)
		if( is_super_admin() ) {

			add_menu_page( 
				'Network', 
				'Network', 
				'manage_network',
				'network/index.php',
				'',
				'dashicons-admin-site',
				0
			);

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
				3
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
				if( $menu_item[0] == 'Dashboard' ) {

					$menu_item[0] 	= 'Network';
					$menu_item[6] 	= 'dashicons-admin-site';
				}

				// It may have already been renamed
				if( $menu_item[0] == 'Sites' || $menu_item[0] == 'Journals' ) {

					$menu_item[0] 	= 'Journals';
					$menu_item[6] 	= 'dashicons-book-alt';

					// Swap it with position 4 (separator)
					$separator		= $menu[4];
					$menu[4] 		= $menu[$key];
					$menu[$key] 	= $separator;
				}
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

}