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
		add_settings_section( 'cpd_template_managment', __('Manage Templates', $this->text_domain), array( $this, 'cpd_template_managment_callback' ), 'cpd_settings_templates' );
		add_settings_section( 'cpd_template_add', __('Create a new Template', $this->text_domain), array( $this, 'cpd_template_add_callback' ), 'cpd_settings_templates' );
	
    	/* Add fields to a section */
		add_settings_field( 'cpd_template_managment_fields', __('Your Templates', $this->text_domain), array( $this, 'cpd_template_managment_fields_callback' ), 'cpd_settings_templates', 'cpd_template_managment' );
		add_settings_field( 'cpd_template_base_fields', __('Template Base', $this->text_domain), array( $this, 'cpd_template_base_fields_callback' ), 'cpd_settings_templates', 'cpd_template_add' );
		add_settings_field( 'cpd_template_name_fields', __('Template Name', $this->text_domain), array( $this, 'cpd_template_name_fields_callback' ), 'cpd_settings_templates', 'cpd_template_add' );
	}

	/**
	 * Show the section message
	 */
	public function cpd_template_managment_callback() {
		?>
		<p>
			<?php _e('Templates define the content and settings that are added to a new Journal when it is created. When you create a new Journal you will be asked which Template you wish to create it from.', $this->text_domain);?>
		</p>
		<?php
	}

	/**
	 * Show the section message
	 */
	public function cpd_template_add_callback() {
		?>
		<p id="add-template">
			<?php _e('You can create a new template by completing the following section.', $this->text_domain);?>
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
				<p><?php _e('You can manage the following Templates:', $this->text_domain);?></p>
				<br/>
				<table>
					<tr>
						<th><?php _e('Name', $this->text_domain);?></th>
						<th><?php _e('Edit', $this->text_domain);?></th>
						<th><?php _e('Delete', $this->text_domain);?></th>
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
									<a href="<?php echo $blog->siteurl . '/wp-admin/';?>"><?php _e('Edit', $this->text_domain);?></a>
								</td>
								<td>
									<?php
									if( $blog->path != '/template-default/' ) {
										?>
										<a href="<?php echo esc_url( wp_nonce_url( admin_url( '?page=cpd_settings_templates&action=delete&amp;id=' . $blog->userblog_id  ) ) );?>"><?php _('Delete', $this->text_domain)?></a>
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
				<p><?php _e('You do not currently have access to any Templates.', $this->text_domain);?></p>
				<?php
			}

	}

	public function cpd_template_base_fields_callback() {

		$blogs = wp_get_sites();
		?>
		<p><?php _e('Choose an existing Template to use as a base for your new Template. All the settings and content from this Template will be copied accross to your new Template.', $this->text_domain);?></p>
		<br/>
		<p>
			<select id="cpd_template_base" name="cpd_template_base">
			<?php
			foreach( $blogs as $blog ) {

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
		</p>
		<?php
	}

	public function cpd_template_name_fields_callback() { 
		?>
		<p><?php _e('Choose a title for your new Template.', $this->text_domain);?></p> 
		<p><strong><?php _e('Note', $this->text_domain);?>:</strong> <?php _e('All Templates are automatically prefixed with the word \'Template\'.', $this->text_domain);?></p>
		<br/>
		<p>
			<?php _e('Template', $this->text_domain);?>
			<input type="text" id="cpd_template_name" name="cpd_template_name" value="<?php echo isset( $_POST['cpd_template_name'] ) ? $_POST['cpd_template_name'] : '';?>"/>
		</p>

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
		
		if( ( is_super_admin() || $is_elevated_user || user_can( $current_user, 'administrator' ) || $is_supervisor ) && current_user_can( 'manage_options' ) ) {
			add_menu_page( __('Templates', $this->text_domain), __('Templates', $this->text_domain), 'manage_options', 'cpd_settings_templates', array( $this, 'render_options_page' ), 'dashicons-welcome-write-blog' );
		}	
	}

	/**
	 * Render the options page
	 */
	public function render_options_page(){
		?>
		<div class="wrap cpd-settings cpd-settings-template">  
			<h2><?php _e('Templates', $this->text_domain);?></h2> 

			<?php
			// Create the new template
			if( isset( $_POST[ 'cpd_template_base' ] ) && isset( $_POST[ 'cpd_template_name' ] ) && !empty( $_POST[ 'cpd_template_name' ] ) ) {
				$name = 'Template ' . $_POST[ 'cpd_template_name' ];
				$name = esc_attr( $name );
				$slug = sanitize_title( $name );
				$base = esc_attr( $_POST[ 'cpd_template_base' ] );

				// Check Title is Unique
				if( get_id_from_blogname( $slug ) ) { 
					?>
					<div class="alert alert-warning">
						<p><?php _e('Sorry a Template with that name already exists.', $this->text_domain);?></p>
					</div>
					<?php
				} else {
					$copy = CPD_Blogs::get_instance();
					add_filter( 'copy_blog_user_id', array( $this, 'copy_blog_user_id' ), 2 );
					$copy->copy_blog( $slug, $name, $base, TRUE );
					remove_filter( 'copy_blog_user_id', array( $this, 'copy_blog_user_id' ), 2 );
				}
			}
			// Delete template
			if( isset( $_GET['id'] ) && isset( $_GET['action'] ) && isset( $_GET['_wpnonce'] ) && wp_verify_nonce( $_GET['_wpnonce'] ) && $_GET['action'] == 'delete' ) {
				$delete_id = esc_attr( $_GET['id'] );
				if( is_numeric( $delete_id ) ) {
					wpmu_delete_blog( $delete_id, FALSE );
					?>
					<div class="alert alert-warning">
						<p><?php _e('Your Template has been deleted.', $this->text_domain);?></p>
					</div>
					<?php
				}
			}
			?>
			<form action="#" method="POST">
	            <?php settings_fields( 'cpd_settings_templates_group' ); ?>
	            <?php do_settings_sections( 'cpd_settings_templates' ); ?>
	            <?php //submit_button(); ?>
	           	<p><input type="submit" class="button button-primary" value="<?php _e('Create New Template', $this->text_domain);?>"/>
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