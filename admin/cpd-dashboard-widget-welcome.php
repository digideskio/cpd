<?php

// Exit if accessed directly
defined( 'ABSPATH' ) || exit;

if( !class_exists( 'CPD_Dashboard_Widget_Welcome' ) ) {

/**
 * Welcome Dashboard Widget
 *
 * Display a welcome message in the dashboard
 *
 * @package    CPD
 * @subpackage CPD/admin
 * @author     Make Do <hello@makedo.in>
 */
class CPD_Dashboard_Widget_Welcome {

	private static $instance = null;
	private $text_domain;

	/**
	 * Creates or returns an instance of this class.
	 */
	public static function get_instance() {
		/**
		 * If an instance hasn't been created and set to $instance create an instance 
		 * and set it to $instance.
		 */
		if ( null == self::$instance ) {
			self::$instance = new self;
		}
		return self::$instance;
	}


	/**
	 * Initialize the class and set its properties.
	 * 
	 * @param      string    $instance       The name of this plugin.
	 * @param      string    $version    The version of this plugin.
	 */
	public function __construct() {
		
	}

	/**
	 * Set the text domain
	 *
	 * @param      string    $text_domain       The text domain of the plugin.
	 */
	public function set_text_domain( $text_domain ) { 
		$this->text_domain = $text_domain;
	}

	/**
	 * Add the dashboard widget
	 */
	public function add_dashboard_widget() {
		
		$welcome_widget_function 		= 'render_welcome_subscriber';
		$welcome_title 					= 'Welcome to Aspire CPD ';
		$dashboard 						= 'dashboard';

		$current_user 					= wp_get_current_user();
		$roles 							= $current_user->roles;
		$is_elevated_user 				= get_user_meta( $current_user->ID, 'elevated_user', TRUE ) == '1';

		if( is_network_admin() ) {
			$dashboard 					= 'dashboard-network';
			$welcome_widget_function 	= 'render_welcome_network';
			$welcome_title 				= 'Welcome to the CPD Network Settings ';
		}
		else if( is_super_admin() || $is_elevated_user ) {
			$welcome_widget_function 	= 'render_welcome_admin';
		}
		else if ( user_can( $current_user, 'subscriber' ) ) {
			$welcome_widget_function 	= 'render_welcome_subscriber';
		}
		else if( in_array( 'supervisor', $roles ) ) {
			$welcome_widget_function 	= 'render_welcome_supervisor';
		}
		else if( in_array( 'participant', $roles ) ) {
			$welcome_widget_function 	= 'render_welcome_participant';
		}

		add_meta_box('cpd_dashboard_widget_welcome', '<span class="cpd-dashboard-widget-title dashicons-before dashicons-book"></span> ' . $welcome_title, array( $this, $welcome_widget_function ), $dashboard, 'normal', 'high' );
	}

	/**
	 * Render the dashboard widget
	 */
	public function render_welcome_participant(){
		
		$template_name 						= 	'cpd-dashboard-widget-welcome-participant';
		$template_path 						= 	CPD_Templates::get_template_path( $template_name );

		if( $template_path !== FALSE ) {
			include $template_path;
		}
	}

	/**
	 * Render the dashboard widget
	 */
	public function render_welcome_admin(){
		
		$template_name 						= 	'cpd-dashboard-widget-welcome-admin';
		$template_path 						= 	CPD_Templates::get_template_path( $template_name );

		if( $template_path !== FALSE ) {
			include $template_path;
		}
	}

	/**
	 * Render the dashboard widget
	 */
	public function render_welcome_supervisor(){
		
		$template_name 						= 	'cpd-dashboard-widget-welcome-supervisor';
		$template_path 						= 	CPD_Templates::get_template_path( $template_name );

		if( $template_path !== FALSE ) {
			include $template_path;
		}
	}

	/**
	 * Render the dashboard widget
	 */
	public function render_welcome_subscriber(){
		
		$template_name 						= 	'cpd-dashboard-widget-welcome-subscriber';
		$template_path 						= 	CPD_Templates::get_template_path( $template_name );

		if( $template_path !== FALSE ) {
			include $template_path;
		}
	}

	/**
	 * Render the dashboard widget
	 */
	public function render_welcome_network(){
		$template_name 						= 	'cpd-dashboard-widget-welcome-network';
		$template_path 						= 	CPD_Templates::get_template_path( $template_name );

		if( $template_path !== FALSE ) {
			include $template_path;
		}
	}
}
}