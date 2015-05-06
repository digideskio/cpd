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

/**
 * The user-specific functionality of the plugin.
 *
 * @package    CPD
 * @subpackage CPD/admin
 * @author     Make Do <hello@makedo.in>
 */
class CPD_Users{

	/**
	 * Initialize the class and set its properties.
	 *
	 * @since    1.0.0
	 * @var      string    $instance       The name of this plugin.
	 * @var      string    $version    The version of this plugin.
	 */
	public function __construct() {
		
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
	 * Create new roles
	 */
	public function create_roles() {

		// Create roles with same capabilities as administrators
		add_role( 'supervisor', 	'Supervisor', 	get_role('administrator')->capabilities );
		add_role( 'participant', 	'Participant', 	get_role('administrator')->capabilities );
		
		// Get the particpant roles
		$role 	= get_role( 'participant' );

		// Remove capabilities
		$role->remove_cap( 'edit_others_posts' );
		$role->remove_cap( 'edit_others_pages' );
		$role->remove_cap( 'delete_others_posts' );
		$role->remove_cap( 'delete_others_pages' );
	}

	/**
	 * Add Meta Data
	 *
	 * If a users role is changed, update their meta
	 * data to add the role to the cpd_role meta
	 * 
	 * @param int 		$user_id 	The users id
	 * @param strong 	$role 		The role that has been assigned
	 */
	public function add_meta_data( $user_id, $role ) {

		if( 'supervisor' === $role || 'participant' === $role ){
			update_user_meta( $user_id, 'cpd_role', $role );
		}
	}

	/**
	 * [editable_roles description
	 *
	 * Disallow participants from adding/removing any roles 
	 * that can edit posts or manage users
	 * 
	 * @param  array 	$all_roles 		All user roles
	 * @return array    $all_roles      All user roles
	 */
	public function remove_user_management_roles( $all_roles ){

		// These are the capabilities that we don't let participants create users with
		$barred_capabilities 	= 	array(
									'edit_users',
									'create_users',
									'edit_posts'
								); 

		$cpd_role 				= 	get_user_meta( get_current_user_id(), 'cpd_role', TRUE );

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
	 * @param  int 		$user_id 	The user ID
	 */
	function redirect_on_create_user( $user_id ){

		// We need to stop the redirect to the user-new.php page happening.
		// the only way is to recreate the password and send the user notification ourselves.
		// then we can forward to the user profile where the admin can set their supervisors
		// (if they are a participant) or participants (if they are a supervisor)
		if ( $user_id ) {

			// all new users get access (as a subscriber) to the default blog
			add_user_to_blog( BLOG_ID_CURRENT_SITE, $user_id, 'subscriber' );

			// create a new password, because we need to send it and we don't have access to the one already generated 
			$password 	= wp_generate_password( 12, false);
			
			wp_set_password($password, $user_id);

			wp_new_user_notification( $user_id, $password );

			wp_redirect( add_query_arg( array( 'user_id' => $user_id ), network_admin_url( 'user-edit.php#cpd_profile' ) ) );
			
			exit;
		}

	}

	public static function get_multisite_users() {

		$users 				= 	array();
		$blogs 				= 	wp_get_sites();

		foreach ( $blogs as $blog ){

			$blog_users = get_users( array( 'blog_id' => $blog['blog_id'] ) );

			if( is_array( $blog_users ) ) {
				foreach( $blog_users as $user ) {
					if( !in_array( $user, $users ) ) {
						$users[] = $user;
					}
				}
			}
		}

		return $users;
	}

	public static function get_participants() {

		$users 				= 	array();
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
					if( !in_array( $user, $users ) ) {
						$users[] = $user;
					}
				}
			}
		}

		return $users;
	}

	public static function get_supervisors() {

		$users 				= 	array();
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
					if( !in_array( $user, $users ) ) {
						$users[] = $user;
					}
				}
			}
		}

		return $users;
	}

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

	public static function remove_user_from_blogs( $user_id ) {
		$current_journals = get_blogs_of_user( $user_id );
		if( count( $current_journals ) > 0 ) {
			
			foreach( $current_journals as $journal ) {
				remove_user_from_blog( $user_id, $journal->userblog_id );
			}
		}
	}

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