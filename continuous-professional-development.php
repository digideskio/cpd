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


		add_option( 'mkdo_admin_show_content_on_dashboard', FALSE );
		add_option( 'mkdo_admin_show_profile_on_dashboard', FALSE ) ;
		add_option( 'mkdo_admin_show_comments_on_dashboard', FALSE );

		// Vendor
		require_once plugin_dir_path( __FILE__ ) . 'vendor/mkdo-admin/mkdo-admin.php';

		// Register Scripts
		require_once plugin_dir_path( __FILE__ ) . 'admin/class-cpd-register-scripts.php';
		require_once plugin_dir_path( __FILE__ ) . 'public/class-cpd-register-scripts-public.php';

		// Journal Users
		require_once plugin_dir_path( __FILE__ ) . 'admin/class-cpd-journal-users.php';

		// Journal Profiles
		require_once plugin_dir_path( __FILE__ ) . 'admin/class-cpd-journal-profiles.php';

		// Journal Menus
		require_once plugin_dir_path( __FILE__ ) . 'admin/class-cpd-journal-menus.php';

		// Journal Dashboards
		require_once plugin_dir_path( __FILE__ ) . 'admin/class-cpd-journal-dashboards.php';

		// Content Blocks
		require_once plugin_dir_path( __FILE__ ) . 'admin/class-cpd-journal-content-blocks.php'; 

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
		
		$admin_scripts 			= new CPD_Register_Scripts			( $this->get_instance(), $this->get_version() );
		$journal_menus 			= new CPD_Journal_Menus				( $this->get_instance(), $this->get_version() );
		$journal_dashboards 	= new CPD_Journal_Dashboards		( $this->get_instance(), $this->get_version() );
		$journal_users 			= new CPD_Journal_Users				( $this->get_instance(), $this->get_version() );
		$journal_profiles		= new CPD_Journal_Profiles			( $this->get_instance(), $this->get_version() );
		$content_blocks			= new CPD_Journal_Content_Blocks	( $this->get_instance(), $this->get_version() );

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

		// On creation of new MS user, redirect to custom area
		if( get_option( 'cpd_redirect_on_create_user', TRUE ) ) { 
			$this->loader->add_action( 'wpmu_new_user', $journal_users, 'redirect_on_create_user' );
		}

		/**
		 * Profiles
		 */
		$this->loader->add_action( 'edit_user_profile', 		$journal_profiles, 'add_cpd_relationship_management' 	);
		$this->loader->add_action( 'show_user_profile', 		$journal_profiles, 'add_cpd_relationship_management' 	);
		$this->loader->add_action( 'edit_user_profile_update', 	$journal_profiles, 'save_cpd_relationship_management' 	);
		$this->loader->add_action( 'personal_options_update', 	$journal_profiles, 'save_cpd_relationship_management' 	);

		/** 
		 * Journal Menus
		 */
		
		// Rename Menu Items
		if( get_option( 'cpd_filter_menu_items', TRUE ) ) { 
			$this->loader->add_filter( 'mkdo_content_menu_add_menu_items', $journal_menus, 'filter_menu_items' );
		}

		// Rename Network sub menus
		if( get_option( 'cpd_filter_network_admin_sub_menus', TRUE ) ) { 
			$this->loader->add_filter( 'mkdo_admin_add_network_admin_sub_menus_filter', $journal_menus, 'filter_network_admin_sub_menus', 99 );
		}

		// Remove sub menus
		if( get_option( 'cpd_remove_admin_sub_menus', TRUE ) ) { 
			$this->loader->add_action( 'admin_menu', $journal_menus, 'remove_admin_sub_menus', 99 );
		}

		/** 
		 * Journal Dashboards
		 */

		// Rename page titles
		if( get_option( 'cpd_rename_page_titles', TRUE ) ) { 
			$this->loader->add_filter( 'gettext', $journal_dashboards, 'rename_page_titles', 10, 3 );
			$this->loader->add_filter( 'init', $journal_dashboards, 'rename_post_object' );
		}
		
		// Force colour scheme based on network and / or user type
		if( get_option( 'cpd_force_network_color_scheme', TRUE ) ) { 
			$this->loader->add_action( 'get_user_option_admin_color', 	$journal_dashboards, 	'force_network_color_scheme' 		);
		}


		/**
		 * Content Blocks
		 */
		
		// Show content on dashboard
		if( get_option( 'cpd_show_welcome_content_block', TRUE ) ) { 
			$this->loader->add_action( 'wp_dashboard_setup', $content_blocks, 'add_welcome_content_block' );
		}

		// TODO: Wire this up fully
		// TODO: Do not show 'My Journals' if supervisor or participant if you are only a subscriber
		// add_action( 'admin_init', 'dashboard_redirect' );
		// function dashboard_redirect() {
			
		// 	global $current_user;
			
		// 	get_currentuserinfo();
			
		// 	if ( user_can( $current_user, 'subscriber' ) ) {
				
		// 		$primary_blog = get_active_blog_for_user( $current_user->ID );
		// 		wp_redirect( $primary_blog->siteurl . '/wp-admin/' );
		// 		exit;
		// 	}
		// }
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
		update_user_meta( $mkdo_user_id, 'mkdo_user', 1 );
	
		// Set option to initialise the redirect
		add_option( 'mkdo_activation_redirect', TRUE );

		/**
		 * Do upgrade from old CPD system
		 */
		
		$table_name = $wpdb->base_prefix . 'cpd_relationships';
		
		// If table exists (old system has been installed)
		if( $wpdb->get_var("SHOW TABLES LIKE '$table_name'") == $table_name && get_site_option( 'cpd_upgraded_from_cpd_journals', FALSE ) === FALSE ) {
			$results 	= 	$wpdb->get_results( 
									"SELECT * FROM {$table_name}"
							);
			if( is_array( $results ) && !empty( $results ) ) {
				foreach( $results as $row ) {
					
					// Add participant to supervisor
					$participants 			= 	get_user_meta( $row->supervisor_id, 'cpd_related_participants', TRUE );
					if( !is_array( $participants ) ) {
						$participants = array();
					}
					if( !in_array( $row->participant_id, $participants ) ) {
						$participants[] = $row->participant_id;
					}
					update_user_meta( $row->supervisor_id, 'cpd_related_participants', $participants );

					// Add supervisor to participant
					$supervisors 			= 	get_user_meta( $row->participant_id, 'cpd_related_supervisors', TRUE );
					if( !is_array( $supervisors ) ) {
						$supervisors = array();
					}
					if( !in_array( $row->supervisor_id, $supervisors ) ) {
						$supervisors[] = $row->supervisor_id;
					}
					update_user_meta( $row->participant_id, 'cpd_related_participants', $supervisors );
				}
			}

			add_site_option( 'cpd_upgraded_from_cpd_journals', TRUE );
		}
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
