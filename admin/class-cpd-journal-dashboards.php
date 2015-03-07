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
class CPD_Journal_Dashboards extends MKDO_Menu {

	/**
	 * Initialize the class and set its properties.
	 *
	 * @since    1.0.0
	 * @var      string    $instance       The name of this plugin.
	 * @var      string    $version    The version of this plugin.
	 */
	public function __construct( $instance, $version ) {
		

		add_option( 'mkdo_admin_show_mkdo_dashboard', FALSE );
		add_option( 'mkdo_admin_show_mkdo_dashboard_all', FALSE );

		$args 								= 	array(
														'page_title' 			=> 	'Dashboard',
														'menu_title' 			=> 	'Dashboard',
														'capibility' 			=> 	'read',
														'slug' 					=> 	'mkdo_dashboard',
														'function'				=> 	array( $this, 'mkdo_dashboard'),
														'icon' 					=> 	'dashicons-admin-page',
														'position' 				=> 	'1',
														'remove_menus'			=> 	array(
																						array( 
																							'menu' 			=> 		'index.php',
																							'admin_remove'	=>		TRUE,
																							'mkdo_remove'	=> 		TRUE,
																						)
																					)
													);

		parent::__construct( $instance, $version, $args );
	}

	/**
	 * Add dashboard to menu
	 */
	public function add_menu() {

		$user_id 		= get_current_user_id();
		$user_role 		= get_user_meta( $user_id , 'cpd_role', TRUE );
		$menu_slug 		= 'dashboard';
		//$this->slug 	= 'dashboard';

		if( !user_can( $user_id, 'edit_posts' ) ) {
			$menu_slug 		= 'subscriber_dashboard';
			//$this->slug 	= 'subscriber_dashboard';
		}
		else if( $user_role == 'participant' ) {
			$menu_slug 		= 'participant_dashboard';
			//$this->slug 	= 'participant_dashboard';
		}
		else if( $user_role == 'supervisor' ) {
			$menu_slug 		= 'supervisor_dashboard';
			//$this->slug 	= 'supervisor_dashboard';
		}

		add_menu_page(
			'Dashboard',
			'Dashboard',
			'read',
			$menu_slug,
			array( $this, $menu_slug ),
			'dashicons-dashboard',
			1
		);
	}

	/**
	 * Render dashboard
	 */
	public function dashboard() {
		
		$mkdo_dashboard_path 			= 	dirname(__FILE__) . '/partials/dashboard.php';
		$theme_path 					= 	get_stylesheet_directory() . '/mkdo-admin/dashboard.php';
		$partials_path					= 	get_stylesheet_directory() . '/partials/dashboard.php';

		if( file_exists( $theme_path ) ) {
			$mkdo_dashboard_path 		= 	$theme_path;
		}
		else if( file_exists( $partials_path ) ) { 
			$mkdo_dashboard_path 		=  	$partials_path;
		}

		include $mkdo_dashboard_path;	
	}

	public function subscriber_dashboard() {
		
		$mkdo_dashboard_path 			= 	dirname(__FILE__) . '/partials/subscriber_dashboard.php';
		$theme_path 					= 	get_stylesheet_directory() . '/mkdo-admin/subscriber_dashboard.php';
		$partials_path					= 	get_stylesheet_directory() . '/partials/subscriber_dashboard.php';

		if( file_exists( $theme_path ) ) {
			$mkdo_dashboard_path 		= 	$theme_path;
		}
		else if( file_exists( $partials_path ) ) { 
			$mkdo_dashboard_path 		=  	$partials_path;
		}

		include $mkdo_dashboard_path;
			
	}

	public function participant_dashboard() {
		
		$mkdo_dashboard_path 			= 	dirname(__FILE__) . '/partials/participant_dashboard.php';
		$theme_path 					= 	get_stylesheet_directory() . '/mkdo-admin/participant_dashboard.php';
		$partials_path					= 	get_stylesheet_directory() . '/partials/participant_dashboard.php';

		if( file_exists( $theme_path ) ) {
			$mkdo_dashboard_path 		= 	$theme_path;
		}
		else if( file_exists( $partials_path ) ) { 
			$mkdo_dashboard_path 		=  	$partials_path;
		}

		include $mkdo_dashboard_path;
			
	}

	public function supervisor_dashboard() {
		
		$mkdo_dashboard_path 			= 	dirname(__FILE__) . '/partials/supervisor_dashboard.php';
		$theme_path 					= 	get_stylesheet_directory() . '/mkdo-admin/supervisor_dashboard.php';
		$partials_path					= 	get_stylesheet_directory() . '/partials/supervisor_dashboard.php';

		if( file_exists( $theme_path ) ) {
			$mkdo_dashboard_path 		= 	$theme_path;
		}
		else if( file_exists( $partials_path ) ) { 
			$mkdo_dashboard_path 		=  	$partials_path;
		}

		include $mkdo_dashboard_path;
			
	}

	public function network_dashboard() {
		
		$mkdo_dashboard_path 			= 	dirname(__FILE__) . '/partials/network_dashboard.php';
		$theme_path 					= 	get_stylesheet_directory() . '/mkdo-admin/network_dashboard.php';
		$partials_path					= 	get_stylesheet_directory() . '/partials/network_dashboard.php';

		if( file_exists( $theme_path ) ) {
			$mkdo_dashboard_path 		= 	$theme_path;
		}
		else if( file_exists( $partials_path ) ) { 
			$mkdo_dashboard_path 		=  	$partials_path;
		}

		include $mkdo_dashboard_path;
			
	}

	/**
	 * Filter the MU dashboad actions
	 * 
	 * @param  string 	$actions 	The actions to be filtered
	 * @return string 	$actions 	The actions to be filtered
	 */
	public function filter_dashboard_actions( $actions ) {

		$user_id 		= get_current_user_id();
		$user_role 		= get_user_meta( $user_id , 'cpd_role', TRUE );
		$menu_slug 		= 'dashboard';

		if( !user_can( $user_id, 'edit_posts' ) ) {
			$menu_slug 		= 'subscriber_dashboard';
		}
		else if( $user_role == 'participant' ) {
			$menu_slug 		= 'participant_dashboard';
		}
		else if( $user_role == 'supervisor' ) {
			$menu_slug 		= 'supervisor_dashboard';
		}

		$actions = str_replace( 'wp-admin/', 'wp-admin/admin.php?page=' . $menu_slug, $actions );
		return $actions;
	}

	/**
	 * Redirect users to dashboard
	 */
	public function login_redirect( $redirect_to, $request_redirect_to, $user ) {
	
		$user_role 		= get_user_meta( $user , 'cpd_role', TRUE );
		$menu_slug 		= 'dashboard';

		if( !user_can( 'edit_posts', $user ) ) {
			$menu_slug 		= 'subscriber_dashboard';
		}
		else if( $user_role == 'participant' ) {
			$menu_slug 		= 'participant_dashboard';
		}
		else if( $user_role == 'supervisor' ) {
			$menu_slug 		= 'supervisor_dashboard';
		}

		if( $user && is_object( $user ) && !is_wp_error( $user ) && is_a( $user, 'WP_User' ) ) {

			$redirect_to = apply_filters( 'mkdo_login_redirect', admin_url( 'admin.php?page=' . $menu_slug ) );

			// if( !get_user_meta( $user->ID, 'mkdo_user', TRUE ) ) {
			
			// 	$redirect_to = apply_filters( 'mkdo_login_redirect', admin_url( 'admin.php?page=mkdo_dashboard' ) );
				
			// } else {
				
			// 	$redirect_to = apply_filters( 'mkdo_super_user_login_redirect', admin_url() );
			// }
		}

		return $redirect_to;

	}

	/**
	 * Rename page titles
	 * 
	 * @param  string 	$translation 	The translated text
	 * @param  string 	$text        	The text to be translated
	 * @param  string 	$domain      	The domain of the text we are translating
	 * @return string 	$translation 	The translated text
	 */
	public function rename_page_titles( $translation, $text, $domain )
	{
	    if ( $domain == 'default' && $text == 'Sites' )
		{
			remove_filter( 'gettext', 'rename_sites_page' );
			return 'Journals';
		}

		if ( $domain == 'default' && $text == 'My Sites' )
		{
			remove_filter( 'gettext', 'rename_sites_page' );
			return 'My Journals';
		}

		if ( $domain == 'default' && $text == 'Primary Site' )
		{
			remove_filter( 'gettext', 'rename_sites_page' );
			return 'Primary Journal';
		}

		if ( $domain == 'default' && $text == 'Posts' )
		{
			remove_filter( 'gettext', 'rename_sites_page' );
			return 'Journal Journal Entries';
		}

		return $translation;
	}

	function rename_post_object() {
		global $wp_post_types;
		$labels = &$wp_post_types['post']->labels;
		$labels->name = 'Journal Entries';
		$labels->singular_name = 'Journal Entries';
		$labels->add_new = 'Add Journal Entry';
		$labels->add_new_item = 'Add Journal Entries';
		$labels->edit_item = 'Edit Journal Entries';
		$labels->new_item = 'Journal Entries';
		$labels->view_item = 'View Journal Entries';
		$labels->search_items = 'Search Journal Entries';
		$labels->not_found = 'No Journal Entries found';
		$labels->not_found_in_trash = 'No Journal Entries found in Trash';
		$labels->all_items = 'All Journal Entries';
		$labels->menu_name = 'Journal Entries';
		$labels->name_admin_bar = 'Journal Entries';
	}

	public function force_network_color_scheme( $color_scheme ) {
		
		$screen = get_current_screen();

		$current_user 	= wp_get_current_user();
		$roles 			= $current_user->roles;

		//$color_scheme 	= 'midnight';

		if( in_array( 'supervisor', 		$roles) ) {
			$color_scheme 	= 'ectoplasm';
		}
		if( in_array( 'participant', 		$roles) ) {
			$color_scheme 	= 'ocean';
		}

		if( strpos( $screen->base, '-network') ) {
			$color_scheme 	= 'sunrise';
		}

		return $color_scheme;
	}
}