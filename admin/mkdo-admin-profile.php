<?php
/**
 * Adds additional fields to profile page
 *
 * @link       http://makedo.in
 * @since      1.0.0
 *
 * @package    MKDO_Admin
 * @subpackage MKDO_Admin/admin
 */

/**
 * Admin profile
 *
 * Adds additional fields to profile page
 *
 * @package    MKDO_Admin
 * @subpackage MKDO_Admin/admin
 * @author     Make Do <hello@makedo.in>
 */
class MKDO_Admin_Profile{

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
	 * Add field to profile
	 */
	public function add_elevated_user_profile_field( $user ) {
	
		// Bail out early if user is not a super user
		if ( ! MKDO_Helper_User::is_elevated_user() )
			return false;
			
		?>

		<table class="form-table">

			<tr>
				<th scope="row">Elevated Admin Privileges</th>

				<td>
					
					<fieldset>
					
						<legend class="screen-reader-text">
							<span>Elevated Admin Privileges</span>
						</legend>
						
						<label>
							<input name="elevated_user" type="checkbox" id="elevated_user" value="1"<?php checked( get_user_meta( $user->ID, 'elevated_user', true ) ) ?> />
							Grant this user elevated admin privileges.
						</label>
					
					</fieldset>
					
				</td>
			</tr>
		
		</table>
		
		<?php
			
	}

	/**
	 * Save field data
	 */
	public function save_elevated_user_profile_field_data( $user_id ) {
		
		/* check the current user is a super admin */
		if ( ! current_user_can( 'manage_options', $user_id ) )
			return false;

		/* If the field has not been sent, exit */
		if ( ! isset( $_POST[ 'elevated_user' ] ) )
			return false;
			
		/* get the current user information */
		$mkdo_current_user = wp_get_current_user();
		
		/* update the user meta with the additional fields on the profile page */
		update_usermeta( $user_id, 'elevated_user', $_POST[ 'elevated_user' ] );	
	}

	/**
	 * Remove admin colour scheme
	 */
	public function remove_admin_color_schemes() {
		remove_action( 'admin_color_scheme_picker', 'admin_color_scheme_picker' );
	}

	/**
	 * Force colour scheme
	 */
	public function force_user_color_scheme( $color_scheme ) {
		
		$screen 		= get_current_screen();
		$current_user 	= wp_get_current_user();
		$roles 			= $current_user->roles;

		$color_scheme 	= 'midnight';

		if ( MKDO_Helper_User::is_elevated_user() ) {
			$color_scheme 	= 'sunrise';
		} 
		else if( in_array( 'administrator', $roles) ) {
			$color_scheme 	= 'ectoplasm';
		}
		else if( in_array( 'editor', 		$roles) ) {
			$color_scheme 	= 'ocean';
		}
		else if( in_array( 'author', 		$roles) ) {
			$color_scheme 	= 'blue';
		}
		else if( in_array( 'contributor', 	$roles) ) {
			$color_scheme 	= 'coffee';
		}
		else if( in_array( 'subscriber', 	$roles) ) {
			$color_scheme 	= 'light';
		}

		if( is_multisite() && strpos( $screen->base, '-network') ) {
			$color_scheme 	= 'sunrise';
		}

		return $color_scheme;
	}

	/**
	 * Change admin capabilites
	 */
	public function edit_admin_capabilities( $capabilities ) {
	
		
		/* setup an array of capabilities to change - filterable */
		$mkdo_capabilities = apply_filters(
			'elevated_user_capabilities',
			array(
				array(
					'capability_name' => 'update_core',
					'capability_action' => MKDO_Helper_User::is_elevated_user(),
				),
				array(
					'capability_name' => 'update_plugins',
					'capability_action' => MKDO_Helper_User::is_elevated_user(),
				),
				array(
					'capability_name' => 'activate_plugins',
					'capability_action' => MKDO_Helper_User::is_elevated_user(),
				),
				array(
					'capability_name' => 'install_plugins',
					'capability_action' => MKDO_Helper_User::is_elevated_user(),
				),
			)
		);
		
		/* loop through each capability */
		foreach( $mkdo_capabilities as $mkdo_capability ) {
			
			/* check if the user has the capability */
			if( ! empty( $capabilities[ $mkdo_capability[ 'capability_name' ] ] ) ) {
			
				/* action the capability - adding or remove accordingly */
				$capabilities[ $mkdo_capability[ 'capability_name' ] ] = $mkdo_capability[ 'capability_action' ];
			
			}
			
		}
										
		/* return the modified capabilities */
		return $capabilities;
		
	}
}