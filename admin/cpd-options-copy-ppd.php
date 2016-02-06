<?php

// Exit if accessed directly
defined( 'ABSPATH' ) || exit;

if( !class_exists( 'CPD_Options_Copy_PPD' ) ) {

/**
 * Copy Pages
 *
 * Functionality to copy pages
 *
 * @package    CPD
 * @subpackage CPD/admin
 * @author     Make Do <hello@makedo.in>
 */
class CPD_Options_Copy_PPD {


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

	/**
	 * Add the options page
	 */
	public function add_options_page() {
		add_submenu_page( 'cpd_settings_templates', 'Copy Activites', 'Copy Activites', 'supervise_users', 'cpd_settings_copy_ppds', array( $this, 'render_options_page' ) );
	}

	/**
	 * Render the options page
	 */
	public function render_options_page(){
		global $wpdb;

		$pages 				    = array();
		$copy_list				= array();
		$error_message			= null;
		$have_pages 			= false;
		$have_pages_message 	= false;
		$journals 				= array();
		$post_valid				= false;
		$sites 					= array();
		$success_message		= '<p><strong>Success: </strong> Activities have been copied:</p>';
		$current_user 			= wp_get_current_user();
		$roles 					= $current_user->roles;
		$is_elevated_user 		= get_user_meta( $current_user->ID, 'elevated_user', TRUE ) == '1';

		// Get all of the sites and journals
		$sites = wp_get_sites(
			array(
				'network_id' => $wpdb->siteid,
				'limit' => 0
			)
		);

		// Hangle post data
		if( isset( $_POST['submit'] ) )
		{
			// Find the journals we are adding pages to
			foreach( $_POST as $key => $value )
			{
				if( strpos( $key, 'journal_' ) === 0 )
				{
					$post_valid = true;
					array_push( $journals, str_replace( 'journal_', '', $key ) );
				}
			}
			// We didnt find any journals, thats an error
			if( !$post_valid )
			{
				$error_message = '<p><strong>Error:</strong> No journals were selected.</p>';
			}
			else
			{
				$post_valid = false;

				// Find all the pages that we are going to copy
				foreach( $_POST as $key => $value )
				{
					if( strpos( $key, 'blog_' ) === 0 )
					{
						$post_valid = true;

						$key_split = explode( '-', $key );

						$copy_list[] = array(
							'blog_id' => str_replace( 'blog_', '', $key_split[0] ),
							'post_id' => str_replace( 'post_', '', $key_split[1] ),
						);
					}
				}
				// We didnt find any pages, thats an error
				if( !$post_valid )
				{
					$error_message = '<p><strong>Error:</strong> No pages were selected.</p>';
				}
				else
				{
					$post_valid = false;
					$success_message .= '<ul>';
					foreach( $journals as $journal)
					{
						foreach( $copy_list as $copy_item )
						{
							$success_message .= '<li>';
							// Switch to blog we are copying from
							switch_to_blog( $copy_item['blog_id'] );

							// Get the post
							$post_to_copy 	= get_post( $copy_item['post_id'] );
							$taxonomies 	= get_object_taxonomies( $post_to_copy->post_type );
							$post_meta 		= $wpdb->get_results( "SELECT meta_key, meta_value FROM $wpdb->postmeta WHERE post_id=$post_to_copy->ID" );

							// Switch back to current blog
							restore_current_blog();

							// Switch to blog we are copying to (journal)
							switch_to_blog( $journal );

							// Get the id of the participant that owns the journal
							$participant_id 	= 0;
							$user_args 			= array(
													'blog_id'		=> $journal,
													'meta_key' 		=> 'cpd_role',
													'meta_value' 	=> 'participant',
													'meta_compare' 	=> '='

							);
							$users 				= get_users( $user_args );

							if( !empty($users) )
							{
								$participant_id = $users[0]->ID;
							}

							// Check that a post with that name dosnt already exist
							$existing_post = get_page_by_title( $post_to_copy->post_title, OBJECT, 'ppd' );

							if( empty( $existing_post ) )
							{
								// Insert the post and copy all the data
								$post_args = array(
									'post_title'		=> $post_to_copy->post_title,
									'post_type'			=> $post_to_copy->post_type,
									'post_status'		=> $post_to_copy->post_status,
									'comment_status'	=> $post_to_copy->comment_status,
									'ping_status'		=> $post_to_copy->ping_status,
									'post_content'		=> $post_to_copy->post_content,
									'post_excerpt'		=> $post_to_copy->post_excerpt,
									'post_name'			=> $post_to_copy->post_name,
									'post_password'		=> $post_to_copy->post_password,
									'to_ping'			=> $post_to_copy->to_ping,
									'post_author' 		=> $participant_id
								);

								// Insert the post
								$insert_id = wp_insert_post( $post_args );

								// If the post was successfully inserted
								if( $insert_id != 0)
								{
									// Copy all the taxonomy data accross
									foreach ($taxonomies as $taxonomy) {
										$post_terms = wp_get_object_terms( $post_to_copy->ID, $taxonomy, array( 'fields' => 'slugs' ) );
										wp_set_object_terms( $insert_id, $post_terms, $taxonomy, false );
									}

									// Copy all the post meta accross
									if (count( $post_meta )!=0)
									{
										$sql_query = "INSERT INTO $wpdb->postmeta ( post_id, meta_key, meta_value ) ";
										foreach ( $post_meta as $meta )
										{
											$meta_key = $meta->meta_key;
											$meta_value = addslashes( $meta->meta_value );
											$sql_query_sel[] = "SELECT $insert_id, '$meta_key', '$meta_value'";
										}
										$sql_query.= implode( " UNION ALL ", $sql_query_sel );
										$wpdb->query( $sql_query );
									}

									// Update status list message
									$success_message .= 'Successfully copied \'<strong>' . $post_to_copy->post_title .'</strong>\' into journal \'<strong>'. wp_title( '', false ) .'</strong>\'.' ;
									$post_valid = true;
								}
								else
								{
									// Update status list message with reason for non copy
									$success_message .= 'Could <strong>not</strong> copy \'<strong>' . $post_to_copy->post_title .'</strong>\' into journal \'<strong>'. wp_title( '', false ) .'</strong>\'.' ;
								}
							}
							else
							{
								// Update status list message with reason for non copy
								$success_message .= 'Did <strong>not</strong> copy \'<strong>' . $post_to_copy->post_title .'</strong>\' into journal \'<strong>'. wp_title( '', false ) .'</strong>\' as there is already a page with that name in the journal.' ;
							}

							// Swith back to current blog
							restore_current_blog();

							$success_message .= '<li>';
						}
					}
					$success_message .= '</ul>';
				}
			}

			if( !$post_valid )
			{

				$error_message = '<p><strong>Error:</strong> All chosen Activities already exist in all chosen Journals and/or Templates.</p>';

				?>
				<div class="error"><?php echo $error_message; ?></div>
				<?php
			}
			else
			{
				?>
				<div class="updated"><?php echo $success_message; ?></div>
				<?php
			}
		}

		// Render the form to copy the pages
		?>
		<div class="wrap cpd_options">
			<h2>Copy Activities</h2>
			<form method="post">
				<?php
					settings_fields( 'cpd_group' );
					do_settings_sections( 'cpd_group' );

					?>
					<table class="form-table">
					<?php

					// Find all unpublished pages (by looking in each journal for a page with the slug 'pages' and finding all the unpublished ones)
					foreach( $sites as $site)
					{
						if( strrpos( $site['path'], '/template-' ) === 0 ) {

							switch_to_blog( $site['blog_id'] );
							$current_blog_details = get_blog_details( array( 'blog_id' => $site['blog_id'] ) );
							// $page = get_page_by_path( 'page-templates' );
							// $page_id = is_object( $page ) ? $page->ID : NULL;

							// If there is a page titled 'Pages' in this blog
							// if( $page_id != null )
							// {
							$args = array(
								'sort_order' => 'ASC',
								'sort_column' => 'post_title',
								// 'parent' => $page_id,
								'post_type' => 'ppd',
								'post_status' => array( 'publish', 'pending', 'draft', 'private' )
							);
							$pages = get_posts( $args );

							// If there are some pages
							if( count( $pages ) > 0 )
							{
								$have_pages = true;

								if( !$have_pages_message )
								{
									?>
									<p>Below is a list of Template Activities. Check all of the Activities that you wish to copy into participant Journals.</p>
									<?php

									$have_pages_message = true;
								}

								?>
								<tr>
								<th>Activities in '<?php echo $current_blog_details->blogname; ?>'</th>

								<?php

								?>

								<td>
								<ul class="pages" >
								<?php
								foreach( $pages as $page )
								{
									?>
									<li>
										<input type="checkbox" id="blog_<?php echo $site['blog_id']; ?>-post_<?php echo $page->ID; ?>" name="blog_<?php echo $site['blog_id']; ?>-post_<?php echo $page->ID; ?>"/>
										<label for="blog_<?php echo $site['blog_id']; ?>-post_<?php echo $page->ID; ?>">
											<?php echo $page->post_title; ?>
										</label>
									</li>
									<?php
								}
								?>
								</ul>
								</td>
								</tr>
								<?php

							}

							restore_current_blog();
						}
					}

					?>
					</table>
					<?php

					if( $have_pages )
					{
						?>
						<div class="journal-wrapper">
							<h2>Select Journals and Templates</h2>
							<p>Check the Journals and Templates you wish to copy the selected Activities into.</p>
							<p><strong>Please note:</strong> If Activities with the same name already exists in a Journal or Template the page will not be copied into that journal.</p>
							<?php

							?>
							<table class="form-table">
							<tr>
							<th>Your Journals</th>
							<td>
							<ul class="journals">
							<?php
							$count = 0;
							foreach( $sites as $site )
							{
								if( strrpos( $site['path'], '/template-' ) !== 0 ) {

								switch_to_blog( $site['blog_id'] );
								$current_user = wp_get_current_user();
								$roles = $current_user->roles;

								if( is_super_admin() || $is_elevated_user || in_array( 'supervisor', $roles ) || in_array( 'administrator', $roles ) ) {
								$journal = get_blog_details( $site['blog_id'] );
								$count++;
								?>
									<li>
										<input type="checkbox" id="journal_<?php echo $site['blog_id']; ?>" name="journal_<?php echo $site['blog_id']; ?>"/>
										<label for="journal_<?php echo $site['blog_id']; ?>">
											<?php echo $journal->blogname; ?>
										</label>
									</li>
								<?php
								}
								restore_current_blog();
								}
							}
							if( $count == 0 ) {
								?>
									<li>You have no Journals</li>
								<?php
							}
							?>

							</ul>
							</td>
							</tr>
							<tr>
							<th>Your Templates</th>
							<td>
							<ul class="journals">
							<?php
							$count = 0;
							foreach( $sites as $site )
							{
								if( strrpos( $site['path'], '/template-' ) === 0 ) {

								switch_to_blog( $site['blog_id'] );
								$current_user = wp_get_current_user();
								$roles = $current_user->roles;

								if( is_super_admin() || $is_elevated_user || in_array( 'supervisor', $roles ) || in_array( 'administrator', $roles ) ) {
								$journal = get_blog_details( $site['blog_id'] );
								$count++;
								?>
									<li>
										<input type="checkbox" id="journal_<?php echo $site['blog_id']; ?>" name="journal_<?php echo $site['blog_id']; ?>"/>
										<label for="journal_<?php echo $site['blog_id']; ?>">
											<?php echo $journal->blogname; ?>
										</label>
									</li>
								<?php
								}
								restore_current_blog();
								}
							}
							if( $count == 0 ) {
								?>
									<li>You have no Templates</li>
								<?php
							}
							?>
							</ul>
							</td>
							</tr>
							</table>
						</div>
						<?php
					}

					if(!$have_pages)
					{
						?>
							<p><strong>No activities have been created as yet.</strong></p>
							<p>Please create an activities to continue.</p>
						<?php
					}
				?>

				<?php
				if( $have_pages )
				{
					submit_button('Copy Activities');
				}
				?>
			</form>
		</div>
		<?php
	}
}
}
