<?php

// Exit if accessed directly
defined( 'ABSPATH' ) || exit;

if( !class_exists( 'CPD_Options_Privacy' ) ) {

/**
 * Copy Assignments
 *
 * Functionality to copy assignments
 *
 * @package    CPD
 * @subpackage CPD/admin
 * @author     Make Do <hello@makedo.in>
 */
class CPD_Options_Privacy {


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
		
		/* Register Settings */
		register_setting( 'cpd_settings_privacy_group', 'cpd_login_to_view' );

		/* Add sections */
		add_settings_section( 'cpd_section_login_to_view', 'Login to view journal', array( $this, 'cpd_section_login_to_view_callback' ), 'cpd_settings_privacy' );
	
    	/* Add fields to a section */
    	add_settings_field( 'cpd_login_to_view', 'Jounal can be viewed by:', array( $this, 'cpd_login_to_view_callback' ), 'cpd_settings_privacy', 'cpd_section_login_to_view' );
	}

	/**
	 * Show the section message
	 */
	public function cpd_section_login_to_view_callback() {
		?>
		<p>
			You can make this journal visible only to people that log in. 
		</p>
		<?php
	}

	/**
	 * Render the field
	 */
	public function cpd_login_to_view_callback() {

		$cpd_login_to_view = get_option( 'cpd_login_to_view', NULL );
		/* Set defaults */
		if( $cpd_login_to_view == NULL ) {
			add_option( 'cpd_login_to_view', 'true' );
			$cpd_login_to_view = get_option( 'cpd_login_to_view' );
		}

		?>
		<label><input type="radio" name="cpd_login_to_view" value="true" <?php checked( 'true', $cpd_login_to_view );?>> People with a username and password only</label><br/>
		<label><input type="radio" name="cpd_login_to_view" value="false" <?php checked( 'false', $cpd_login_to_view );?>> Anybody (available to the public)</label><br/>
		
		<br/>
		<p>People who have access to this journal via a username and password are as follows:</p>
		<br/>
		<table>
		<tr>
			<th>Name</th>
			<th>Role</th>
		</tr>
		<?php

		$users = get_users(); 
		foreach( $users as $user ) {
			$name 		= 	$user->user_firstname . ' ' . $user->user_lastname;
			$name 		=	trim( $name );
			if( empty( $name ) ) {
				$name = $user->display_name;
			}
			$roles = '';

			if( is_array( $user->roles ) && count( $user->roles ) > 0 ){
				foreach( $user->roles as $key=>$role ) {
					if( $key > 0 ) {
						$roles .= ', ';
					}
					$roles .= ucfirst( $role );
				}
			}
			?>
			<tr>
				<td><?php echo $name;?></td>
				<td><?php echo $roles;?></td>
			</tr>
			<?php
		}
		?>
		</table>
		<?php
	}

	/**
	 * Add the options page
	 */
	public function add_options_page() {
		add_menu_page( 'Privacy', 'Privacy', 'manage_options', 'cpd_settings_privacy', array( $this, 'render_options_page' ), 'dashicons-shield' );
	}

		/**
	 * Render the options page
	 */
	public function render_options_page(){ 
		?>
		<div class="wrap cpd-settings cpd-settings-privacy">  
			<h2>Privacy Settings</h2> 
			<form action="options.php" method="POST">
	            <?php settings_fields( 'cpd_settings_privacy_group' ); ?>
	            <?php do_settings_sections( 'cpd_settings_privacy' ); ?>
	            <?php submit_button(); ?>
	        </form>
		</div> 
	<?php
	}
}
}