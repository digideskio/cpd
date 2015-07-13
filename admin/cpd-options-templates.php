<?php

// Exit if accessed directly
defined( 'ABSPATH' ) || exit;

if( !class_exists( 'CPD_Options_Templates' ) ) {

/**
 * Copy Assignments
 *
 * Functionality to copy assignments
 *
 * @package    CPD
 * @subpackage CPD/admin
 * @author     Make Do <hello@makedo.in>
 */
class CPD_Options_Templates {


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
		add_settings_section( 'cpd_template_managment', 'Manage Templates', array( $this, 'cpd_template_managment_callback' ), 'cpd_settings_templates' );
	
    	/* Add fields to a section */
		add_settings_field( 'cpd_template_managment_fields', 'Your Templates', array( $this, 'cpd_template_managment_fields_callback' ), 'cpd_settings_templates', 'cpd_template_managment' );
	}

	/**
	 * Show the section message
	 */
	public function cpd_template_managment_callback() {
		?>
		<p>
			Templates define the content and settings that are added to a new Journal when it is created.
		</p>
		<?php
	}

	/**
	 * Render the field
	 */
	public function cpd_template_managment_fields_callback() {
		
		$current_user                   = wp_get_current_user();
		$roles                          = $current_user->roles;
		$is_supervisor                  = CPD_Users::user_is_site_supervisor( $current_user );
		$is_elevated_user 				= get_user_meta( $current_user->ID, 'elevated_user', TRUE ) == '1';
		$has_templates 					= CPD_Blogs::user_has_templates( $current_user );
		$blogs                          = get_blogs_of_user( $current_user->ID );

		if( $has_templates ) {
				?>
				<p>You can manage the following Templates:</p>
				<br/>
				<table>
					<tr>
						<th>Name</th>
						<th>Edit</th>
						<th>Delete</th>
					</tr>
					<?php 
					foreach( $blogs as $blog ) {
						if( strrpos( $blog->path, '/template-' ) === 0 ) {
						?>
							<tr>
								<td>
									<a href="<?php echo $blog->siteurl . '/wp-admin/';?>"><?php echo $blog->blogname;?></a>
								</td>
								<td>
									<a href="<?php echo $blog->siteurl . '/wp-admin/';?>">Edit</a>
								</td>
								<td>
									<?php
									if( $blog->path != '/template-default/' ) {
										?>
										<a href="<?php echo esc_url( wp_nonce_url( network_admin_url( 'sites.php?action=confirm&amp;action2=deleteblog&amp;id=' . $blog->userblog_id . '&amp;msg=' . urlencode( sprintf( __( 'You are about to delete the site %s.' ), $blog->blogname ) ) ), 'confirm') );?>">Delete</a>
										<?php
									} else {
										?>
										-
										<?php 
									}
									?>
								</td>
							</tr>
						<?php
						}
					}
					?>
				</table>
				<?php
			} else {
				?>
				<p>You do not currently have access to any Templates.</p>
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
		
		if( ( is_super_admin() || $is_elevated_user || user_can( $current_user, 'administrator' ) || $is_supervisor ) && current_user_can( 'manage_options' ) && SITE_ID_CURRENT_SITE != $blog_id ) {
			add_menu_page( 'Templates', 'Templates', 'manage_options', 'cpd_settings_templates', array( $this, 'render_options_page' ), 'dashicons-welcome-write-blog' );
		}	
	}

	/**
	 * Render the options page
	 */
	public function render_options_page(){ 
		?>
		<div class="wrap cpd-settings cpd-settings-template">  
			<h2>Template Settings</h2> 
			<form action="/wp-admin/options.php" method="POST">
	            <?php settings_fields( 'cpd_settings_templates_group' ); ?>
	            <?php do_settings_sections( 'cpd_settings_templates' ); ?>
	            <?php //submit_button(); ?>
	        </form>
		</div> 
	<?php
	}
}
}