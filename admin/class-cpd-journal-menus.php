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
	 * Filter menus
	 */
	public function filter_menu_items( $menus ) {

		foreach( $menus as &$menu ) {

			if( $menu['post_type'] == 'post' ) {

				$menu['post_name'] 					= 	'Journal Entries';
				$menu['menu_name'] 					= 	'Journal Entries';
				$menu['add_to_dashboard_block'] 	= 	array(
															'dashicon' 		=> 'dashicons-book'
														);
			}
		}

		return $menus;
	}

	/**
	 * Rename network sub menus
	 */
	public function filter_network_admin_sub_menus( $menus ) {
		
		foreach( $menus as &$menu ) {
			if( $menu['page_title'] == 'Sites' ) {
				$menu['page_title'] = 'Journals';
				$menu['menu_title'] = 'Journals';
			}
		}

		return $menus;
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

		if( $user_type == 'participant' || $user_type == 'supervisor' ) {
			remove_submenu_page( 'index.php', 'my-sites.php' );
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

		global $submenu;
		$screen = get_current_screen();

		if( strpos( $screen->base, '-network' ) ) {
			if ( 
					$parent_file == 'sites.php' 		||
					$parent_file == 'users.php' 		|| 
					$parent_file == 'themes.php' 		|| 
					$parent_file == 'plugins.php' 		|| 
					$parent_file == 'settings.php' 		|| 
					$parent_file == 'update-core.php'
				) {
				$parent_file = 'index.php';
			}
		}
		
		/* return the new parent file */	
		return $parent_file;
	}
}