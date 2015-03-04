<?php

/**
 * The plugin bootstrap file
 *
 * This file is read by WordPress to generate the plugin information in the plugin
 * Dashboard. This file also includes all of the dependencies used by the plugin,
 * registers the activation and deactivation functions, and defines a function
 * that starts the plugin.
 *
 * @link              http://makedo.in
 * @since             1.0.0
 * @package           Continuous_Professional_Development
 *
 * @wordpress-plugin
 * Plugin Name:       Continuous Professional Development
 * Plugin URI:        https://github.com/mkdo/continuous-professional-development
 * Description:       A plugin to clean up the WordPress dashboard
 * Version:           1.0.0
 * Author:            MKDO Ltd. (Make Do)
 * Author URI:        http://makedo.in
 * License:           GPL-2.0+
 * License URI:       http://www.gnu.org/licenses/gpl-2.0.txt
 * Text Domain:       continuous-professional-development
 * Domain Path:       /languages
 */

// If this file is called directly, abort.
if ( ! defined( 'WPINC' ) ) {
	die;
}

// Auto Update From GitHub
if( !class_exists( 'WP_GitHub_Updater' ) ) {
	include_once plugin_dir_path( __FILE__ ) . 'vendor/WordPress-GitHub-Plugin-Updater/updater.php';
}
if ( is_admin() ) { // note the use of is_admin() to double check that this is happening in the admin
	$config = array(
		'slug' 					=> plugin_basename(__FILE__), // this is the slug of your plugin
		'proper_folder_name' 	=> 'plugin-name', // this is the name of the folder your plugin lives in
		'api_url' 				=> 'https://api.github.com/repos/mkdo/continuous-professional-development', // the GitHub API url of your GitHub repo
		'raw_url' 				=> 'https://raw.github.com/mkdo/continuous-professional-development/master', // the GitHub raw url of your GitHub repo
		'github_url' 			=> 'https://github.com/mkdo/continuous-professional-development', // the GitHub url of your GitHub repo
		'zip_url' 				=> 'https://github.com/mkdo/continuous-professional-development/zipball/master', // the zip url of the GitHub repo
		'sslverify' 			=> true, // whether WP should check the validity of the SSL cert when getting an update, see https://github.com/jkudish/WordPress-GitHub-Plugin-Updater/issues/2 and https://github.com/jkudish/WordPress-GitHub-Plugin-Updater/issues/4 for details
		'requires' 				=> '4.1.1', // which version of WordPress does your plugin require?
		'tested' 				=> '4.1.1 ', // which version of WordPress is your plugin tested up to?
		'readme' 				=> 'README.md', // which file to use as the readme for the version number
		'access_token' 			=> '', // Access private repositories by authorizing under Appearance > GitHub Updates when this example plugin is installed
    );
    new WP_GitHub_Updater( $config );
}

// Load MKDO Dependancies
require_once plugin_dir_path( __FILE__ ) . 'vendor/mkdo-dependencies/mkdo-dependencies.php';

/**
 * Continuous_Professional_Development
 *
 * This is the class that orchestrates the entire plugin
 *
 * @since             	1.0.0
 */
class Continuous_Professional_Development extends MKDO_Class {

	/**
	 * The loader that's responsible for maintaining and registering all hooks that power
	 * the plugin.
	 *
	 * @since    1.0.0
	 * @access   protected
	 * @var      Continuous_Professional_Development_Loader    $loader    Maintains and registers all hooks for the plugin.
	 */
	protected $loader;

	/**
	 * The unique identifier of this plugin.
	 *
	 * @since    1.0.0
	 * @access   protected
	 * @var      string    $continuous_professional_development    The string used to uniquely identify this plugin.
	 */
	protected $continuous_professional_development;

	/**
	 * The current version of the plugin.
	 *
	 * @since    1.0.0
	 * @access   protected
	 * @var      string    $version    The current version of the plugin.
	 */
	protected $version;

	/**
	 * Define the core functionality of the plugin.
	 *
	 * Set the plugin name and the plugin version that can be used throughout the plugin.
	 * Load the dependencies, define the locale, and set the hooks for the Dashboard and
	 * the public-facing side of the site.
	 *
	 * @since    1.0.0
	 */
	public function __construct( $instance, $version ) {

		parent::__construct( $instance, $version );

		$this->load_dependencies();
		$this->set_locale();
		$this->define_admin_hooks();
		$this->define_public_hooks();

	}

	/**
	 * Load the required dependencies for this plugin.
	 *
	 * Include the following files that make up the plugin:
	 *
	 * - Continuous_Professional_Development_Loader. Orchestrates the hooks of the plugin.
	 * - Continuous_Professional_Development_i18n. Defines internationalization functionality.
	 * - Continuous_Professional_Development_Admin. Defines all hooks for the dashboard.
	 * - Continuous_Professional_Development_Public. Defines all hooks for the public side of the site.
	 *
	 * Create an instance of the loader which will be used to register the hooks
	 * with WordPress.
	 *
	 * @since    1.0.0
	 * @access   private
	 */
	private function load_dependencies() {

		// Vendor
		require_once plugin_dir_path( __FILE__ ) . 'vendor/mkdo-admin/index.php';

		// Register Scripts
		require_once plugin_dir_path( __FILE__ ) . 'admin/class-register-scripts-admin.php';
		require_once plugin_dir_path( __FILE__ ) . 'public/class-register-scripts-public.php';

		// Dashboard		

		// Content Blocks

		// Profiles

		// Custom post types

		// Meta boxes

		// Taxonomies

		// Columns

		// Status

		$this->loader = new MKDO_Loader();
	}

	/**
	 * Define the locale for this plugin for internationalization.
	 *
	 * Uses the Continuous_Professional_Development_i18n class in order to set the domain and to register the hook
	 * with WordPress.
	 *
	 * @since    1.0.0
	 * @access   private
	 */
	private function set_locale() {

		$plugin_i18n = new MKDO_i18n();
		$plugin_i18n->set_domain( $this->get_instance() );

		$this->loader->add_action( 'plugins_loaded', $plugin_i18n, 'load_plugin_textdomain' );

	}

	/**
	 * Register all of the hooks related to the dashboard functionality
	 * of the plugin.
	 *
	 * @since    1.0.0
	 * @access   private
	 */
	private function define_admin_hooks() {

		/** 
		 * Classes
		 *
		 * Load the admin classes used in this Plugin
		 */
		
		$admin_scripts 			= new MKDO_Register_Scripts_Admin			( $this->get_instance(), $this->get_version() );

		/** 
		 * Scripts
		 */
		
		// Enqueue the styles
		if( get_option( 'continuous_professional_development_enqueue_styles', TRUE ) ) { 
			$this->loader->add_action( 'admin_enqueue_scripts', $admin_scripts, 'enqueue_styles' );
		}

		// Enqueue the scripts
		if( get_option( 'continuous_professional_development_enqueue_scripts', TRUE ) ) { 
			$this->loader->add_action( 'admin_enqueue_scripts', $admin_scripts, 'enqueue_scripts' );
		}
	}

	/**
	 * Register all of the hooks related to the public-facing functionality
	 * of the plugin.
	 *
	 * @since    1.0.0
	 * @access   private
	 */
	private function define_public_hooks() {

		$public_scripts = new MKDO_Register_Scripts_Public( $this->get_instance(), $this->get_version() );

		// Enqueue the styles
		if( get_option( 'continuous_professional_development_enqueue_styles_public', FALSE ) ) { 
			$this->loader->add_action( 'wp_enqueue_scripts', $public_scripts, 'enqueue_styles' );
		}

		// Enqueue the scripts
		if( get_option( 'continuous_professional_development_enqueue_scripts_public', FALSE ) ) { 
			$this->loader->add_action( 'wp_enqueue_scripts', $public_scripts, 'enqueue_scripts' );
		}

	}

	/**
	 * Run the loader to execute all of the hooks with WordPress.
	 *
	 * @since    1.0.0
	 */
	public function run() {
		$this->loader->run();
	}

	/**
	 * The name of the plugin used to uniquely identify it within the context of
	 * WordPress and to define internationalization functionality.
	 *
	 * @since     1.0.0
	 * @return    string    The name of the plugin.
	 */
	public function get_instance() {
		return $this->instance;
	}

	/**
	 * The reference to the class that orchestrates the hooks with the plugin.
	 *
	 * @since     1.0.0
	 * @return    Continuous_Professional_Development_Loader    Orchestrates the hooks of the plugin.
	 */
	public function get_loader() {
		return $this->loader;
	}

	/**
	 * Retrieve the version number of the plugin.
	 *
	 * @since     1.0.0
	 * @return    string    The version number of the plugin.
	 */
	public function get_version() {
		return $this->version;
	}

	/**
	 * Fired during plugin activation.
	 *
	 * All code necessary to run during the plugin's activation.
	 *
	 * @since      1.0.0
	 */
	public static function activate() {

		// Get the current users, user ID
		$mkdo_user_id = get_current_user_id();
	
		// Make the user a mkdo super user
		update_usermeta( $mkdo_user_id, 'mkdo_user', 1 );
	
		// Set option to initialise the redirect
		add_option( 'mkdo_activation_redirect', TRUE );
	}

	/**
	 * Fired during plugin deactivation.
	 *
	 * All code necessary to run during the plugin's deactivation.
	 *
	 * @since      1.0.0
	 */
	public static function deactivate() {

	}

}

/**
 * The code that runs during plugin activation.
 * This action is documented in includes/class-continuous-professional-development-activator.php
 */
register_activation_hook( __FILE__, 'activate_continuous_professional_development' );
function activate_continuous_professional_development() {
	Continuous_Professional_Development::activate();
}

/**
 * The code that runs during plugin deactivation.
 * This action is documented in includes/class-continuous-professional-development-deactivator.php
 */
register_deactivation_hook( __FILE__, 'deactivate_continuous_professional_development' );
function deactivate_continuous_professional_development() {
	Continuous_Professional_Development::deactivate();
}

/**
 * Begins execution of the plugin.
 *
 * Since everything within the plugin is registered via hooks,
 * then kicking off the plugin from this point in the file does
 * not affect the page life cycle.
 *
 * @since    1.0.0
 */
function run_continuous_professional_development() {

	$plugin = new Continuous_Professional_Development( 'continuous-professional-development', '1.0.0' );
	$plugin->run();

}
run_continuous_professional_development();
