<?php

// Exit if accessed directly
defined( 'ABSPATH' ) || exit;

if( !class_exists( 'CPD_Options_Users' ) ) {

/**
 * Copy Assignments
 *
 * Functionality to copy assignments
 *
 * @package    CPD
 * @subpackage CPD/admin
 * @author     Make Do <hello@makedo.in>
 */
class CPD_Options_Users {


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
		add_settings_section( 'cpd_user_managment', 'Manage Participants', array( $this, 'cpd_user_managment_callback' ), 'cpd_settings_users' );
	
    	/* Add fields to a section */
		add_settings_field( 'cpd_user_managment_fields', 'Your Participants', array( $this, 'cpd_user_managment_fields_callback' ), 'cpd_settings_users', 'cpd_user_managment' );
		add_settings_field( 'cpd_user_managment_all_fields', 'All Participants', array( $this, 'cpd_user_managment_all_fields_callback' ), 'cpd_settings_users', 'cpd_user_managment' );

	}

	/**
	 * Show the section message
	 */
	public function cpd_user_managment_callback() {
		?>
		<p>
			Listed are all the participants that you currently manage. You can update them individually below.
		</p>
		<?php
	}

	/**
	 * Show the section message
	 */
	public function cpd_template_add_callback() {
		?>
		<p id="add-template">
			You can create a new template by completing the following section.
		</p>
		<?php
	}

	/**
	 * Render the field
	 */
	public function cpd_user_managment_fields_callback() {
		
		$current_user      = wp_get_current_user();
		$roles             = $current_user->roles;
		$is_supervisor     = CPD_Users::user_is_site_supervisor( $current_user );
		$is_elevated_user  = get_user_meta( $current_user->ID, 'elevated_user', TRUE ) == '1';
		$participants 	   = get_user_meta( $current_user->ID, 'cpd_related_participants', TRUE );

		if( is_array( $participants ) && count( $participants ) > 0 ) {

			?>
			<p>You can manage the following participants:</p>
			<br/>
			<table>
				<tr>
					<td>User</td>
					<td>Blog</td>
					<td>Supervisors</td>
				</tr>
				<?php 
					foreach( $participants as $participant ) {
						$user        = get_userdata( $participant );
						$name        = $user->first_name . ' ' . $user->last_name;
						$name        = trim( $name );
						$username    = $user->user_login;
						$blog        = get_active_blog_for_user( $participant );
						$supervisors = get_user_meta( $participant, 'cpd_related_supervisors', TRUE );
						
						if( empty( $name ) ) {
							$name    = $username;
						}
						?>
						<tr>
							<td>
							<strong><?php echo $name;?></strong> <em>(<?php echo $username;?>)</em>
							</td>
							<td>
							<?php
								if(is_object( $blog ) ) {
									?>
									<a href="<?php echo $blog->siteurl;?>"><?php echo $blog->blogname;?></a>
									<?php
								} else {
									echo 'None set';
								}
							?>
							</td>
							<td>
							<?php 
								if( is_array( $supervisors ) && count( $supervisors ) > 0 ) {
									?>
									<ul>
										<?php
										foreach( $supervisors as $supervisor ) {
											$user        = get_userdata( $supervisor );
											$name        = $user->first_name . ' ' . $user->last_name;
											$name        = trim( $name );
											$username    = $user->user_login;
											if( empty( $name ) ) {
												$name    = $username;
											}
											?>
											<li>
												<strong><?php echo $name;?></strong> <em>(<?php echo $username;?>)</em>
											</li>
											<?php
										}
										?>
									</ul>
									<?php
								} else {
									echo 'None set';
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
			<p>You do not currently manage any particpants.</p>
			<?php
		}

	}

	/**
	 * Render the field
	 */
	public function cpd_user_managment_all_fields_callback() {

		$current_user      = wp_get_current_user();
		$participants 	   = CPD_Users::get_participants();

		if( is_array( $participants ) && count( $participants ) > 0 ) {

			?>
			<p>Select participants to manage:</p>
			<ul>
				<?php 
					foreach( $participants as $participant ) {
						$name             = $participant->first_name . ' ' . $participant->last_name;
						$name             = trim( $name );
						$username         = $participant->user_login;
						$user_particpants = get_user_meta( $current_user->ID, 'cpd_related_participants', TRUE );
						$checked          = '';
						
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
							<input type="checkbox" name="cpd_participants[]" <?php echo $checked;?>/>
								<strong><?php echo $name;?></strong> <em>(<?php echo $username;?>)</em>
							</label>
						</li>
						<?php
					}
				?>
			</ul>
			<?php
		} else {
			?>
			<p>There are no participants available.</p>
			<?php
		}

	}


	/**
	 * Add the options page
	 */
	public function add_options_page() {

		$blog_id          = get_current_blog_id();
		$current_user     = wp_get_current_user();
		$is_elevated_user = get_user_meta( $current_user->ID, 'elevated_user', TRUE ) == '1';
        $is_supervisor    = CPD_Users::user_is_site_supervisor( $current_user );
		
		// if( ( is_super_admin() || $is_elevated_user || user_can( $current_user, 'administrator' ) || $is_supervisor ) && current_user_can( 'manage_options' ) ) {
		if( $is_supervisor && current_user_can( 'manage_options' ) ) {
			add_submenu_page( 'users.php', 'Manage Participants', 'Manage Participants', 'manage_options', 'cpd_settings_users', array( $this, 'render_options_page' ) );
		}	
	}

	/**
	 * Render the options page
	 */
	public function render_options_page(){
		?>
		<div class="wrap cpd-settings cpd-settings-users">  
			<h2>Manage Participants</h2> 
			<form action="#" method="POST">
	            <?php settings_fields( 'cpd_settings_users_group' ); ?>
	            <?php do_settings_sections( 'cpd_settings_users' ); ?>
	            <?php //submit_button(); ?>
	           	<!-- <p><input type="submit" class="button button-primary" value="Create New Template"/> -->
	        </form>
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