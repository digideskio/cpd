<?php
/**
 * The dashboard-specific functionality of the plugin.
 *
 * @link       http://makedo.in
 * @since      1.0.0
 *
 * @package    CPD
 * @subpackage CPD/admin
 */

/**
 * The dashboard-specific functionality of the plugin.
 *
 * @package    CPD
 * @subpackage CPD/admin
 * @author     Make Do <hello@makedo.in>
 */
class CPD_Journal_Profiles{

	/**
	 * Initialize the class and set its properties.
	 *
	 * @since    1.0.0
	 * @var      string    $instance       The name of this plugin.
	 * @var      string    $version    The version of this plugin.
	 */
	public function __construct() {
		
	}

	public function add_cpd_relationship_management( $user ) {

		if(!is_super_admin()) {
			return;
		}

		global $wpdb;

		$cpd_role 			= 	get_user_meta( $user->ID, 'cpd_role', TRUE );
		$mu_users 			= 	$wpdb->get_results( "SELECT ID, user_nicename FROM $wpdb->users" );
		$user_supervisors	=	array();
		$all_supervisors 	=	array();
		$user_participants 	=	array();
		$all_participants 	=	array();
		$cpd_journal 		= 	get_active_blog_for_user( $user->ID );
		$all_cpd_journals 	= 	wp_get_sites();

		if( is_object( $cpd_journal ) ) {
			$cpd_journal 		=   get_object_vars( $cpd_journal );
		}

		foreach( $mu_users as $mu_user ) {

			// delete_user_meta( $mu_user->ID, 'cpd_related_participants' );
			// delete_user_meta( $mu_user->ID, 'cpd_related_supervisors' );

			$mu_cpd_role 			= 	get_user_meta( $mu_user->ID, 'cpd_role', TRUE );

			if( $mu_cpd_role == 'participant' ) {
				$all_participants[] = $mu_user;
			}

			if( $mu_cpd_role == 'supervisor' ) {
				$all_supervisors[] = $mu_user;
			}

			// Get list of participants that this user supervisors
			$mu_user_related_participants = get_user_meta( $mu_user->ID, 'cpd_related_participants', TRUE );

			if( is_array( $mu_user_related_participants ) && count( $mu_user_related_participants ) > 0 ) {

				// Check if the user is a supervisor for the current user
				if( in_array( $user->ID, $mu_user_related_participants ) ) {
					$user_supervisors[] = $mu_user->ID;
				}
			}

			// Get list of participants that this user supervisors
			$mu_user_related_supervisors = get_user_meta( $mu_user->ID, 'cpd_related_supervisors', TRUE );

			if( is_array( $mu_user_related_supervisors ) && count( $mu_user_related_supervisors ) > 0 ) {

				// Check if the user is a participant for the current user
				if( in_array( $user->ID, $mu_user_related_supervisors ) ) {
					$user_participants[] = $mu_user->ID;
				}
			}

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
											?>
											<span>
												<input type="checkbox" name="cpd_supervisors[]" value="<?php echo $supervisor->ID;?>" id="cpd_supervisor_<?php echo $supervisor->ID;?>" <?php echo in_array( $supervisor->ID, $user_supervisors, TRUE ) ? 'checked' : '';?>/>
												<label for="cpd_supervisor_<?php echo $supervisor->ID;?>"><?php echo htmlentities( $supervisor->user_nicename );?></label>
											</span>
											<?php
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
											?>
											<span>
												<input type="checkbox" name="cpd_participants[]" value="<?php echo $participant->ID;?>" id="cpd_participant_<?php echo $participant->ID;?>" <?php echo in_array( $participant->ID, $user_participants ) ? 'checked' : '';?>/>
												<label for="cpd_participant_<?php echo $participant->ID;?>"><?php echo htmlentities( $participant->user_nicename );?></label>
											</span>
											<?php
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


	public function save_cpd_relationship_management( $user_id ) {
			
		global $errors, $wpdb;

		if(!is_super_admin()) {
			return;
		}

		if( !isset( $_POST['cpd_role'] ) ) {
			return;
		}

		if ( !is_object( $errors ) ) {
			$errors = new WP_Error();
		}

		$user 				= 	get_userdata( $user_id );
		$user_data 			= 	$user->data;
		$current_cpd_role 	= 	get_user_meta( $user_id, 'cpd_role' , TRUE );
		$mu_users 			= 	$wpdb->get_results( "SELECT ID, user_nicename FROM $wpdb->users" );
		$user_supervisors	=	array();
		$all_supervisors 	=	array();
		$user_participants 	=	array();
		$all_participants 	=	array();
		$cpd_journal 		= 	get_active_blog_for_user( $user->ID );
		$all_cpd_journals 	= 	wp_get_sites();

		if( is_object( $cpd_journal ) ) {
			$cpd_journal 		=   get_object_vars( $cpd_journal );
		}
		
		foreach( $mu_users as $mu_user ) {

			$mu_cpd_role 			= 	get_user_meta( $mu_user->ID, 'cpd_role', TRUE );

			if( $mu_cpd_role == 'participant' ) {
				$all_participants[] = $mu_user;
			}

			if( $mu_cpd_role == 'supervisor' ) {
				$all_supervisors[] = $mu_user;
			}

			// Get list of participants that this user supervisors
			$mu_user_related_participants = get_user_meta( $mu_user->ID, 'cpd_related_participants', TRUE );

			if( is_array( $mu_user_related_participants ) && count( $mu_user_related_participants ) > 0 ) {

				// Check if the user is a supervisor for the current user
				if( in_array( $user_id, $mu_user_related_participants ) ) {
					$user_supervisors[] = $mu_user->ID;
				}
			}

			// Get list of participants that this user supervisors
			$mu_user_related_supervisors = get_user_meta( $mu_user->ID, 'cpd_related_supervisors', TRUE );

			if( is_array( $mu_user_related_supervisors ) && count( $mu_user_related_supervisors ) > 0 ) {

				// Check if the user is a participant for the current user
				if( in_array( $user_id, $mu_user_related_supervisors ) ) {
					$user_participants[] = $mu_user->ID;
				}
			}
		}

		if( $_POST['cpd_role'] == 'participant' ) {

			$cpd_journal 	= 	$_POST['cpd_journal'];

			if( $current_cpd_role == 'supervisor' ) {

				$current_journals = get_blogs_of_user( $user_id );
				
				if( count( $current_journals ) > 0 ) {
					
					foreach( $current_journals as $journal ) {
						remove_user_from_blog( $user_id, $journal->userblog_id );
					}
				}

				// Remove related supervisors
				delete_user_meta( $user_id, 'cpd_related_participants' );

				// Remove as a participant from all supervisors
				foreach( $all_participants as $participant ) {
					$participant 						= 	$participant->ID;
					$participant_supervisors 			= 	get_user_meta( $participant, 'cpd_related_supervisors', TRUE );
					$position 							= 	array_search( $user_id, (array)$participant_supervisors );
					if( $position !== FALSE ) {
						unset( $participant_supervisors[$position] );
						update_user_meta( $participant, 'cpd_related_supervisors', $participant_supervisors );
					}
				}
			}

			if( $cpd_journal == 'new' ) {
				
				$current_site 	= 	network_site_url();
				$domain 		= 	parse_url( network_site_url(), PHP_URL_HOST );
				$path 			= 	parse_url( network_site_url(), PHP_URL_PATH ) . $user_data->user_login . '/';

				$cpd_settings 	= 	get_option( 'cpd_new_blog_options' );
				$cpd_settings 	= 	preg_replace( '/[\n\r]+/', '&', $cpd_settings );
				$cpd_settings 	= 	preg_replace( '/[\s\:]+/', '=', $cpd_settings );
				parse_str( $cpd_settings, $options );

				$cpd_journal 	= 	wpmu_create_blog( $domain, $path, 'CPD Journal for ' . $user_data->user_nicename, $user_id, $options, 1 );
				
				if(!$cpd_journal) {
					$errors->add_error('journal_creation_failed', __( 'Failed create a journal for ' ) . $user_username );
					return;
				}
			}

			// If the journal picked is not the current one for this user
			if ( $cpd_journal != get_active_blog_for_user( $user_id ) ) { 
				
				// add user to blog
				$added_to_blog 	= 	add_user_to_blog( $cpd_journal, $user_id, 'participant' );
				
				if( is_wp_error( $added_to_blog ) ) {
					$errors[] = $added_to_blog;
					return;
				}
				update_user_meta( $user_id, 'primary_blog', $cpd_journal );
			}

			$post_supervisors 		= $_POST['cpd_supervisors'];
			$post_supervisors 		= is_array( $post_supervisors ) ? $post_supervisors : array();
			$current_supervisors	= $user_supervisors;

			if( count( $all_supervisors ) > 0 ) {
				
				foreach( $all_supervisors as $supervisor ) {

					$supervisor = $supervisor->ID;

					if( in_array( $supervisor, $post_supervisors ) && !in_array( $supervisor, $current_supervisors ) ) {

						// Add supervisor to list of participants supervisors
						$user_supervisors[] 				= 	$supervisor;
						
						// Add participant to supervisors participants
						$supervisor_participants 			= 	get_user_meta( $supervisor, 'cpd_related_participants', TRUE );
						(array)$supervisor_participants[] 	= 	$user_id;
						update_user_meta( $supervisor, 'cpd_related_participants', $supervisor_participants );
					} 
					else if( !in_array( $supervisor, $post_supervisors ) && in_array( $supervisor, $current_supervisors ) ) {
						
						// Remove supervisor from list of participants supervisors
						$position 							= 	array_search( $supervisor, (array)$user_supervisors );
						if( $position !== FALSE ) {
							unset( $user_supervisors[$position] );
						}

						// Remove participant from supervisors participants
						$supervisor_participants 			= 	get_user_meta( $supervisor, 'cpd_related_participants', TRUE );
						$position 							= 	array_search( $user_id, (array)$supervisor_participants );
						if( $position !== FALSE ) {
							unset( $supervisor_participants[$position] );
							update_user_meta( $supervisor, 'cpd_related_participants', $supervisor_participants );
						}
						
					}
				}

				// Update supervisors
				update_user_meta( $user_id, 'cpd_related_supervisors', $user_supervisors );

				// Associate supervisors with the correct journals
				$current_journals = get_blogs_of_user( $user_id );
				if( is_array( $current_journals  ) && count( $current_journals  ) ) {
					foreach( $current_journals as $journal ) {
						foreach( $post_supervisors as $supervisor ) {
							add_user_to_blog( $journal->userblog_id, $supervisor, 'supervisor' );
						}
					}
				}

			}

		} else if( $_POST['cpd_role'] == 'supervisor' ) {

			// Iterate the participants adding and removing the participants
			$post_participants 						=	$_POST['cpd_participants'];
			$post_participants 						= 	is_array($post_participants) ? $post_participants : array();
			$current_participants 					=	$user_participants;
			
			if( count( $all_participants ) > 0 ) {
				
				foreach( $all_participants as $participant ) {

					$participant = $participant->ID;

					if( in_array($participant, $post_participants) && !in_array($participant, $current_participants ) ) {
						
						// Add participant to list of supervisor participants
						$user_participants[] 				= 	$participant;
						
						// Add supervisor to participants supervisors
						$participant_supervisors 			= 	get_user_meta( $participant, 'cpd_related_supervisors', TRUE );
						(array)$participant_supervisors[] 	= 	$user_id;
						update_user_meta( $participant, 'cpd_related_supervisors', $participant_supervisors );
					} 
					else if( !in_array($participant, $post_participants ) && in_array( $participant, $current_participants ) ) {
						
						// Remove participant from list of supervisors participants
						$position 							= 	array_search( $participant, (array)$user_participants );
						if( $position !== FALSE ) {
							unset( $user_participants[$position] );
						}

						// Remove participant from supervisors participants
						$participant_supervisors 			= 	get_user_meta( $participant, 'cpd_related_supervisors', TRUE );
						$position 							= 	array_search( $user_id, (array)$participant_supervisors );
						if( $position !== FALSE ) {
							unset( $participant_supervisors[$position] );
							update_user_meta( $participant, 'cpd_related_supervisors', $participant_supervisors );
						}
					}
				}

				update_user_meta( $user_id, 'cpd_related_participants', $user_participants );

			}

			// Make sure the supervisor is on each of the pariticpants' primary blog
			$should_have_journals = array();
			$participants = get_user_meta( $user_id, 'cpd_related_participants', TRUE );

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

		} else {
			$_POST['cpd_role'] = 'none';
			
			if( $current_cpd_role == 'supervisor' ) {
				
				$current_journals = get_blogs_of_user( $user_id );
				
				if( count( $current_journals ) > 0 ) {
					
					foreach( $current_journals as $journal ) {
						remove_user_from_blog( $user_id, $journal->userblog_id );
					}
				}
				
				// Remove related supervisors
				delete_user_meta( $user_id, 'cpd_related_participants' );

				// Remove as a participant from all supervisors
				foreach( $all_participants as $participant ) {
					$participant 						= 	$participant->ID;
					$participant_supervisors 			= 	get_user_meta( $participant, 'cpd_related_supervisors', TRUE );
					$position 							= 	array_search( $user_id, (array)$participant_supervisors );
					if( $position !== FALSE ) {
						unset( $participant_supervisors[$position] );
						update_user_meta( $participant, 'cpd_related_supervisors', $participant_supervisors );
					}
				}
			}
			else if( $current_cpd_role == 'participant' ) {

				// Remove related supervisors
				delete_user_meta( $user_id, 'cpd_related_supervisors' );

				// Remove as a participant from all supervisors
				foreach( $all_supervisors as $supervisor ) {
					$supervisor 						= 	$supervisor->ID;
					$supervisor_participants 			= 	get_user_meta( $supervisor, 'cpd_related_participants', TRUE );
					$position 							= 	array_search( $user_id, (array)$supervisor_participants );
					if( $position !== FALSE ) {
						unset( $supervisor_participants[$position] );
						update_user_meta( $supervisor, 'cpd_related_participants', $supervisor_participants );
					}
					
				}
			}
		}

		update_user_meta( $user_id,'cpd_role', $_POST['cpd_role'] );
		add_user_to_blog( BLOG_ID_CURRENT_SITE, $user_id, 'subscriber' );
	}
}