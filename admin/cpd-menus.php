<?php

// Exit if accessed directly
defined( 'ABSPATH' ) || exit;

if ( !class_exists( 'CPD_Menus' ) ) {

	/**
	 * Menus
	 *
	 * Menu functionality
	 *
	 * @package    CPD
	 * @subpackage CPD/admin
	 * @author     Make Do <hello@makedo.in>
	 */
	class CPD_Menus {
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
				self::$instance = new self();
			}

			return self::$instance;
		}

		/**
		 * Set the text domain
		 *
		 * @param string  $instance The name of this plugin.
		 * @param string  $version  The version of this plugin.
		 */
		public function __construct() {
		}

		/**
		 * Set the text domain
		 *
		 * @param string  $text_domain The text domain of the plugin.
		 */
		public function set_text_domain( $text_domain ) {
			$this->text_domain = $text_domain;
		}

		/**
		 * Add the CPD Content Menu
		 */
		public function add_content_menu() {
			add_object_page(
				'Content',
				'Content',
				'edit_posts',
				'cpd_content_menu',
				array( $this, 'render_content_menu' ),
				'dashicons-admin-page'
			);
		}

		/**
		 * Render the CPD Content Menu
		 */
		public function render_content_menu() {
			$template_name                        =    'cpd-content-menu';
			$template_path                        =    CPD_Templates::get_template_path( $template_name );

			if ( $template_path !== FALSE ) {
				include $template_path;
			}
		}

		/**
		 * Add menu items to the  CPD Content Menu
		 *
		 * @hook  filter_cpd_add_content_menu_items  Filter menu items we are adding to the CPD Content Menu
		 */
		public function add_content_menu_items() {

			$posts_name = 'Journal Entries';

			$blog_id = get_current_blog_id();

			if( SITE_ID_CURRENT_SITE == $blog_id ) {
				$posts_name = 'Posts';
			}

			$cpd_content_menu_items      =    array();

			// Posts
			$cpd_content_menu_items[]    =    array(
				'post_name'        	=>    'post',
				'menu_name'        	=>    $posts_name,
				'capability'		=>    'edit_posts',
				'function'			=>    'edit.php'
			);

			// Pages
			$cpd_content_menu_items[]    =    array(
				'post_name' 		=>    'page',
				'menu_name'			=>    'Pages',
				'capability'		=>    'edit_posts',
				'function'			=>    defined( 'CMS_TPV_URL' ) ? 'edit.php?post_type=page&page=cms-tpv-page-page' : 'edit.php?post_type=page'
			);

			$menus = apply_filters(
				'filter_cpd_add_content_menu_items',
				$cpd_content_menu_items
			);

			foreach ( $menus as $menu ) {
				add_submenu_page(
					'cpd_content_menu',
					$menu['post_name'],
					$menu['menu_name'],
					$menu['capability'],
					$menu['function']
				);
			}
		}

		/**
		 * Add dashboard widgets to the CPD Content Menu
		 *
		 * @hook  filter_cpd_content_menu_dashboard_widgets  Filter dashboard widgets we are adding to the CPD Content Menu
		 */
		public function add_content_menu_dashboard_widgets() {
			$content_menu_dashboard_widgets    =    array();
			$counter                           =    1;
			$template_name                     =    'cpd-content-menu-dashboard-widget';
			$template_path                     =    CPD_Templates::get_template_path( $template_name );

			$posts_name = 'Journal Entries';

			$blog_id = get_current_blog_id();

			if( SITE_ID_CURRENT_SITE == $blog_id ) {
				$posts_name = 'Posts';
			}

			// Posts
			$content_menu_dashboard_widgets[]    =    array(
				'title'                   => __( $posts_name, $this->text_domain ),
				'dashicon'                => 'dashicons-admin-post',
				'desc'                    => '<p>' . __( 'This content type is for managing '.$posts_name.'.</p>', $this->text_domain ),
				'post_type'               => 'post',
				'button_label'            => __( 'Edit / Manage ' . $posts_name, $this->text_domain ),
				'css_class'               => 'post',
				'show_tax'                => TRUE,
				'link'                    => admin_url( 'edit.php' ),
				'call_to_action_text'     => __( 'Add New', $this->text_domain ),
				'call_to_action_link'     => admin_url( 'post-new.php' )
			);

			// Pages
			$content_menu_dashboard_widgets[]    =    array(
				'title'                   => __( 'Pages', $this->text_domain ),
				'dashicon'                => 'dashicons-admin-page',
				'desc'                    => '<p>' . __( 'This content type is for managing Pages.</p>', $this->text_domain ),
				'post_type'               => 'page',
				'button_label'            => __( 'Edit / Manage Pages', $this->text_domain ),
				'css_class'               => 'page',
				'show_tax'                => TRUE,
				'link'                    => admin_url( 'edit.php?post_type=page' ),
				'call_to_action_text'     => 'Add New',
				'call_to_action_link'     => admin_url( 'post-new.php?post_type=page' )
			);

			// If this is not the root site
			if( SITE_ID_CURRENT_SITE != $blog_id ) {

				// PPD
				$content_menu_dashboard_widgets[]    =    array(
					'title'                   => __( 'Activity Logs', $this->text_domain ),
					'dashicon'                => 'dashicons-index-card',
					'desc'                    => '<p>' . __( 'This content type is for managing your Activity Log.</p>', $this->text_domain ),
					'post_type'               => 'ppd',
					'button_label'            => __( 'Edit / Manage Activity Log', $this->text_domain ),
					'css_class'               => 'ppd',
					'show_tax'                => TRUE,
					'link'                    => admin_url( 'edit.php?post_type=ppd' ),
					'call_to_action_text'     => 'Add New',
					'call_to_action_link'     => admin_url( 'post-new.php?post_type=ppd' )
				);

				// Assessments
				$content_menu_dashboard_widgets[]    =    array(
					'title'                   => __( 'Assessments', $this->text_domain ),
					'dashicon'                => 'dashicons-yes',
					'desc'                    => '<p>' . __( 'This content type is for managing your Assessments.</p>', $this->text_domain ),
					'post_type'               => 'assessment',
					'button_label'            => __( 'Edit / Manage Assessments', $this->text_domain ),
					'css_class'               => 'assessment',
					'show_tax'                => TRUE,
					'link'                    => admin_url( 'edit.php?post_type=assessment' ),
					'call_to_action_text'     => 'Add New',
					'call_to_action_link'     => admin_url( 'post-new.php?post_type=assessment' )
				);
			}

			$widgets = apply_filters(
				'filter_cpd_content_menu_dashboard_widgets',
				$content_menu_dashboard_widgets
			);

			foreach ( $widgets as $widget ) {

				$function_name = 'cpd_content_menu_dashboard_widget_' . $counter;
				$$function_name = function () use ( $widget, $template_path ) {
					if ( $template_path !== FALSE ) {
						include $template_path;
					}
				};

				$position = 'side';
				$is_even = ( $counter % 2 == 0 );

				if ( $is_even ) {
					$position = 'normal';
				}
				$screen = get_current_screen();

				add_meta_box( 'cpd_content_menu_dashboard_widget_' . $counter, '<span class="mkdo-block-title dashicons-before ' . esc_attr( $widget[ 'dashicon' ] ) . '"></span> ' . esc_html( $widget[ 'title' ] ), $$function_name, $screen, $position );

				$counter++;
			}

		}

		/**
		 * Remove menus from the admin screen
		 *
		 * @hook  filter_cpd_remove_admin_menus  Filter menus to remove from the admin screen
		 */
		public function remove_admin_menus() {
			$user_id            =    get_current_user_id();
			$is_admin           =    current_user_can( 'manage_options' );
			$is_elevated_user   =    get_user_meta( $user_id, 'elevated_user', true ) == '1';
			$user_type          =    get_user_meta( $user_id, 'cpd_role', true );
			$admin_menus        =    array();

			// Remove for everyone
			$admin_menus[]      =    'edit.php';                                        // Posts
			$admin_menus[]      =    'edit.php?post_type=page';                         // Pages

			// Remove if is template
			if( CPD_Blogs::blog_is_template() ) {
				$admin_menus[]   =  'edit-comments.php';                                // Comments
			}

			// Remove for participants
			if ( $user_type == 'participant' ) {
				// $admin_menus[]        =    'themes.php';                             // Themes
			}

			// Remove for supervisors
			if ( $user_type == 'supervisor' ) {
			}

			// Remove for participants or supervisors
			if ( $user_type == 'participant' || $user_type == 'supervisor' ) {
			}

			// Remove for everyone but elevated users
			if ( !$is_elevated_user ) {
				$admin_menus[]        =    'seperator1';                               // Seperator
				$admin_menus[]        =    'tools.php';                                // Tools
				$admin_menus[]        =    'options-general.php';                      // Settings
				$admin_menus[]        =    'plugins.php';                              // Plugins
				$admin_menus[]        =    'wpseo_dashboard';                          // Yoast SEO
				$admin_menus[]        =    'all-in-one-seo-pack/aioseop_class.php';    // All in one SEO
				$admin_menus[]        =    'activity_log_page';                        // Activity Log
				$admin_menus[]        =    'edit.php?post_type=acf';                   // ACF
				$admin_menus[]        =    'wp-user-avatar';                           // Avatar
			}

			// Remove for everyone but admins
			if ( ! $is_admin || $is_elevated_user ) {

			}

			// Remove for everyone but elevated users and admins
			if ( ! $is_elevated_user && ! $is_admin ) {

			}

			$menus            =    apply_filters(
				'filter_cpd_remove_admin_menus',
				$admin_menus
			);

			foreach ( $menus as $menu ) {
				remove_menu_page( $menu );
			}
		}

		/**
		 * Remove sub menus from the admin screen
		 *
		 * @hook  filter_cpd_remove_sub_admin_menus  Filter menus to remove from the admin screen
		 */
		public function remove_admin_sub_menus() {
			global $submenu;
			$user_id            =    get_current_user_id();
			$is_admin           =    current_user_can( 'manage_options' );
			$is_elevated_user   =    get_user_meta( $user_id, 'elevated_user', true ) == '1';
			$user_type          =    get_user_meta( $user_id, 'cpd_role', true );
			$sub_menus          =    array();

			// Remove for everyone

			$blog_id = get_current_blog_id();

			if( SITE_ID_CURRENT_SITE != $blog_id ) {
				// Users
				$sub_menus[]    =    array(
					'parent'    =>    'users.php',
					'menu'      =>    'users.php',
				);

				$sub_menus[]    =    array(
					'parent'    =>    'users.php',
					'menu'      =>    'user-new.php',
				);

				if( $user_type != 'supervisor' ) {

					$sub_menus[]    =    array(
						'parent'    =>    'users.php',
						'menu'      =>    'cpd_settings_users_participants',
					);
					
				}

				$sub_menus[]    =    array(
					'parent'    =>    'users.php',
					'menu'      =>    'cpd_settings_users_supervisors',
				);
			}

			// Remove for participants
			if ( $user_type == 'participant' ) {
				// Users
				$sub_menus[]    =    array(
					'parent'    =>    'users.php',
					'menu'      =>    'users.php',
				);

				// New User
				$sub_menus[]    =    array(
					'parent'    =>    'users.php',
					'menu'      =>    'user-new.php',
				);

				// Discussion Settings
				$sub_menus[]    =    array(
					'parent'    =>    'users.php',
					'menu'      =>    'options-discussion.php',
				);

				// Discussion Settings
				$sub_menus[]    =    array(
					'parent'    =>    'options-general.php',
					'menu'      =>    'options-discussion.php',
				);

				// Menus
				$sub_menus[]    =    array(
					'parent'    =>    'themes.php',
					'menu'      =>    'nav-menus.php',
				);

				// Widgets
				$sub_menus[]    =    array(
					'parent'    =>    'themes.php',
					'menu'      =>    'widgets.php',
				);

				// My Sites
				$sub_menus[]    =    array(
					'parent'    =>    'index.php',
					'menu'      =>    'my-sites.php',
				);

				// Customize
				// $sub_menus[] =    array(
				// 	'parent'    =>    'themes.php',
				// 	'menu'      =>    'customize.php',
				// );
			}

			// Remove for supervisors
			if ( $user_type == 'supervisor' ) {
			}

			// Remove for participants or supervisors
			if ( $user_type == 'participant' || $user_type == 'supervisor' ) {

				// Users
				$sub_menus[]    =    array(
					'parent'    =>    'users.php',
					'menu'      =>    'users.php',
				);

				// New User
				$sub_menus[]    =    array(
					'parent'    =>    'users.php',
					'menu'      =>    'user-new.php',
				);

				// Themes
				$sub_menus[]    =    array(
					'parent'    =>    'themes.php',
					'menu'      =>    'themes.php',
				);
			}

			// Remove for everyone but elevated users
			if ( ! $is_elevated_user ) {

				// Themes
				$sub_menus[]    =    array(
					'parent'    =>    'themes.php',
					'menu'      =>    'themes.php',
				);

				// Theme Editor
				$sub_menus[]    =    array(
					'parent'    =>    'themes.php',
					'menu'      =>    'theme-editor.php',
				);

				// Delete Site
				$sub_menus[]    =    array(
					'parent'    =>    'tools.php',
					'menu'      =>    'ms-delete-site.php',
				);
			}

			// Remove for everyone but admins
			if ( !$is_admin || $is_elevated_user ) {

			}

			// Remove for everyone but elevated users and admins
			if ( ! $is_elevated_user && !$is_admin ) {

			}

			$menus = apply_filters(
				'filter_cpd_remove_sub_admin_menus',
				$sub_menus
			);

			foreach ( $menus as $menu ) {
				remove_submenu_page( $menu['parent'], $menu['menu'] );

				// if ($menu['parent'] == 'themes.php' && $menu['menu'] = 'customize.php') {
				//     foreach ($submenu as $key=>$menu) {
				//         if ($key == 'themes.php') {
				//             foreach ($menu as $item_key=>$item) {
				//                 if ( isset( $item[4] ) && $item[4] == 'hide-if-no-customize' ) {
				//                     unset( $submenu[$key][$item_key] );
				//                 }
				//             }
				//         }
				//     }
				// }
			}
		}

		/**
		 * Correct the content menu hierarchy
		 */
		public function correct_content_menu_hierarchy( $parent_file ) {
			global $current_screen;
			global $submenu;

			$pages        = array();
			$parent       = 'cpd_content_menu';

			if ( is_array( $submenu ) && isset( $submenu[ $parent ] ) ) {

				foreach ( (array) $submenu[ $parent ] as $item ) {

					if ( current_user_can( $item[1] ) ) {
						$menu_file = $item[2];
						if ( false !== ( $pos = strpos( $menu_file, '?' ) ) ) {
							$menu_file = substr( $menu_file, 0, $pos );
						}

						if ( $item[2] == 'edit.php' ) {
							$blog_id = get_current_blog_id();
							if( SITE_ID_CURRENT_SITE != $blog_id ) {
								$pages[] = 'Journal Entries';
							}else {
								$pages[] = 'Posts';
							}
						} elseif ( $item[2] == 'edit.php?post_type=page' || $item[2] == 'edit.php?post_type=page&page=cms-tpv-page-page' ) {
							$pages[] = 'Pages';
						} else {
							$pages[] = $item[0];
						}

					}
				}
			}

			$post_type = get_post_type_object( $current_screen->post_type );

			if ( isset( $post_type->labels ) && isset( $post_type->labels->name ) ) {
				$post_type = $post_type->labels->name;
			}

			/* get the base of the current screen */
			$screenbase = $current_screen->base;

			/* if this is the edit.php base */
			if ( ( $screenbase == 'edit' && in_array( $post_type, $pages ) )|| ( $screenbase == 'post' && in_array( $post_type, $pages ) ) ) {

				/* set the parent file slug to the custom content page */
				$parent_file = $parent;

			}

			if ( defined( 'CMS_TPV_URL' ) && $screenbase == 'pages_page_cms-tpv-page-page' && in_array( $post_type, $pages ) ) {
				$parent_file = $parent;
			}

			if ( strpos( $screenbase, '-network' ) ) {
				if (
					$parent_file == 'sites.php'			||
					$parent_file == 'users.php'			||
					$parent_file == 'themes.php'		||
					$parent_file == 'plugins.php'		||
					$parent_file == 'settings.php'		||
					$parent_file == 'update-core.php'
				) {
					$parent_file = 'index.php';
				}
			}

			/* return the new parent file */

			return $parent_file;
		}

		/**
		 * Correct the content sub menu hierarchy
		 */
		public function correct_content_menu_sub_hierarchy() {
			global $submenu;

			if ( array_key_exists( 'edit.php?post_type=page', $submenu ) ) {

				foreach ( $submenu['edit.php?post_type=page'] as $key=>$smenu ) {
					$submenu['edit.php?post_type=page'][$key][2] = $smenu[2] . '&post_type=page';
				}
			}
		}

		/**
		 * Add sub menus to admin menu
		 *
		 * @hook  filter_cpd_add_admin_sub_menus  Filter to add sub menus to admin menus
		 */
		public function add_admin_sub_menus() {
			$sub_menus            =    array();

			// Add for all users

			// Add for network admins
			if ( is_super_admin() ) {

				// Network Admin Menus
				$sub_menus[] = array(
					'parent'        =>    'index.php',
					'page_title'    =>    'Network Settings',
					'menu_title'    =>    'Network Settings',
					'capability'    =>    'manage_network',
					'menu_slug'     =>    'network/index.php',
					'function'      =>    ''
				);
			}

			$menus = apply_filters(
				'filter_cpd_add_admin_sub_menus',
				$sub_menus
			);

			foreach ( $menus as $menu ) {
				add_submenu_page(
					$menu['parent'],
					$menu['page_title'],
					$menu['menu_title'],
					$menu['capability'],
					$menu['menu_slug'],
					$menu['function']
				);
			}
		}

		/**
		 * Add menus to network admin menu
		 *
		 * @hook  filter_cpd_add_network_admin_menus  Filter to add menus to network admin menus
		 */
		public function add_network_admin_menus() {

			$network_menus = array();

			// Add for all users

			// Add for network admins
			if ( is_super_admin() ) {

				$network_menus[]    =    array(
					'page_title'    =>    'Dashboard',
					'menu_title'    =>    'Dashboard',
					'capability'    =>    'manage_network',
					'menu_slug'     =>    '../',
					'function'      =>    '',
					'dashicon'      =>    'dashicons-dashboard',
					'position'      =>    1
				);
			}

			$menus = apply_filters(
				'filter_cpd_add_network_admin_menus',
				$network_menus
			);

			foreach ( $menus as $menu ) {
				add_menu_page(
					$menu['page_title'],
					$menu['menu_title'],
					$menu['capability'],
					$menu['menu_slug'],
					$menu['function'],
					$menu['dashicon'],
					$menu['position']
				);
			}
		}

		/**
		 * Rename network admin menus
		 */
		public function rename_network_admin_menus() {
			if ( is_super_admin() ) {

				global $menu;

				// Rename menu items
				foreach ( $menu as $key=>&$menu_item ) {
					if ( $menu_item[0] == 'Dashboard' || $menu_item[0] == 'Network Settings' ) {

						$menu_item[0]    = 'Network Settings';
						$menu_item[6]    = 'dashicons-admin-site';
						$network         = $menu[$key];
						unset( $menu[$key] );
						$menu[2]         = $network;
					}
				}
			}
		}

		/**
		 * Rename admin menus
		 */
		public function rename_admin_menus() {
			if ( is_super_admin() ) {

				global $menu;

				// Rename menu items
				foreach ( $menu as $key=>&$menu_item ) {
					$blog_id = get_current_blog_id();
					if( SITE_ID_CURRENT_SITE != $blog_id ) {

						if ( $menu_item[0] == 'Users' ) {
							$menu_item[0] = 'Profile';
						}
					}
				}
			}
		}
	}
}
