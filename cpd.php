<?php

/**
 * CPD
 *
 * Turns WordPress into a CPD Journal management system.
 *
 * @link              http://makedo.in
 * @since             2.0.0
 * @package           CPD
 *
 * @wordpress-plugin
 * Plugin Name:       CPD
 * Plugin URI:        https://github.com/mkdo/cpd
 * Description:       A plugin to clean up the WordPress dashboard
 * Version:           2.0.0
 * Author:            MKDO Ltd. (Make Do)
 * Author URI:        http://makedo.in
 * License:           GPL-2.0+
 * License URI:       http://www.gnu.org/licenses/gpl-2.0.txt
 * Text Domain:       cpd
 * Domain Path:       /languages
 */

/** 
 * Change Log
 * 
 * 1.0.0		Initial Prototype
 * 2.0.0		Complete Refactor
 */

// Exit if accessed directly
defined( 'ABSPATH' ) || exit;

if( !class_exists( 'CPD' ) ) {

/**
 * CPD
 *
 * This is the class that orchestrates the entire plugin
 *
 * @since             	2.0.0
 */
class CPD {

	private static $instance = null;
	private $plugin_path;
	private $plugin_url;
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
	 * Define the core functionality of the plugin.
	 *
	 * Load the dependencies, define the locale, and set the hooks
	 *
	 * @since    2.0.0
	 */
	private function __construct() {

		$this->plugin_path 	= plugin_dir_path( __FILE__ );
		$this->plugin_url  	= plugin_dir_url( __FILE__ );
		$this->text_domain	= 'cpd';

		$this->load_dependencies();
		load_plugin_textdomain( $this->text_domain, false, $this->plugin_path . '\languages' );
		register_activation_hook( __FILE__, array( $this, 'activation' ) );
		register_deactivation_hook( __FILE__, array( $this, 'deactivation' ) );

		$this->run();
	}

	/**
	 * Load the required dependencies for this plugin.
	 *
	 * This dependancy loader lets you group dependancies into logical load order
	 * groupings. This allows easy reading of what loads in what order.
	 *
	 * It also allows easier and documented reading of what each dependancie is.
	 *
	 * @since    2.0.0
	 * @access   private
	 */
	private function load_dependencies() {

		// Order of dependancy load
		$dependencies 				= 	array( 
			'vendor', 							// Any third party plugins or libraries
			'includes',							// Functions common to admin and public
			'admin', 							// Admin functions
			'public',							// Public functions
			'upgrade'							// Upgrade functions
		);

		// Prepare vendor dependancies
		$dependencies['vendor'] 	= 	array(
			'cpd-comment-scores/index', 		// CPD comment scores
			'cpd-copy-assignments/index', 		// CPD copy assignments
		);
		
		// Prepare common dependancies
		$dependencies['includes'] 	= 	array(
			'cpd-templates',					// Templating Engine
			'mkdo-helper-screen',				// Screen helpers
			'mkdo-helper-user'					// User Helpers
		);
		
		// Prepare admin dependancies
		$dependencies['admin'] 		= 	array(
			
			'cpd-admin-scripts',				// Register Admin Scripts
			'cpd-admin', 						// WordPress Admin Overrides
			'cpd-menus',						// Menu ammendments
			'cpd-dashboards',					// Dashboard ammendments
			'cpd-dashboard-widget-comments',	// Dashboard widget (comments)
			'cpd-notices',						// Admin notices modifications
			'cpd-profile',						// Profile ammendments
			'cpd-metaboxes',					// Metabox ammendments
			'cpd-columns',						// Column modifications
			'cpd-users',						// User functions
			'cpd-options', 						// Create options page
			'cpd-blogs',						// Blog settings
			'cpd-content-blocks', 				// Register content blocks
			'cpd-email', 						// Send emails
		);
		
		// Prepare public dependancies
		$dependencies['public'] 	= 	array(
			'cpd-register-scripts-public' 		// Register public scripts
		);

		// Prepare public dependancies
		$dependencies['upgrade'] 	= 	array(
			'cpd-upgrade-legacy' 				// Upgrade the legacy CPD database 
		);

		// Load dependancies
		foreach( $dependencies as $order => $dependancy ) {
			if( is_array( $dependancy ) ) {
				foreach( $dependancy as $path ) {
					require_once $this->plugin_path  . $order . '/' . $path . '.php';
				}
			}
		}
	}

	/**
	 * Fired during plugin activation.
	 *
	 * All code necessary to run during the plugin's activation.
	 *
	 * @since      2.0.0
	 */
	public static function activation() {

		$user_id 				= 	get_current_user_id();
		$cpd_upgrade_legacy		= 	CPD_Upgrade_Legacy::get_instance();

		// Make current user an elivated user
		update_user_meta( $user_id, 'elevated_user', 1 );

		// Upgrade the legacy CPD plugin
		$cpd_upgrade_legacy->upgrade_relationships();

		// Setup the regular email
		if( !wp_next_scheduled( 'cpd_unassigned_users_email' ) ) {
			wp_schedule_event( strtotime( '02:00am' ), 'daily', 'cpd_unassigned_users_email' );
		}
	}

	/**
	 * Fired during plugin deactivation.
	 *
	 * All code necessary to run during the plugin's deactivation.
	 *
	 * @since      2.0.0
	 */
	public static function deactivation() {

	}

	/**
	 * Run the plugin loader
	 *
	 * @since    2.0.0
	 */
	public function run() {
		$this->admin_hooks();
		$this->public_hooks();
	}

			/**
	 * Register all of the hooks related to the dashboard functionality
	 * of the plugin.
	 *
	 * @since    2.0.0
	 * @access   private
	 */
	private function admin_hooks() {

		$scripts 							= CPD_Admin_Scripts::get_instance();
		$admin 								= CPD_Admin::get_instance();
		$menus 								= CPD_Menus::get_instance();
		$dashboards 						= CPD_Dashboards::get_instance();
		$dashboard_widget_comments 			= CPD_Dashboard_Widget_Comments::get_instance();
		$notices							= CPD_Notices::get_instance();
		$profile							= CPD_Profile::get_instance();
		$metaboxes							= CPD_Metaboxes::get_instance();
		$columns 							= CPD_Columns::get_instance();
		$users 								= CPD_Users::get_instance();
		$options							= CPD_Options::get_instance();
		$blogs 								= CPD_Blogs::get_instance();

		/** 
		 * Set Text Domain
		 */
		
		$scripts->set_text_domain( $this->text_domain );
		$admin->set_text_domain( $this->text_domain );
		$menus->set_text_domain( $this->text_domain );
		$dashboards->set_text_domain( $this->text_domain );
		$dashboard_widget_comments->set_text_domain( $this->text_domain );
		$notices->set_text_domain( $this->text_domain );
		$profile->set_text_domain( $this->text_domain );
		$metaboxes->set_text_domain( $this->text_domain );
		$columns->set_text_domain( $this->text_domain );
		$users->set_text_domain( $this->text_domain );
		$options->set_text_domain( $this->text_domain );
		$blogs->set_text_domain( $this->text_domain );

		$content_blocks			= new CPD_Journal_Content_Blocks	();
		$email 					= new CPD_Journal_Email				();

		/** 
		 * Scripts
		 *
		 * [1] Register styles
		 * [2] Register scripts
		 */
		
		/*1*/ add_action( 'admin_enqueue_scripts', array( $scripts, 'enqueue_styles' ) );
		/*2*/ add_action( 'admin_enqueue_scripts', array( $scripts, 'enqueue_scripts' ) );
		
		/** 
		 * Admin Bar
		 *
		 * [1] Remove menus from the admin bar
		 * [2] Add menu switcher to the admin bar
		 */

		/*1*/ add_action( 'wp_before_admin_bar_render', array( $admin, 'remove_admin_bar_menus' ) );
		/*2*/ add_action( 'wp_before_admin_bar_render', array( $admin, 'add_admin_bar_menu_switcher' ) );

		/** 
		 * Admin Footer
		 *
		 *  [1] Remove admin footer text
		 *  [2]	Remove admin footer version
		 */

		/*1*/ add_action( 'admin_footer_text', array( $admin, 'remove_admin_footer_text'), 99 );
		/*2*/ add_action( 'update_footer', array( $admin, 'remove_admin_version'), 99 );

		/**
		 * Admin Globals
		 *
		 * [1] Rename page titles
		 * [2] Rename the post object
		 */
		
		/*1*/ add_filter( 'gettext', array( $admin, 'rename_page_titles' ), 10, 3 );
		/*2*/ add_filter( 'init', array( $admin, 'rename_post_object' ) );
		
		/**
		 * Menus
		 *
		 * [1] Add the cpd_content_menu
		 * [2] Add menu items to the cpd_content_menu
		 * [3] Add dashboard widgets to the cpd_content_menu 
		 * [4] Remove admin menu items
		 * [5] Remove admin sub menu items
		 * [6] Correct cpd_content_menu menu hierarchy
		 * [7] Correct cpd_content_menu sub menu hierarchy
		 * [8] Add admin sub menus
		 * [9] Add network admin mneus
		 * [10] Rename network admin menus
		 */

		/*1*/ add_action( 'admin_menu', array( $menus, 'add_content_menu'), 5 );
		/*2*/ add_action( 'admin_menu', array( $menus, 'add_content_menu_items'), 99 );
		/*3*/ add_action( 'cpd_content_menu_render_widgets', array( $menus, 'add_content_menu_dashboard_widgets'), 99 );
		/*4*/ add_action( 'admin_menu', array( $menus, 'remove_admin_menus'), 99 );
		/*5*/ add_action( 'admin_menu', array( $menus, 'remove_admin_sub_menus'), 99 );
		/*6*/ add_filter( 'parent_file', array( $menus, 'correct_content_menu_hierarchy'), 10000 );
		/*7*/ add_filter( 'admin_head', array( $menus, 'correct_content_menu_sub_hierarchy') );
		/*8*/ add_action( 'admin_menu', array( $menus, 'add_admin_sub_menus'), 99 );
		/*9*/ add_action( 'network_admin_menu', array( $menus, 'add_network_admin_menus'), 100 );
		/*10*/ add_action( 'network_admin_menu', array( $menus, 'rename_network_admin_menus'), 99 );

		/**
		 * Content Menu Dashboard Widgets
		 *
		 * [1] Add comments dashboard widget
		 */
		
		/*1*/ add_action( 'cpd_content_menu_render_widgets', array( $dashboard_widget_comments, 'add_dashboard_widget' ) );

		/**
		 * Dashboard
		 *
		 * [1] Remove dashboard widgets
		 */
		
		/*1*/ add_action( 'admin_init', array( $dashboards, 'remove_dashboard_widgets' ) );

		/**
		 * Dashboard Widgets
		 */

		/**
		 * Admin notices
		 *
		 * [1] Add taxonomy selecter as notice
		 * [2] Add tree view switcher as notice
		 */
		
		/*1*/ add_action( 'all_admin_notices', array( $notices, 'add_notice_taxonomy' ) );
		/*2*/ add_action( 'all_admin_notices', array( $notices, 'add_notice_tree_view' ) );

		/**
		 * Profile
		 *
		 * [1] Add elevated user field to the profile page
		 * [2] Save elevated user field on update
		 * [3] Save elevated user field on edit user
		 * [4] Prevent changes to colour scheme
		 * [5] Set colour scheme based on user type
		 * [6] Add relationshp fields on user edit
		 * [7] Add relationshp fields on user screen
		 * [8] Save relationshp fields on update
		 * [9] Save relationshp fields on edit user
		 */
	
		/*1*/ add_action( 'personal_options', array( $profile, 'add_field_elevated_user' ) );
		/*2*/ add_action( 'personal_options_update', array( $profile, 'save_field_elevated_user' ) );
		/*3*/ add_action( 'edit_user_profile_update', array( $profile, 'save_field_elevated_user' ) );
		/*4*/ add_action( 'admin_init',	array( $profile, 'remove_admin_color_schemes' ) );
		/*5*/ add_action( 'get_user_option_admin_color', array( $profile, 'set_color_scheme' ) );
		/*6*/ add_action( 'edit_user_profile', array( $profile, 'add_field_cpd_relationship_management' ) );
		/*7*/ add_action( 'show_user_profile', array( $profile, 'add_field_cpd_relationship_management' ) );
		/*8*/ add_action( 'edit_user_profile_update', array( $profile, 'save_field_cpd_relationship_management' ) );
		/*9*/ add_action( 'personal_options_update', array( $profile, 'save_cpd_relationship_management' ) );

		/** 
		 * Metaboxes
		 *
		 * [1] Hide metaboxes
		 */
		
		/*1*/ add_action( 'default_hidden_meta_boxes', array( $metaboxes, 'hide_metaboxes'), 10, 2 );

		/** 
		 * Columns
		 *
		 * [1] Hide columns
		 * [2] Remove column filters
		 * [3] Add a column for users to sort CPD roles 
		 * [4] Manage column for users to sort CPD roles 
		 * [5] Sort column for users to sort CPD roles 
		 * [6] View count of users in CPD roles
		 * [7] Filter the CPD role column
		 */
		
		/*1*/ add_action( 'wp_login', array( $columns, 'hide_columns') , 10, 2 );
		/*2*/ add_filter( 'admin_init', array( $columns, 'remove_column_filters'), 99 );
		/*3*/ add_filter( 'wpmu_users_columns', array( $columns,'add_column_cpd_role' ), 15, 1 ); 
		/*4*/ add_action( 'manage_users_custom_column', array( $columns, 'manage_column_cpd_role' ), 15, 3 );
		/*5*/ add_filter( 'manage_users-network_sortable_columns', array( $columns,'sort_column_cpd_role' ) );
		/*6*/ add_filter( 'views_users-network', array( $columns,'view_count_cpd_role' ) );
		/*7*/ add_action( 'pre_user_query', array( $columns,'filter_column_cpd_role' ) );

		/**
		 * Users
		 *
		 * [1] Set capabilities so high end updates can only be done by elivated users
		 * [2] Create roles for participants and supervisors
		 * [3] On users role change, set the user role meta
		 * [4] Remove Participant Capabilities
		 * [5] Prevent participants from removing supervisors
		 * [6] Redirect users on creation
		 *
		 * Static Methods
		 * 
		 * [7] Get all multisite users
		 * [8] Get all participants
		 * [9] Get all supervisors
		 * [10] Remove a supervisor from related supervisors
		 * [11] Remove a participant from related participants
		 * [12] Add a relationship between a participant and a supervisor
		 * [13] Remove a relationship between a participant and a supervisor
		 * [14] Add a supervisor to a participants journals
		 * [15] Remove a user from all blogs
		 * [16] Create a user journal based on a user
		 */
		
		/*1*/ add_action( 'user_has_cap', array( $users, 'set_admin_capabilities' ) ); 
		/*2*/ add_action( 'init', array( $users, 'create_roles' ) );
		/*3*/ add_action( 'set_user_role', array( $users, 'set_user_role' ), 10, 2  );
		/*4*/ add_filter( 'editable_roles', array( $users, 'remove_participant_capabilities' ) );
		/*5*/ add_filter( 'user_has_cap', array( $users, 'prevent_partcipant_removing_supervisor' ), 10, 3 );
		/*6*/ add_action( 'wpmu_new_user', array( $users, 'redirect_on_create_user' ) );

		/*7*/ // CPD_Users::get_multisite_users();
		/*8*/ // CPD_Users::get_participants();
		/*9*/ // CPD_Users::get_supervisors();
		/*10*/ // CPD_Users::remove_user_from_related_supervisors( $user_id, $participants );
		/*11*/ // CPD_Users::remove_user_from_related_participants( $user_id, $supervisors );
		/*12*/ // CPD_Users::add_cpd_relationship( $supervisor, $participant );
		/*13*/ // CPD_Users::remove_cpd_relationship( $supervisor, $participant );
		/*14*/ // CPD_Users::add_supervisor_to_participant_journals( $user_id );
		/*15*/ // CPD_Users::remove_user_from_blogs( $user_id );
		/*16*/ // CPD_Users::create_user_journal( $user_id );

		/**
		 * Options
		 *
		 * [1] Initialise the options page
		 * [2] Add the options page (uses settings)
		 */

		/*1*/ add_action( 'admin_init', array( $options, 'init_options_page' ) );
		/*2*/ add_action( 'network_admin_menu', array( $options, 'add_options_page' ) );

		/**
		 * Blogs
		 *
		 * [1] Add pages on new blog creation
		 */
		
		/*1*/ add_action( 'wpmu_new_blog', array( $blogs, 'new_blog' ) );

		/**
		 * Content Blocks
		 */
		
		// Show content on dashboard
		if( get_option( 'cpd_show_welcome_content_block', TRUE ) ) { 
			add_action( 'wp_dashboard_setup', array( $content_blocks, 'add_welcome_content_block' ) );
			add_action( 'wp_network_dashboard_setup', array( $content_blocks, 'add_welcome_content_block' ) );
		}

		add_action( 'wp_network_dashboard_setup', array( $content_blocks, 'add_cpd_dashboard_widgets' ) );
		add_action( 'wp_dashboard_setup', array( $content_blocks, 'add_cpd_dashboard_widgets' ) );

		

		add_action( 'save_post', array( $email, 'send_mail_on_update' ) );
		add_action( 'cpd_unassigned_users_email', array( $email, 'unassigned_users_email' ) );

		
	}

	/**
	 * Register all of the hooks related to the public-facing functionality
	 * of the plugin.
	 *
	 * @since    2.0.0
	 * @access   private
	 */
	private function public_hooks() {

		// $public_scripts = new CPD_Register_Scripts_Public();

		// add_action( 'wp_enqueue_scripts', array( $public_scripts, 'enqueue_styles' ) );
		// add_action( 'wp_enqueue_scripts', array( $public_scripts, 'enqueue_scripts' ) );

	}

}
}

CPD::get_instance();
