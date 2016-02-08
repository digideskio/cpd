<?php

// Exit if accessed directly
defined( 'ABSPATH' ) || exit;

if( !class_exists( 'CPD_Options_Users_Participants' ) ) {

/**
 * Copy Assignments
 *
 * Functionality to copy assignments
 *
 * @package    CPD
 * @subpackage CPD/admin
 * @author     Make Do <hello@makedo.in>
 */
class CPD_Options_Users_Participants {


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
	 * @param      string    $instance       The name of this plugin.
	 * @param      string    $version    The version of this plugin.
	 */
	public function __construct() {

	}

	/**
	 * Set the text domain
	 *
	 * @param      string    $text_domain       The text domain of the plugin.
	 */
	public function set_text_domain( $text_domain ) {
		$this->text_domain = $text_domain;
	}

	public function init_options_page() {

		/* Add sections */
		add_settings_section( 'cpd_user_managment', 'Manage Participants', array( $this, 'cpd_user_managment_callback' ), 'cpd_settings_users_participants' );
		add_settings_section( 'cpd_user_managment_new', 'Add New Participant', array( $this, 'cpd_user_managment_new_callback' ), 'cpd_settings_users_participants' );

    	/* Add fields to a section */
		add_settings_field( 'cpd_user_managment_add_fields', 'Add Participant', array( $this, 'cpd_user_managment_add_fields_callback' ), 'cpd_settings_users_participants', 'cpd_user_managment_new' );

	}

	/**
	 * Show the section message
	 */
	public function cpd_user_managment_callback() {
		?>
		<p>
			Listed are all the participants. You can update them individually below.
		</p>
		<p>
			<strong>Note:</strong> You can only make alterations to participants <strong>one at a time</strong>, and you must save the information by clicking the 'Update Participant' button.
		</p>

		<?php

		if( isset( $_POST['cpd_id'] ) && !empty( $_POST['cpd_id'] ) && isset( $_POST['cpd_update_participant_nonce'] ) && wp_verify_nonce( $_POST['cpd_update_participant_nonce'], 'cpd_update_participant' ) ) {
			$user_id = $_POST['cpd_id'];
			$profile = CPD_Profile::get_instance();
			$profile->save_field_cpd_relationship_management( $user_id );
		}

		$current_user      = wp_get_current_user();
		$roles             = $current_user->roles;
		$is_supervisor     = CPD_Users::user_is_site_supervisor( $current_user );
		$is_elevated_user  = get_user_meta( $current_user->ID, 'elevated_user', TRUE ) == '1';
		$participants 	   = CPD_Users::get_participants();
		$all_supervisors   = CPD_Users::get_supervisors();
		$all_cpd_journals  = wp_get_sites();

		if( is_array( $participants ) && count( $participants ) > 0 ) {

			?>

			<table class="form-table">
				<?php
					foreach( $participants as $participant ) {
						$participant = $participant->ID;
						$user        = get_userdata( $participant );
						if( is_object( $user ) ) {
							$name        = $user->first_name . ' ' . $user->last_name;
							$name        = trim( $name );
							$username    = $user->user_login;
							$blog        = get_active_blog_for_user( $participant );
							$supervisors = get_user_meta( $participant, 'cpd_related_supervisors', TRUE );

							if( empty( $name ) ) {
								$name    = $username;
							}
							$cpd_journal = 	get_active_blog_for_user( $participant );

							if( is_object( $cpd_journal ) ) {
								$cpd_journal 		=   get_object_vars( $cpd_journal );
							}
							?>
							<tr>
								<th>
								<strong><?php echo $name;?></strong> <em>(<?php echo $username;?>)</em>
								</th>
								<td>
								<form method="post" action="" autocomplete="off" id="cpd_profile">

									<p><strong>Journal</strong></p>
									<p><label for="cpd_journal">Choose the participants Journal:</label></p>
									<br/>
									<select id="cpd_journal" name="cpd_journal">
										<option value="new">Create a new journal</option>
										<?php
											if( count( $all_cpd_journals ) ) {
												foreach( $all_cpd_journals as $journal ) {

													if( BLOG_ID_CURRENT_SITE != $journal['blog_id'] && strpos( $journal['path'], '/template-' ) === false ) {
													?>
														<option value="<?php echo $journal['blog_id'];?>" <?php echo $journal['blog_id'] == $cpd_journal['blog_id'] ? 'selected' : '';?>>
															http://<?php echo $journal['domain'] . $journal['path'];?>
														</option>
													<?php
													}
												}
											}
										?>
									</select>
									<div class="cpd_journals_base">
										<br/>
										<p><label for="cpd_template_base">Choose template:</label></p>
										<br/>
										<select id="cpd_template_base" name="cpd_template_base">
										<?php
										foreach( $all_cpd_journals as $blog ) {

											if( strrpos( $blog['path'], '/template-' ) === 0 ) {
												switch_to_blog( $blog['blog_id'] );
							 					$site_title = get_bloginfo( 'name' );
							 					$selected = '';
							 					if( !isset( $_POST['cpd_template_base'] ) ) {
							 						if( $blog['path'] == '/template-default/' ) {
							 							$selected = 'selected';
							 						}
							 					} else {
							 						if( $_POST['cpd_template_base'] == $blog['blog_id'] ) {
							 							$selected = 'selected';
							 						}
							 					}
												?>
												<option value="<?php echo $blog['blog_id'];?>" <?php echo $selected;?>><?php echo $site_title;?></option>
												<?php
												restore_current_blog();
											}
										}
										?>
										</select>
									</div>
									<br/>
									<br/>
									<p><strong>Supervisors</strong></p>
									<p>Assign other supervisors of the participant:</p>
									<?php
										if( is_array( $all_supervisors  ) && count( $all_supervisors  ) > 0 ) {
											?>
											<ul>
												<?php
												foreach( $all_supervisors  as $supervisor ) {
													if( $supervisor->ID != $current_user->ID ) {

														$name        = $supervisor->first_name . ' ' . $supervisor->last_name;
														$name        = trim( $name );
														$username    = $supervisor->user_login;

														if( empty( $name ) ) {
															$name    = $username;
														}
														$checked = '';

														if( in_array( $supervisor->ID, (array)$supervisors ) ) {
															$checked = 'checked';
														}
													?>
													<li>
														<label>
															<input type="checkbox" name="cpd_supervisors[]" <?php echo $checked;?> value="<?php echo $supervisor->ID;?>"/>
															<strong><?php echo $name;?></strong> <em>(<?php echo $username;?>)</em>
														</label>
													</li>
													<?php
													}
												}
												?>
											</ul>
											<?php
										} else {
											?>
											<p>No supervisors available</p>
											<?php
										}
									?>
									<br/>
									<input type="hidden" name="cpd_supervisors[]" value="<?php echo $current_user->ID;?>"/>
									<?php wp_nonce_field( 'cpd_update_participant', 'cpd_update_participant_nonce' ) ?>
									<input type="hidden" id="cpd_id" name="cpd_id" value="<?php echo $participant;?>"/>
									<input type="hidden" id="cpd_role" name="cpd_role" value="participant"/>
									<p><input type="submit" class="button button-primary" value="Update Participant"/>
								</form>
								</td>
							</tr>
						<?php
						} else {
							CPD_Users::remove_cpd_relationship( $current_user->ID, $participant );
						}
					}
				?>
			</table>
			<?php
		} else {
			?>
			<p>You do not currently manage any particpants.</p>
			<?php
		}
	}

	/**
	 * Show the section message
	 */
	public function cpd_user_managment_new_callback() {
		?>
		<p>
			You can add a new participant by completing the details below:
		</p>
		<p><strong>Note:</strong> The new user will <strong>Not</strong> automatically be added to a supervisors workload, but you can assign them using this page after they have been created.</p>
		<?php
	}

	/**
	 * Render the field
	 */
	public function cpd_user_managment_add_fields_callback() {
		?>
		<p>Add the username and email address of the participant.</p>
		<br/>
		<form method="post" action="">

			<p>
				<label for="cpd_new_username"><strong>Username</strong></label><br/>
				<input type="text" id="cpd_new_username" name="cpd_new_username" />
			</p>
			<br/>
			<p>
				<label for="cpd_new_email"><strong>Email</strong></label><br/>
				<input type="text" id="cpd_new_email" name="cpd_new_email" />
			</p>
			<br/>
			<p>Username and password will be mailed to the above email address.</p>
			<?php wp_nonce_field( 'cpd_add_participant', 'cpd_add_participant_nonce' ) ?>
			<br/>
			<p><input type="submit" class="button button-primary" value="Add Participant"/>

		</form>
		<?php
	}


	/**
	 * Add the options page
	 */
	public function add_options_page() {

		$blog_id          = get_current_blog_id();
		$current_user     = wp_get_current_user();
		$is_elevated_user = get_user_meta( $current_user->ID, 'elevated_user', TRUE ) == '1';
        $is_supervisor    = CPD_Users::user_is_site_supervisor( $current_user );

		if( ( is_super_admin() || $is_elevated_user || user_can( $current_user, 'administrator' ) ) && current_user_can( 'manage_options' ) ) {
			add_submenu_page( 'users.php', 'Manage Participants', 'Manage Participants', 'manage_options', 'cpd_settings_users_participants', array( $this, 'render_options_page' ) );
		}
	}

	/**
	 * Render the options page
	 */
	public function render_options_page(){

		?>
		<div class="wrap cpd-settings cpd-settings-users">
			<h2>Manage Participants</h2>
			<?php
				$current_user      = wp_get_current_user();
				$user_participants = get_user_meta( $current_user->ID, 'cpd_related_participants', TRUE );
				$supervisor        = $current_user->ID;
				$all_participants  = CPD_Users::get_participants();
				$user              = CPD_Users::get_instance();

				// Add new Participant
				if( isset( $_POST['cpd_new_username'] ) && !empty( $_POST['cpd_new_username'] ) && isset( $_POST['cpd_new_email'] ) && !empty( $_POST['cpd_new_email'] ) && isset( $_POST['cpd_add_participant_nonce'] ) && wp_verify_nonce( $_POST['cpd_add_participant_nonce'], 'cpd_add_participant' ) ) {

					switch_to_blog( SITE_ID_CURRENT_SITE );

					$user_name  = esc_attr( $_POST['cpd_new_username'] );
					$user_email = esc_attr( $_POST['cpd_new_email'] );

					$user_id = username_exists( $user_name );

					if ( !$user_id && is_email( $user_email ) && email_exists( $user_email ) == FALSE ) {
						$random_password = wp_generate_password( $length = 12, $include_standard_special_chars = FALSE );
						$user_id = wp_create_user( $user_name, $random_password, $user_email );
						$user->set_user_role( $user_id, 'participant' );
						wp_new_user_notification( $user_id, null, 'both' );
					} else {
						if( $user_id ) {
							?>
							<div class="alert alert-warning">
								<p>A user with this username already exists.</p>
							</div>
							<?php
						} else if( !is_email( $user_email ) ) {
							?>
							<div class="alert alert-warning">
								<p>Please enter a valid email address.</p>
							</div>
							<?php
						} else if( email_exists( $user_email ) ) {
							?>
							<div class="alert alert-warning">
								<p>A user with this email address already exists.</p>
							</div>
							<?php
						}
					}

					restore_current_blog();
				}

				// Update Participants
				if( isset( $_POST['cpd_participants'] ) && !empty( $_POST['cpd_participants'] ) && isset( $_POST['cpd_update_participant_management_nonce'] ) && wp_verify_nonce( $_POST['cpd_update_participant_management_nonce'], 'cpd_update_participant_management' ) ) {

					$post_participants = $_POST['cpd_participants'];

					if( is_array( $post_participants ) ) {

						foreach( $all_participants as $participant ) {

							$participant = $participant->ID;

							if( in_array( $participant, $post_participants ) && !in_array( $participant, $user_participants ) ) {
								CPD_Users::add_cpd_relationship( $supervisor, $participant );
								$journal =	get_active_blog_for_user( $participant );
								add_user_to_blog( $journal->blog_id, $supervisor, 'supervisor' );

							} else if( !in_array( $participant, $post_participants ) && in_array( $participant, $user_participants ) ) {
								CPD_Users::remove_cpd_relationship( $supervisor, $participant );
								$journal =	get_active_blog_for_user( $participant );
								remove_user_from_blog( $supervisor, $journal->blog_id );
							}
						}
					}
				}
				?>

	            <?php settings_fields( 'cpd_settings_users_participants_group' ); ?>
	            <?php do_settings_sections( 'cpd_settings_users_participants' ); ?>

		</div>
	<?php
	}

	public function copy_blog_user_id( $user_id ) {

		if( isset( $_POST[ 'cpd_template_base' ] ) && isset( $_POST[ 'cpd_template_name' ] ) && !empty( $_POST[ 'cpd_template_name' ] ) ) {

			$from_blog_id = esc_attr( $_POST[ 'cpd_template_base' ] );
			$user_id = get_current_user_id();
		}

		return $user_id;
	}
}
}
