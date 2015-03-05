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
class CPD_Journal_Users extends MKDO_Class {

	/**
	 * Initialize the class and set its properties.
	 *
	 * @since    1.0.0
	 * @var      string    $instance       The name of this plugin.
	 * @var      string    $version    The version of this plugin.
	 */
	public function __construct( $instance, $version ) {
		parent::__construct( $instance, $version );
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
}