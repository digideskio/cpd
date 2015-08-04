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
 * Description:       Turns WordPress into a CPD Journal management system.
 * Version:           2.4.2.1
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

			// WordPress GitHub Plugin Updater
			if ( !class_exists( 'WP_GitHub_Updater' ) ) {
				$dependencies['vendor'][] = 'radishconcepts/WordPress-GitHub-Plugin-Updater/updater';
			}

			// Prepare common dependancies
			$dependencies['includes']  =  array(
				'cpd-templates',                  // Templating Engine
				'cpd-widget-latest-activities',   // Load Latest Activites Widget
				'cpd-widget-latest-assessments',  // Load Latest Assessments Widget
			);

			// Prepare admin dependancies
			$dependencies['admin']   =  array(
				'cpd-admin-scripts',                     // Register Admin Scripts
				'cpd-admin',                             // WordPress Admin Overrides
				'cpd-menus',                             // Menu ammendments
				'cpd-dashboards',                        // Dashboard ammendments
				'cpd-dashboard-widget-comments',         // Dashboard widget (comments)
				'cpd-dashboard-widget-welcome',          // Dashboard widget (welcome)
				'cpd-dashboard-widget-user-guide',       // Dashboard widget (User Guide)
				'cpd-dashboard-widget-unassigned-users', // Dashboard widget (unassigned users)
				'cpd-dashboard-widget-latest-posts',     // Dashboard widget (latest posts)
				'cpd-dashboard-widget-user-posts',       // Dashboard widget (user posts)
				'cpd-dashboard-widget-user-development-categories',    // Dashboard widget (user posts)
				'cpd-dashboard-widget-user-competency-categories',    // Dashboard widget (user posts)
				'cpd-dashboard-widget-privacy',          // Dashboard widget (privacy)
				'cpd-dashboard-widget-templates',        // Dashboard widget (templates)
				'cpd-dashboard-widget-comments-supervisor', // Dashboard widget (comment guidance)
				'cpd-notices',                           // Admin notices modifications
				'cpd-profile',                           // Profile ammendments
				'cpd-metaboxes',                         // Metabox ammendments
				'cpd-columns',                           // Column modifications
				'cpd-users',                             // User functions
				// 'cpd-options',                        // Create options page
				// 'cpd-options-copy-assignments',       // Create options page
				'cpd-options-privacy',                   // Create options page
				'cpd-options-theme',                     // Create options page
				'cpd-options-templates',                 // Create options page
				'cpd-options-copy-pages',                // Create options page
				'cpd-options-copy-posts',                // Create options page
				'cpd-options-copy-ppd',                  // Create options page
				'cpd-options-copy-assessment',           // Create options page
				'cpd-options-users',                     // Create options page
				'cpd-options-users-participants',        // Create options page
				'cpd-options-users-supervisors',         // Create options page
				'cpd-blogs',                             // Blog settings
				'cpd-emails',                            // Send emails
				'cpd-comments',                          // Manage comments
				'cpd-cpt-ppd',                           // PPD CPT
				'cpd-cpt-assessment',                    // PPD CPT
				'cpd-meta-box-date-completed',           // Date Completed Meta Box
				'cpd-meta-box-description',              // Description Meta Box
				'cpd-meta-box-evidence',                 // Evidence Capture Meta Box
				'cpd-meta-box-points',                   // Points Meta Box
				'cpd-meta-box-score',                    // Points Meta Box
				'cpd-meta-box-privacy',                  // Privacy Meta Box
				'cpd-meta-box-guidance',                 // Guidance Meta Box
				'cpd-meta-box-criteria',                 // Criteria Meta Box
				'cpd-meta-box-submit',                   // Submit Meta Box
				'cpd-meta-box-feedback',                 // Submit Meta Box
				'cpd-taxonomy-development-category',     // Taxonomy for the development category
				'cpd-taxonomy-competency-category',      // Taxonomy for the competency category
				'cpd-widgets',                           // Register Widgets
				'cpd-cmb-plugin-render',                 // CMB Plugin to render value
				'cpd-login',                             // WordPress Login Overrides
				'cpd-theme',                             // Theme advisor, installer and updator
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

			add_action( 'after_switch_theme', 'flush_rewrite_rules' );
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
			$dashboard_widget_user_guide        = CPD_Dashboard_Widget_User_guide::get_instance();
			$dashboard_widget_unassigned_users  = CPD_Dashboard_Widget_Unassigned_Users::get_instance();
			$dashboard_widget_latest_posts      = CPD_Dashboard_Widget_Latest_Posts::get_instance();
			$dashboard_widget_user_posts        = CPD_Dashboard_Widget_User_Posts::get_instance();
			$dashboard_widget_user_development_categories    = CPD_Dashboard_Widget_User_Development_Categories::get_instance();
			$dashboard_widget_user_competency_categories     = CPD_Dashboard_Widget_User_Competency_Categories::get_instance();
			$dashboard_widget_privacy           = CPD_Dashboard_Widget_Privacy::get_instance();
			$dashboard_widget_templates         = CPD_Dashboard_Widget_Templates::get_instance();
			$dashboard_widget_comments_supervisor   = CPD_Dashboard_Widget_Comments_Supervisor::get_instance();
			$notices                            = CPD_Notices::get_instance();
			$profile                            = CPD_Profile::get_instance();
			$metaboxes                          = CPD_Metaboxes::get_instance();
			$columns                            = CPD_Columns::get_instance();
			$users                              = CPD_Users::get_instance();
			// $options                            = CPD_Options::get_instance();
			// $options_copy_assignments           = CPD_Options_Copy_Assignments::get_instance();
			$options_privacy                    = CPD_Options_Privacy::get_instance();
			$options_theme                      = CPD_Options_Theme::get_instance();
			$options_templates                  = CPD_Options_Templates::get_instance();
			$options_copy_pages                 = CPD_Options_Copy_Pages::get_instance();
			$options_copy_posts                 = CPD_Options_Copy_Posts::get_instance();
			$options_copy_ppd                   = CPD_Options_Copy_PPD::get_instance();
			$options_copy_assessment            = CPD_Options_Copy_Assessment::get_instance();
			$options_users                      = CPD_Options_Users::get_instance();
			$options_users_participants         = CPD_Options_Users_Participants::get_instance();
			$options_users_supervisors          = CPD_Options_Users_Supervisors::get_instance();
			$blogs                              = CPD_Blogs::get_instance();
			$emails                             = CPD_Emails::get_instance();
			$comments                           = CPD_Comments::get_instance();
			$cpd_ppd                            = CPD_CPT_PPD::get_instance();
			$cpd_assessment                     = CPD_CPT_Assessment::get_instance();
			$meta_box_date_completed            = CPD_Meta_Box_Date_Completed::get_instance();
			$meta_box_description               = CPD_Meta_Box_Description::get_instance();
			$meta_box_evidence                  = CPD_Meta_Box_Evidence::get_instance();
			$meta_box_points                    = CPD_Meta_Box_Points::get_instance();
			$meta_box_score                     = CPD_Meta_Box_Score::get_instance();
			$meta_box_privacy                   = CPD_Meta_Box_Privacy::get_instance();
			$meta_box_guidance                  = CPD_Meta_Box_Guidance::get_instance();
			$meta_box_criteria                  = CPD_Meta_Box_Criteria::get_instance();
			$meta_box_submit                    = CPD_Meta_Box_Submit::get_instance();
			$meta_box_feedback                  = CPD_Meta_Box_Feedback::get_instance();
			$taxonomy_development_category      = CPD_Taxonomy_Development_Category::get_instance();
			$taxonomy_competency_category       = CPD_Taxonomy_Competency_Category::get_instance();
			$widgets 			                = CPD_Widgets::get_instance();
			$cpd_login 			                = CPD_Login::get_instance();
			$cpd_theme 			                = CPD_Theme::get_instance();


			/**
			 * Set Text Domain
			 */

			$scripts->set_text_domain( $this->text_domain );
			$admin->set_text_domain( $this->text_domain );
			$menus->set_text_domain( $this->text_domain );
			$dashboards->set_text_domain( $this->text_domain );
			$dashboard_widget_comments->set_text_domain( $this->text_domain );
			$dashboard_widget_welcome->set_text_domain( $this->text_domain );
			$dashboard_widget_user_guide->set_text_domain( $this->text_domain );
			$dashboard_widget_unassigned_users->set_text_domain( $this->text_domain );
			$dashboard_widget_latest_posts->set_text_domain( $this->text_domain );
			$dashboard_widget_user_posts->set_text_domain( $this->text_domain );
			$dashboard_widget_user_development_categories->set_text_domain( $this->text_domain );
			$dashboard_widget_user_competency_categories->set_text_domain( $this->text_domain );
			$dashboard_widget_privacy->set_text_domain( $this->text_domain );
			$dashboard_widget_templates->set_text_domain( $this->text_domain );
			$dashboard_widget_comments_supervisor->set_text_domain( $this->text_domain );
			$notices->set_text_domain( $this->text_domain );
			$profile->set_text_domain( $this->text_domain );
			$metaboxes->set_text_domain( $this->text_domain );
			$columns->set_text_domain( $this->text_domain );
			$users->set_text_domain( $this->text_domain );
			// $options->set_text_domain( $this->text_domain );
			// $options_copy_assignments->set_text_domain( $this->text_domain );
			$options_privacy->set_text_domain( $this->text_domain );
			$options_theme->set_text_domain( $this->text_domain );
			$options_templates->set_text_domain( $this->text_domain );
			$options_copy_pages->set_text_domain( $this->text_domain );
			$options_copy_posts->set_text_domain( $this->text_domain );
			$options_copy_ppd->set_text_domain( $this->text_domain );
			$options_copy_assessment->set_text_domain( $this->text_domain );
			$options_users->set_text_domain( $this->text_domain );
			$options_users_participants->set_text_domain( $this->text_domain );
			$options_users_supervisors->set_text_domain( $this->text_domain );
			$blogs->set_text_domain( $this->text_domain );
			$emails->set_text_domain( $this->text_domain );
			$comments->set_text_domain( $this->text_domain );
			$cpd_ppd->set_text_domain( $this->text_domain );
			$cpd_assessment->set_text_domain( $this->text_domain );
			$meta_box_date_completed->set_text_domain( $this->text_domain );
			$meta_box_description->set_text_domain( $this->text_domain );
			$meta_box_evidence->set_text_domain( $this->text_domain );
			$meta_box_points->set_text_domain( $this->text_domain );
			$meta_box_score->set_text_domain( $this->text_domain );
			$meta_box_privacy->set_text_domain( $this->text_domain );
			$meta_box_guidance->set_text_domain( $this->text_domain );
			$meta_box_criteria->set_text_domain( $this->text_domain );
			$meta_box_submit->set_text_domain( $this->text_domain );
			$meta_box_feedback->set_text_domain( $this->text_domain );
			$taxonomy_development_category->set_text_domain( $this->text_domain );
			$taxonomy_competency_category->set_text_domain( $this->text_domain );
			$widgets->set_text_domain( $this->text_domain );
			$cpd_login->set_text_domain( $this->text_domain );
			$cpd_theme->set_text_domain( $this->text_domain );

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
			 * [3] Add Aspire CPD about link
			 * [4] Add custom logo
			 */

			/*1*/ add_action( 'wp_before_admin_bar_render', array( $admin, 'remove_admin_bar_menus' ) );
			/*2*/ add_action( 'wp_before_admin_bar_render', array( $admin, 'add_admin_bar_menu_switcher' ) );
			/*3*/ add_action( 'wp_before_admin_bar_render', array( $admin, 'add_admin_bar_about_link' ), 0  );
			/*4*/ add_action( 'admin_bar_menu', array( $admin, 'add_admin_bar_logo' ) );

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
			 * [3] Add user guide to dashboard
			 * [4] Add user guide to network dashboard
			 * [5] Add orphaned participants and users widget to network dashboard
			 * [6] Add latest posts widget to dashboard
			 * [7] Add latest posts widget to network dashboard
			 * [8] Add user posts widget to dashboard
			 * [9] Add user posts widget to network dashboard
			 * [10] Add user development categories widget to dashboard
			 * [11] Add user development categories widget to network dashboard
			 * [12] Add user competency categories widget to dashboard
			 * [13] Add user competency categories widget to network dashboard
			 * [14] Add privacy widget to dashboard
			 * [15] Add template widget to dashboard
			 * [16] Add notice to supervisors about comments
			 */

			/*1*/ add_action( 'wp_dashboard_setup', array( $dashboard_widget_welcome, 'add_dashboard_widget' ) );
			/*2*/ add_action( 'wp_network_dashboard_setup', array( $dashboard_widget_welcome, 'add_dashboard_widget' ) );
			/*3*/ add_action( 'wp_dashboard_setup', array( $dashboard_widget_user_guide, 'add_dashboard_widget' ) );
			/*4*/ add_action( 'wp_network_dashboard_setup', array( $dashboard_widget_user_guide, 'add_dashboard_widget' ) );
			/*5*/ add_action( 'wp_network_dashboard_setup', array( $dashboard_widget_unassigned_users, 'add_dashboard_widget' ) );
			/*6*/ add_action( 'wp_dashboard_setup', array( $dashboard_widget_latest_posts, 'add_dashboard_widget' ) );
			/*7*/ add_action( 'wp_network_dashboard_setup', array( $dashboard_widget_latest_posts, 'add_dashboard_widget' ) );
			/*8*/ add_action( 'wp_dashboard_setup', array( $dashboard_widget_user_posts, 'add_dashboard_widget' ) );
			/*9*/ add_action( 'wp_network_dashboard_setup', array( $dashboard_widget_user_posts, 'add_dashboard_widget' ) );
			/*10*/ add_action( 'wp_dashboard_setup', array( $dashboard_widget_user_development_categories, 'add_dashboard_widget' ) );
			/*11*/ add_action( 'wp_network_dashboard_setup', array( $dashboard_widget_user_development_categories, 'add_dashboard_widget' ) );
			/*12*/ add_action( 'wp_dashboard_setup', array( $dashboard_widget_user_competency_categories, 'add_dashboard_widget' ) );
			/*13*/ add_action( 'wp_network_dashboard_setup', array( $dashboard_widget_user_competency_categories, 'add_dashboard_widget' ) );
			/*14*/ add_action( 'wp_dashboard_setup', array( $dashboard_widget_privacy, 'add_dashboard_widget' ) );
			/*15*/ add_action( 'wp_dashboard_setup', array( $dashboard_widget_templates, 'add_dashboard_widget' ) );
			/*16*/ add_action( 'wp_dashboard_setup', array( $dashboard_widget_comments_supervisor, 'add_dashboard_widget' ) );


			/**
			 * Admin notices
			 *
			 * [1] Add taxonomy selecter as notice
			 * [2] Add tree view switcher as notice
			 * [3] Add editing disabled as a notice
			 */

			/*1*/ add_action( 'all_admin_notices', array( $notices, 'add_notice_taxonomy' ) );
			/*2*/ add_action( 'all_admin_notices', array( $notices, 'add_notice_tree_view' ) );
			/*3*/ add_action( 'all_admin_notices', array( $notices, 'add_notice_editing_disabled' ) );

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
			/*3*/ add_filter( 'manage_edit-assessment_columns', array( $columns, 'add_column_assessment_status' ), 15, 1 );
			/*4*/ add_action( 'manage_assessment_posts_custom_column', array( $columns, 'manage_column_assessment_status' ), 15, 3 );

			/**
			 * Users
			 *
			 * [1] Set capabilities so high end updates can only be done by elivated users
			 * [2] Create roles for participants and supervisors
			 * [3] On users role change, set the user role meta
			 * [4] Remove Participant Capabilities
			 * [5] Prevent participants from removing supervisors
			 * [6] Redirect users on creation
			 * [7] Redirect on login
			 * [8] Force users to login to view the site (if option set)
			 * [9] Add a login message
			 * [10] Remove the built in WP authentication filter
			 * [11] Add Filter to authenticate with email address
			 * [12] Prevent CPD roles on root
			 * [13] Prevent admins from being supervisors
			 */

			/*1*/ add_action( 'user_has_cap', array( $users, 'set_admin_capabilities' ) );
			/*2*/ add_action( 'init', array( $users, 'create_roles' ) );
			/*3*/ add_action( 'set_user_role', array( $users, 'set_user_role' ), 10, 2  );
			/*4*/ add_filter( 'editable_roles', array( $users, 'remove_participant_capabilities' ) );
			/*5*/ add_filter( 'user_has_cap', array( $users, 'prevent_partcipant_removing_supervisor' ), 10, 3 );
			/*6*/ add_action( 'wpmu_new_user', array( $users, 'redirect_on_create_user' ) );
			/*7*/ add_action( 'login_redirect', array( $users, 'login_redirect' ), 9999, 3 );
			/*8*/ add_action( 'init', array( $users, 'force_login' ) );
			/*9*/ add_filter( 'login_message', array( $users, 'force_login_message' ) );
			/*10*/ remove_filter( 'authenticate', 'wp_authenticate_username_password', 20, 3 );
			/*11*/ add_filter( 'authenticate', array( $users, 'authenticate_email_username_password' ), 20, 3 );
			/*12*/ add_action( 'init', array( $users, 'prevent_cpd_roles_on_root' ) );
			/*13*/ add_action( 'init', array( $users, 'prevent_admin_being_supervisor' ) );

			/**
			 * Users Static Methods
			 *
			 * [1] Get all multisite users
			 * [2] Get all participants
			 * [3] Get all supervisors
			 * [4] Remove a supervisor from related supervisors
			 * [5] Remove a participant from related participants
			 * [6] Add a relationship between a participant and a supervisor
			 * [7] Remove a relationship between a participant and a supervisor
			 * [8] Add a supervisor to a participants journals
			 * [9] Remove a user from all blogs
			 * [10] Create a user journal based on a user
			 */

			/*1*/ // CPD_Users::get_multisite_users();
			/*2*/ // CPD_Users::get_participants();
			/*3*/ // CPD_Users::get_supervisors();
			/*4*/ // CPD_Users::remove_user_from_related_supervisors( $user_id, $participants );
			/*5*/ // CPD_Users::remove_user_from_related_participants( $user_id, $supervisors );
			/*6*/ // CPD_Users::add_cpd_relationship( $supervisor, $participant );
			/*7*/ // CPD_Users::remove_cpd_relationship( $supervisor, $participant );
			/*8*/ // CPD_Users::add_supervisor_to_participant_journals( $user_id );
			/*9*/ // CPD_Users::remove_user_from_blogs( $user_id );
			/*10*/ // CPD_Users::create_user_journal( $user_id, $base_id );

			/**
			 * Options
			 *
			 * [1] Initialise the options page
			 * [2] Add the options page (uses settings API)
			 * [3] Add the copy assignments options page (Page also saves data)
			 * [4] Initialise the privacy options page
			 * [5] Add the privacy options page (uses settings API)
			 * [6] Initialise the theme options page
			 * [7] Add the theme options page (uses settings API)
			 * [8] Initialise the template options page
			 * [9] Add the template options page (uses settings API)
			 * [10] Add the theme copy pages option page (uses settings API)
			 * [11] Add the theme copy posts option page (uses settings API)
			 * [12] Add the theme copy ppd option page (uses settings API)
			 * [13] Add the theme copy assessments option page (uses settings API)
			 * [14] Initialise the users option page
			 * [15] Add the users option page (uses settings API)
			 * [16] Initialise the users participants option page
			 * [17] Add the users participants option page (uses settings API)
			 * [18] Initialise the users supervisors option page
			 * [19] Add the users supervisors option page (uses settings API)
			 */

			// /*1*/ add_action( 'admin_init', array( $options, 'init_options_page' ) );
			// /*2*/ add_action( 'network_admin_menu', array( $options, 'add_options_page' ) );
			// /*3*/ add_action( 'network_admin_menu', array( $options_copy_assignments, 'add_options_page' ) );
			/*4*/ add_action( 'admin_init', array( $options_privacy, 'init_options_page' ) );
			/*5*/ add_action( 'admin_menu', array( $options_privacy, 'add_options_page' ) );
			/*6*/ add_action( 'admin_init', array( $options_theme, 'init_options_page' ) );
			/*7*/ add_action( 'network_admin_menu', array( $options_theme, 'add_options_page' ) );
			/*8*/ add_action( 'admin_init', array( $options_templates, 'init_options_page' ) );
			/*9*/ add_action( 'admin_menu', array( $options_templates, 'add_options_page' ) );
			/*10*/ add_action( 'admin_menu', array( $options_copy_pages, 'add_options_page' ) );
			/*11*/ add_action( 'admin_menu', array( $options_copy_posts, 'add_options_page' ) );
			/*12*/ add_action( 'admin_menu', array( $options_copy_ppd, 'add_options_page' ) );
			/*13*/ add_action( 'admin_menu', array( $options_copy_assessment, 'add_options_page' ) );
			/*14*/ add_action( 'admin_init', array( $options_users, 'init_options_page' ) );
			/*15*/ add_action( 'admin_menu', array( $options_users, 'add_options_page' ) );
			/*16*/ add_action( 'admin_init', array( $options_users_participants, 'init_options_page' ) );
			/*17*/ add_action( 'admin_menu', array( $options_users_participants, 'add_options_page' ) );
			/*18*/ add_action( 'admin_init', array( $options_users_supervisors, 'init_options_page' ) );
			/*19*/ add_action( 'admin_menu', array( $options_users_supervisors, 'add_options_page' ) );

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
			 * [10] Enable comments on journal entries
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
			/*10*/ add_filter( 'wp_insert_post_data', array( $comments, 'comments_on_journal_entries' ) );

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
			 * [10] Register the PPD CPT
			 * [11] Move advanced metaboxes above the editor
			 * [12] Set the featured image metabox
			 * [13] Add helper text to the title
			 * [14] Add helper text to the editor
			 * [15] Fallback if no single template exists
			 * [16] Fallback if no archive template exists
			 * [17] Prevent submission of submitted assessments
			 * [18] Prevent submission of submitted assessments by removing publish button
			 */

			/*1*/ add_action( 'init', array( $cpd_ppd, 'register_post_type' ) );
			/*2*/ add_action( 'edit_form_after_title', array( $cpd_ppd, 'move_advanced_metaboxes_above_editor' ) );
			/*3*/ add_action( 'edit_form_after_title', array( $cpd_ppd, 'set_featured_image_metabox_title' ) );
			/*4*/ add_action( 'edit_form_top', array( $cpd_ppd, 'add_title_helper_text' ), 99 );
			/*5*/ add_action( 'edit_form_after_title', array( $cpd_ppd, 'add_editor_helper_text' ), 99 );
			/*6*/ add_action( 'admin_init', array( $cpd_ppd, 'remove_excerpt' ) );
			/*7*/ add_action( 'save_post', array( $cpd_ppd, 'update_excerpt_on_save' ) );
			/*8*/ add_filter( 'template_include', array( $cpd_ppd, 'fallback_template_single' ) , 99 );
			/*9*/ add_filter( 'template_include', array( $cpd_ppd, 'fallback_template_archive' ) , 99 );
			/*10*/ add_action( 'init', array( $cpd_assessment, 'register_post_type' ) );
			/*11*/ add_action( 'edit_form_after_title', array( $cpd_assessment, 'move_advanced_metaboxes_above_editor' ) );
			/*12*/ add_action( 'edit_form_after_title', array( $cpd_assessment, 'set_featured_image_metabox_title' ) );
			/*13*/ add_action( 'edit_form_top', array( $cpd_assessment, 'add_title_helper_text' ), 99 );
			/*14*/ add_action( 'edit_form_after_title', array( $cpd_assessment, 'add_editor_helper_text' ), 99 );
			/*15*/ add_filter( 'template_include', array( $cpd_assessment, 'fallback_template_single' ) , 99 );
			/*16*/ add_filter( 'template_include', array( $cpd_assessment, 'fallback_template_archive' ) , 99 );
			/*17*/ add_action( 'pre_post_update', array( $cpd_assessment, 'prevent_assessment_publish' ), 10, 2 );
			/*18*/ add_action( 'load-post.php', array( $cpd_assessment, 'prevent_assessment_publish_button' ), 10, 2 );


			/**
			 * Meta Boxes
			 *
			 * [1] Register the date completed Meta Box
			 * [2] Register the description Meta Box (excerpt)
			 * [3] Register the Evidence Meta Box
			 * [4] Register the Points Meta Box
			 * [5] Register the Score Meta Box
			 * [6] Register the Privacy Meta Box
			 * [7] Change post status
			 * [8] Register the guidance Meta Box
			 * [9] Criteria Metabox
			 * [10] Submit Metabox
			 * [11] Submit Metabox notify supervisors
			 * [12] Submit Metabox notify participant
			 * [13] Register the feedback Meta Box
			 */

			/*1*/ add_filter( 'cmb_meta_boxes', array( $meta_box_date_completed, 'register_metabox' ) );
			/*2*/ add_filter( 'cmb_meta_boxes', array( $meta_box_description, 'register_metabox' ) );
			/*3*/ add_filter( 'cmb_meta_boxes', array( $meta_box_evidence, 'register_metabox' ), 98 );
			/*4*/ add_filter( 'cmb_meta_boxes', array( $meta_box_points, 'register_metabox' ), 99 );
			/*5*/ add_filter( 'cmb_meta_boxes', array( $meta_box_score, 'register_metabox' ), 99 );
			/*6*/ add_filter( 'cmb_meta_boxes', array( $meta_box_privacy, 'register_metabox' ) );
			/*7*/ add_action( 'save_post', array( $meta_box_privacy, 'change_post_status' ), 99, 2 );
			/*8*/ add_filter( 'cmb_meta_boxes', array( $meta_box_guidance, 'register_metabox' ) );
			/*9*/ add_filter( 'cmb_meta_boxes', array( $meta_box_criteria, 'register_metabox' ) );
			/*10*/ add_filter( 'cmb_meta_boxes', array( $meta_box_submit, 'register_metabox' ) );
			/*11*/ add_action( 'save_post', array( $meta_box_submit, 'notify_supervisor' ), 99, 2 );
			/*12*/ add_action( 'save_post', array( $meta_box_submit, 'notify_participant' ), 99, 2 );
			/*13*/ add_action( 'cmb_meta_boxes', array( $meta_box_feedback, 'register_metabox' ), 99 );


			/**
			 * Taxonomies
			 *
			 * [1] Register the development categories taxonomy
			 * [2] Register the competency categories taxonomy
			 */

			/*1*/ add_action( 'init', array( $taxonomy_development_category, 'register_taxonomy' ) );
			/*2*/ add_action( 'init', array( $taxonomy_competency_category, 'register_taxonomy' ) );

			/**
			 * Widgets
			 *
			 * [1] Register Latest Activites Widget
			 */

			/*1*/ add_action( 'init', array( $widgets, 'register_widgets' ), 1 );

			/**
			 * Login
			 *
			 * [1] Change Login Logo
			 */

			/*1*/ add_action( 'login_enqueue_scripts', array( $cpd_login, 'add_login_logo' ), 1 );


			/**
			 * Theme
			 *
			 * [1] Add updates to transients
			 * [2] Move folder after update
			 * [3] Add missing theme notice
			 * [4] Hook into theme installer
			 * [5] Hide missing theme notice
			 */

			/*1*/ add_filter( 'pre_set_site_transient_update_themes', array( $cpd_theme, 'pre_set_site_transient_update_themes' ) );
			/*2*/ add_filter( 'upgrader_post_install', array( $cpd_theme, 'upgrader_post_install' ), 999, 3 );
			/*3*/ add_action( 'all_admin_notices', array( $cpd_theme, 'add_missing_theme_notice' ) );
			/*4*/ add_filter( 'themes_api', array( $cpd_theme, 'get_theme_info' ), 10, 3 );
			/*5*/ add_filter( 'admin_init', array( $cpd_theme, 'hide_missing_theme_notice' ) );


			/**
			 * CMB Plugins
			 *
			 * [1] Add the 'render' plugin
			 */

       /*1*/ add_filter( 'cmb_field_types', 'cmb_field_types' );
       function cmb_field_types( $classes ) {
           return array_merge( $classes, array( 'render' => 'CPD_CMB_Plugin_Render' ) );
       }

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
			/*2*/ add_action( 'login_enqueue_scripts', array( $scripts, 'enqueue_scripts' ) );

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

/**
 * GitHub Updater
 */
if ( is_admin() ) { // note the use of is_admin() to double check that this is happening in the admin
    $config = array(
        'slug'                => plugin_basename(__FILE__),                    // this is the slug of your plugin
        'proper_folder_name'  => 'cpd',                                        // this is the name of the folder your plugin lives in
        'api_url'             => 'https://api.github.com/repos/mkdo/cpd',      // the GitHub API url of your GitHub repo
        'raw_url'             => 'https://raw.github.com/mkdo/cpd/master',     // the GitHub raw url of your GitHub repo
        'github_url'          => 'https://github.com/mkdo/cpd',                // the GitHub url of your GitHub repo
        'zip_url'             => 'https://github.com/mkdo/cpd/zipball/master', // the zip url of the GitHub repo
        'sslverify'           => true,                                         // whether WP should check the validity of the SSL cert when getting an update, see https://github.com/jkudish/WordPress-GitHub-Plugin-Updater/issues/2 and https://github.com/jkudish/WordPress-GitHub-Plugin-Updater/issues/4 for details
        'requires'            => '4.0',                                        // which version of WordPress does your plugin require?
        'tested'              => '4.0',                                        // which version of WordPress is your plugin tested up to?
        'readme'              => 'README.md',                                  // which file to use as the readme for the version number
        'access_token'        => '',                                           // Access private repositories by authorizing under Appearance > GitHub Updates when this example plugin is installed
    );
    new WP_GitHub_Updater( $config );
}
