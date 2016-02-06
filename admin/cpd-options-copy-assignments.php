<?php

// Exit if accessed directly
defined( 'ABSPATH' ) || exit;

if( !class_exists( 'CPD_Options_Copy_Assignments' ) ) {

/**
 * Copy Assignments
 *
 * Functionality to copy assignments
 *
 * @package    CPD
 * @subpackage CPD/admin
 * @author     Make Do <hello@makedo.in>
 */
class CPD_Options_Copy_Assignments {


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
		add_submenu_page( 'settings.php', 'CPD Copy Assignments', 'CPD Copy Assignments', 'supervise_users', 'cpd_settings_copy_assignments', array( $this, 'render_options_page' ) );
	}

	/**
	 * Render the options page
	 */
	public function render_options_page(){
		global $wpdb;

		$assignments 				= array();
		$copy_list					= array();
		$error_message				= null;
		$have_assigments 			= false;
		$have_assigments_message 	= false;
		$journals 					= array();
		$post_valid					= false;
		$sites 						= array();
		$success_message			= '<p><strong>Success: </strong> Assignments have been copied:</p>';

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
			// Find the journals we are adding assignments to
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

				// Find all the assignments that we are going to copy
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
				// We didnt find any assignments, thats an error
				if( !$post_valid )
				{
					$error_message = '<p><strong>Error:</strong> No assignments were selected.</p>';
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
							$existing_post = get_page_by_title( $post_to_copy->post_title );

							if( empty( $existing_post ) )
							{
								// Insert the post and copy all the data
								$post_args = array(
									'post_title'		=> $post_to_copy->post_title,
									'post_type'			=> $post_to_copy->post_type,
									'post_status'		=> 'draft',
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
								$success_message .= 'Did <strong>not</strong> copy \'<strong>' . $post_to_copy->post_title .'</strong>\' into journal \'<strong>'. wp_title( '', false ) .'</strong>\' as there is already an assignment with that name in the journal.' ;
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

				$error_message = '<p><strong>Error:</strong> All chosen assignments already exist in all chosen journals.</p>';

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

		// Render the form to copy the assignments
		?>
		<div class="wrap cpd_options">
			<h2>CPD Copy Assignments</h2>
			<form method="post">
				<?php
					settings_fields( 'cpd_group' );
					do_settings_sections( 'cpd_group' );

					// Find all unpublished assignments (by looking in each journal for a page with the slug 'assigments' and finding all the unpublished ones)
					foreach( $sites as $site)
					{
						switch_to_blog( $site['blog_id'] );
						$current_blog_details = get_blog_details( array( 'blog_id' => $site['blog_id'] ) );
						$assignment = get_page_by_path( 'assignment-templates' );
						$assignment_id = is_object( $assignment ) ? $assignment->ID : NULL;

						// If there is a page titled 'Assignments' in this blog
						if( $assignment_id != null )
						{
							$args = array(
								'sort_order' => 'ASC',
								'sort_column' => 'post_title',
								'parent' => $assignment_id,
								'post_type' => 'page',
								'post_status' => array( 'publish', 'pending', 'draft', 'private' )
							);
							$assignments = get_pages( $args );

							// If there are some assignments
							if( count( $assignments ) > 0 )
							{
								$have_assigments = true;

								if( !$have_assigments_message )
								{
									?>
									<p>Below is a list of assignments. Check all of the assignments that you wish to copy into participant journals.</p>
									<?php

									$have_assigments_message = true;
								}

								?>
								<h3>Assignments in '<?php echo $current_blog_details->blogname; ?>'</h3>
								<?php

								?>
								<ul class="assignments">
								<?php
								foreach( $assignments as $assignment )
								{
									?>
									<li>
										<input type="checkbox" id="blog_<?php echo $site['blog_id']; ?>-post_<?php echo $assignment->ID; ?>" name="blog_<?php echo $site['blog_id']; ?>-post_<?php echo $assignment->ID; ?>"/>
										<label for="blog_<?php echo $site['blog_id']; ?>-post_<?php echo $assignment->ID; ?>">
											<?php echo $assignment->post_title; ?>
										</label>
									</li>
									<?php
								}
								?>
								</ul>
								<?php

							}
						}

						restore_current_blog();
					}

					if( $have_assigments )
					{
						?>
						<div class="journal-wrapper">
							<h2>Select Journals</h2>
							<p>Below is a list of journals. Check the participant journals you wish to copy the selected assignments into.</p>
							<p><strong>Please note:</strong></p>
							<p>If an assignment with the same name already exists in a journal the assignment will not be copied into that journal.</p>
							<p>Assignments will not be published automatically.</p>
							<?php

							?>
							<ul class="journals">
							<?php
							foreach( $sites as $site)
							{
								$journal = get_blog_details( $site['blog_id'] );
								?>
									<li>
										<input type="checkbox" id="journal_<?php echo $site['blog_id']; ?>" name="journal_<?php echo $site['blog_id']; ?>"/>
										<label for="journal_<?php echo $site['blog_id']; ?>">
											<?php echo $journal->blogname; ?>
										</label>
									</li>
								<?php
							}
							?>
							</ul>
						</div>
						<?php
					}

					if(!$have_assigments)
					{
						?>
							<p><strong>No assignment templates have been created as yet.</strong></p>
							<p>To create an assignment template, follow these steps:</p>
							<ol>
								<li>In one of the journals, or the master website, create a <strong>page</strong> titled 'Assigment Templates' (make sure it has the slug <em>'assignment-templates'</em>).</li>
								<li>Create your assignments (using the pages menu option) and ensure that their parent page is set as the 'Assignment Templates' page</li>
								<li>You do not have to publish these pages, this system will find them and list them here</li>
							</ol>
						<?php
					}
				?>

				<?php
				if( $have_assigments )
				{
					submit_button('Copy Assignments');
				}
				?>
			</form>
		</div>
		<?php
	}
}
}
