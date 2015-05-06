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
			'vendor', 						// Any third party plugins or libraries
			'includes',						// Functions common to admin and public
			'admin', 						// Admin functions
			'public',						// Public functions
			'upgrade'						// Upgrade functions
		);

		// Prepare vendor dependancies
		$dependencies['vendor'] 	= 	array(
			'cpd-comment-scores/index', 	// CPD comment scores
			'cpd-copy-assignments/index', 	// CPD copy assignments
			'cpd-new-journal/index' 		// CPD new journals
		);
		
		// Prepare common dependancies
		$dependencies['includes'] 	= 	array(
			'cpd-templates',				// Templating Engine
			'mkdo-helper-screen',			// Screen helpers
			'mkdo-helper-user'				// User Helpers
		);
		
		// Prepare admin dependancies
		$dependencies['admin'] 		= 	array(
			'cpd-admin', 					// WordPress Admin Overrides
			'cpd-register-scripts-admin',	// Register Admin Scripts
			'mkdo-admin-mu-menus',			// Multisite menu modifications
			'mkdo-admin-dashboard',			// Dashboad modifications
			'mkdo-admin-notices',			// Admin notices modifications
			'mkdo-admin-content-blocks',	// Register content blocks
			'mkdo-admin-profile',			// Profile screen ammendments
			'mkdo-admin-metaboxes',			// Deregister metaboxes
			'mkdo-admin-columns',			// Column modifications

			'cpd-users',					// User functions
			'cpd-profiles',					// Profile ammendments
			'cpd-menus',					// Menu ammendments
			'cpd-dashboards',				// Dashboard ammendments
			'cpd-content-blocks', 			// Register content blocks
			'cpd-options', 					// Create options page
			'cpd-email', 					// Send emails
			'cpd-columns'					// Column modifications
		);
		
		// Prepare public dependancies
		$dependencies['public'] 	= 	array(
			'cpd-register-scripts-public' 	// Register public scripts
		);

		// Prepare public dependancies
		$dependencies['upgrade'] 	= 	array(
			'cpd-upgrade-legacy' 			// Upgrade the legacy CPD database 
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

		$admin 					= CPD_Admin::get_instance();
		$menus 					= CPD_Menus::get_instance();

		$admin->set_text_domain( $this->text_domain );
		$menus->set_text_domain( $this->text_domain );
		
		$mu_menus 				= new MKDO_Admin_MU_Menus					();
		$content_blocks			= new MKDO_Admin_Content_Blocks				();
		$dashboard				= new MKDO_Admin_Dashboard					();
		$notices				= new MKDO_Admin_Notices					();
		$admin_profile			= new MKDO_Admin_Profile					();
		$metaboxes				= new MKDO_Admin_Metaboxes					();
		$columns				= new MKDO_Admin_Columns					();
		
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
		 * Menus
		 *
		 * [1] Add the cpd_content_menu
		 * [2] Add menu items to the cpd_content_menu
		 * [3] Add dashboard widgets to the cpd_content_menu 
		 * [4] Remove admin menu items
		 * [5] Remove admin sub menu items
		 */

		/*1*/ add_action( 'admin_menu', array( $menus, 'add_content_menu'), 5 );
		/*2*/ add_action( 'admin_menu', array( $menus, 'add_content_menu_items'), 99 );
		/*3*/ add_action( 'cpd_content_menu_render_widgets', array( $menus, 'add_content_menu_dashboard_widgets'), 99 );
		/*4*/ add_action( 'admin_menu', array( $menus, 'remove_admin_menus'), 99  );
		/*5*/ add_action( 'admin_menu', array( $menus, 'remove_admin_sub_menus'), 99 );

		// Correct menu hierarchy
		if( get_option( 'mkdo_admin_correct_menu_hierarchy', TRUE ) ) { 
			add_filter( 'parent_file', 	array( $admin_menus, 'correct_menu_hierarchy'), 10000  );
			add_action( 'admin_head', 	array( $admin_menus, 'correct_sub_menu_hierarchy' 	) );
		}	

		/** 
		 * MU Menus
		 */
		if( is_multisite() ) {

			// Add Admin sub menus
			if( get_option( 'mkdo_admin_add_mu_admin_sub_menus', TRUE ) ) { 
				add_action( 'admin_menu', array( $mu_menus, 'add_admin_sub_menus'), 99  );
			}
			
			// Add Network menus
			if( get_option( 'mkdo_admin_add_mu_network_admin_menus', TRUE ) ) { 
				add_action( 'network_admin_menu', array( $mu_menus, 'add_network_admin_menus'), 100  );
			}

			// Add Network sub menus
			if( get_option( 'mkdo_admin_add_mu_network_admin_sub_menus', TRUE ) ) { 
				add_action( 'network_admin_menu', array( $mu_menus, 'add_network_admin_sub_menus'), 100  );
			}
			
			// Rename Network menus
			if( get_option( 'mkdo_admin_rename_mu_network_admin_menus', TRUE ) ) { 
				add_action( 'network_admin_menu', array( $mu_menus, 'rename_network_admin_menus'), 99  );
			}

			// Remove network admin menus
			if( get_option( 'mkdo_admin_remove_mu_network_admin_menus', TRUE ) ) { 
				add_action( 'network_admin_menu', array( $mu_menus, 'remove_network_admin_menus'), 99  );
			}

			// Correct menu hierarchy
			if( get_option( 'mkdo_admin_correct_mu_menu_hierarchy', TRUE ) ) { 
				add_action( 'admin_head', 	array( $mu_menus, 'correct_sub_menu_hierarchy' 	  ) );
			}
		}

		/**
		 * Dashboard
		 */
		
		// Remove dashbaord items
		if( get_option( 'mkdo_admin_remove_dashboard_meta', TRUE ) ) { 
			add_action( 'admin_init', 	array( $dashboard, 'remove_dashboard_meta' 	  ) );
		}

		/**
		 * Content Blocks
		 */

		// Show comments on cpd_content_menu
		if( get_option( 'mkdo_admin_show_comments_on_cpd_content_menu', TRUE ) ) { 
			add_action( 'cpd_content_menu_after_blocks', array( $content_blocks, 'add_comments' ) );
		}


		/**
		 * Admin notices
		 */
		
		// Show Taxonomies at the top of the post
		if( get_option( 'mkdo_admin_show_taxonomy_admin_notices', TRUE ) ) { 
			add_action( 'all_admin_notices', 				array( $notices, 		'show_taxonomy_admin_notices' 						) );
		}

		// Show Tree Page View switcher as a notice at the top of the post
		if( get_option( 'mkdo_admin_show_tree_page_view_switcher', TRUE ) ) { 
			add_action( 'all_admin_notices', 				array( $notices, 		'show_tree_page_view_switcher' 						) );
		}

		/**
		 * Profile
		 */
		
		// Add MKDO user checkbox
		if( get_option( 'mkdo_admin_add_elevated_user_profile_field', TRUE ) ) { 
			add_action( 'personal_options', 			array( $admin_profile, 'add_elevated_user_profile_field' 		) );
			add_action( 'personal_options_update', array( 	$admin_profile, 'save_elevated_user_profile_field_data' ) );
			add_action( 'edit_user_profile_update', 	array( $admin_profile, 'save_elevated_user_profile_field_data' ) );
		}

		// Force colour scheme
		if( get_option( 'mkdo_admin_force_user_color_scheme', TRUE ) ) { 
			add_action( 'admin_init',					array( $admin_profile, 	'remove_admin_color_schemes'	) );
			add_action( 'get_user_option_admin_color', 	array( $admin_profile, 	'force_user_color_scheme' 		) );
		}

		// Prevent admins from making system and plugin updates
		if( get_option( 'mkdo_admin_edit_admin_capabilities', TRUE ) ) { 
			add_action( 'user_has_cap',  array( $admin_profile, 'edit_admin_capabilities' ) );
		}

		/** 
		 * Metaboxes
		 */
		
		// Remove the metaboxes (for all but mkdo users )
		// 
		// To remove metaboxes you can edit the filter 'mkdo_remove_metaboxes'. Here you can 
		// add arrays of the slugs you wish to hide in the following format:
		// 
		// 		$hidden_metabox[] = array(
		// 								'id' 		=> 'postcustom',
		// 								'page' 		=> array('post','page'),
		// 								'context' 	=> 'normal'
		// 							);
		// 
		// 'id' is the slug of the metabox you want to remove
		// 'page' is an array of the post_types it should be removed from
		// 'context' is the position the metabox should be removed from
		if( get_option( 'mkdo_admin_remove_metaboxes', FALSE ) ) { 
			add_action( 'do_meta_boxes', array( $metaboxes ), 'remove_metaboxes'  );
		}
		
		// Hide the metaboxes
		// 
		// To hide metaboxes you can edit the filter 'mkdo_hide_metaboxes'. Here you can just 
		// list the metabox slugs you wish to hide. Eg, the default hidden metaboxes are:
		// 
		// - 'postcustom',
		// - 'commentsdiv',
		// - 'commentstatusdiv',
		// - 'slugdiv',
		// - 'trackbacksdiv',
		// - 'revisionsdiv',
		// - 'tagsdiv-post_tag',
		// - 'authordiv',
		// - 'wpseo_meta',
		// - 'relevanssi_hidebox'
		// 
		// By default it will remove the metaboxes from all posts. If you want to do a custom hide
		// Then you will need to write a new method hooking into the 'default_hidden_meta_boxes' action
		if( get_option( 'mkdo_admin_hide_metaboxes', TRUE ) ) { 
			add_action( 'default_hidden_meta_boxes', array( $metaboxes, 'hide_metaboxes'), 10, 2  );
		}

		/** 
		 * Columns
		 */
		
		// Remove columns
		// 
		// You can add columns to be removed by using the filter 'mkdo_edit_columns', you will need to use the
		// ID of the column you want deleting eg. 'comments'.
		// 
		// By default the column is removed from all posts. If you want to do a custom remove you will need to 
		// create a custom function by hooking into the 'init' action.
		if( get_option( 'mkdo_admin_remove_columns', FALSE ) ) { 
			add_filter( 'init', array( $columns, 'remove_custom_post_columns'), 98, 1  );
		}
		
		// Hide columns
		// 
		// Columns will be hidden everytime a user logs in (cannot be set perminantly hidden like metaboxes).
		// You can edit whats hidden with the filter 'mkdo_hide_columns'. The default hidden columns are:
		// 
		// - 'comments',
		// - 'tags',
		// - 'wpseo-score',
		// - 'wpseo-title',
		// - 'wpseo-metadesc',
		// - 'wpseo-focuskw',
		// - 'google_last30',
		// - 'twitter_shares',
		// - 'linkedin_shares',
		// - 'facebook_likes',
		// - 'facebook_shares',
		// - 'total_shares',
		// - 'decay_views',
		// - 'decay_shares',
		// 
		// By default it will remove the columns from all posts. If you want to do a custom hide you will need
		// to write a new method hooking into the 'wp_login' action.
		if( get_option( 'mkdo_admin_hide_columns', TRUE ) ) { 
			add_action( 'wp_login', array( $columns, 'hide_columns') , 10, 2 );
		}

		// Remove column filters
		// 
		// At the moment this filter is hardwired to remove the Yoast posts_filter_dropdown, however it may get
		// expanded in the future.
		if( get_option( 'mkdo_admin_remove_column_filters', TRUE ) ) { 
			add_filter( 'admin_init', array( $columns, 'remove_column_filters'), 99  );
		}

		/** 
		 * Classes
		 *
		 * Load the admin classes used in this Plugin
		 */
		
		$admin_scripts 			= new CPD_Register_Scripts_Admin	();
		
		$journal_dashboards 	= new CPD_Journal_Dashboards		();
		$journal_users 			= new CPD_Journal_Users				();
		$journal_profiles		= new CPD_Journal_Profiles			();
		$content_blocks			= new CPD_Journal_Content_Blocks	();
		$options				= CPD_Options::get_instance();
		$email 					= new CPD_Journal_Email				();
		$columns 				= new CPD_Journal_Columns			();

		/** 
		 * Scripts
		 */
		
		// Enqueue the styles
		if( get_option( 'cpd_enqueue_styles', TRUE ) ) { 
			add_action( 'admin_enqueue_scripts', array( $admin_scripts, 'enqueue_styles' ) );
		}

		// Enqueue the scripts
		if( get_option( 'cpd_enqueue_scripts', TRUE ) ) { 
			add_action( 'admin_enqueue_scripts', array( $admin_scripts, 'enqueue_scripts' ) );
		}


		/**
		 * Journal User Management
		 */
		
		// Create roles
		if( get_option( 'cpd_create_roles', TRUE ) ) { 
			add_action( 'init', array( $journal_users, 'create_roles' ) );
		}

		// Add user meta data
		if( get_option( 'cpd_add_meta_data', TRUE ) ) { 
			add_action( 'set_user_role', array( $journal_users, 'add_meta_data' ), 10, 2  );
		}

		// Remove user management roles
		if( get_option( 'cpd_remove_user_management_roles', TRUE ) ) { 
			add_filter( 'editable_roles', array( $journal_users, 'remove_user_management_roles' ) );
		}

		// Prevent participants removing supervisors
		if( get_option( 'cpd_prevent_partcipant_removing_supervisor', TRUE ) ) { 
			add_filter( 'user_has_cap', array( $journal_users, 'prevent_partcipant_removing_supervisor' ), 10, 3  );
		}

		// On creation of new MS user, redirect to custom area
		if( get_option( 'cpd_redirect_on_create_user', TRUE ) ) { 
			add_action( 'wpmu_new_user', array( $journal_users, 'redirect_on_create_user' ) );
		}

		/**
		 * Profiles
		 */
		add_action( 'edit_user_profile', 		array( $journal_profiles, 'add_cpd_relationship_management' 	) );
		add_action( 'show_user_profile', 		array( $journal_profiles, 'add_cpd_relationship_management' 	) );
		add_action( 'edit_user_profile_update', 	array( $journal_profiles, 'save_cpd_relationship_management' 	) );
		add_action( 'personal_options_update', 	array( $journal_profiles, 'save_cpd_relationship_management' 	) );

		/** 
		 * Journal Menus
		 */
		
		// Rename Menu Items
		if( get_option( 'cpd_filter_menu_items', TRUE ) ) { 
			add_filter( 'cpd_content_menu_add_menu_items', array( $menus, 'filter_menu_items' ) );
		}

		// Rename Network sub menus
		if( get_option( 'cpd_filter_network_admin_sub_menus', TRUE ) ) { 
			add_filter( 'mkdo_admin_add_network_admin_sub_menus_filter', array( $menus, 'filter_network_admin_sub_menus' ), 99  );
		}


		/** 
		 * Journal Dashboards
		 */

		// Rename page titles
		if( get_option( 'cpd_rename_page_titles', TRUE ) ) { 
			add_filter( 'gettext', array( $journal_dashboards, 'rename_page_titles' ), 10, 3  );
			add_filter( 'init', array( $journal_dashboards, 'rename_post_object' ) );
		}
		
		// Force colour scheme based on network and / or user type
		if( get_option( 'cpd_force_network_color_scheme', TRUE ) ) { 
			add_action( 'get_user_option_admin_color', 	array( $journal_dashboards, 	'force_network_color_scheme' 		) );
		}


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

		add_action( 'admin_init', array( $options, 'init_options_page' ) );
		add_action( 'network_admin_menu', array( $options, 'add_options_page' ) );
		add_action( 'network_admin_edit_update_cpd_settings', array( $options, 'update_options_page' ) );

		add_action( 'save_post', array( $email, 'send_mail_on_update' ) );
		add_action( 'cpd_unassigned_users_email', array( $email, 'unassigned_users_email' ) );

		add_action( 'manage_users_custom_column', array( $columns, 'cpd_role_column' ), 15, 3 );
		add_filter( 'manage_users-network_sortable_columns', array( $columns,'add_cpd_role_column_sort' ) );
		add_filter( 'views_users-network', array( $columns,'add_cpd_role_views' ) );
		add_filter( 'wpmu_users_columns', array( $columns,'add_cpd_role_column' ), 15, 1 ); 
		add_action( 'pre_user_query', array( $columns,'filter_and_order_by_cpd_column' ) );
	}

	/**
	 * Register all of the hooks related to the public-facing functionality
	 * of the plugin.
	 *
	 * @since    2.0.0
	 * @access   private
	 */
	private function public_hooks() {

		$public_scripts = new CPD_Register_Scripts_Public				();

		// Enqueue the styles
		if( get_option( 'cpd_enqueue_styles_public', FALSE ) ) { 
			add_action( 'wp_enqueue_scripts', array( $public_scripts, 'enqueue_styles' ) );
		}

		// Enqueue the scripts
		if( get_option( 'cpd_enqueue_scripts_public', FALSE ) ) { 
			add_action( 'wp_enqueue_scripts', array( $public_scripts, 'enqueue_scripts' ) );
		}

	}

}
}

CPD::get_instance();
