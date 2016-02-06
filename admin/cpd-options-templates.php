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
		add_settings_section( 'cpd_template_add', 'Create a new Template', array( $this, 'cpd_template_add_callback' ), 'cpd_settings_templates' );

    	/* Add fields to a section */
		add_settings_field( 'cpd_template_managment_fields', 'Your Templates', array( $this, 'cpd_template_managment_fields_callback' ), 'cpd_settings_templates', 'cpd_template_managment' );
		add_settings_field( 'cpd_template_base_fields', 'Template Base', array( $this, 'cpd_template_base_fields_callback' ), 'cpd_settings_templates', 'cpd_template_add' );
		add_settings_field( 'cpd_template_name_fields', 'Template Name', array( $this, 'cpd_template_name_fields_callback' ), 'cpd_settings_templates', 'cpd_template_add' );
	}

	/**
	 * Show the section message
	 */
	public function cpd_template_managment_callback() {
		?>
		<p>
			Templates define the content and settings that are added to a new Journal when it is created. When you create a new Journal you will be asked which Template you wish to create it from.
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
										<a href="<?php echo esc_url( wp_nonce_url( admin_url( '?page=cpd_settings_templates&action=delete&amp;id=' . $blog->userblog_id  ) ) );?>">Delete</a>
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

	public function cpd_template_base_fields_callback() {

		$blogs = wp_get_sites();
		?>
		<p>Choose an existing Template to use as a base for your new Template. All the settings and content from this Template will be copied accross to your new Template.</p>
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
		<p>Choose a title for your new Template.</p>
		<p><strong>Note:</strong> All Templates are automatically prefixed with the word 'Template'.</p>
		<br/>
		<p>
			Template
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

		if( ( is_super_admin() || $is_elevated_user || user_can( $current_user, 'administrator' ) || $is_supervisor ) && current_user_can( 'supervise_users' ) ) {
			add_menu_page( 'Templates', 'Templates', 'supervise_users', 'cpd_settings_templates', array( $this, 'render_options_page' ), 'dashicons-welcome-write-blog' );
		}
	}

	/**
	 * Render the options page
	 */
	public function render_options_page(){
		?>
		<div class="wrap cpd-settings cpd-settings-template">
			<h2>Templates</h2>

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
						<p>Sorry a Template with that name already exists.</p>
					</div>
					<?php
				} else {
					$copy = CPD_Blogs::get_instance();
					add_filter( 'copy_blog_user_id', array( $this, 'copy_blog_user_id' ), 2 );
					$copy->copy_blog( $slug, $name, $base, false );
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
						<p>Your Template has been deleted.</p>
					</div>
					<?php
				}
			}
			?>
			<form action="#" method="POST">
	            <?php settings_fields( 'cpd_settings_templates_group' ); ?>
	            <?php do_settings_sections( 'cpd_settings_templates' ); ?>
	            <?php //submit_button(); ?>
	           	<p><input type="submit" class="button button-primary" value="Create New Template"/>
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
