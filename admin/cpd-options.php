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

// Exit if accessed directly
defined( 'ABSPATH' ) || exit;

if( !class_exists( 'CPD_Options' ) ) {

/**
 * The dashboard-specific functionality of the plugin.
 *
 * Defines the plugin name, version, and enqueue the dashboard-specific stylesheet and JavaScript.
 *
 * @package    CPD
 * @subpackage CPD/admin
 * @author     Make Do <hello@makedo.in>
 */
class CPD_Options {


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
	 * @since    1.0.0
	 * @var      string    $instance       The name of this plugin.
	 * @var      string    $version    The version of this plugin.
	 */
	public function __construct() {
		
	}

	/**
	 * Initialize the class and set its properties.
	 *
	 * @var      string    $text_domain       The text domain of the plugin.
	 *
	 * @since    2.0.0
	 **/
	public function set_text_domain( $text_domain ) { 
		$this->text_domain = $text_domain;
	}

	public function init_options_page() {
		
		/* Register Settings */
		register_setting( 'cpd_settings_group', 'cpd_default_posts' );
		register_setting( 'cpd_settings_group', 'cpd_new_blog_options' );

		/* Add a section */
		add_settings_section( 'cpd_section_assignments', 'Assignment Templates', array( $this, 'cpd_section_assignments_callback' ), 'cpd_settings' );
		add_settings_section( 'cpd_section_pages', 'Page Templates', array( $this, 'cpd_section_pages_callback' ), 'cpd_settings' );
		add_settings_section( 'cpd_section_posts', 'Post Templates', array( $this, 'cpd_section_posts_callback' ), 'cpd_settings' );
		add_settings_section( 'cpd_section_manual_defaults', 'Manual Defaults', array( $this, 'cpd_section_manual_defaults_callback' ), 'cpd_settings' );
    	
    	/* Add fields to a section */
    	add_settings_field( 'cpd_section_assignments_default_assignments', 'Default Assignments', array( $this, 'cpd_section_assignments_default_assignments_callback' ), 'cpd_settings', 'cpd_section_assignments' );
    	add_settings_field( 'cpd_section_pages_default_pages', 'Default Pages', array( $this, 'cpd_section_pages_default_pages_callback' ), 'cpd_settings', 'cpd_section_pages' );
    	add_settings_field( 'cpd_section_posts_default_posts', 'Default Posts', array( $this, 'cpd_section_posts_default_posts_callback' ), 'cpd_settings', 'cpd_section_posts' );
    	add_settings_field( 'cpd_section_manual_defaults_key_values', 'Key / Values', array( $this, 'cpd_section_manual_defaults_key_values_callback' ), 'cpd_settings', 'cpd_section_manual_defaults' );
	}


	/**
	 * Example section help text
	 *
	 * @since    2.0.0
	 */
	public function cpd_section_assignments_callback() {
		?>
		<p>
			Please check the assignments that you wish to be created within every new journal.
		</p>
		<ul>
			<li>
				- If you check the 'Add to menu' box next to anassignment, that item will be added to the top level menu on the participants journal. 
			</li>
			<li>
				- To alter the order of items in the menu, you can assign a number next to an item. The lower the number, the earlier in the menu it will appear.
			</li>
			<li>
				- Items will <strong>not</strong> be published in the new journal automatically.</em>
			</li>
		</ul>
		<?php
	}

	public function cpd_section_pages_callback() {
		?>
		<p>
			Please check the pages that you wish to be created within every new journal.
		</p>
		<ul>
			<li>
				- If you check the 'Add to menu' box next to a page, that item will be added to the top level menu on the participants journal. 
			</li>
			<li>
				- To alter the order of items in the menu, you can assign a number next to an item. The lower the number, the earlier in the menu it will appear.
			</li>
			<li>
				- Items will <strong>not</strong> be published in the new journal automatically.</em>
			</li>
		</ul>
		<?php
	}

	public function cpd_section_posts_callback() {
		?>
		<p>
			Please check the posts that you wish to be created within every new journal.
		</p>
		<ul>
			<li>
				- If you check the 'Add to menu' box next to a post, that item will be added to the top level menu on the participants journal. 
			</li>
			<li>
				- To alter the order of items in the menu, you can assign a number next to an item. The lower the number, the earlier in the menu it will appear.
			</li>
			<li>
				- Items will <strong>not</strong> be published in the new journal automatically.</em>
			</li>
		</ul>
		<?php
	}

	public function cpd_section_manual_defaults_callback() {
		?>
		<p>
			These options are used to set up a new blog for a CPD participant. For example, they can be used to set a default theme.
		</p>
		<ul>
			<li>- Enter any valid blog meta name/value pairs.</li>
			<li>- Enter one pair per line separated by any whitespace characters.</li>
		</ul>
		<?php
	}

	/**
	 * Example field render
	 *
	 * @since    2.0.0
	 */
	public function cpd_section_assignments_default_assignments_callback() {

		global $wpdb;

		$sites 						= 	array();
		$have_assignments 			= 	FALSE;
		$post_list 					= 	get_option( 'cpd_default_posts' );
		
		if( !is_array( $post_list ) ) {
			$post_list 				= 	array();
		}

		// Get all of the journals
		$sites 						= 	wp_get_sites( 
											array( 
												'network_id' 	=> 	$wpdb->siteid, 
												'limit' 		=> 	0 
											) 
										);

		foreach( $sites as $site)
		{
			switch_to_blog( $site['blog_id'] );

			$assignment 			= 	get_page_by_path( 'assignment-templates' );
			$assignment_id 			= 	is_object( $assignment ) ? $assignment->ID : NULL;

			if( $assignment_id != NULL )
			{
				$args = array(
					'sort_order' 	=> 	'ASC',
					'sort_column' 	=> 	'post_title',
					'parent' 		=> 	$assignment_id,
					'post_type' 	=> 	'page',
					'post_status' 	=> 	array( 'publish', 'pending', 'draft', 'private' )
				); 
				
				$assignments 		= 	get_pages( $args );

				// If there are some assignments
				if( count( $assignments ) > 0 )
				{
					$have_assignments = TRUE;
					
					?>
					<p><strong>Assignments in '<?php echo wp_title(); ?>'</strong><br/><br/></p>
					<?php

					?>
					<table class="assignments">
					<tr>
						<th width="80%" class="left">Assignment</th>
						<th width="10%">Add to menu</th>
						<th width="10%">Menu order</th>
					</tr>
					<?php
					foreach( $assignments as $assignment )
					{
						$checked 			= 	isset( $post_list[ $site['blog_id'] ] ) && array_key_exists( $assignment->ID, $post_list[ $site['blog_id'] ] ) ? ' checked' : '';
						$menu 				=	'';
						$menu_order 		= 	0;
						
						if( $checked != '' ) {
							$menu 			= 	isset( $post_list[ $site['blog_id'] ][ $assignment->ID ][ 'in_menu' ] ) ? ' checked' : '';

							if( $menu != '' ) {
								$menu_order =	isset( $post_list[ $site['blog_id'] ][ $assignment->ID ][ 'menu_order' ] ) ? $post_list[ $site['blog_id'] ][ $assignment->ID ][ 'menu_order' ] : 0;
							}
						}

						?>
						<tr>
							<td>

								<input 
									type="checkbox" 
									class="check" 
									name="cpd_default_posts[<?php echo $site['blog_id'];?>][<?php echo $assignment->ID;?>][post_id]"
									value="<?php echo $assignment->ID;?>"
									<?php echo $checked;?> 
									id="blog_<?php echo $site['blog_id']; ?>-post_<?php echo $assignment->ID; ?>"
								/>
								<label for="blog_<?php echo $site['blog_id']; ?>-post_<?php echo $assignment->ID; ?>">
									<?php echo $assignment->post_title; ?>
								</label>
							</td>
							<td>
								<input 
									type="checkbox" 
									class="menu disabled" 
									disabled
									name="cpd_default_posts[<?php echo $site['blog_id'];?>][<?php echo $assignment->ID;?>][in_menu]"
									value="true"
									<?php echo $menu;?> 
									id="menu_blog_<?php echo $site['blog_id']; ?>-post_<?php echo $assignment->ID; ?>" 
								/>
								<label for="menu_blog_<?php echo $site['blog_id']; ?>-post_<?php echo $assignment->ID; ?>" class="screen-reader-text">
									Add '<?php echo $assignment->post_title; ?>' to menu
								</label>
							</td>
							<td>
								<input 
									type="text" 
									class="menu_order disabled"
									disabled
									name="cpd_default_posts[<?php echo $site['blog_id'];?>][<?php echo $assignment->ID;?>][menu_order]"
									value="<?php echo $menu_order;?>"
									id="menu_order_blog_<?php echo $site['blog_id']; ?>-post_<?php echo $assignment->ID; ?>" 
								/>
								<label for="menu_order_blog_<?php echo $site['blog_id']; ?>-post_<?php echo $assignment->ID; ?>" class="screen-reader-text">
									Menu order for '<?php echo $assignment->post_title; ?>'
								</label>
							</td>
						</tr>
						<?php
					}
					?>
					</table>
					<?php
				}
			}
			
			restore_current_blog();
		}
		if( !$have_assignments )
		{
			?>
				<p><strong>No assignment templates have been created as yet.</strong></p>
				<p>To create an assignment template, follow these steps:</p>
				<ol>
					<li>In one of the journals, or the master website, create a <strong>page</strong> titled 'Assignment Templates' (make sure it has the slug <em>'assignment-templates'</em>).</li>
					<li>Create your assignments (using the pages menu option) and ensure that their parent page is set as the 'Assignment Templates' page</li>
					<li>You do not have to publish these pages, this system will find them and list them here</li> 
				</ol>
			<?php
		}
	}

	public function cpd_section_pages_default_pages_callback() {

		global $wpdb;

		$sites 						= 	array();
		$have_pages 			= 	FALSE;
		$post_list 					= 	get_option( 'cpd_default_posts' );
		
		if( !is_array( $post_list ) ) {
			$post_list 				= 	array();
		}

		// Get all of the journals
		$sites 						= 	wp_get_sites( 
											array( 
												'network_id' 	=> 	$wpdb->siteid, 
												'limit' 		=> 	0 
											) 
										);

		foreach( $sites as $site)
		{
			switch_to_blog( $site['blog_id'] );

			$page 			= 	get_page_by_path( 'page-templates' );
			$page_id 			= 	is_object( $page ) ? $page->ID : NULL;

			if( $page_id != NULL )
			{
				$args = array(
					'sort_order' 	=> 	'ASC',
					'sort_column' 	=> 	'post_title',
					'parent' 		=> 	$page_id,
					'post_type' 	=> 	'page',
					'post_status' 	=> 	array( 'publish', 'pending', 'draft', 'private' )
				); 
				
				$pages 		= 	get_pages( $args );

				// If there are some pages
				if( count( $pages ) > 0 )
				{
					$have_pages = TRUE;
					
					?>
					<p><strong>Pages in '<?php echo wp_title(); ?>'</strong><br/><br/></p>
					<?php

					?>
					<table class="pages">
					<tr>
						<th width="80%" class="left">Page</th>
						<th width="10%">Add to menu</th>
						<th width="10%">Menu order</th>
					</tr>
					<?php
					foreach( $pages as $page )
					{
						$checked 			= 	isset( $post_list[ $site['blog_id'] ] ) && array_key_exists( $page->ID, $post_list[ $site['blog_id'] ] ) ? ' checked' : '';
						$menu 				=	'';
						$menu_order 		= 	0;
						
						if( $checked != '' ) {
							$menu 			= 	isset( $post_list[ $site['blog_id'] ][ $page->ID ][ 'in_menu' ] ) ? ' checked' : '';

							if( $menu != '' ) {
								$menu_order =	isset( $post_list[ $site['blog_id'] ][ $page->ID ][ 'menu_order' ] ) ? $post_list[ $site['blog_id'] ][ $page->ID ][ 'menu_order' ] : 0;
							}
						}

						?>
						<tr>
							<td>

								<input 
									type="checkbox" 
									class="check" 
									name="cpd_default_posts[<?php echo $site['blog_id'];?>][<?php echo $page->ID;?>][post_id]"
									value="<?php echo $page->ID;?>"
									<?php echo $checked;?> 
									id="blog_<?php echo $site['blog_id']; ?>-post_<?php echo $page->ID; ?>"
								/>
								<label for="blog_<?php echo $site['blog_id']; ?>-post_<?php echo $page->ID; ?>">
									<?php echo $page->post_title; ?>
								</label>
							</td>
							<td>
								<input 
									type="checkbox" 
									class="menu disabled" 
									disabled
									name="cpd_default_posts[<?php echo $site['blog_id'];?>][<?php echo $page->ID;?>][in_menu]"
									value="true"
									<?php echo $menu;?> 
									id="menu_blog_<?php echo $site['blog_id']; ?>-post_<?php echo $page->ID; ?>" 
								/>
								<label for="menu_blog_<?php echo $site['blog_id']; ?>-post_<?php echo $page->ID; ?>" class="screen-reader-text">
									Add '<?php echo $page->post_title; ?>' to menu
								</label>
							</td>
							<td>
								<input 
									type="text" 
									class="menu_order disabled"
									disabled
									name="cpd_default_posts[<?php echo $site['blog_id'];?>][<?php echo $page->ID;?>][menu_order]"
									value="<?php echo $menu_order;?>"
									id="menu_order_blog_<?php echo $site['blog_id']; ?>-post_<?php echo $page->ID; ?>" 
								/>
								<label for="menu_order_blog_<?php echo $site['blog_id']; ?>-post_<?php echo $page->ID; ?>" class="screen-reader-text">
									Menu order for '<?php echo $page->post_title; ?>'
								</label>
							</td>
						</tr>
						<?php
					}
					?>
					</table>
					<?php
				}
			}
			
			restore_current_blog();
		}
		if( !$have_pages )
		{
			?>
				<p><strong>No page templates have been created as yet.</strong></p>
				<p>To create an page template, follow these steps:</p>
				<ol>
					<li>In one of the journals, or the master website, create a <strong>page</strong> titled 'Page Templates' (make sure it has the slug <em>'page-templates'</em>).</li>
					<li>Create your pages (using the pages menu option) and ensure that their parent page is set as the 'Page Templates' page</li>
					<li>You do not have to publish these pages, this system will find them and list them here</li> 
				</ol>
			<?php
		}
	}

	public function cpd_section_posts_default_posts_callback() {

		global $wpdb;

		$sites 						= 	array();
		$have_posts 				= 	FALSE;
		$post_list 					= 	get_option( 'cpd_default_posts' );
		
		if( !is_array( $post_list ) ) {
			$post_list 				= 	array();
		}

		// Get all of the journals
		$sites 						= 	wp_get_sites( 
											array( 
												'network_id' 	=> 	$wpdb->siteid, 
												'limit' 		=> 	0 
											) 
										);

		foreach( $sites as $site)
		{
			switch_to_blog( $site['blog_id'] );

			$post_template 			= 	get_page_by_path( 'post-templates' );
			$post_template_id 		= 	is_object( $post_template ) ? $post_template->ID : NULL;

			if( $post_template_id != NULL )
			{
				$args = array(
					'sort_order' 	=> 	'ASC',
					'sort_column' 	=> 	'post_title',
					'parent' 		=> 	$post_template_id,
					'post_type' 	=> 	'page',
					'post_status' 	=> 	array( 'publish', 'pending', 'draft', 'private' )
				); 
				
				$posts 		= 	get_pages( $args );

				// If there are some posts
				if( count( $posts ) > 0 )
				{
					$have_posts = TRUE;
					
					?>
					<p><strong>Pages in '<?php echo wp_title(); ?>'</strong><br/><br/></p>
					<?php

					?>
					<table class="posts">
					<tr>
						<th width="80%" class="left">Page</th>
						<th width="10%">Add to menu</th>
						<th width="10%">Menu order</th>
					</tr>
					<?php
					foreach( $posts as $post )
					{
						$checked 			= 	isset( $post_list[ $site['blog_id'] ] ) && array_key_exists( $post->ID, $post_list[ $site['blog_id'] ] ) ? ' checked' : '';
						$menu 				=	'';
						$menu_order 		= 	0;
						
						if( $checked != '' ) {
							$menu 			= 	isset( $post_list[ $site['blog_id'] ][ $post->ID ][ 'in_menu' ] ) ? ' checked' : '';

							if( $menu != '' ) {
								$menu_order =	isset( $post_list[ $site['blog_id'] ][ $post->ID ][ 'menu_order' ] ) ? $post_list[ $site['blog_id'] ][ $post->ID ][ 'menu_order' ] : 0;
							}
						}

						?>
						<tr>
							<td>

								<input 
									type="checkbox" 
									class="check" 
									name="cpd_default_posts[<?php echo $site['blog_id'];?>][<?php echo $post->ID;?>][post_id]"
									value="<?php echo $post->ID;?>"
									<?php echo $checked;?> 
									id="blog_<?php echo $site['blog_id']; ?>-post_<?php echo $post->ID; ?>"
								/>
								<label for="blog_<?php echo $site['blog_id']; ?>-post_<?php echo $post->ID; ?>">
									<?php echo $post->post_title; ?>
								</label>
							</td>
							<td>
								<input 
									type="checkbox" 
									class="menu disabled" 
									disabled
									name="cpd_default_posts[<?php echo $site['blog_id'];?>][<?php echo $post->ID;?>][in_menu]"
									value="true"
									<?php echo $menu;?> 
									id="menu_blog_<?php echo $site['blog_id']; ?>-post_<?php echo $post->ID; ?>" 
								/>
								<label for="menu_blog_<?php echo $site['blog_id']; ?>-post_<?php echo $post->ID; ?>" class="screen-reader-text">
									Add '<?php echo $post->post_title; ?>' to menu
								</label>
							</td>
							<td>
								<input 
									type="text" 
									class="menu_order disabled"
									disabled
									name="cpd_default_posts[<?php echo $site['blog_id'];?>][<?php echo $post->ID;?>][menu_order]"
									value="<?php echo $menu_order;?>"
									id="menu_order_blog_<?php echo $site['blog_id']; ?>-post_<?php echo $post->ID; ?>" 
								/>
								<label for="menu_order_blog_<?php echo $site['blog_id']; ?>-post_<?php echo $post->ID; ?>" class="screen-reader-text">
									Menu order for '<?php echo $post->post_title; ?>'
								</label>
							</td>
						</tr>
						<?php
					}
					?>
					</table>
					<?php
				}
			}
			
			restore_current_blog();
		}
		if( !$have_posts )
		{
			?>
				<p><strong>No post templates have been created as yet.</strong></p>
				<p>To create an post template, follow these steps:</p>
				<ol>
					<li>In one of the journals, or the master website, create a <strong>post</strong> titled 'Post Templates' (make sure it has the slug <em>'post-templates'</em>).</li>
					<li>Create your posts (using the posts menu option) and ensure that their parent post is set as the 'Post Templates' post</li>
					<li>You do not have to publish these pages, this system will find them and list them here</li> 
				</ol>
			<?php
		}
	}

	public function cpd_section_manual_defaults_key_values_callback() {
		$cpd_settings 	= 	get_option( 'cpd_new_blog_options' );
		?>
		<textarea name="cpd_new_blog_options" id="cpd_new_blog_options" style="width:100%;" rows="8"><?php echo $cpd_settings; ?></textarea>
		<?php
	}

	/**
	 * Add the options page
	 *
	 * @since    2.0.0
	 */
	public function add_options_page() {
		add_submenu_page( 'settings.php', 'CPD Journal defaults', 'CPD Journal defaults', 'manage_network_options', 'cpd_settings', array( $this, 'render_options_page' ) );
	}

	/**
	 * Render the options page
	 *
	 * @since    2.0.0
	 */
	public function render_options_page(){ 
		?>
		<div class="wrap cpd-settings">  
			<h2>CPD Journal Defaults</h2> 
			<form action="/wp-admin/options.php" method="POST">
	            <?php settings_fields( 'cpd_settings_group' ); ?>
	            <?php do_settings_sections( 'cpd_settings' ); ?>
	            <?php submit_button(); ?>
	        </form>
		</div> 
	<?php
	}

	/**
	 * Update the options page settings
	 *
	 * @since    2.0.0
	 */
	function update_options_page(){

		check_admin_referer( 'update_cpd_settings' );

		if( !current_user_can('manage_network_options' )) 
		{
			wp_die( 'You do not have permission to do this' );
		}

		update_option( 'cpd_new_blog_options', $_POST['cpd_new_blog_options'] );

		$cpd_settings 	= 	get_option( 'cpd_new_blog_options' );
		$cpd_settings 	= 	preg_replace( '/[\n\r]+/', '&', $cpd_settings );
		$cpd_settings 	= 	preg_replace( '/[\s\:]+/', '=', $cpd_settings );
		parse_str( $cpd_settings, $update_options );

		wp_redirect( add_query_arg( array( 'page' => 'cpd_settings', 'updated' => 'true' ), network_admin_url( 'index.php' ) ) );
		exit;  
	}
}
}