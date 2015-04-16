<?php
/**
 * The admin bar
 *
 * @link       http://makedo.in
 * @since      1.0.0
 *
 * @package    MKDO_Admin
 * @subpackage MKDO_Admin/admin
 */

/**
 * The admin bar
 *
 * Changes the default functionality of the admin bar
 *
 * @package    MKDO_Admin
 * @subpackage MKDO_Admin/admin
 * @author     Make Do <hello@makedo.in>
 */
class MKDO_Admin_Bar{

	/**
	 * Initialize the class and set its properties.
	 *
	 * @since    1.0.0
	 * @var      string    $instance       	The name of this plugin.
	 * @var      string    $version    		The version of this plugin.
	 */
	public function __construct() {
		
	}

	/**
	 * Remove the admin bar for non admins
	 */
	public function remove_admin_bar_for_non_admins() {
		if (!current_user_can('manage_options')) {
			add_filter( 'show_admin_bar', '__return_false', 99 );
		}
	}

	/**
	 * Remove the admin bar for everyone
	 */
	public function remove_admin_bar() {
		add_filter( 'show_admin_bar', '__return_false', 99 );
	}

	/**
	 * Only let admins into the dashboard
	 */
	public function restrict_admin_access() {
		if(!( defined('DOING_AJAX') && DOING_AJAX )) {
			if (!current_user_can('manage_options')) {
				wp_redirect(home_url()); exit;
			}
		}
	}

	/**
	 * Remove 'howdy' from the admin bar
	 */
	public function remove_howdy() {

		global $wp_admin_bar;

		$profile 	=	$wp_admin_bar->get_node('my-account');
		$title		= 	str_replace( 'Howdy,', '', $profile->title );

		$wp_admin_bar->add_node( 
			array(
				'id' => 'my-account',
				'title' => $title,
    		) 
    	);
	}

	/**
	 * Remove 'my sites' from the admin bar
	 */
	public function remove_my_sites() {
		global $wp_admin_bar;

		$wp_admin_bar->remove_menu( 'my-sites' );
	}

	/**
	 * Remove 'wp logo' from the admin bar
	 */
	public function remove_wp_logo() {
		global $wp_admin_bar;
		
		$wp_admin_bar->remove_menu( 'wp-logo' );
	}

	/**
	 * Remove 'site name' from the admin bar
	 */
	public function remove_site_name() {
		global $wp_admin_bar;

		$wp_admin_bar->remove_menu( 'site-name' );
	}

	/**
	 * Remove 'wp seo' from the admin bar
	 */
	public function remove_wp_seo_menu() {
		global $wp_admin_bar;
		
		$wp_admin_bar->remove_menu( 'wpseo-menu' );
	}

	/**
	 * Remove 'comments' from the admin bar
	 */
	public function remove_comments() {
		global $wp_admin_bar;
		
		$wp_admin_bar->remove_menu( 'comments' );
	}

	/**
	 * Remove '+New' from the admin bar
	 */
	public function remove_new_content() {
		global $wp_admin_bar;

		$wp_admin_bar->remove_menu('new-content');
	}

	/**
	 * Remove 'updates' from the admin bar
	 */
	public function remove_updates() {
		global $wp_admin_bar;
		
		if( ! MKDO_Helper_User::is_mkdo_user() ) {
			$wp_admin_bar->remove_menu('updates');
		}
	}

	/**
	 * Remove 'search' from the admin bar
	 */
	public function remove_search() {
		global $wp_admin_bar;
		
		$wp_admin_bar->remove_menu('search');
	}

	/**
	 * Add custom logo to the admin bar
	 */
	public function custom_admin_logo() {
		
		$custom_admin_logo_css_path 	= 	dirname(__FILE__) . '/partials/custom-admin-logo.php';
		$theme_path 					= 	get_stylesheet_directory() . '/mkdo-admin/custom-admin-logo.php';
		$partials_path					= 	get_stylesheet_directory() . '/partials/custom-admin-logo.php';

		if( file_exists( $theme_path ) ) {
			$custom_admin_logo_css_path = 	$theme_path;
		}
		else if( file_exists( $partials_path ) ) { 
			$custom_admin_logo_css_path =  	$partials_path;
		}

		include $custom_admin_logo_css_path;
	}

	/**
	 * Add custom menu switcher to the admin bar
	 */
	public function add_menu_switcher() {

		global $wp_admin_bar;

		if( is_admin() ) {

			$site_link = home_url();
			$link_name = 'View Site';

		} else {

			$site_link = admin_url( 'index.php?page=mkdo_content_menu' );
			$link_name = 'Site Admin';

		}

		$wp_admin_bar->add_menu(
			array(
				'id' => 'mkdo_menu_switcher',
				'title' => $link_name,
				'href' => $site_link
			)
		);
	}
}