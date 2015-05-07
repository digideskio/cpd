<?php
/**
 * The user-specific functionality of the plugin.
 *
 * @link       http://makedo.in
 * @since      1.0.0
 *
 * @package    CPD
 * @subpackage CPD/admin
 */

// Exit if accessed directly
defined( 'ABSPATH' ) || exit;

if( !class_exists( 'CPD_Users' ) ) {

/**
 * The admin-specific functionality of the plugin.
 *
 * Defines the admin settings
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
			self::$instance = new self;
		}
		return self::$instance;
	}

	/**
	 * Initialize the class and set its properties.
	 *
	 * @since    2.0.0
	 */
	public function __construct() {
		
	}

	/**
	 * Initialize the class and set its properties.
	 *
	 * @var      string    $text_domain       The text domain of the plugin.
	 *
	 * @since    2.0.0
	 **/
	public function set_text_domain( $text_domain ) { 
		$this->text_domain = $text_domain;
	}

	/**
	 * Set Admin Capabilities
	 *
	 * @hook 	filter_cpd_set_admin_capabilities 	Filter the admin capabilities
	 * 
	 * @param array 	$capabilities 	Array of Capabilities
	 * 
	 * @since    2.0.0
	 */
	public function set_admin_capabilities( $capabilities ) {
	
		$user_id 			= get_current_user_id();
		$is_elevated_user 	= get_user_meta( $user_id, 'elevated_user', TRUE ) == '1';
		
		// Setup an array of capabilities to change - filterable
		$set_capabilities = apply_filters(
			'filter_cpd_set_admin_capabilities',
			array(

				// Update WordPress
				array(
					'name' 		=> 'update_core',
					'action' 	=> $is_elevated_user,
				),

				// Update Plugins
				array(
					'name' 		=> 'update_plugins',
					'action' 	=> $is_elevated_user
				),

				// Activate Plugins
				array(
					'name' 		=> 'activate_plugins',
					'action' 	=> $is_elevated_user,
				),

				// Install Plugins
				array(
					'name' 		=> 'install_plugins',
					'action' 	=> $is_elevated_user,
				),
			)
		);
		
		// Loop through each capability
		foreach( $set_capabilities as $capability ) {
			
			// Check if the user has the capability
			if( ! empty( $capabilities[ $capability[ 'name' ] ] ) ) {
			
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
	 * @hook 	filter_cpd_remove_participant_capabilities 	Capabilities to remove from participants
	 *
	 * @since    2.0.0
	 */
	public function create_roles() {

		// Create roles with same capabilities as administrators
		add_role( 'supervisor', 	'Supervisor', 	get_role('editor')->capabilities );
		add_role( 'participant', 	'Participant', 	get_role('editor')->capabilities );

		$role 	= get_role( 'participant' );

		$barred_participant_capabilities 	= 	apply_filters(
													'filter_cpd_remove_participant_capabilities',
													array(
														'edit_users',
														'create_users',
														'edit_posts',
														'edit_others_posts',
														'edit_others_pages',
														'delete_others_posts',
														'delete_others_pages'
													)
												);

		foreach( $barred_participant_capabilities as $capability ) {
			$role->remove_cap( $capability );
		}
	}

	/**
	 * On users role change, set the user role meta
	 * 
	 * @param int 		$user_id 	The users id
	 * @param strong 	$role 		The role that has been assigned
	 *
	 * @since    2.0.0
	 */
	public function set_user_role( $user_id, $role ) {

		if( 'supervisor' === $role || 'participant' === $role ){
			update_user_meta( $user_id, 'cpd_role', $role );
		}
	}

	/**
	 * Remove participant capabilities
	 * 
	 * @param  array 	$all_roles 		All user roles
	 * @return array    $all_roles      All user roles
	 *
	 * @hook 	filter_cpd_remove_participant_capabilities 	Capabilities to remove from participants
	 *
	 * @since   2.0.0
	 */
	public function remove_participant_capabilities( $all_roles ){

		$user_id 				=	get_current_user_id();
		$cpd_role 				= 	get_user_meta( $user_id, 'cpd_role', TRUE );
		
		$barred_capabilities 	= 	apply_filters(
										'filter_cpd_remove_participant_capabilities',
										array(
											'edit_users',
											'create_users',
											'edit_posts',
											'edit_others_posts',
											'edit_others_pages',
											'delete_others_posts',
											'delete_others_pages'
										)
									);
		
		if( $cpd_role == 'participant' ) {

			foreach( $all_roles as $rolename=>$role ) {

				$capabilities = $role['capabilities'];

				foreach( $barred_capabilities as $capability ) {

					if( array_key_exists( $capability, $capabilities ) && isset( $capabilities[$capability] ) ) {
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
	 * @param array 	$allcaps 	All the capabilities of the user
	 * @param array 	$cap     	[0] Required capability
	 * @param array 	$args    	[0] Requested capability
	 *                        		[1] User ID
	 *                        	 	[2] Associated object ID
	 *
	 * @since   2.0.0
	 */
	public function prevent_partcipant_removing_supervisor( $allcaps, $caps, $args ) {
			
		$requested_capability 	=	$args[0];
		$user_id 				=	$args[1];

		if( array_key_exists( 2, $args ) ) {

			$removing_user_id 		=	$args[2];
			
			if( $requested_capability === 'remove_user' ) {

				if( get_user_meta( $user_id, 'cpd_role', TRUE ) == 'participant' && get_user_meta( $removing_user_id, 'cpd_role', TRUE ) == 'supervisor' ) {
					$allcaps['remove_users'] = FALSE;
				}
			}
		}

		return $allcaps;
	}

	/**
	 * Redirect user on creation
	 * 
	 * @param  int 		$user_id 	The user ID
	 *
	 * @since   2.0.0
	 */
	public function redirect_on_create_user( $user_id ){

		// We need to stop the redirect to the user-new.php page happening.
		// the only way is to recreate the password and send the user notification ourselves.
		// then we can forward to the user profile where the admin can set their supervisors
		// (if they are a participant) or participants (if they are a supervisor)
		if ( $user_id ) {

			// all new users get access (as a subscriber) to the default blog
			add_user_to_blog( BLOG_ID_CURRENT_SITE, $user_id, 'subscriber' );

			// create a new password, because we need to send it and we don't have access to the one already generated 
			$password 	= wp_generate_password( 12, false);
			
			wp_set_password( $password, $user_id );

			wp_new_user_notification( $user_id, $password );

			wp_redirect( add_query_arg( array( 'user_id' => $user_id ), network_admin_url( 'user-edit.php#cpd_profile' ) ) );
			
			exit;
		}

	}

	/**
	 * Get all multisite users
	 *
	 * @since   2.0.0
	 */
	public static function get_multisite_users() {

		$users 				= 	array();
		$user_ids 			=	array();
		$blogs 				= 	wp_get_sites();

		foreach ( $blogs as $blog ){

			$blog_users = get_users( array( 'blog_id' => $blog['blog_id'] ) );

			if( is_array( $blog_users ) ) {
				foreach( $blog_users as $user ) {
					if( !in_array( $user->ID, $user_ids, TRUE ) ) {
						$users[] 	= $user;
						$user_ids[] = $user->ID;
					}
				}
			}
		}

		return $users;
	}

	/**
	 * Get all participants
	 *
	 * @since   2.0.0
	 */
	public static function get_participants() {

		$users 				= 	array();
		$user_ids 			=	array();
		$blogs 				= 	wp_get_sites();

		foreach ( $blogs as $blog ){

			$blog_users 	= 	get_users(
									array( 
										'blog_id' 		=> 	$blog['blog_id'],
										'meta_key' 		=>	'cpd_role',
										'meta_value'	=>	'participant',
										'meta_compare' 	=>	'='
									) 
								);

			if( is_array( $blog_users ) ) {
				foreach( $blog_users as $user ) {
					if( !in_array( $user->ID, $user_ids, TRUE ) ) {
						$users[] 	= $user;
						$user_ids[] = $user->ID;
					}
				}
			}
		}

		return $users;
	}

	/**
	 * Get all supervisors
	 *
	 * @since   2.0.0
	 */
	public static function get_supervisors() {

		$users 				= 	array();
		$user_ids 			=	array();
		$blogs 				= 	wp_get_sites();

		foreach ( $blogs as $blog ){

			$blog_users 	= 	get_users(
									array( 
										'blog_id' 		=> 	$blog['blog_id'],
										'meta_key' 		=>	'cpd_role',
										'meta_value'	=>	'supervisor',
										'meta_compare' 	=>	'='
									) 
								);

			if( is_array( $blog_users ) ) {
				foreach( $blog_users as $user ) {
					if( !in_array( $user->ID, $user_ids, TRUE ) ) {
						$users[] 	= $user;
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
	 * @param  int 		$user_id 		The user ID
	 * @param  array 	$participants 	Participants to be removed from
	 *
	 * @since   2.0.0
	 */
	public static function remove_user_from_related_supervisors( $user_id, $participants ) {

		foreach( $participants as $participant ) {

			if( is_object( $participant ) ) {
				$participant 					= 	$participant->ID;
			}
			$participant_supervisors 			= 	get_user_meta( $participant, 'cpd_related_supervisors', TRUE );
			$position 							= 	array_search( $user_id, (array)$participant_supervisors );
			if( $position !== FALSE ) {
				unset( $participant_supervisors[$position] );
				update_user_meta( $participant, 'cpd_related_supervisors', $participant_supervisors );
			}
		}
	}

	/**
	 * Remove user from related participants
	 *
	 * @param  int 		$user_id 		The user ID
	 * @param  array 	$supervisors 	Supervisors to be removed from
	 *
	 * @since   2.0.0
	 */
	public static function remove_user_from_related_participants( $user_id, $supervisors ) {

		foreach( $supervisors as $supervisor ) {

			if( is_object( $supervisor ) ) {
				$supervisor 					= 	$supervisor->ID;
			}
			$supervisor_participants 			= 	get_user_meta( $supervisor, 'cpd_related_participants', TRUE );
			$position 							= 	array_search( $user_id, (array)$supervisor_participants );
			if( $position !== FALSE ) {
				unset( $supervisor_participants[$position] );
				update_user_meta( $supervisor, 'cpd_related_participants', $supervisor_participants );
			}
		}
	}

	/**
	 * Add a CPD relationship
	 *
	 * @param  int 		$supervisor 		The supervisor ID
	 * @param  int 		$participant 		The participant ID
	 *
	 * @since   2.0.0
	 */
	public static function add_cpd_relationship( $supervisor, $participant ) {

		$supervisors 	= get_user_meta( $participant, 'cpd_related_supervisors', TRUE );
		$participants 	= get_user_meta( $supervisor, 'cpd_related_participants', TRUE );

		if( !in_array( $supervisor, $supervisors ) ) {
			$supervisors[] = $supervisor;
		}

		if( !in_array( $participant, $participants ) ) {
			$participants[] = $participant;
		}

		update_user_meta( $participant, 'cpd_related_supervisors', $supervisors );
		update_user_meta( $supervisor, 'cpd_related_participants', $participants );
	}

	/**
	 * Remove a CPD relationship
	 *
	 * @param  int 		$supervisor 		The supervisor ID
	 * @param  int 		$participant 		The participant ID
	 *
	 * @since   2.0.0
	 */
	public static function remove_cpd_relationship( $supervisor, $participant ) {

		$supervisors 	= get_user_meta( $participant, 'cpd_related_supervisors', TRUE );
		$participants 	= get_user_meta( $supervisor, 'cpd_related_participants', TRUE );

		if( in_array( $supervisor, $supervisors ) ) {
			$position 	= 	array_search( $supervisor, (array)$supervisors );
			unset( $supervisors[$position] );
		}

		if( in_array( $participant, $participants ) ) {
			$position 	= 	array_search( $participant, (array)$participants );
			unset( $participants[$position] );
		}

		update_user_meta( $participant, 'cpd_related_supervisors', $supervisors );
		update_user_meta( $supervisor, 'cpd_related_participants', $participants );
	}

	/**
	 * Add a supervisor to its participants journals
	 *
	 * @param  int 		$user_id 		The user ID
	 *
	 * @since   2.0.0
	 */
	public static function add_supervisor_to_participant_journals( $user_id ) {

		$all_cpd_journals 		= 	wp_get_sites();
		$should_have_journals 	= 	array();
		$participants 			= 	get_user_meta( $user_id, 'cpd_related_participants', TRUE );

		foreach( $participants as $participant ) {
			$blogs = get_blogs_of_user( $participant );
			if( is_array( $blogs ) && count( $blogs ) > 0 ) {
				foreach ( $blogs as $blog ) {
					$should_have_journals[] = $blog->userblog_id;
				}
			}
		}
		if( count( $all_cpd_journals ) > 0 ) {
			foreach( $all_cpd_journals as $journal ) {
				$journal = $journal['blog_id'];
				if( in_array( $journal, $should_have_journals ) ) {
					add_user_to_blog( $journal, $user_id, 'supervisor' );
				} 
				else {
					remove_user_from_blog( $user_id, $journal );
				}
			}
		}
	}


	/**
	 * Remove a user from all blogs
	 *
	 * @param  int 		$user_id 		The user ID
	 *
	 * @since   2.0.0
	 */
	public static function remove_user_from_blogs( $user_id ) {
		$current_journals = get_blogs_of_user( $user_id );
		if( count( $current_journals ) > 0 ) {
			
			foreach( $current_journals as $journal ) {
				remove_user_from_blog( $user_id, $journal->userblog_id );
			}
		}
	}

	/**
	 * Create a  user journal
	 *
	 * @param  int 		$user_id 		The user ID
	 *
	 * @since   2.0.0
	 */
	public static function create_user_journal( $user_id ) {
		$user 			= 	get_userdata( $user_id );
		$user_data 		= 	$user->data;
		$current_site 	= 	network_site_url();
		$domain 		= 	parse_url( network_site_url(), PHP_URL_HOST );
		$path 			= 	parse_url( network_site_url(), PHP_URL_PATH ) . $user_data->user_login . '/';

		$cpd_settings 	= 	get_option( 'cpd_new_blog_options' );
		$cpd_settings 	= 	preg_replace( '/[\n\r]+/', '&', $cpd_settings );
		$cpd_settings 	= 	preg_replace( '/[\s\:]+/', '=', $cpd_settings );
		parse_str( $cpd_settings, $options );

		$cpd_journal 	= 	wpmu_create_blog( $domain, $path, 'CPD Journal for ' . $user_data->user_nicename, $user_id, $options, 1 );
	}
}		
}