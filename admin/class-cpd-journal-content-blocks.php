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
class CPD_Journal_Content_Blocks extends MKDO_Class {

	/**
	 * Initialize the class and set its properties.
	 *
	 * @since    1.0.0
	 * @var      string    $instance       	The name of this plugin.
	 * @var      string    $version    		The version of this plugin.
	 */
	public function __construct( $instance, $version ) {

		parent::__construct( $instance, $version );
	}

	/**
	 * Add 'Comments' to the menu dashboard
	 */
	public function add_welcome_content_block() {
		
		$welcome_widget_function 		= 'render_welcome_subscriber';
		$welcome_title 					= 'Welcome to CPD Journals ';

		$current_user 					= wp_get_current_user();
		$roles 							= $current_user->roles;

		
		if( is_super_admin() || MKDO_Helper_User::is_mkdo_user() ) {
			$welcome_widget_function 	= 'render_welcome_admin';
			// $welcome_title 				= 'Welcome Administrator';
		}
		else if ( user_can( $current_user, 'subscriber' ) ) {
			$welcome_widget_function 	= 'render_welcome_subscriber';
			// $welcome_title 				= 'Welcome Administrator';
		}
		else if( in_array( 'supervisor', $roles ) ) {
			$welcome_widget_function 	= 'render_welcome_supervisor';
			// $welcome_title 				= 'Welcome Supervisor';
		}
		else if( in_array( 'participant', $roles ) ) {
			$welcome_widget_function 	= 'render_welcome_participant';
			// $welcome_title 				= 'Welcome Participant';
		}

		add_meta_box('welcome_widget', '<span class="mkdo-block-title dashicons-before dashicons-book"></span> ' . $welcome_title, array( $this, $welcome_widget_function ), 'dashboard', 'normal', 'high' );
	}

	/**
	 * Render 'Comments' block in menu dashboard 
	 */
	public function render_welcome_participant(){
		$mkdo_content_block_path 		= 	dirname(__FILE__) . '/partials/content-block-participant.php';
		$theme_path 					= 	get_stylesheet_directory() . '/cpd/content-block-participant.php';
		$partials_sub_path 				= 	get_stylesheet_directory() . '/partials/cpd/content-block-participant.php';
		$partials_path					= 	get_stylesheet_directory() . '/partials/content-block-participant.php';

		if( file_exists( $theme_path ) ) {
			$mkdo_content_block_path = 	$theme_path;
		}
		else if( file_exists( $partials_sub_path ) ) { 
			$mkdo_content_block_path =  	$partials_sub_path;
		}
		else if( file_exists( $partials_path ) ) { 
			$mkdo_content_block_path =  	$partials_path;
		}

		include $mkdo_content_block_path;
	}

	public function render_welcome_admin(){
		$mkdo_content_block_path 		= 	dirname(__FILE__) . '/partials/content-block-admin.php';
		$theme_path 					= 	get_stylesheet_directory() . '/cpd/content-block-admin.php';
		$partials_sub_path 				= 	get_stylesheet_directory() . '/partials/cpd/content-block-admin.php';
		$partials_path					= 	get_stylesheet_directory() . '/partials/content-block-admin.php';

		if( file_exists( $theme_path ) ) {
			$mkdo_content_block_path = 	$theme_path;
		}
		else if( file_exists( $partials_sub_path ) ) { 
			$mkdo_content_block_path =  	$partials_sub_path;
		}
		else if( file_exists( $partials_path ) ) { 
			$mkdo_content_block_path =  	$partials_path;
		}

		include $mkdo_content_block_path;
	}

	public function render_welcome_supervisor(){
		$mkdo_content_block_path 		= 	dirname(__FILE__) . '/partials/content-block-supervisor.php';
		$theme_path 					= 	get_stylesheet_directory() . '/cpd/content-block-supervisor.php';
		$partials_sub_path 				= 	get_stylesheet_directory() . '/partials/cpd/content-block-supervisor.php';
		$partials_path					= 	get_stylesheet_directory() . '/partials/content-block-supervisor.php';

		if( file_exists( $theme_path ) ) {
			$mkdo_content_block_path = 	$theme_path;
		}
		else if( file_exists( $partials_sub_path ) ) { 
			$mkdo_content_block_path =  	$partials_sub_path;
		}
		else if( file_exists( $partials_path ) ) { 
			$mkdo_content_block_path =  	$partials_path;
		}

		include $mkdo_content_block_path;
	}

	public function render_welcome_subscriber(){
		$mkdo_content_block_path 		= 	dirname(__FILE__) . '/partials/content-block-subscriber.php';
		$theme_path 					= 	get_stylesheet_directory() . '/cpd/content-block-subscriber.php';
		$partials_sub_path 				= 	get_stylesheet_directory() . '/partials/cpd/content-block-subscriber.php';
		$partials_path					= 	get_stylesheet_directory() . '/partials/content-block-subscriber.php';

		if( file_exists( $theme_path ) ) {
			$mkdo_content_block_path = 	$theme_path;
		}
		else if( file_exists( $partials_sub_path ) ) { 
			$mkdo_content_block_path =  	$partials_sub_path;
		}
		else if( file_exists( $partials_path ) ) { 
			$mkdo_content_block_path =  	$partials_path;
		}

		include $mkdo_content_block_path;
	}
}