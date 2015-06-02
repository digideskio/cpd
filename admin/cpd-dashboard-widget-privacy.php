<?php

// Exit if accessed directly
defined( 'ABSPATH' ) || exit;

if( !class_exists( 'CPD_Dashboard_Widget_Privacy' ) ) {

/**
 * Welcome Dashboard Widget
 *
 * Display a welcome message in the dashboard
 *
 * @package    CPD
 * @subpackage CPD/admin
 * @author     Make Do <hello@makedo.in>
 */
class CPD_Dashboard_Widget_Privacy {

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
		
		add_meta_box('cpd_dashboard_widget_welcome', '<span class="cpd-dashboard-widget-title dashicons-before dashicons-shield"></span> ' . 'Privacy', array( $this, 'render_dashboard_widget' ), 'dashboard', 'side', 'high' );
	}

	/**
	 * Render the dashboard widget
	 */
	public function render_dashboard_widget(){
		
		$template_name 						= 	'cpd-dashboard-widget-privacy';
		$template_path 						= 	CPD_Templates::get_template_path( $template_name );

		if( $template_path !== FALSE ) {
			include $template_path;
		}
	}
}
}