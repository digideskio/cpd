<?php

/**
 * The public-facing functionality of the plugin.
 *
 * @link       http://makedo.in
 * @since      1.0.0
 *
 * @package    Continuous_Professional_Development
 * @subpackage Continuous_Professional_Development/public
 */

/**
 * The public-facing functionality of the plugin.
 *
 * Defines the plugin name, version, and two examples hooks for how to
 * enqueue the dashboard-specific stylesheet and JavaScript.
 *
 * @package    Continuous_Professional_Development
 * @subpackage Continuous_Professional_Development/public
 * @author     MKDO Limited <hello@makedo.in>
 */
class MKDO_Register_Scripts_Public {

	/**
	 * The ID of this plugin.
	 *
	 * @since    1.0.0
	 * @access   private
	 * @var      string    $mkdo_admin    The ID of this plugin.
	 */
	private $mkdo_admin;

	/**
	 * The version of this plugin.
	 *
	 * @since    1.0.0
	 * @access   private
	 * @var      string    $version    The current version of this plugin.
	 */
	private $version;

	/**
	 * Initialize the class and set its properties.
	 *
	 * @since    1.0.0
	 * @var      string    $mkdo_admin       The name of the plugin.
	 * @var      string    $version    The version of this plugin.
	 */
	public function __construct( $instance, $version ) {

		$this->instance = $instance;
		$this->version 	= $version;

	}

	/**
	 * Register the stylesheets for the public-facing side of the site.
	 *
	 * @since    1.0.0
	 */
	public function enqueue_styles() {

		/**
		 * This function is provided for demonstration purposes only.
		 *
		 * An instance of this class should be passed to the run() function
		 * defined in Continuous_Professional_Development_Public_Loader as all of the hooks are defined
		 * in that particular class.
		 *
		 * The Continuous_Professional_Development_Public_Loader will then create the relationship
		 * between the defined hooks and the functions defined in this
		 * class.
		 */

		wp_enqueue_style( $this->instance, plugin_dir_url( __FILE__ ) . 'css/continuous-professional-development-public.css', array(), $this->version, 'all' );

	}

	/**
	 * Register the stylesheets for the public-facing side of the site.
	 *
	 * @since    1.0.0
	 */
	public function enqueue_scripts() {

		/**
		 * This function is provided for demonstration purposes only.
		 *
		 * An instance of this class should be passed to the run() function
		 * defined in Continuous_Professional_Development_Public_Loader as all of the hooks are defined
		 * in that particular class.
		 *
		 * The Continuous_Professional_Development_Public_Loader will then create the relationship
		 * between the defined hooks and the functions defined in this
		 * class.
		 */

		wp_enqueue_script( $this->instance, plugin_dir_url( __FILE__ ) . 'js/continuous-professional-development-public.js', array( 'jquery' ), $this->version, false );

	}

}
