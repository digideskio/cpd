<?php

// Exit if accessed directly
defined( 'ABSPATH' ) || exit;

if( !class_exists( 'CPD_Dashboard_Widget_User_Guide' ) ) {

/**
 * Welcome Dashboard Widget
 *
 * Display a welcome message in the dashboard
 *
 * @package    CPD
 * @subpackage CPD/admin
 * @author     Make Do <hello@makedo.in>
 */
class CPD_Dashboard_Widget_User_Guide {

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
		
		$user_guide_widget_function 		= 'render_user_guide';
		$user_guide_title 					= __('User Guide ', $this->text_domain );
		$dashboard 						    = 'dashboard';

		add_meta_box('cpd_dashboard_widget_user_guide', '<span class="cpd-dashboard-widget-title dashicons-before dashicons-editor-help"></span> ' . $user_guide_title, array( $this, $user_guide_widget_function ), $dashboard, 'normal', 'high' );
	}

	/**
	 * Render the dashboard widget
	 */
	public function render_user_guide(){
		
		$template_name 						= 	'cpd-dashboard-widget-user-guide';
		$template_path 						= 	CPD_Templates::get_template_path( $template_name );

		if( $template_path !== FALSE ) {
			include $template_path;
		}
	}
}
}