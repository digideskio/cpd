<?php

/**
 * The public-facing functionality of the plugin.
 *
 * @link       http://makedo.in
 * @since      2.0.0
 *
 * @package    CPD
 * @subpackage CPD/public
 */

// Exit if accessed directly
defined( 'ABSPATH' ) || exit;

if( !class_exists( 'CPD_Public_Scripts' ) ) {

/**
 * The admin-specific functionality of the plugin.
 *
 * Defines the admin settings
 *
 * @package    CPD
 * @subpackage CPD/admin
 * @author     Make Do <hello@makedo.in>
 */
class CPD_Public_Scripts {

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
	 **/
	public function set_text_domain( $text_domain ) { 
		$this->text_domain = $text_domain;
	}

	/**
	 * Register the stylesheets for the public-facing side of the site.
	 */
	public function enqueue_styles() {
		wp_enqueue_style( 'cpd-admin-public', plugin_dir_url( __FILE__ ) . 'css/cpd.css', array(), '1.0', 'all' );
	}

	/**
	 * Register the stylesheets for the public-facing side of the site.
	 */
	public function enqueue_scripts() {
		wp_enqueue_script( 'cpd-admin-public', plugin_dir_url( __FILE__ ) . 'js/cpd.js', array( 'jquery' ), '1.0', false );
	}
}
}
