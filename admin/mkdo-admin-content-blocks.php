<?php
/**
 * The content blocks
 *
 * @link       http://makedo.in
 * @since      1.0.0
 *
 * @package    MKDO_Admin
 * @subpackage MKDO_Admin/admin
 */

/**
 * The content blocks
 *
 * Changes the default functionality of the admin bar
 *
 * @package    MKDO_Admin
 * @subpackage MKDO_Admin/admin
 * @author     Make Do <hello@makedo.in>
 */
class MKDO_Admin_Content_Blocks{

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
	 * Add 'Comments' to the menu dashboard
	 */
	public function add_comments() {
		if ( current_user_can('moderate_comments') && get_option( 'default_comment_status' ) != 'closed' ) {
			wp_add_dashboard_widget(
				'comments_dash_widget',
				'<span class="mkdo-block-title dashicons-before dashicons-admin-comments"></span> Comments',
				array( $this, 'render_comments' )
			);
		}
	}

	/**
	 * Render 'Comments' block in menu dashboard 
	 */
	public function render_comments(){
		$mkdo_content_block_path 		= 	dirname(__FILE__) . '/partials/content-block-comments.php';
		$theme_path 					= 	get_stylesheet_directory() . '/mkdo-admin/content-block-comments.php';
		$partials_path					= 	get_stylesheet_directory() . '/partials/content-block-comments.php';

		if( file_exists( $theme_path ) ) {
			$mkdo_content_block_path = 	$theme_path;
		}
		else if( file_exists( $partials_path ) ) { 
			$mkdo_content_block_path =  	$partials_path;
		}

		include $mkdo_content_block_path;
	}

	/**
	 * Add 'Content' block to dashbaord
	 */
	public function add_content_block() {

		wp_add_dashboard_widget(
				'content_dash_widget',
				'<span class="mkdo-block-title dashicons-before dashicons-admin-page"></span> Content',
				array( $this, 'render_content_block' )
		);

	}

	/**
	 * Render 'Content' block
	 */
	public function render_content_block(){
		$mkdo_content_block_path 		= 	dirname(__FILE__) . '/partials/content-block-content.php';
		$theme_path 					= 	get_stylesheet_directory() . '/mkdo-admin/content-block-content.php';
		$partials_path					= 	get_stylesheet_directory() . '/partials/content-block-content.php';

		if( file_exists( $theme_path ) ) {
			$mkdo_content_block_path = 	$theme_path;
		}
		else if( file_exists( $partials_path ) ) { 
			$mkdo_content_block_path =  	$partials_path;
		}

		include $mkdo_content_block_path;							
	}

	/**
	 * Add 'Profile' block to dashbaord
	 */
	public function add_profile_block() {

		// wp_add_dashboard_widget(
		// 		'profile_dash_widget',
		// 		'<span class="mkdo-block-title dashicons-before dashicons-admin-users"></span> Profile',
		// 		array( $this, 'render_profile_block' )
		// );

		add_meta_box('profile_dash_widget', '<span class="mkdo-block-title dashicons-before dashicons-admin-users"></span> Profile', array( $this, 'render_profile_block' ), 'dashboard', 'side');
	}

	/**
	 * Render 'Profile' block
	 */
	public function render_profile_block(){
		$mkdo_content_block_path 		= 	dirname(__FILE__) . '/partials/content-block-profile.php';
		$theme_path 					= 	get_stylesheet_directory() . '/mkdo-admin/content-block-profile.php';
		$partials_path					= 	get_stylesheet_directory() . '/partials/content-block-profile.php';

		if( file_exists( $theme_path ) ) {
			$mkdo_content_block_path = 	$theme_path;
		}
		else if( file_exists( $partials_path ) ) { 
			$mkdo_content_block_path =  	$partials_path;
		}

		include $mkdo_content_block_path;								
	}
}