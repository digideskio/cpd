<?php

/**
 * CPD
 *
 * Turns WordPress into a CPD Journal management system.
 *
 * @link              http://makedo.in
 * @package           CPD
 *
 * @wordpress-plugin
 * Plugin Name:       CPD
 * Plugin URI:        https://github.com/mkdo/cpd
 * Description:       A plugin to clean up the WordPress dashboard
 * Version:           2.1.0
 * Author:            MKDO Ltd. (Make Do)
 * Author URI:        http://makedo.in
 * License:           GPL-2.0+
 * License URI:       http://www.gnu.org/licenses/gpl-2.0.txt
 * Text Domain:       cpd
 * Domain Path:       /languages
 */

// Exit if accessed directly
defined( 'ABSPATH' ) || exit;

if ( !class_exists( 'CPD' ) ) {

	/**
	 * CPD
	 *
	 * This is the class that orchestrates the entire plugin
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
		 */
		private function __construct() {

			$this->plugin_path  = plugin_dir_path( __FILE__ );
			$this->plugin_url   = plugin_dir_url( __FILE__ );
			$this->text_domain = 'cpd';

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
		 */
		private function load_dependencies() {

			// Order of dependancy load
			$dependencies     =  array(
				'vendor',        // Any third party plugins or libraries
				'includes',      // Functions common to admin and public
				'admin',         // Admin functions
				'public',        // Public functions
				'upgrade'        // Upgrade functions
			);

			// Prepare vendor dependancies
			$dependencies['vendor']  =  array(
			);

			// Human Made Custom Meta Boxes
			if ( !class_exists( 'CMB_Meta_Box' ) ) {
				$dependencies['vendor'][] = 'humanmade/Custom-Meta-Boxes/custom-meta-boxes';
			}

			// Prepare common dependancies
			$dependencies['includes']  =  array(
				'cpd-templates',                 // Templating Engine
				'cpd-widget-latest-activities',   // Load Latest Activites Widget
			);

			// Prepare admin dependancies
			$dependencies['admin']   =  array(
				'cpd-admin-scripts',                     // Register Admin Scripts
				'cpd-admin',                             // WordPress Admin Overrides
				'cpd-menus',                             // Menu ammendments
				'cpd-dashboards',                        // Dashboard ammendments
				'cpd-dashboard-widget-comments',         // Dashboard widget (comments)
				'cpd-dashboard-widget-welcome',          // Dashboard widget (welcome)
				'cpd-dashboard-widget-unassigned-users', // Dashboard widget (unassigned users)
				'cpd-dashboard-widget-latest-posts',     // Dashboard widget (latest posts)
				'cpd-dashboard-widget-user-posts',       // Dashboard widget (user posts)
				'cpd-dashboard-widget-user-development-categories',    // Dashboard widget (user posts)
				'cpd-notices',                           // Admin notices modifications
				'cpd-profile',                           // Profile ammendments
				'cpd-metaboxes',                         // Metabox ammendments
				'cpd-columns',                           // Column modifications
				'cpd-users',                             // User functions
				'cpd-options',                           // Create options page
				'cpd-options-copy-assignments',          // Create options page
				'cpd-blogs',                             // Blog settings
				'cpd-emails',                            // Send emails
				'cpd-comments',                          // Manage comments
				'cpd-cpt-ppd',                           // PPD CPT
				'cpd-meta-box-date-completed',           // Date Completed Meta Box
				'cpd-meta-box-description',              // Description Meta Box
				'cpd-meta-box-evidence',                 // Evidence Capture Meta Box
				'cpd-meta-box-points',                   // Points Meta Box
				'cpd-taxonomy-development-category',     // Taxonomy for the development category
				'cpd-widgets',                           // Register Widgets
			);

			// Prepare public dependancies
			$dependencies['public']  =  array(
				'cpd-public-scripts',     // Register public scripts
				'cpd-comments-ui',        // Manage comments
				'cpd-archive-titles',     // Set archive titles
			);

			// Prepare public dependancies
			$dependencies['upgrade']  =  array(
				'cpd-upgrade-legacy'     // Upgrade the legacy CPD database
			);

			// Load dependancies
			foreach ( $dependencies as $order => $dependancy ) {
				if ( is_array( $dependancy ) ) {
					foreach ( $dependancy as $path ) {
						require_once $this->plugin_path  . $order . '/' . $path . '.php';
					}
				}
			}
		}

		/**
		 * Fired during plugin activation.
		 *
		 * All code necessary to run during the plugin's activation.
		 */
		public static function activation() {

			$user_id     =  get_current_user_id();
			$cpd_upgrade_legacy  =  CPD_Upgrade_Legacy::get_instance();

			/**
			 * User permissions
			 *
			 * [1] Make current user an elivated user  
			 */
			
			/*1*/ update_user_meta( $user_id, 'elevated_user', 1 );


			/**
			 * Upgrade legacy database references
			 *
			 * [1] Upgrade the legacy CPD plugin
			 */
			
			/*1*/ $cpd_upgrade_legacy->upgrade_relationships();

			/**
			 * Create the default theme template
			 *
			 * [1] If the Template Default dosnt exist, create it
			 */
			
			// /*1*/ Create Template Default
			$blog = get_blog_details( 'template-default' );
			if( !is_object( $blog ) ) {
				$domain    =    parse_url( network_site_url(), PHP_URL_HOST );
				$path      =    parse_url( network_site_url(), PHP_URL_PATH ) . 'template-default/';
				$user_id   = 	get_current_user_id();
				wpmu_create_blog( $domain, $path, 'Template Default', $user_id );
			}

			/**
			 * Setup email
			 *
			 * [1] Setup the regular email
			 */
			
			// /*1*/ Setup the regular email
			if ( !wp_next_scheduled( 'cpd_unassigned_users_email' ) ) {
				wp_schedule_event( strtotime( '02:00am' ), 'daily', 'cpd_unassigned_users_email' );
			}
		}

		/**
		 * Fired during plugin deactivation.
		 *
		 * All code necessary to run during the plugin's deactivation.
		 */
		public static function deactivation() {

		}

		/**
		 * Run the plugin loader
		 */
		public function run() {
			$this->admin_hooks();
			$this->public_hooks();
		}

		/**
		 * Register all of the hooks related to the dashboard functionality
		 * of the plugin.
		 *
		 * @access   private
		 */
		private function admin_hooks() {

			$scripts                            = CPD_Admin_Scripts::get_instance();
			$admin                              = CPD_Admin::get_instance();
			$menus                              = CPD_Menus::get_instance();
			$dashboards                         = CPD_Dashboards::get_instance();
			$dashboard_widget_comments          = CPD_Dashboard_Widget_Comments::get_instance();
			$dashboard_widget_welcome           = CPD_Dashboard_Widget_Welcome::get_instance();
			$dashboard_widget_unassigned_users  = CPD_Dashboard_Widget_Unassigned_Users::get_instance();
			$dashboard_widget_latest_posts      = CPD_Dashboard_Widget_Latest_Posts::get_instance();
			$dashboard_widget_user_posts        = CPD_Dashboard_Widget_User_Posts::get_instance();
			$dashboard_widget_user_development_categories    = CPD_Dashboard_Widget_User_Development_Categories::get_instance();
			$notices                            = CPD_Notices::get_instance();
			$profile                            = CPD_Profile::get_instance();
			$metaboxes                          = CPD_Metaboxes::get_instance();
			$columns                            = CPD_Columns::get_instance();
			$users                              = CPD_Users::get_instance();
			$options                            = CPD_Options::get_instance();
			$options_copy_assignments           = CPD_Options_Copy_Assignments::get_instance();
			$blogs                              = CPD_Blogs::get_instance();
			$emails                             = CPD_Emails::get_instance();
			$comments                           = CPD_Comments::get_instance();
			$ppd                                = CPD_CPT_PPD::get_instance();
			$date_completed                     = CPD_Meta_Box_Date_Completed::get_instance();
			$description                        = CPD_Meta_Box_Description::get_instance();
			$evidence                           = CPD_Meta_Box_Evidence::get_instance();
			$points                             = CPD_Meta_Box_Points::get_instance();
			$development_category               = CPD_Taxonomy_Development_Category::get_instance();
			$widgets 			                = CPD_Widgets::get_instance();

			/**
			 * Set Text Domain
			 */

			$scripts->set_text_domain( $this->text_domain );
			$admin->set_text_domain( $this->text_domain );
			$menus->set_text_domain( $this->text_domain );
			$dashboards->set_text_domain( $this->text_domain );
			$dashboard_widget_comments->set_text_domain( $this->text_domain );
			$dashboard_widget_welcome->set_text_domain( $this->text_domain );
			$dashboard_widget_unassigned_users->set_text_domain( $this->text_domain );
			$dashboard_widget_latest_posts->set_text_domain( $this->text_domain );
			$dashboard_widget_user_posts->set_text_domain( $this->text_domain );
			$dashboard_widget_user_development_categories->set_text_domain( $this->text_domain );
			$notices->set_text_domain( $this->text_domain );
			$profile->set_text_domain( $this->text_domain );
			$metaboxes->set_text_domain( $this->text_domain );
			$columns->set_text_domain( $this->text_domain );
			$users->set_text_domain( $this->text_domain );
			$options->set_text_domain( $this->text_domain );
			$options_copy_assignments->set_text_domain( $this->text_domain );
			$blogs->set_text_domain( $this->text_domain );
			$emails->set_text_domain( $this->text_domain );
			$comments->set_text_domain( $this->text_domain );
			$ppd->set_text_domain( $this->text_domain );
			$date_completed->set_text_domain( $this->text_domain );
			$description->set_text_domain( $this->text_domain );
			$evidence->set_text_domain( $this->text_domain );
			$points->set_text_domain( $this->text_domain );
			$development_category->set_text_domain( $this->text_domain );
			$widgets->set_text_domain( $this->text_domain );

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
			 *  [2] Remove admin footer version
			 */

			/*1*/ add_action( 'admin_footer_text', array( $admin, 'remove_admin_footer_text' ), 99 );
			/*2*/ add_action( 'update_footer', array( $admin, 'remove_admin_version' ), 99 );

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

			/*1*/ add_action( 'admin_menu', array( $menus, 'add_content_menu' ), 5 );
			/*2*/ add_action( 'admin_menu', array( $menus, 'add_content_menu_items' ), 99 );
			/*3*/ add_action( 'cpd_content_menu_render_widgets', array( $menus, 'add_content_menu_dashboard_widgets' ), 99 );
			/*4*/ add_action( 'admin_menu', array( $menus, 'remove_admin_menus' ), 99 );
			/*5*/ add_action( 'admin_menu', array( $menus, 'remove_admin_sub_menus' ), 999 );
			/*6*/ add_filter( 'parent_file', array( $menus, 'correct_content_menu_hierarchy' ), 10000 );
			/*7*/ add_filter( 'admin_head', array( $menus, 'correct_content_menu_sub_hierarchy' ) );
			/*8*/ add_action( 'admin_menu', array( $menus, 'add_admin_sub_menus' ), 99 );
			/*9*/ add_action( 'network_admin_menu', array( $menus, 'add_network_admin_menus' ), 100 );
			/*10*/ add_action( 'network_admin_menu', array( $menus, 'rename_network_admin_menus' ), 99 );

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
			 *
			 * [1] Add welcome widget to dashboard
			 * [2] Add welcome widget to network dashboard
			 * [3] Add orphaned participants and users widget to network dashboard
			 * [4] Add latest posts widget to dashboard
			 * [5] Add latest posts widget to network dashboard
			 * [6] Add user posts widget to dashboard
			 * [7] Add user posts widget to network dashboard
			 * [8] Add user development categories widget to dashboard
			 * [9] Add user development categories widget to network dashboard
			 */

			/*1*/ add_action( 'wp_dashboard_setup', array( $dashboard_widget_welcome, 'add_dashboard_widget' ) );
			/*2*/ add_action( 'wp_network_dashboard_setup', array( $dashboard_widget_welcome, 'add_dashboard_widget' ) );
			/*3*/ add_action( 'wp_network_dashboard_setup', array( $dashboard_widget_unassigned_users, 'add_dashboard_widget' ) );
			/*4*/ add_action( 'wp_dashboard_setup', array( $dashboard_widget_latest_posts, 'add_dashboard_widget' ) );
			/*5*/ add_action( 'wp_network_dashboard_setup', array( $dashboard_widget_latest_posts, 'add_dashboard_widget' ) );
			/*6*/ add_action( 'wp_dashboard_setup', array( $dashboard_widget_user_posts, 'add_dashboard_widget' ) );
			/*7*/ add_action( 'wp_network_dashboard_setup', array( $dashboard_widget_user_posts, 'add_dashboard_widget' ) );
			/*8*/ add_action( 'wp_dashboard_setup', array( $dashboard_widget_user_development_categories, 'add_dashboard_widget' ) );
			/*9*/ add_action( 'wp_network_dashboard_setup', array( $dashboard_widget_user_development_categories, 'add_dashboard_widget' ) );


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
			/*4*/ add_action( 'admin_init', array( $profile, 'remove_admin_color_schemes' ) );
			/*5*/ add_action( 'get_user_option_admin_color', array( $profile, 'set_color_scheme' ) );
			/*6*/ add_action( 'edit_user_profile', array( $profile, 'add_field_cpd_relationship_management' ) );
			/*7*/ add_action( 'show_user_profile', array( $profile, 'add_field_cpd_relationship_management' ) );
			/*8*/ add_action( 'edit_user_profile_update', array( $profile, 'save_field_cpd_relationship_management' ) );
			/*9*/ add_action( 'personal_options_update', array( $profile, 'save_cpd_relationship_management' ) );

			/**
			 * Metaboxes
			 *
			 * [1] Hide metaboxes
			 * [2] Remove metaboxes
			 */

			/*1*/ add_action( 'default_hidden_meta_boxes', array( $metaboxes, 'hide_metaboxes' ), 10, 2 );
			/*2*/ add_action( 'admin_menu', array( $metaboxes, 'remove_metaboxes' ) );

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

			/*1*/ add_action( 'wp_login', array( $columns, 'hide_columns' ) , 10, 2 );
			/*2*/ add_filter( 'admin_init', array( $columns, 'remove_column_filters' ), 99 );
			/*3*/ add_filter( 'wpmu_users_columns', array( $columns, 'add_column_cpd_role' ), 15, 1 );
			/*4*/ add_action( 'manage_users_custom_column', array( $columns, 'manage_column_cpd_role' ), 15, 3 );
			/*5*/ add_filter( 'manage_users-network_sortable_columns', array( $columns, 'sort_column_cpd_role' ) );
			/*6*/ add_filter( 'views_users-network', array( $columns, 'view_count_cpd_role' ) );
			/*7*/ add_action( 'pre_user_query', array( $columns, 'filter_column_cpd_role' ) );

			/**
			 * Users
			 *
			 * [1] Set capabilities so high end updates can only be done by elivated users
			 * [2] Create roles for participants and supervisors
			 * [3] On users role change, set the user role meta
			 * [4] Remove Participant Capabilities
			 * [5] Prevent participants from removing supervisors
			 * [6] Redirect users on creation
			 * [7]
			 *
			 * Static Methods
			 *
			 * [8] Get all multisite users
			 * [9] Get all participants
			 * [10] Get all supervisors
			 * [11] Remove a supervisor from related supervisors
			 * [12] Remove a participant from related participants
			 * [13] Add a relationship between a participant and a supervisor
			 * [14] Remove a relationship between a participant and a supervisor
			 * [15] Add a supervisor to a participants journals
			 * [16] Remove a user from all blogs
			 * [17] Create a user journal based on a user
			 */

			/*1*/ add_action( 'user_has_cap', array( $users, 'set_admin_capabilities' ) );
			/*2*/ add_action( 'init', array( $users, 'create_roles' ) );
			/*3*/ add_action( 'set_user_role', array( $users, 'set_user_role' ), 10, 2  );
			/*4*/ add_filter( 'editable_roles', array( $users, 'remove_participant_capabilities' ) );
			/*5*/ add_filter( 'user_has_cap', array( $users, 'prevent_partcipant_removing_supervisor' ), 10, 3 );
			/*6*/ add_action( 'wpmu_new_user', array( $users, 'redirect_on_create_user' ) );
			/*7*/ add_action( 'login_redirect', array( $users, 'login_redirect' ), 9999, 3 );

			/*8*/ // CPD_Users::get_multisite_users();
			/*9*/ // CPD_Users::get_participants();
			/*20*/ // CPD_Users::get_supervisors();
			/*12*/ // CPD_Users::remove_user_from_related_supervisors( $user_id, $participants );
			/*13*/ // CPD_Users::remove_user_from_related_participants( $user_id, $supervisors );
			/*13*/ // CPD_Users::add_cpd_relationship( $supervisor, $participant );
			/*14*/ // CPD_Users::remove_cpd_relationship( $supervisor, $participant );
			/*15*/ // CPD_Users::add_supervisor_to_participant_journals( $user_id );
			/*16*/ // CPD_Users::remove_user_from_blogs( $user_id );
			/*17*/ // CPD_Users::create_user_journal( $user_id );

			/**
			 * Options
			 *
			 * [1] Initialise the options page
			 * [2] Add the options page (uses settings API)
			 */

			/*1*/ add_action( 'admin_init', array( $options, 'init_options_page' ) );
			/*2*/ add_action( 'network_admin_menu', array( $options, 'add_options_page' ) );

			/**
			 * Options
			 *
			 * [1] Add the options page (Page also saves data)
			 */

			/*1*/ add_action( 'network_admin_menu', array( $options_copy_assignments, 'add_options_page' ) );

			/**
			 * Blogs
			 *
			 * [1] Add pages on new blog creation
			 */

			/*1*/ add_action( 'wpmu_new_blog', array( $blogs, 'new_blog' ) );

			/**
			 * Emails
			 *
			 * [1] Send a mail to supervisors when a participant updates their blog
			 * [2] Email the admin with details of supervisor and particpants that are unassigned
			 */

			/*1*/ add_action( 'save_post', array( $emails, 'send_mail_on_update' ) );
			/*2*/ add_action( 'cpd_unassigned_users_email', array( $emails, 'unassigned_users_email' ) );

			/**
			 * Comments
			 *
			 * [1] Prevent participants editing supervisor comments (UI)
			 * [2] Prevent participants editing supervisor comments
			 * [3] Add score column to comments page
			 * [4] Add data to the score column
			 * [5] Add score metabox to comment
			 * [6] Save data from the metabox
			 * [7] Set comment options (prevent options from being unticked)
			 * [8] Enable TinyMCE for comments
			 * [9] Enable HTML Tags in comments
			 */

			/*1*/ add_action( 'current_screen', array( $comments, 'prevent_participants_editing_supervisor_comments' ), 10, 1 );
			/*2*/ add_filter( 'comment_row_actions', array( $comments, 'prevent_participants_editing_supervisor_comments_ui' ), 11, 1 );
			/*3*/ add_filter( 'manage_edit-comments_columns', array( $comments, 'add_score_column' ) );
			/*4*/ add_filter( 'manage_comments_custom_column',  array( $comments, 'add_score_column_data' ), 10, 2 );
			/*5*/ add_action( 'add_meta_boxes_comment',  array( $comments, 'add_comment_metabox_score' ) );
			/*6*/ add_filter( 'comment_save_pre',  array( $comments, 'save_comment_metabox_score' ) );
			/*7*/ add_action( 'init', array( $comments, 'set_comment_options' ), 10, 1 );
			/*8*/ add_filter( 'comment_form_defaults', array( $comments, 'enable_comment_tinymce' ) );
			/*9*/ add_filter( 'preprocess_comment', array( $comments, 'enable_comment_tags' ) );

			/**
			 * PPD
			 *
			 * [1] Register the PPD CPT
			 * [2] Move advanced metaboxes above the editor
			 * [3] Set the featured image metabox
			 * [4] Add helper text to the title
			 * [5] Add helper text to the editor
			 * [6] Remove the excerpt
			 * [7] Save description to excerpt
			 * [8] Fallback if no single template exists
			 * [9] Fallback if no archive template exists
			 */

			/*1*/ add_action( 'init', array( $ppd, 'register_post_type' ) );
			/*2*/ add_action( 'edit_form_after_title', array( $ppd, 'move_advanced_metaboxes_above_editor' ) );
			/*3*/ add_action( 'edit_form_after_title', array( $ppd, 'set_featured_image_metabox_title' ) );
			/*4*/ add_action( 'edit_form_top', array( $ppd, 'add_title_helper_text' ), 99 );
			/*5*/ add_action( 'edit_form_after_title', array( $ppd, 'add_editor_helper_text' ), 99 );
			/*6*/ add_action( 'admin_init', array( $ppd, 'remove_excerpt' ) );
			/*7*/ add_action( 'save_post', array( $ppd, 'update_excerpt_on_save' ) );
			/*8*/ add_filter( 'template_include', array( $ppd, 'fallback_template_single' ) , 99 );
			/*9*/ add_filter( 'template_include', array( $ppd, 'fallback_template_archive' ) , 99 );


			/**
			 * Meta Boxes
			 *
			 * [1] Register the date completed Meta Box
			 * [2] Register the description Meta Box (excerpt)
			 * [3] Register the Evidence Meta Box
			 * [4] Register the Points Meta Box
			 */

			/*1*/ add_filter( 'cmb_meta_boxes', array( $date_completed, 'register_metabox' ) );
			/*2*/ add_filter( 'cmb_meta_boxes', array( $description, 'register_metabox' ) );
			/*3*/ add_filter( 'cmb_meta_boxes', array( $evidence, 'register_metabox' ) );
			/*4*/ add_filter( 'cmb_meta_boxes', array( $points, 'register_metabox' ) );

			/**
			 * Taxonomies
			 *
			 * [1] Register the development categories taxonomy
			 */

			/*1*/ add_action( 'init', array( $development_category, 'register_taxonomy' ) );

			/**
			 * Widgets
			 *
			 * [1] Register Latest Activites Widget
			 */
			
			/*1*/ add_action( 'init', array( $widgets, 'register_widgets' ), 1 );
		}

		/**
		 * Register all of the hooks related to the public-facing functionality
		 * of the plugin.
		 */
		private function public_hooks() {

			$scripts            = CPD_Public_Scripts::get_instance();
			$comments_ui        = CPD_Comments_UI::get_instance();
			$archive_titles     = CPD_Archive_Titles::get_instance();

			/**
			 * Set Text Domain
			 */

			$scripts->set_text_domain( $this->text_domain );
			$comments_ui->set_text_domain( $this->text_domain );
			$archive_titles->set_text_domain( $this->text_domain );

			/**
			 * Scripts
			 *
			 * [1] Register styles
			 * [2] Register scripts
			 */

			/*1*/ add_action( 'wp_enqueue_scripts', array( $scripts, 'enqueue_styles' ) );
			// /*2*/ add_action( 'wp_enqueue_scripts', array( $scripts, 'enqueue_scripts' ) );

			/**
			 * Comments UI
			 *
			 * [1] Show score field to supervisors
			 * [2] Ensure score is required for supervisors
			 * [3] Add the score meta data to the comment
			 * [4] Render the comment meta in the UI
			 */

			/*1*/ add_action( 'comment_form_logged_in_after',  array( $comments_ui, 'add_comment_field_score' ), 10, 2 );
			/*2*/ add_filter( 'preprocess_comment', array( $comments_ui, 'verify_comment_field_score' ), 99 );
			/*3*/ add_action( 'comment_post', array( $comments_ui, 'add_comment_field_score_meta' ), 1 );
			/*4*/ add_filter( 'comments_array', array( $comments_ui, 'render_comment_field_score' ) );

			/**
			 * Archive Titles
			 *
			 * [1] Change the Archive titles
			 */

			/*1*/ add_filter( 'get_the_archive_title', array( $archive_titles, 'change_archive_titles' ), 99 );
		}

	}
}

CPD::get_instance();