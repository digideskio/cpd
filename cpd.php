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
			'mkdo-helper-screen',			// Screen helpers
			'mkdo-helper-user'				// User Helpers
		);
		
		// Prepare admin dependancies
		$dependencies['admin'] 		= 	array(
			'cpd-register-scripts-admin',	// Register Admin Scripts
			'mkdo-menu',					// Menu base class
			'mkdo-admin-bar',				// Admin bar modifications
			'mkdo-admin-footer',			// Footer modifications
			'mkdo-admin-menus',				// Menu modifications
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

		$admin_bar				= new MKDO_Admin_bar						();
		$admin_footer			= new MKDO_Admin_Footer						();
		$admin_menus			= new MKDO_Admin_Menus						();
		$mu_menus 				= new MKDO_Admin_MU_Menus					();
		$content_blocks			= new MKDO_Admin_Content_Blocks				();
		$dashboard				= new MKDO_Admin_Dashboard					();
		$notices				= new MKDO_Admin_Notices					();
		$admin_profile			= new MKDO_Admin_Profile					();
		$metaboxes				= new MKDO_Admin_Metaboxes					();
		$columns				= new MKDO_Admin_Columns					();
		
		/** 
		 * Admin Bar
		 */
		
		// Removes the admin bar for all users
		if( get_option( 'mkdo_admin_remove_admin_bar', FALSE ) ) { 
			add_action( 'init', array( $admin_bar, 'remove_admin_bar' ) );
		}

		// Removes the admin bar for non admins
		if( get_option( 'mkdo_admin_remove_admin_bar_non_admins', FALSE ) ) { 
			add_action( 'init', array( $admin_bar, 'remove_admin_bar_for_non_admins' ) );
		}

		// Restricts access to the dashboard for non admins
		if( get_option( 'mkdo_admin_restrict_admin_access', FALSE ) ) { 
			add_action( 'admin_init', array( $admin_bar, 'restrict_admin_access' ) );
		}

		// Remove howdy message
		if( get_option( 'mkdo_admin_remove_howdy', TRUE ) ) { 
			add_action( 'wp_before_admin_bar_render', array( $admin_bar, 'remove_howdy' ) );
		}

		// Remove my sites
		if( get_option( 'mkdo_admin_remove_my_sites', TRUE ) ) { 
			add_action( 'wp_before_admin_bar_render', array( $admin_bar, 'remove_my_sites' ) );
		}

		// Remove logo
		if( get_option( 'mkdo_admin_remove_wp_logo', FALSE ) ) { 
			add_action( 'wp_before_admin_bar_render', array( $admin_bar, 'remove_wp_logo' ) );
		}

		// Remove site name
		if( get_option( 'mkdo_admin_remove_site_name', TRUE ) ) { 
			add_action( 'wp_before_admin_bar_render', array( $admin_bar, 'remove_site_name' ) );
		}

		// Remove WP SEO menu
		if( get_option( 'mkdo_admin_remove_wp_seo_menu', TRUE ) ) { 
			add_action( 'wp_before_admin_bar_render', array( $admin_bar, 'remove_wp_seo_menu' ) );
		}

		// Remove Comments
		if( get_option( 'mkdo_admin_remove_comments', TRUE ) ) { 
			add_action( 'wp_before_admin_bar_render', array( $admin_bar, 'remove_comments' ) );
		}

		// Remove +New
		if( get_option( 'mkdo_admin_remove_new_content', TRUE ) ) { 
			add_action( 'wp_before_admin_bar_render', array( $admin_bar, 'remove_new_content' ) );
		}

		// Remove updates
		// - Does not remove updates for Super Users
		if( get_option( 'mkdo_admin_remove_updates', TRUE ) ) { 
			add_action( 'wp_before_admin_bar_render', array( $admin_bar, 'remove_updates' ) );
		}

		// Remove search
		if( get_option( 'mkdo_admin_remove_search', TRUE ) ) { 
			add_action( 'wp_before_admin_bar_render', array( $admin_bar, 'remove_search' ) );
		}
		
		// Add custom admin logo
		// 
		// - To use a custom logo you must not use 'remove_wp_admin_logo'
		// - The CSS in this function will vary from stie to site
		// - For best results the logo should not be larger then 20px in height
		// - To make this work by default place an image 20x20px in your theme /img/ 
		//   folder named 'admin-logo.php'
		// - For more complex customisation copy the template in the /admin/partials/ folder in this plugin 
		//   to your theme in one of these locations. Here you can alter the image path and CSS as required:
		//   - /mkdo-admin/custom-admin-logo.php
		//   - /partials/custom-admin-logo.php
		if( get_option( 'mkdo_admin_custom_admin_logo', FALSE ) ) { 
			add_action( 'admin_head', array( $admin_bar, 'custom_admin_logo' ) );
		}

		// Add menu switcher
		if( get_option( 'mkdo_admin_add_menu_switcher', TRUE ) ) { 
			add_action( 'wp_before_admin_bar_render', array( $admin_bar, 'add_menu_switcher' ) );
		}

		/** 
		 * Admin Footer
		 */
		
		// Removes the admin footer message
		if( get_option( 'mkdo_admin_remove_admin_footer', TRUE ) ) { 
			add_action( 'admin_footer_text', array( $admin_footer, 'remove_admin_footer'), 99  );
		}

		// Removes the WP version number
		if( get_option( 'mkdo_admin_remove_admin_version', TRUE ) ) { 
			add_action( 'update_footer', array( $admin_footer, 'remove_admin_version'), 99  );
		}

		// Add custom footer text
		// - Use the filter 'mkdo_footer_text' to add your own text
		if( get_option( 'mkdo_admin_add_footer_text', FALSE ) ) { 
			add_action( 'admin_footer_text', array( $admin_footer, 'add_footer_text'), 99 );
		}

		/**
		 * Menus
		 */

		
		// Add custom menu
		// 
		// - Use the filter 'mkdo_content_menu_add_menu_items' to add menu items
		// - Each item in the filter is an array in the following format
		// 
		// 		$mkdo_content_menus[] 	= 	array( 
		// 										'post_type'							=>		'page',
		//										'post_name' 						=> 		'Pages',
		//										'menu_name' 						=> 		'Pages',
		//										'capability' 						=> 		'edit_posts',
		//										'function' 							=> 		defined('CMS_TPV_URL') ? 'edit.php?post_type=page&page=cms-tpv-page-page' : 'edit.php?post_type=page',
		//										'admin_add'							=>		TRUE,
		//										'mkdo_add'							=> 		TRUE,
		//										'remove_original_menu' 				=> 		TRUE,
		//										'remove_original_sub_menu' 			=> 		FALSE,
		//										'remove_original_sub_menu_parent' 	=> 		'',
		// 										'admin_remove'						=>		TRUE,
		// 										'mkdo_remove'						=> 		TRUE,
		//										'add_to_dashboard'					=> 		TRUE,
		//										'add_to_dashboard_slug'				=> 		'mkdo_content_menu',
		//									);
		//									
		//	 - 'post_type' is the post_type you are adding
		//	 - 'post_name' is the name of the page (if you are renaming the menu also change this)
		//	 - 'menu_name' is the name of the menu (if you are renaming the mneuy also change this)
		//	 - 'capability' is the access required to view the menu item
		//	 - 'function' is the function or the URL that the menu item links to
		//	 - 'admin_add' will add the item only if the user is an administrator
		//	 - 'mkdo_add' will add the item only if the user is an MKDO admin
		//	 - 'remove_original_menu' will remove the original menu item before adding it to the menu
		//	 - 'remove_original_sub_menu' will remove the original sub menu item before adding it to the menu
		//	 - 'remove_original_sub_menu_parent' the parent of the sub menu item that needs removing
		//	 - 'admin_remove' will remove the item for admins
		// 	 - 'mkdo_remove' will remove the item for super users
		//	 - 'add_to_dashboard' will add the menu item to a dashboard
		//	 - 'add_to_dashboard_slug' the slug of the dashboard to add to
		//	 
		// - For more complex customisation of the admin page created by the menu copy the template in the 
		//   /admin/partials/ folder in this plugin to your theme in one of these locations. Here you 
		//   can alter the image path and CSS as required:
		//   - /mkdo-admin/mkdo-content-menu.php
		//   - /partials/mkdo-content-menu.php
		//   
		// - The admin page has 'blocks' of content in a custom dashboard, there are several ways to get custom
		//   blocks into this dashboard. These are:
		//   - Add a custom menu item using 'mkdo_content_menu_add_menu_items' and set 'add_to_dashboard' to 
		//     TRUE, and enter the 'add_to_dashbaord_slug' as 'mkdo_content_menu'
		//   - Filter the 'mkdo_content_menu_blocks' filter like so:
		//     		add_filter( 'mkdo_content_menu_blocks', mkdo_content_menu_blocks_filter );
		//     		function mkdo_content_menu_blocks_filter( $blocks ) {
		//     			
		//     			$blocks[] 	=	array(
		//     								'title' 		=> 'Custom Block Name',
		// 									'dashicon' 		=> 'dashicons-welcome-add-page',
		// 									'desc' 			=> '<p>This content type is for managing ' . 'Custom Block Name' . '.</p>',
		// 									'post_type' 	=> 'custom-block-name',
		// 									'button_label' 	=> 'Edit / Manage ' . 'Custom Block Name',
		// 									'css_class' 	=> 'custom-block-name',
		// 									'show_tax' 		=> FALSE
		//     							);
		//     			
		//     			return $blocks;
		//     		}
		//     		
		//   - When adding a custom post type using the MKDO Objects framework, simply add the following line of code:
		//   
		//   		if( class_exists( 'MKDO_Admin' ) ) {
		// 				add_filter( 'mkdo_content_menu_blocks', $my_post_type_class, 'add_content_block' );
		// 			}
		if( get_option( 'mkdo_admin_add_mkdo_content_menu', TRUE ) ) { 
			add_action( 'admin_menu', 						array( $admin_menus, 'add_menu'), 							5  );
			add_action( 'admin_menu', 						array( $admin_menus, 'add_menu_items') , 					99 );
			add_action( 'mkdo_content_menu_render_blocks', 	array( $admin_menus, 'mkdo_content_menu_render_blocks'), 	99  );
		}
		
		// Remove admin menus
		// 
		// - Use the filter 'mkdo_content_menu_remove_admin_menus' to add menu items to be removed
		// - Each item in the filter is an array in the following format:
		// 
		// 		$admin_menu[] 	= 	array( 
		// 								'menu' 			=> 		'edit.php',
		// 								'admin_remove'	=>		TRUE,
		// 								'mkdo_remove'	=> 		TRUE
		// 							);
		// 							
		// 	  - 'menu' is the menu to remove
		// 	  - 'admin_remove' will remove the item for admins
		// 	  - 'mkdo_remove' will remove the item for super users
		if( get_option( 'mkdo_admin_remove_admin_menus', TRUE ) ) { 
			add_action( 'admin_menu', array( $admin_menus, 'remove_admin_menus'), 99  );
		}

		// Remove admin sub menus
		// 
		// - Use the filter 'mkdo_content_menu_remove_admin_sub_menus' to add sub menu items to be removed
		// - Each item in the filter is an array in the following format:
		// 
		// 		$admin_sub_menu[] 	= 	array(
		// 								'parent' 		=> 		'themes.php',
		//								'child' 		=> 		'theme-editor.php',
		//								'admin_remove'	=>		TRUE,
		//								'mkdo_remove'	=> 		FALSE
		//							);
		//							
		// 	  - 'parent' is the parent of the sub menu to remove						
		// 	  - 'child' is the sub menu to remove
		// 	  - 'admin_remove' will remove the item for admins
		// 	  - 'mkdo_remove' will remove the item for super users
		if( get_option( 'mkdo_admin_remove_admin_sub_menus', TRUE ) ) { 
			add_action( 'admin_menu', array( $admin_menus, 'remove_admin_sub_menus'), 99  );
		}

		// Rename Media Library to Assets Library
		if( get_option( 'mkdo_admin_rename_media_library', FALSE ) ) { 
			add_action( 'admin_menu', 	array( $admin_menus,	'rename_media_menu' 	) );
			add_filter( 'gettext', 		array( $admin_menus,	'rename_media_page'), 	10,	3  );
		}

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

		// Show comments on mkdo_content_menu
		if( get_option( 'mkdo_admin_show_comments_on_mkdo_content_menu', TRUE ) ) { 
			add_action( 'mkdo_content_menu_after_blocks', array( $content_blocks, 'add_comments' ) );
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
		$journal_menus 			= new CPD_Journal_Menus				();
		$journal_dashboards 	= new CPD_Journal_Dashboards		();
		$journal_users 			= new CPD_Journal_Users				();
		$journal_profiles		= new CPD_Journal_Profiles			();
		$content_blocks			= new CPD_Journal_Content_Blocks	();
		$options				= new CPD_Journal_Options			();
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
			add_filter( 'mkdo_content_menu_add_menu_items', array( $journal_menus, 'filter_menu_items' ) );
		}

		// Rename Network sub menus
		if( get_option( 'cpd_filter_network_admin_sub_menus', TRUE ) ) { 
			add_filter( 'mkdo_admin_add_network_admin_sub_menus_filter', array( $journal_menus, 'filter_network_admin_sub_menus' ), 99  );
		}

		// Remove sub menus
		if( get_option( 'cpd_remove_admin_sub_menus', TRUE ) ) { 
			add_action( 'admin_menu', array( $journal_menus, 'remove_admin_sub_menus' ), 99  );
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
