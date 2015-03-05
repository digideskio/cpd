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
 * @package           CPD
 *
 * @wordpress-plugin
 * Plugin Name:       Continuous Professional Development
 * Plugin URI:        https://github.com/mkdo/continuous-professional-development
 * Description:       A plugin to clean up the WordPress dashboard
 * Version:           1.0.1
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
		'proper_folder_name' 	=> 'continuous-professional-development', // this is the name of the folder your plugin lives in
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
 * CPD
 *
 * This is the class that orchestrates the entire plugin
 *
 * @since             	1.0.0
 */
class CPD extends MKDO_Class {

	/**
	 * The loader that's responsible for maintaining and registering all hooks that power
	 * the plugin.
	 *
	 * @since    1.0.0
	 * @access   protected
	 * @var      CPD_Loader    $loader    Maintains and registers all hooks for the plugin.
	 */
	protected $loader;

	/**
	 * The unique identifier of this plugin.
	 *
	 * @since    1.0.0
	 * @access   protected
	 * @var      string    $cpd    The string used to uniquely identify this plugin.
	 */
	protected $cpd;

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
	 * - CPD_Loader. Orchestrates the hooks of the plugin.
	 * - CPD_i18n. Defines internationalization functionality.
	 * - CPD_Admin. Defines all hooks for the dashboard.
	 * - CPD_Public. Defines all hooks for the public side of the site.
	 *
	 * Create an instance of the loader which will be used to register the hooks
	 * with WordPress.
	 *
	 * @since    1.0.0
	 * @access   private
	 */
	private function load_dependencies() {

		// Vendor
		require_once plugin_dir_path( __FILE__ ) . 'vendor/mkdo-admin/mkdo-admin.php';

		// Register Scripts
		require_once plugin_dir_path( __FILE__ ) . 'admin/class-cpd-register-scripts.php';
		require_once plugin_dir_path( __FILE__ ) . 'public/class-cpd-register-scripts-public.php';

		// CPD Admin
		
		require_once plugin_dir_path( __FILE__ ) . 'admin/class-cpd-admin.php';

		// Journal Users
		require_once plugin_dir_path( __FILE__ ) . 'admin/class-cpd-journal-users.php';

		$this->loader = new MKDO_Loader();
	}

	/**
	 * Define the locale for this plugin for internationalization.
	 *
	 * Uses the CPD_i18n class in order to set the domain and to register the hook
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
		
		$admin_scripts 	= new CPD_Register_Scripts	( $this->get_instance(), $this->get_version() );
		$cpd_admin 		= new CPD_Admin			( $this->get_instance(), $this->get_version() );
		$journal_users 	= new CPD_Journal_Users		( $this->get_instance(), $this->get_version() );

		/** 
		 * Scripts
		 */
		
		// Enqueue the styles
		if( get_option( 'cpd_enqueue_styles', TRUE ) ) { 
			$this->loader->add_action( 'admin_enqueue_scripts', $admin_scripts, 'enqueue_styles' );
		}

		// Enqueue the scripts
		if( get_option( 'cpd_enqueue_scripts', TRUE ) ) { 
			$this->loader->add_action( 'admin_enqueue_scripts', $admin_scripts, 'enqueue_scripts' );
		}

		/**
		 * Journal Admin
		 */
		
		if( get_option( 'cpd_add_menus', TRUE ) ) { 
			$this->loader->add_action( 'admin_menu', 			$cpd_admin, 'add_admin_menus',				99 	);
			$this->loader->add_action( 'network_admin_menu', 	$cpd_admin, 'add_network_admin_menus',		5	);
		}

		if( get_option( 'cpd_filter_actions', TRUE ) ) { 
			$this->loader->add_filter( 'myblogs_blog_actions', 		$cpd_admin, 'multisite_my_sites_use_custom_dash' );
			$this->loader->add_filter( 'manage_sites_action_links', $cpd_admin, 'multisite_my_sites_use_custom_dash' );
		}


		/**
		 * Journal User Management
		 */
		
		// Create roles
		if( get_option( 'cpd_create_roles', TRUE ) ) { 
			$this->loader->add_action( 'init', $journal_users, 'create_roles' );
		}

		// Add user meta data
		if( get_option( 'cpd_add_meta_data', TRUE ) ) { 
			$this->loader->add_action( 'set_user_role', $journal_users, 'add_meta_data', 10, 2);
		}

		// Remove user management roles
		if( get_option( 'cpd_remove_user_management_roles', TRUE ) ) { 
			$this->loader->add_filter( 'editable_roles', $journal_users, 'remove_user_management_roles' );
		}

		// Prevent participants removing supervisors
		if( get_option( 'cpd_prevent_partcipant_removing_supervisor', TRUE ) ) { 
			$this->loader->add_filter( 'user_has_cap', $journal_users, 'prevent_partcipant_removing_supervisor', 10, 3 );
		}

		/** 
		 * Journal Network Dashboard
		 */


	}

	/**
	 * Register all of the hooks related to the public-facing functionality
	 * of the plugin.
	 *
	 * @since    1.0.0
	 * @access   private
	 */
	private function define_public_hooks() {

		$public_scripts = new CPD_Register_Scripts_Public				( $this->get_instance(), $this->get_version() );

		// Enqueue the styles
		if( get_option( 'cpd_enqueue_styles_public', FALSE ) ) { 
			$this->loader->add_action( 'wp_enqueue_scripts', $public_scripts, 'enqueue_styles' );
		}

		// Enqueue the scripts
		if( get_option( 'cpd_enqueue_scripts_public', FALSE ) ) { 
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
	 * @return    CPD_Loader    Orchestrates the hooks of the plugin.
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

		global $wpdb;

		/**
		 * Create MKDO super user
		 */

		// Get the current users, user ID
		$mkdo_user_id = get_current_user_id();
	
		// Make the user a mkdo super user
		update_usermeta( $mkdo_user_id, 'mkdo_user', 1 );
	
		// Set option to initialise the redirect
		add_option( 'mkdo_activation_redirect', TRUE );

		/**
		 * Create Journal logging
		 *
		 * TODO: I think there is a more WordPress way to do this, so commented out for now.
		 */

		// // this table keeps a record of the relationsips between participants and supervisors
		// $sql="CREATE TABLE IF NOT EXISTS {$this->relationship_table} (
		// 	supervisor_id bigint(20) NOT NULL,
		// 	participant_id bigint(20) NOT NULL,
		// 	INDEX supervisor_id_FI (supervisor_id),
		// 	INDEX participant_id_FI (participant_id),
		// 	PRIMARY KEY relationship (supervisor_id,participant_id))";
		// $wpdb->query($sql);

		// // we keep a record of all of the posts on any blog in this table to make reporting across all blogs easier
		// // otherwise we'd have to run lot's of queries to get stuff like post titles and dates.
		// $sql="CREATE TABLE IF NOT EXISTS {$this->posts_table} (
		// 	cpd_post_id BIGINT(20) NOT NULL AUTO_INCREMENT,
		// 	user_id BIGINT(20) NOT NULL,
		// 	post_id BIGINT(20) NOT NULL,
		// 	blog_id BIGINT(20) NOT NULL,
		// 	site_id BIGINT(20) NOT NULL,
		// 	post_date DATETIME,
		// 	post_status VARCHAR(20),
		// 	post_title TEXT,
		// 	guid VARCHAR(255),
		// 	INDEX user_id_FI (user_id),
		// 	UNIQUE KEY  (post_id,site_id,blog_id),
		// 	PRIMARY KEY post_id_PI (cpd_post_id))";
		// $wpdb->query($sql);


		// // if it's not already scheduled - setup the regular email
		// if(!wp_next_scheduled('cpd_unassigned_users_email')) {
		// 	wp_schedule_event(strtotime("02:00am"), 'daily', 'cpd_unassigned_users_email');
		// }
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
register_activation_hook( __FILE__, 'activate_cpd' );
function activate_cpd() {
	CPD::activate();
}

/**
 * The code that runs during plugin deactivation.
 * This action is documented in includes/class-continuous-professional-development-deactivator.php
 */
register_deactivation_hook( __FILE__, 'deactivate_cpd' );
function deactivate_cpd() {
	CPD::deactivate();
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
function run_cpd() {

	$plugin = new CPD( 'continuous-professional-development', '1.0.0' );
	$plugin->run();

}
run_cpd();
