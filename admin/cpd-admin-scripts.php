<?php

// Exit if accessed directly
defined( 'ABSPATH' ) || exit;

if( !class_exists( 'CPD_Admin_Scripts' ) ) {

/**
 * Admin Scripts
 *
 * Load Admin Scripts
 *
 * @package    CPD
 * @subpackage CPD/admin
 * @author     Make Do <hello@makedo.in>
 */
class CPD_Admin_Scripts {

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
	 * Register the stylesheets for the Dashboard.
	 */
	public function enqueue_styles() {

		wp_enqueue_style( 'cpd-admin-scripts', plugin_dir_url( __FILE__ ) . 'css/cpd.css', array(), '1.0', 'all' );
	}

	/**
	 * Register the JavaScript for the dashboard.
	 */
	public function enqueue_scripts() {

		
		add_thickbox();

		if ( wp_is_mobile() ) {
			wp_enqueue_script( 'jquery-touch-punch' );
		}

		// Scripts unique to the plugin
		wp_enqueue_script( 'cpd-admin-scripts', plugin_dir_url( __FILE__ ) . 'js/cpd.js', array( 'jquery' ), '1.0', TRUE );

	}
}
}