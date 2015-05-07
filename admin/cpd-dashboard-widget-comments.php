<?php
/**
 * The content blocks
 *
 * @link       http://makedo.in
 * @since      2.0.0
 *
 * @package    MKDO_Admin
 * @subpackage MKDO_Admin/admin
 */

// Exit if accessed directly
defined( 'ABSPATH' ) || exit;

if( !class_exists( 'CPD_Dashboard_Widget_Comments' ) ) {

/**
 * The content blocks
 *
 * Changes the default functionality of the admin bar
 *
 * @package    MKDO_Admin
 * @subpackage MKDO_Admin/admin
 * @author     Make Do <hello@makedo.in>
 */
class CPD_Dashboard_Widget_Comments {

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
	 * @since    2.0.0
	 * @var      string    $instance       The name of this plugin.
	 * @var      string    $version    The version of this plugin.
	 */
	public function __construct() {
		
	}

	/**
	 * Initialize the class and set its properties.
	 *
	 * @var      string    $text_domain       The text domain of the plugin.
	 *
	 * @since    2.0.0
	 **/
	public function set_text_domain( $text_domain ) { 
		$this->text_domain = $text_domain;
	}

	/**
	 * Add the dashboard widget
	 *
	 * @since    2.0.0
	 */
	public function add_dashboard_widget() {
		
		if ( current_user_can('moderate_comments') && get_option( 'default_comment_status' ) != 'closed' ) {
			wp_add_dashboard_widget(
				'cpd_dashboard_widget_comments',
				'<span class="cpd-dashboard-widget-title dashicons-before dashicons-admin-comments"></span> Comments',
				array( $this, 'render_dashboard_widget' )
			);
		}
	}

	/**
	 * Render the dashboard widget
	 *
	 * @since    2.0.0
	 */
	public function render_dashboard_widget(){
		
		$template_name 						= 	'cpd-dashboard-widget-comments';
		$template_path 						= 	CPD_Templates::get_template_path( $template_name );

		if( $template_path !== FALSE ) {
			include $template_path;
		}
	}
}
}