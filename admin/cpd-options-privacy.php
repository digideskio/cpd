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

		/* Set defaults */
		$cpd_login_to_view = get_option( 'cpd_login_to_view', NULL );
		if( $cpd_login_to_view == NULL ) {
			
			$blog_id = get_current_blog_id();
			
			if( SITE_ID_CURRENT_SITE == $blog_id ) {
				add_option( 'cpd_login_to_view', 'false' );
			}
			else {
				add_option( 'cpd_login_to_view', 'true' );
			}
			$cpd_login_to_view = get_option( 'cpd_login_to_view' );
		}
		
		/* Add sections */
		add_settings_section( 'cpd_section_login_to_view', 'Login to view journal', array( $this, 'cpd_section_login_to_view_callback' ), 'cpd_settings_privacy' );
		add_settings_section( 'cpd_section_private_content', 'Private content access', array( $this, 'cpd_section_private_content_callback' ), 'cpd_settings_privacy' );
		add_settings_section( 'cpd_section_private_content_overview', 'Private content overview', array( $this, 'cpd_section_private_content_overview_callback' ), 'cpd_settings_privacy' );
	
    	/* Add fields to a section */
		add_settings_field( 'cpd_login_to_view', 'Jounal can be viewed by:', array( $this, 'cpd_login_to_view_callback' ), 'cpd_settings_privacy', 'cpd_section_login_to_view' );
		add_settings_field( 'cpd_private_content', 'Private content can be viewed by:', array( $this, 'cpd_private_content_callback' ), 'cpd_settings_privacy', 'cpd_section_private_content' );
		add_settings_field( 'cpd_private_content_overview', 'Private content:', array( $this, 'cpd_private_content_overview_callback' ), 'cpd_settings_privacy', 'cpd_section_private_content_overview' );

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
	 * Show the section message
	 */
	public function cpd_section_private_content_callback() {
		?>
		<p id="private-content-who">
			Private content can only be viewed by certain people, even if your journal is open to members of the public.
			The following people can view content that is marked as private in your Journal.
		</p>
		<p>To view private content, visitors must be logged into the Journal with a username and password.</p>
		<?php
	}

	/**
	 * Show the section message
	 */
	public function cpd_section_private_content_overview_callback() {
		?>
		<p id="private-content-overview">
			The following peices of content are set to private in your journal.
		</p>
		<?php
	}

	/**
	 * Render the field
	 */
	public function cpd_login_to_view_callback() {

		$cpd_login_to_view = get_option( 'cpd_login_to_view', NULL );

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

		

		// $users = CPD_Users::get_multisite_users();
		// foreach ( $users as $key => $user ) {
		// 	if( is_super_admin( $user->ID ) ) {
		// 	$name 		= 	$user->user_firstname . ' ' . $user->user_lastname;
		// 	$name 		=	trim( $name );
		// 	if( empty( $name ) ) {
		// 		$name = $user->display_name;
		// 	}
		// 	$roles = 'Network Administrator';
			?>
<!-- 			<tr>
				<td><a href="mailto:<?php echo $user->user_email;?>"><?php echo $name;?></a></td>
				<td><?php echo $roles;?></td>
			</tr> -->
			<?php
		// 	}
		// }

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
				<td><a href="mailto:<?php echo $user->user_email;?>"><?php echo $name;?> <?php echo  get_current_user_id() == $user->ID ? '(You)' : '';?></a> </td>
				<td><?php echo $roles;?></td>
			</tr>
			<?php
		}
		?>
		</table>
		<br/>
		<p><strong>Please note:</strong> system administrators can also view your journal.</p>
		<?php
	}

	/**
	 * Render the field
	 */
	public function cpd_private_content_callback() {

		?>

		<p>People who have access to view private content on this journal are as follows:</p>
		<br/>
		<table>
		<tr>
			<th>Name</th>
			<th>Role</th>
		</tr>
		<?php

		$users = get_users(); 
		foreach( $users as $user ) {
			if( user_can( $user, 'read_private_posts' ) ) {
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
					<td><a href="mailto:<?php echo $user->user_email;?>"><?php echo $name;?> <?php echo  get_current_user_id() == $user->ID ? '(You)' : '';?></a> </td>
					<td><?php echo $roles;?></td>
				</tr>
				<?php
			}
		}
		?>
		</table>
		<br/>
		<p><strong>Please note:</strong> system administrators can also view private posts.</p>
		<?php
	}

	/**
	 * Render the field
	 */
	public function cpd_private_content_overview_callback() {

		?>
		<p>You can mark individual peices of content as private by editing that peice of content.</p>
		<?php
		$private_posts = get_posts(
			array(
				'post_type'        => 'any',
				'posts_per_page'   => -1,
				'post_status'      => 'private'
			)
		);

		if( !is_array( $private_posts ) ) {
			$private_posts = array();
		}

		if( count( $private_posts ) > 0 ) {
			?>
				<br/>
				<table>
					<tr>
						<th>Name</th>
						<th>Content Type</th>
						<th>View</th>
						<th>Edit</th>
					</tr>
				<?php
				foreach ( $private_posts as $key => $private_post ) {
					$post_type = get_post_type_object( $private_post->post_type );
					?>
					<tr>
						<td><?php echo $private_post->post_title;?></td>
						<td><?php echo $post_type->labels->singular_name;?></td>
						<td><a href="<?php echo get_permalink( $private_post->ID );?>">View<span class="screen-reader-text"> '<?php echo $private_post->post_title;?>'</span></td>
						<td><a href="<?php echo get_edit_post_link( $private_post->ID );?>">Edit<span class="screen-reader-text"> '<?php echo $private_post->post_title;?>'</span></td>
					</tr>
					<?php
				}
				?>
				</table>
			<?php
		}
		else {
			?>
				<p><strong>0</strong> peices of content have been set to private in your Journal.</p>
			<?php 
		}
	}

	/**
	 * Add the options page
	 */
	public function add_options_page() {
		$blog_id = get_current_blog_id();
		if( current_user_can( 'manage_options' ) && SITE_ID_CURRENT_SITE != $blog_id && !CPD_Blogs::blog_is_template() ) {
			add_menu_page( 'Privacy', 'Privacy', 'manage_options', 'cpd_settings_privacy', array( $this, 'render_options_page' ), 'dashicons-shield' );
		}
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