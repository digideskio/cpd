<?php
/**
 * The dashboard-specific functionality of the plugin.
 *
 * @link       http://makedo.in
 * @since      2.0.0
 *
 * @package    CPD
 * @subpackage CPD/admin
 */

// Exit if accessed directly
defined( 'ABSPATH' ) || exit;

if( !class_exists( 'CPD_Profile' ) ) {

/**
 * The admin-specific functionality of the plugin.
 *
 * Defines the admin settings
 *
 * @package    CPD
 * @subpackage CPD/admin
 * @author     Make Do <hello@makedo.in>
 */
class CPD_Profile {

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

	 */
	public function __construct() {
		
	}

	/**
	 * Set the text domain
	 *
	 * @param      string    $text_domain       The text domain of the plugin.
	 *

	 **/
	public function set_text_domain( $text_domain ) { 
		$this->text_domain = $text_domain;
	}

	/**
	 * Add elevated user field to the profile page
	 * 
	 * @param object 	$user 	Current user object
	 *

	 */
	public function add_field_elevated_user( $user ) {

		// If user is not an elevated user exit
		if( get_user_meta( $user->ID, 'elevated_user', TRUE ) != '1' ) {
			return false;
		}

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
	 * Save elevated user field data
	 * 
	 * @param int 	$user_id 	Current user ID
	 *

	 */
	public function save_field_elevated_user( $user_id ) {
		
		// Check the current user is a super admin
		if ( ! current_user_can( 'manage_options', $user_id ) ) {
			return false;
		}

		// If the field has not been set
		if ( ! isset( $_POST[ 'elevated_user' ] ) ) {
			return false;
		}
		
		// Update the user meta with the additional fields on the profile page
		update_usermeta( $user_id, 'elevated_user', $_POST[ 'elevated_user' ] );	
	}

	/**
	 * Remove admin colour scheme
	 *

	 */
	public function remove_admin_color_schemes() {
		remove_action( 'admin_color_scheme_picker', 'admin_color_scheme_picker' );
	}

	/**
	 * Set Colour Schemes
	 *
	 * @param string 	$color_scheme 	Current Colour Scheme
	 * 

	 */
	public function set_color_scheme( $color_scheme ) {
		
		$screen 		= get_current_screen();
		$user 			= wp_get_current_user();
		$roles 			= $user->roles;

		$color_scheme 	= 'light';

		if ( get_user_meta( $user->ID, 'elevated_user', TRUE ) == '1' ) {
			$color_scheme 	= 'midnight';
		} 
		else if( in_array( 'administrator', $roles) ) {
			$color_scheme 	= 'ectoplasm';
		}
		else if( in_array( 'supervisor', 	$roles) ) {
			$color_scheme 	= 'ectoplasm';
		}
		else if( in_array( 'participant', 	$roles) ) {
			$color_scheme 	= 'ocean';
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
	 * Add relationship management field to the profile page
	 * 
	 * @param object 	$user 	Current user object
	 *

	 */
	public function add_field_cpd_relationship_management( $user ) {

		if( !is_super_admin() ) {
			return;
		}

		$cpd_role 			= 	get_user_meta( $user->ID, 'cpd_role', TRUE );
		$user_supervisors	=	get_user_meta( $user->ID, 'cpd_related_supervisors', TRUE );
		$all_supervisors 	=	CPD_Users::get_supervisors();
		$user_participants 	=	get_user_meta( $user->ID, 'cpd_related_participants', TRUE );
		$all_participants 	=	CPD_Users::get_participants();
		$cpd_journal 		= 	get_active_blog_for_user( $user->ID );
		$all_cpd_journals 	= 	wp_get_sites();

		if( is_object( $cpd_journal ) ) {
			$cpd_journal 		=   get_object_vars( $cpd_journal );
		}

		?>
		<a name="cpd_profile"></a>
		<div id="cpd_profile">

			<h3>Journal Relationship Management</h3>

			<table class="form-table">
				<tbody>
					<tr>
						<th>Set Role</th>
						<td> <?php
							
							?>
							<select id="cpd_role" name="cpd_role">
								<option value="" 			<?php echo empty( $cpd_role ) 			? 'selected' : '';?>>No Role</option>
								<option value="participant" <?php echo $cpd_role == 'participant' 	? 'selected' : '';?>>Participant</option>
								<option value="supervisor" 	<?php echo $cpd_role == 'supervisor' 	? 'selected' : '';?>>Supervisor</option>
							</select>
						</td>
					</tr>
					<tr class="cpd_journals">
						<th>Choose Journal</th> 
						<td>
							<select id="cpd_journal" name="cpd_journal">
								<option value="new">Create a new journal</option>
								<?php
									if( count( $all_cpd_journals ) ) {
										foreach( $all_cpd_journals as $journal ) {
											?>
												<option value="<?php echo $journal['blog_id'];?>" <?php echo $journal['blog_id'] == $cpd_journal['blog_id'] ? 'selected' : '';?>>
													http://<?php echo $journal['domain'] . $journal['path'];?>
												</option>
											<?php
										}
									}
								?>
							</select>
						</td>
					</tr>
					<?php
						if( count( $all_supervisors ) ) {
							?>
							<tr class="cpd_supervisors">
								<th>Allocated supervisors</th> 
								<td>
									<?php
									
									if( count( $all_supervisors ) ) {
										foreach( $all_supervisors as $supervisor ) { 
											if( $user->ID != $supervisor->ID ) {
											?>
											<span>
												<input type="checkbox" name="cpd_supervisors[]" value="<?php echo $supervisor->ID;?>" id="cpd_supervisor_<?php echo $supervisor->ID;?>" <?php echo is_array( $user_supervisors ) && in_array( $supervisor->ID, $user_supervisors, TRUE ) ? 'checked' : '';?>/>
												<label for="cpd_supervisor_<?php echo $supervisor->ID;?>"><?php echo htmlentities( $supervisor->user_nicename );?></label>
											</span>
											<?php
											}
										}
									} ?>
								</td>
							</tr>
							<?php
						}
					?>
					<?php
						if( count( $all_participants ) ) {
							?>
							<tr class="cpd_participants">
								<th>Allocated participants</th>
								<td>
									<?php
									if( count( $all_participants ) ) {
										foreach( $all_participants as $participant ) { 
											if( $user->ID != $participant->ID ) {
											?>
											<span>
												<input type="checkbox" name="cpd_participants[]" value="<?php echo $participant->ID;?>" id="cpd_participant_<?php echo $participant->ID;?>" <?php echo is_array( $user_participants ) && in_array( $participant->ID, $user_participants ) ? 'checked' : '';?>/>
												<label for="cpd_participant_<?php echo $participant->ID;?>"><?php echo htmlentities( $participant->user_nicename );?></label>
											</span>
											<?php
											}
										}
									} ?>
								</td>
							</tr>
							<?php
						}
					?>
				</tbody>
			</table>
		</div>
		<?php
	}

	/**
	 * Save relationship management field
	 * 
	 * @param int 	$user_id 	Current user ID
	 *

	 */
	public function save_field_cpd_relationship_management( $user_id ) {
			
		if(!is_super_admin()) {
			return;
		}

		if( !isset( $_POST['cpd_role'] ) ) {
			return;
		}

		$current_cpd_role 	= 	get_user_meta( $user_id, 'cpd_role' , TRUE );
		$user_supervisors	=	get_user_meta( $user_id, 'cpd_related_supervisors', TRUE );
		$all_supervisors 	=	CPD_Users::get_supervisors();
		$user_participants 	=	get_user_meta( $user_id, 'cpd_related_participants', TRUE );
		$all_participants 	=	CPD_Users::get_participants();
		$cpd_journal 		= 	get_active_blog_for_user( $user_id );
		$all_cpd_journals 	= 	wp_get_sites();
		$current_journals 	= 	get_blogs_of_user( $user_id );

		if( is_object( $cpd_journal ) ) {
			$cpd_journal 		=   get_object_vars( $cpd_journal );
		}

		// If the user has been set to a participant
		if( $_POST['cpd_role'] == 'participant' ) {

			$cpd_journal 	= 	$_POST['cpd_journal'];

			// If they are currently a supervisor
			if( $current_cpd_role == 'supervisor' ) {
				
				// Remove them from all journals
				if( count( $current_journals ) > 0 ) {
					foreach( $current_journals as $journal ) {
						remove_user_from_blog( $user_id, $journal->userblog_id );
					}
				}

				// Remove related supervisors
				delete_user_meta( $user_id, 'cpd_related_participants' );

				// Remove as a participant from all supervisors
				CPD_Users::remove_user_from_related_supervisors( $user_id, $all_participants );
			}

			// If they are creating a new journal
			if( $cpd_journal == 'new' ) {
				
				// Create the new journal
				CPD_User::create_user_journal( $user_id );
			}

			// If the journal picked is not the current one for this user
			if ( $cpd_journal != get_active_blog_for_user( $user_id ) ) { 
				
				// Add user to blog
				$added_to_blog 	= 	add_user_to_blog( $cpd_journal, $user_id, 'participant' );
				
				// Set the journal as the primary blog
				update_user_meta( $user_id, 'primary_blog', $cpd_journal );
			}

			// Get the posted supervisors
			$post_supervisors 		= $_POST['cpd_supervisors'];
			$post_supervisors 		= is_array( $post_supervisors ) ? $post_supervisors : array();

			if( count( $all_supervisors ) > 0 ) {
				
				// Loop through all the supervisors
				foreach( $all_supervisors as $supervisor ) {

					$supervisor = $supervisor->ID;

					// If the supervisor is in the list of posted supervisors and they are not already in the list of user supervisors
					if( in_array( $supervisor, $post_supervisors ) && !in_array( $supervisor, $user_supervisors ) ) {

						CPD_Users::add_cpd_relationship( $supervisor, $user_id /* $participant */ );
					} 
					// If the supervisor is not in the list of posted supervisors, but are in the list of user supervisors
					else if( !in_array( $supervisor, $post_supervisors ) && in_array( $supervisor, $user_supervisors ) ) {

						CPD_Users::remove_cpd_relationship( $supervisor, $user_id /* $participant */ );						
					}
				}

				// Associate supervisors with the correct journals
				$current_journals = get_blogs_of_user( $user_id );
				if( is_array( $current_journals ) && count( $current_journals  ) ) {
					foreach( $current_journals as $journal ) {
						foreach( $all_supervisors as $supervisor ) {
							$supervisor = $supervisor->ID;
							if( in_array( $supervisor, $post_supervisors ) ) {
								add_user_to_blog( $journal->userblog_id, $supervisor, 'supervisor' );
							} else if( BLOG_ID_CURRENT_SITE != $journal->userblog_id ) {
								remove_user_from_blog( $supervisor, $journal->userblog_id );
							}
						}
					}
				}
			}

		} else if( $_POST['cpd_role'] == 'supervisor' ) {

			// If they are currently a participant
			if( $current_cpd_role == 'participant' ) {
				
				// Remove them from all journals
				if( count( $current_journals ) > 0 ) {
					foreach( $current_journals as $journal ) {
						remove_user_from_blog( $user_id, $journal->userblog_id );
					}
				}

				// Remove related supervisors
				delete_user_meta( $user_id, 'cpd_related_supervisors' );

				// Remove as a participant from all supervisors
				CPD_Users::remove_user_from_related_participants( $user_id, $all_supervisors );
			}

			// Iterate the participants adding and removing the participants
			$post_participants 						=	$_POST['cpd_participants'];
			$post_participants 						= 	is_array($post_participants) ? $post_participants : array();
			
			if( count( $all_participants ) > 0 ) {
				
				foreach( $all_participants as $participant ) {

					$participant = $participant->ID;

					if( in_array($participant, $post_participants) && !in_array( $participant, $user_participants ) ) {
						
						CPD_Users::add_cpd_relationship( /* $supervisor */ $user_id, $participant );
					} 
					else if( !in_array($participant, $post_participants ) && in_array( $participant, $user_participants ) ) {
						
						CPD_Users::remove_cpd_relationship( /* $supervisor */ $user_id, $participant );
					}
				}
			}

			// Make sure the supervisor is on each of the pariticpants' primary blog
			CPD_Users::add_supervisor_to_participant_journals( $user_id );

		} else {

			// Set the role to none
			$_POST['cpd_role'] = 'none';
				
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
		}

		// Update the user role
		update_user_meta( $user_id,'cpd_role', $_POST['cpd_role'] );

		// Make sure they are a subscriber on the main blog
		add_user_to_blog( BLOG_ID_CURRENT_SITE, $user_id, 'subscriber' );
	}
}
}