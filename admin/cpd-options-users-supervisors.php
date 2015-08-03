<?php

// Exit if accessed directly
defined( 'ABSPATH' ) || exit;

if( !class_exists( 'CPD_Options_Users_Supervisors' ) ) {

/**
 * Copy Assignments
 *
 * Functionality to copy assignments
 *
 * @package    CPD
 * @subpackage CPD/admin
 * @author     Make Do <hello@makedo.in>
 */
class CPD_Options_Users_Supervisors {


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
		// add_settings_section( 'cpd_user_managment', 'Manage Your Participants', array( $this, 'cpd_user_managment_callback' ), 'cpd_settings_users_supervisors' );
		add_settings_section( 'cpd_user_managment_all', __('Manage Supervisors', $this->text_domain), array( $this, 'cpd_user_managment_all_callback' ), 'cpd_settings_users_supervisors' );
		add_settings_section( 'cpd_user_managment_new', __('Add New Supervisor', $this->text_domain), array( $this, 'cpd_user_managment_new_callback' ), 'cpd_settings_users_supervisors' );

    	/* Add fields to a section */
		add_settings_field( 'cpd_user_managment_add_fields', __('Add Supervisor', $this->text_domain), array( $this, 'cpd_user_managment_add_fields_callback' ), 'cpd_settings_users_supervisors', 'cpd_user_managment_new' );

	}

	

	/**
	 * Show the section message
	 */
	public function cpd_user_managment_all_callback() {
		?>
		<p>
			<?php _e('Listed are all the superviors and their participants. You can add or remove participants to and from a supervisors workload by checking the boxes next to their name.', $this->text_domain);?>
		</p>
		<?php

		$supervisors      = CPD_Users::get_supervisors();
		$participants 	  = CPD_Users::get_participants();

		if( is_array( $supervisors ) && count( $supervisors ) > 0 ) {

			?>
			<table class="form-table">
			<?php

			foreach( $supervisors as $supervisor ) {
				$name        = $supervisor->first_name . ' ' . $supervisor->last_name;
				$name        = trim( $name );
				$username    = $supervisor->user_login;

				if( empty( $name ) ) {
					$name    = $username;
				}
				?>
				<tr>
					<th>
						<strong><?php echo $name;?></strong> <em>(<?php echo $username;?>)</em>
					</th>
					<td>
				<?php

				if( is_array( $participants ) && count( $participants ) > 0 ) {

					?>
					<p><?php _e('Select participants to manage', $this->text_domain);?>:</p>
					<form method="post" action="">
					<ul>
						<?php 
							foreach( $participants as $participant ) {
								$name             = $participant->first_name . ' ' . $participant->last_name;
								$name             = trim( $name );
								$username         = $participant->user_login;
								$user_particpants = get_user_meta( $supervisor->ID, 'cpd_related_participants', TRUE );
								$checked          = '';
								$journal          =	get_active_blog_for_user( $participant->ID );
								$disabled         = '';
								
								if( empty( $name ) ) {
									$name    = $username;
								}

								if( !is_array( $user_particpants ) ) {
									$user_particpants = array();
								}

								if( in_array( $participant->ID, $user_particpants ) ) {
									$checked = 'checked';
								}

								?>
								<li>
									<label>
									<input type="checkbox" name="cpd_participants[]" <?php echo $checked;?> value="<?php echo $participant->ID;?>"/>
										<strong><?php echo $name;?></strong> <em>(<?php echo $username;?>)</em>
									</label>
								</li>
								<?php
							}
						?>
					</ul>
					<?php wp_nonce_field( 'cpd_update_participant_management', 'cpd_update_participant_management_nonce' ) ?>
					<input type="hidden" value="<?php echo $supervisor->ID;?>" name="cpd_supervisor";?>
					<p><input type="submit" class="button button-primary" value="<?php _e('Update Participants', $this->text_domain);?>"/></p>
					</form>
					<?php
				} else {
					?>
					<p><?php _e('There are no participants available.', $this->text_domain);?></p>
					<?php
				}

				?>
					</td>
				</tr>
				<?php
			}

			?>
			</table>
			<?php

		} else {
			?>
			<p><?php _e('There are no supervisors available.', $this->text_domain);?></p>
			<?php
		}
	}

	/**
	 * Show the section message
	 */
	public function cpd_user_managment_new_callback() {
		?>
		<p>
			<?php _e('You can add a new supervisor by completing the details below', $this->text_domain);?>:
		</p>
		<p><strong><?php _e('Note', $this->text_domain);?>:</strong> <?php _e('The new user will', $this->text_domain);?> <strong><?php _e('Not', $this->text_domain);?></strong> <?php _e('automatically be added to a workload, but you can assign participants to them using this page after they have been created.', $this->text_domain);?></p>
		<?php
	}

	/**
	 * Render the field
	 */
	public function cpd_user_managment_add_fields_callback() {
		?>
		<p><?php _e('Add the username and email address of the supervisor.', $this->text_domain);?></p> 
		<br/>
		<form method="post" action="">

			<p>
				<label for="cpd_new_username"><strong><?php _e('Username', $this->text_domain);?></strong></label><br/>
				<input type="text" id="cpd_new_username" name="cpd_new_username" />
			</p>
			<br/>
			<p>
				<label for="cpd_new_email"><strong><?php _e('Email', $this->text_domain);?></strong></label><br/>
				<input type="text" id="cpd_new_email" name="cpd_new_email" />
			</p>
			<br/>
			<p><?php _e('Username and password will be mailed to the above email address.', $this->text_domain);?></p>
			<?php wp_nonce_field( 'cpd_add_supervisor', 'cpd_add_supervisor_nonce' ) ?>
			<br/>
			<p><input type="submit" class="button button-primary" value="<?php _e('Add Supervisor', $this->text_domain);?>"/>

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
		
			add_submenu_page( 'users.php', __('Manage Supervisors', $this->text_domain), __('Manage Supervisors', $this->text_domain), 'manage_options', 'cpd_settings_users_supervisors', array( $this, 'render_options_page' ) );
		}	
	}

	/**
	 * Render the options page
	 */
	public function render_options_page(){

		?>
		<div class="wrap cpd-settings cpd-settings-users">  
			<h2><?php _e('Manage Supervisors', $this->text_domain);?></h2> 
			<?php
				$current_user      = wp_get_current_user();
				$user_participants = get_user_meta( $current_user->ID, 'cpd_related_participants', TRUE );
				$supervisor        = $current_user->ID;
				$all_participants  = CPD_Users::get_participants();
				$user              = CPD_Users::get_instance();

				// Add new Participant
				if( isset( $_POST['cpd_new_username'] ) && !empty( $_POST['cpd_new_username'] ) && isset( $_POST['cpd_new_email'] ) && !empty( $_POST['cpd_new_email'] ) && isset( $_POST['cpd_add_supervisor_nonce'] ) && wp_verify_nonce( $_POST['cpd_add_supervisor_nonce'], 'cpd_add_supervisor' ) ) {
					
					switch_to_blog( SITE_ID_CURRENT_SITE );
					
					$user_name  = esc_attr( $_POST['cpd_new_username'] );
					$user_email = esc_attr( $_POST['cpd_new_email'] );

					$user_id = username_exists( $user_name );

					if ( !$user_id && is_email( $user_email ) && email_exists( $user_email ) == FALSE ) {
						$random_password = wp_generate_password( $length = 12, $include_standard_special_chars = FALSE );
						$user_id = wp_create_user( $user_name, $random_password, $user_email );
						$user->set_user_role( $user_id, 'supervisor' );
						wp_new_user_notification( $user_id, $random_password );
					} else {
						if( $user_id ) {
							?>
							<div class="alert alert-warning">
								<p><?php _e('A user with this username already exists.', $this->text_domain);?></p>
							</div>
							<?php
						} else if( !is_email( $user_email ) ) {
							?>
							<div class="alert alert-warning">
								<p><?php _e('Please enter a valid email address.', $this->text_domain);?></p>
							</div>
							<?php
						} else if( email_exists( $user_email ) ) {
							?>
							<div class="alert alert-warning">
								<p><?php _e('A user with this email address already exists.', $this->text_domain);?></p>
							</div>
							<?php
						}
					}

					restore_current_blog();
				}

				// Update Supervisors
				if( isset( $_POST['cpd_participants'] ) && !empty( $_POST['cpd_participants'] ) && isset( $_POST['cpd_update_participant_management_nonce'] ) && wp_verify_nonce( $_POST['cpd_update_participant_management_nonce'], 'cpd_update_participant_management' ) ) {

					$post_participants = $_POST['cpd_participants'];
					$supervisor = $_POST['cpd_supervisor'];
					$user_participants = get_user_meta( $supervisor, 'cpd_related_participants', TRUE );

					if( is_array( $post_participants ) ) {

						foreach( $all_participants as $participant ) {

							$participant = $participant->ID;

							if( in_array( $participant, (array) $post_participants ) && !in_array( $participant, (array) $user_participants ) ) {
								CPD_Users::add_cpd_relationship( $supervisor, $participant );
								$journal =	get_active_blog_for_user( $participant );
								add_user_to_blog( $journal->blog_id, $supervisor, 'supervisor' );	

							} else if( !in_array( $participant, (array) $post_participants ) && in_array( $participant, (array) $user_participants ) ) {
								CPD_Users::remove_cpd_relationship( $supervisor, $participant );
								$journal =	get_active_blog_for_user( $participant );
								remove_user_from_blog( $supervisor, $journal->blog_id );					
							}
						}
					}
				}
				?>

	            <?php settings_fields( 'cpd_settings_users_supervisors_group' ); ?>
	            <?php do_settings_sections( 'cpd_settings_users_supervisors' ); ?>	           

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