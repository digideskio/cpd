<?php

// Exit if accessed directly
defined( 'ABSPATH' ) || exit;

if ( !class_exists( 'CPD_Users' ) ) {

	/**
	 * Users
	 *
	 * Manage user privaliges
	 *
	 * @package    CPD
	 * @subpackage CPD/admin
	 * @author     Make Do <hello@makedo.in>
	 */
	class CPD_Users {

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
		 * Initialize the class and set its properties.
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
		 * Set Admin Capabilities
		 *
		 * @hook  filter_cpd_set_admin_capabilities  Filter the admin capabilities
		 *
		 * @param array   $capabilities Array of Capabilities
		 */
		public function set_admin_capabilities( $capabilities ) {
			$user_id            = get_current_user_id();
			$is_elevated_user    = get_user_meta( $user_id, 'elevated_user', true ) == '1';

			// Setup an array of capabilities to change - filterable
			$set_capabilities = apply_filters(
				'filter_cpd_set_admin_capabilities',
				array(

					// Update WordPress
					array(
						'name'        => 'update_core',
						'action'    => $is_elevated_user,
					),

					// Update Plugins
					array(
						'name'        => 'update_plugins',
						'action'    => $is_elevated_user
					),

					// Activate Plugins
					array(
						'name'        => 'activate_plugins',
						'action'    => $is_elevated_user,
					),

					// Install Plugins
					array(
						'name'        => 'install_plugins',
						'action'    => $is_elevated_user,
					),
				)
			);

			// Loop through each capability
			foreach ( $set_capabilities as $capability ) {

				// Check if the user has the capability
				if ( ! empty( $capabilities[ $capability[ 'name' ] ] ) ) {

					// Action the capability - adding or remove accordingly
					$capabilities[ $capability[ 'name' ] ] = $capability[ 'action' ];
				}

			}

			/* return the modified capabilities */

			return $capabilities;

		}

		/**
		 * Create roles for Supervisors and Participants
		 *
		 * @hook  filter_cpd_remove_participant_capabilities  Capabilities to remove from participants
		 */
		public function create_roles() {
			// Create roles with same capabilities as administrators
			add_role( 'supervisor',    'Supervisor',    get_role( 'editor' )->capabilities );
			add_role( 'participant',    'Participant',    get_role( 'editor' )->capabilities );

			$role    = get_role( 'participant' );
			$role->add_cap( 'edit_theme_options' );
			$role->add_cap( 'is_participant' );
			$role->add_cap( 'edit_others_posts' );

			$barred_participant_capabilities    =    apply_filters(
				'filter_cpd_remove_participant_capabilities',
				array(
					'edit_users',
					'create_users',
					// 'edit_others_posts',
					'edit_others_pages',
					'delete_others_posts',
					'delete_others_pages',
					'manage_privacy'
				)
			);

			foreach ( $barred_participant_capabilities as $capability ) {
				$role->remove_cap( $capability );
			}

			// Make sure supervisors can copy assignments
			$role = get_role( 'supervisor' );
			$role->add_cap( 'copy_assignments' );
			$role->add_cap( 'edit_theme_options' );
			$role->add_cap( 'supervise_users' );
			$role->add_cap( 'manage_privacy' );

			// Let supervisors manage users
			$role->add_cap('edit_users');
	        $role->add_cap('list_users');
	        $role->add_cap('promote_users');
	        $role->add_cap('create_users');
	        $role->add_cap('add_users');
	        $role->add_cap('delete_users');

			// Make sure admins have the supervisor privilege
			$role = get_role( 'administrator' );
			$role->add_cap( 'supervise_users' );
			$role->add_cap( 'manage_privacy' );
		}

		/**
		 * On users role change, set the user role meta
		 *
		 * @param int     $user_id The users id
		 * @param strong  $role    The role that has been assigned
		 */
		public function set_user_role( $user_id, $role ) {
			if ( 'supervisor' === $role || 'participant' === $role ) {
				update_user_meta( $user_id, 'cpd_role', $role );
			}
		}

		/**
		 * Remove participant capabilities
		 *
		 * @param array   $all_roles All user roles
		 * @return array    $all_roles      All user roles
		 *
		 * @hook  filter_cpd_remove_participant_capabilities  Capabilities to remove from participants
		 */
		public function remove_participant_capabilities( $all_roles ) {
			$user_id                =    get_current_user_id();
			$cpd_role                =    get_user_meta( $user_id, 'cpd_role', true );

			$barred_capabilities    =    apply_filters(
				'filter_cpd_remove_participant_capabilities',
				array(
					'edit_users',
					'create_users',
					'edit_others_posts',
					'edit_others_pages',
					'delete_others_posts',
					'delete_others_pages'
				)
			);

			if ( $cpd_role == 'participant' ) {

				foreach ( $all_roles as $rolename=>$role ) {

					$capabilities = $role['capabilities'];

					foreach ( $barred_capabilities as $capability ) {

						if ( array_key_exists( $capability, $capabilities ) && isset( $capabilities[$capability] ) ) {
							unset( $all_roles[$rolename] );
						}
					}
				}
			}

			return $all_roles;
		}

		/**
		 * Prevent the participant removing a supervisor
		 *
		 * @param array   $allcaps All the capabilities of the user
		 * @param array   $cap     [0] Required capability
		 * @param array   $args    [0] Requested capability
		 *                          [1] User ID
		 *                           [2] Associated object ID
		 */
		public function prevent_partcipant_removing_supervisor( $allcaps, $caps, $args ) {
			$requested_capability    =    $args[0];
			$user_id                =    $args[1];

			if ( array_key_exists( 2, $args ) ) {

				$removing_user_id        =    $args[2];

				if ( $requested_capability === 'remove_user' ) {

					if ( get_user_meta( $user_id, 'cpd_role', true ) == 'participant' && get_user_meta( $removing_user_id, 'cpd_role', true ) == 'supervisor' ) {
						$allcaps['remove_users'] = false;
					}
				}
			}

			return $allcaps;
		}

		/**
		 * Redirect user on creation
		 *
		 * @param int     $user_id The user ID
		 */
		public function redirect_on_create_user( $user_id ) {
			// We need to stop the redirect to the user-new.php page happening.
			// the only way is to recreate the password and send the user notification ourselves.
			// then we can forward to the user profile where the admin can set their supervisors
			// (if they are a participant) or participants (if they are a supervisor)
			if ( $user_id ) {

				// all new users get access (as a subscriber) to the default blog
				add_user_to_blog( BLOG_ID_CURRENT_SITE, $user_id, 'subscriber' );

				// create a new password, because we need to send it and we don't have access to the one already generated
				$password    = wp_generate_password( 12, false );

				wp_set_password( $password, $user_id );

				wp_new_user_notification( $user_id, null, 'both' );

				wp_redirect( add_query_arg( array( 'user_id' => $user_id ), network_admin_url( 'user-edit.php#cpd_profile' ) ) );

				exit;
			}

		}

		/**
		 * Get all multisite users
		 */
		public static function get_multisite_users() {
            $users    = array();
            $user_ids = array();
            $blogs    = wp_get_sites();

			foreach ( $blogs as $blog ) {

				$blog_users = get_users( array( 'blog_id' => $blog['blog_id'] ) );

				if ( is_array( $blog_users ) ) {
					foreach ( $blog_users as $user ) {
						if ( !in_array( $user->ID, $user_ids, true ) ) {
							$users[]    = $user;
							$user_ids[] = $user->ID;
						}
					}
				}
			}

			return $users;
		}

		/**
		 * Get all participants
		 */
		public static function get_participants() {
            $users    = array();
            $user_ids = array();
            $blogs    = wp_get_sites();

			foreach ( $blogs as $blog ) {

				$blog_users    =    get_users(
					array(
						'blog_id'         =>    $blog['blog_id'],
						'meta_key'        =>    'cpd_role',
						'meta_value'      =>    'participant',
						'meta_compare'    =>    '='
					)
				);

				if ( is_array( $blog_users ) ) {
					foreach ( $blog_users as $user ) {
						if ( !in_array( $user->ID, $user_ids, true ) ) {
							$users[]    = $user;
							$user_ids[] = $user->ID;
						}
					}
				}
			}

			return $users;
		}

		/**
		 * Get all supervisors
		 */
		public static function get_supervisors() {
			$users                =    array();
			$user_ids             =    array();
			$blogs                =    wp_get_sites();

			foreach ( $blogs as $blog ) {

				$blog_users    =    get_users(
					array(
						'blog_id'         =>    $blog['blog_id'],
						'meta_key'        =>    'cpd_role',
						'meta_value'      =>    'supervisor',
						'meta_compare'    =>    '='
					)
				);

				if ( is_array( $blog_users ) ) {
					foreach ( $blog_users as $user ) {
						if ( !in_array( $user->ID, $user_ids, true ) ) {
							$users[]    = $user;
							$user_ids[] = $user->ID;
						}
					}
				}
			}

			return $users;
		}

		/**
		 * Remove user from related supervisors
		 *
		 * @param int     $user_id      The user ID
		 * @param array   $participants Participants to be removed from
		 */
		public static function remove_user_from_related_supervisors( $user_id, $participants ) {
			foreach ( $participants as $participant ) {

				if ( is_object( $participant ) ) {
					$participant                    =    $participant->ID;
				}
				$participant_supervisors            =    get_user_meta( $participant, 'cpd_related_supervisors', true );
				$position                            =    array_search( $user_id, (array) $participant_supervisors );
				if ( $position !== FALSE ) {
					unset( $participant_supervisors[$position] );
					update_user_meta( $participant, 'cpd_related_supervisors', $participant_supervisors );
				}
			}
		}

		/**
		 * Remove user from related participants
		 *
		 * @param int     $user_id     The user ID
		 * @param array   $supervisors Supervisors to be removed from
		 */
		public static function remove_user_from_related_participants( $user_id, $supervisors ) {
			foreach ( $supervisors as $supervisor ) {

				if ( is_object( $supervisor ) ) {
					$supervisor                    =    $supervisor->ID;
				}
				$supervisor_participants            =    get_user_meta( $supervisor, 'cpd_related_participants', true );
				$position                            =    array_search( $user_id, (array) $supervisor_participants );
				if ( $position !== FALSE ) {
					unset( $supervisor_participants[$position] );
					update_user_meta( $supervisor, 'cpd_related_participants', $supervisor_participants );
				}
			}
		}

		/**
		 * Add a CPD relationship
		 *
		 * @param int     $supervisor  The supervisor ID
		 * @param int     $participant The participant ID
		 */
		public static function add_cpd_relationship( $supervisor, $participant ) {
			$supervisors     = get_user_meta( $participant, 'cpd_related_supervisors', true );
			$participants    = get_user_meta( $supervisor, 'cpd_related_participants', true );

			$supervisors  =   is_array($supervisors) ? $supervisors : array();
			$participants =   is_array($participants) ? $participants : array();

			if ( !in_array( $supervisor, $supervisors ) ) {
				$supervisors[] = $supervisor;
			}

			if ( !in_array( $participant, $participants ) ) {
				$participants[] = $participant;
			}

			update_user_meta( $participant, 'cpd_related_supervisors', $supervisors );
			update_user_meta( $supervisor, 'cpd_related_participants', $participants );
		}

		/**
		 * Remove a CPD relationship
		 *
		 * @param int     $supervisor  The supervisor ID
		 * @param int     $participant The participant ID
		 */
		public static function remove_cpd_relationship( $supervisor, $participant ) {
			$supervisors    = get_user_meta( $participant, 'cpd_related_supervisors', true );
			$participants    = get_user_meta( $supervisor, 'cpd_related_participants', true );

			if ( in_array( $supervisor, (array) $supervisors ) ) {
				$position    =    array_search( $supervisor, (array) $supervisors );
				unset( $supervisors[$position] );
			}

			if ( in_array( $participant, (array) $participants ) ) {
				$position    =    array_search( $participant, (array) $participants );
				unset( $participants[$position] );
			}

			update_user_meta( $participant, 'cpd_related_supervisors', $supervisors );
			update_user_meta( $supervisor, 'cpd_related_participants', $participants );
		}

		/**
		 * Add a supervisor to its participants journals
		 *
		 * @param int     $user_id The user ID
		 */
		public static function add_supervisor_to_participant_journals( $user_id ) {
			$all_cpd_journals        =    wp_get_sites();
			$should_have_journals    =    array();
			$participants            =    get_user_meta( $user_id, 'cpd_related_participants', true );

			if( ! is_array( $participants ) ) {
				$participants = array();
			}

			if( ! is_array( $all_cpd_journals ) ) {
				$all_cpd_journals = array();
			}

			foreach ( $participants as $participant ) {
				$blogs = get_blogs_of_user( $participant );
				if ( is_array( $blogs ) && count( $blogs ) > 0 ) {
					foreach ( $blogs as $blog ) {
						$should_have_journals[] = $blog->userblog_id;
					}
				}
			}
			if ( count( $all_cpd_journals ) > 0 ) {
				foreach ( $all_cpd_journals as $journal ) {
					$journal = $journal['blog_id'];
					if ( in_array( $journal, $should_have_journals ) ) {
						add_user_to_blog( $journal, $user_id, 'supervisor' );
					} else {
						remove_user_from_blog( $user_id, $journal );
					}
				}
			}
		}

		/**
		 * Remove a user from all blogs
		 *
		 * @param int     $user_id The user ID
		 */
		public static function remove_user_from_blogs( $user_id ) {
			$current_journals = get_blogs_of_user( $user_id );
			if ( count( $current_journals ) > 0 ) {

				foreach ( $current_journals as $journal ) {
					remove_user_from_blog( $user_id, $journal->userblog_id );
				}
			}
		}

		/**
		 * Create a user journal
		 *
		 * @param int     $user_id The user ID
		 */
		public static function create_user_journal( $user_id, $base_id ) {

			$blogs           =    CPD_Blogs::get_instance();

			$user            =    get_userdata( $user_id );
			$user_data       =    $user->data;
			$current_site    =    network_site_url();
			$domain        	 =    parse_url( network_site_url(), PHP_URL_HOST );
			$path            =    sanitize_title( $user_data->user_login ) . '/';
			$title 			 =	  'CPD Journal for ' . $user_data->user_nicename;

			// $cpd_settings    =    get_option( 'cpd_new_blog_options' );
			// $cpd_settings    =    preg_replace( '/[\n\r]+/', '&', $cpd_settings );
			// $cpd_settings    =    preg_replace( '/[\s\:]+/', '=', $cpd_settings );
			// parse_str( $cpd_settings, $options );

			// $cpd_journal     =    wpmu_create_blog( $domain, $path, 'CPD Journal for ' . $user_data->user_nicename, $user_id, $options, 1 );

			$blog = get_blog_details( $path );

			if( empty($blog) ) {
				$blogs->copy_blog( $path, $title, $base_id, false );
			} else {
				$path = uniqid() . '/';
				$blogs->copy_blog( $path, $title, $base_id, false );
			}
		}

		/**
		 * Redirect users on redirect
		 *
		 * @param string  $redirect_to         location of redirect
		 * @param string  $request_redirect_to location of new redirect
		 * @param object  $user                User object
		 * @return string                      New redirect
		 */
		public function login_redirect( $redirect_to, $request_redirect_to, $user ) {
			if ( $user && is_object( $user ) && !is_wp_error( $user ) && is_a( $user, 'WP_User' ) ) {

				$user_type                =    get_user_meta( $user->ID, 'cpd_role', true );

				if ( $user_type == 'participant' ) {
                    $primary_blog = get_active_blog_for_user( $user->ID );
                    $redirect_to  = get_admin_url( $primary_blog->blog_id );
				} else if( $user_type == 'supervisor' ) {

                    $user_blogs = get_blogs_of_user( $user->ID );

					if( is_array( $user_blogs ) ) {
						if( count( $user_blogs ) == 2 ) {
							$last       = end( $user_blogs );
							$redirect_to  = get_admin_url( $last->userblog_id );
						} else if( count( $user_blogs ) > 2 ) {
							$redirect_to  = admin_url( 'index.php' );
						}
					}
				}
			}

			return $redirect_to;
		}

		/**
		 * If the site settings dictate it, force the user to login
		 */
		public function force_login() {
			if ( !in_array( $GLOBALS['pagenow'], array( 'wp-login.php', 'wp-register.php' ) ) ) {
				$cpd_login_to_view = get_option( 'cpd_login_to_view', NULL );
				if( $cpd_login_to_view == 'true' && !is_user_logged_in() ) {
					auth_redirect();
					exit;
				}
			}
		}

		/**
		 * Add a custom message to the login page
		 */
		public function force_login_message( $message ) {

			if ( in_array( $GLOBALS['pagenow'], array( 'wp-login.php', 'wp-register.php' ) ) ) {
				$cpd_login_to_view = get_option( 'cpd_login_to_view', NULL );
				if( $cpd_login_to_view == 'true' && !is_user_logged_in() ) {
					if( empty( $message ) ) {
						return '<div id="login_error"><p><strong>NOTE: </strong> You must be logged in to view this journal.</p></div>';
					}
				}
			}

			return $message;
		}

		/**
		 * Is the user a supervisor of any Journal
		 */
		public static function user_is_site_supervisor( $user ) {
			$supervisors   = CPD_Users::get_supervisors();
			$is_supervisor = false;

			if( !is_array( $supervisors ) ) {
				$supervisors = array();
			}

			foreach( $supervisors as $supervisor ) {
				if( $supervisor->ID == $user->ID ) {
					$is_supervisor = true;
					break;
				}
			}

			return $is_supervisor;
		}

		/**
		 * Is the user a participant of any Journal
		 */
		public static function user_is_site_participant( $user ) {
			$participants   = CPD_Users::get_participants();
			$is_participant = false;

			if( !is_array( $participants ) ) {
				$participants = array();
			}

			foreach( $participants as $participant ) {
				if( $participant->ID == $user->ID ) {
					$is_participant = true;
					break;
				}
			}

			return $is_participant;
		}

		/**
		 * If an email address is entered in the username box, then look up the matching username and authenticate as per normal, using that.
		 *
		 * @param string $user
		 * @param string $username
		 * @param string $password
		 * @return Results of autheticating via wp_authenticate_username_password(), using the username found when looking up via email.
		 */
		public function authenticate_email_username_password( $user, $username, $password ) {

			if ( is_a( $user, 'WP_User' ) )
				return $user;

			if ( !empty( $username ) ) {
				$username = str_replace( '&', '&amp;', stripslashes( $username ) );
				$user = get_user_by( 'email', $username );
				if ( isset( $user, $user->user_login, $user->user_status ) && 0 == (int) $user->user_status )
					$username = $user->user_login;
			}

			return wp_authenticate_username_password( null, $username, $password );
		}

		public function prevent_cpd_roles_on_root() {

			$blog_id = get_current_blog_id();

			if( SITE_ID_CURRENT_SITE == $blog_id ) {

				$users = get_users(
					array(
						'blog_id' => $blog_id
					)
				);
				foreach( $users as &$user ) {
					$roles    = $user->roles;
					if( in_array( 'participant', $roles ) || in_array( 'supervisor', $roles ) ) {
						$user->set_role('subscriber');
					}
				}

			}
		}

		public function prevent_admin_being_supervisor() {

			$blog_id = get_current_blog_id();

			if( SITE_ID_CURRENT_SITE == $blog_id ) {

				$current_user 					= wp_get_current_user();
				$user_id                        = $current_user->ID;
				$roles 							= $current_user->roles;
				$is_elevated_user 				= get_user_meta( $current_user->ID, 'elevated_user', TRUE ) == '1';
				$all_supervisors 	            = CPD_Users::get_supervisors();
				$all_participants 	            = CPD_Users::get_participants();

				if( is_super_admin() ) {

					if( in_array( 'supervisor', $roles ) || in_array( 'participant', $roles ) || self::user_is_site_supervisor( $current_user ) || self::user_is_site_participant( $current_user ) ) {

						// Remove the CPD Role
						update_user_meta( $user_id, 'cpd_role', 'none' );

						// Remove the user from all blogs
						CPD_Users::remove_user_from_blogs( $user_id );

						// Remove related participants
						delete_user_meta( $user_id, 'cpd_related_participants' );

						// Remove related supervisors
						delete_user_meta( $user_id, 'cpd_related_supervisors' );

						// Remove from all participants
						CPD_Users::remove_user_from_related_supervisors( $user_id, $all_participants );

						// Remove from all supervisors
						CPD_Users::remove_user_from_related_participants( $user_id, $all_supervisors );

						// Add them to the default blog as an admin
						add_user_to_blog( BLOG_ID_CURRENT_SITE, $user_id, 'administrator' );
					}
				}

			}
		}
	}
}
