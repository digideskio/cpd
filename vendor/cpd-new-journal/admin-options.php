<?php
/**
 * @package CPD New Journal
 */

/**
 * 
 * @since  		1.0.0
 * 
 * Register the options page
 * 
 */
function cpdnj_add_options_page() {

	add_submenu_page( 'settings.php', 'CPD journal defaults', 'CPD journal defaults', 'manage_options', 'cpdnj-settings', 'cpdnj_render_options_page' );

	add_action( 'admin_init', 'cpdnj_register_settings' );
}
add_action( 'network_admin_menu', 'cpdnj_add_options_page' );




/**
 * 
 * @since  		1.0.0
 * 
 * Register the options settings
 * 
 */
function cpdnj_register_settings() 
{
	register_setting( 'cpdnj_group', '_cpdnj_journal_defaults' );
}


/**
 * 
 * @since  		1.0.0
 * 
 * Render the options page
 * 
 */
function cpdnj_render_options_page()
{	
	global $wpdb;

	$sites 						= array();
	$have_assigments 			= false;
	$have_assigments_message 	= false;
	$have_templates 			= false;
	$have_templates_message 	= false;
	$have_posts 				= false;
	$have_posts_message 		= false;
	$page_list 					= array();

	// Get all of the sites and journals
	$sites = wp_get_sites( 
		array( 
			'network_id' => $wpdb->siteid, 
			'limit' => 0 
		) 
	);

	// Handle post data
	// Find all the assignments and templates that we are going to copy
	
	if( isset( $_POST['submit'] ) )
	{ 
		foreach( $_POST as $key => $value ) 
		{
			if( strpos( $key, 'blog_' ) === 0 ) 
			{
				$key_split = split( '-', $key );

				$page_list[$key] = array(
					'blog_id' 		=> str_replace( 'blog_', '', $key_split[0] ),
					'post_id' 		=> str_replace( 'post_', '', $key_split[1] ),
					'menu'			=> isset( $_POST['menu_' . $key] ) ? 1 : 0,
					'menu_order'	=> isset( $_POST['menu_order_' . $key] ) ? $_POST['menu_order_' . $key] : 0,
					'type'			=> 'page'
				);
			}

			if( strpos( $key, 'post_blog_' ) === 0 )
			{
				$key_split = split( '-', $key );

				$page_list[$key] = array(
					'blog_id' 		=> str_replace( 'blog_', '', $key_split[0] ),
					'post_id' 		=> str_replace( 'post_', '', $key_split[1] ),
					'menu'			=> 0,
					'menu_order'	=> 0,
					'type'			=> 'post'
				);
			}
		}

		update_option( '_cpdnj_journal_defaults', $page_list );
	}

	$page_list = get_option('_cpdnj_journal_defaults');

	?>
		<div class="wrap cpdnj_options">
			<h2>CPD journal defaults</h2>
			<p>Please choose the assignments, pages and blog posts that you wish to be created within every new journal.</p>
			<p>If you check the 'Add to menu' box next to a page or assignment, that item will be added to the top level menu on the participants journal.</p>
			<p>To alter the order of items in the menu, you can assign a number next to an item. The lower the number, the earlier in the menu it will appear.</p>
			<p><strong>Please note:</strong> items will not be published in the new journal automatically.</p>

			<form method="post">
			<br/>
			<hr/>
			<br/>
			<h3>Assignment templates</h3>
			<?php 
				settings_fields( 'cpdnj_group' );
				do_settings_sections( 'cpdnj_group' );

				// Find all unpublished assignments (by looking in each journal for a page with the slug 'assigments' and finding all the unpublished ones)
				foreach( $sites as $site)
				{
					switch_to_blog( $site['blog_id'] );

					$assignment = get_page_by_path( 'assignments' );
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
							<h4>Assignments in '<?php echo wp_title(); ?>'</h4>
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
								$checked = '';
								$menu = '';
								$menu_order = 0;

								if ( isset( $page_list['blog_' . $site['blog_id'] . '-post_' . $assignment->ID] ) )
								{
									$checked = 'checked';
									if( $page_list['blog_' . $site['blog_id'] . '-post_' . $assignment->ID]['menu'] == 1 )
									{
										$menu = 'checked';
									}
									$menu_order = $page_list['blog_' . $site['blog_id'] . '-post_' . $assignment->ID]['menu_order'];
								}

								?>
								<tr>
									<td>
										<input type="checkbox" class="check" id="blog_<?php echo $site['blog_id']; ?>-post_<?php echo $assignment->ID; ?>" name="blog_<?php echo $site['blog_id']; ?>-post_<?php echo $assignment->ID; ?>" <?php echo $checked; ?> />
										<label for="blog_<?php echo $site['blog_id']; ?>-post_<?php echo $assignment->ID; ?>">
											<?php echo $assignment->post_title; ?>
										</label>
									</td>
									<td class="center">
										<input type="checkbox" class="menu disabled" disabled id="menu_blog_<?php echo $site['blog_id']; ?>-post_<?php echo $assignment->ID; ?>" name="menu_blog_<?php echo $site['blog_id']; ?>-post_<?php echo $assignment->ID; ?>" <?php echo $menu;?>/>
										<label for="menu_blog_<?php echo $site['blog_id']; ?>-post_<?php echo $assignment->ID; ?>" class="visuallyhidden">
											Add '<?php echo $assignment->post_title; ?>' to menu
										</label>
									</td>
									<td class="center">
										<input type="text" class="menu_order disabled" disabled value="<?php echo $menu_order;?>" id="menu_order_blog_<?php echo $site['blog_id']; ?>-post_<?php echo $assignment->ID; ?>" name="menu_order_blog_<?php echo $site['blog_id']; ?>-post_<?php echo $assignment->ID; ?>"/>
										<label for="menu_order_blog_<?php echo $site['blog_id']; ?>-post_<?php echo $assignment->ID; ?>" class="visuallyhidden">
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
				if( !$have_assigments )
				{
					?>
						<p><strong>No assignment templates have been created as yet.</strong></p>
						<p>To create an assignment template, follow these steps:</p>
						<ol>
							<li>In one of the journals, or the master website, create a <strong>page</strong> titled 'Assigments'</li>
							<li>Create your assignments (using the pages menu option) and ensure that their parent page is set as the 'Assignments' page</li>
							<li>You do not have to publish these pages, this system will find them and list them here</li> 
						</ol>
					<?php
				}
			?>
			<br/><br/>
			<hr/>
			<br/>
			<h3>Page templates</h3>
			<?php 

				// Find all  pages (by looking in each journal for a page with the slug 'page-templates' )
				foreach( $sites as $site)
				{
					switch_to_blog( $site['blog_id'] );

					$template = get_page_by_path( 'page-templates' );
					$template_id = is_object( $template ) ? $template->ID : NULL;

					// If there is a page titled 'Assignments' in this blog
					if( $template_id != null )
					{
						$args = array(
							'sort_order' => 'ASC',
							'sort_column' => 'post_title',
							'parent' => $template_id,
							'post_type' => 'page',
							'post_status' => array( 'publish', 'pending', 'draft', 'private' )
						); 
						$templates = get_pages( $args );

						// If there are some assignments
						if( count( $templates ) > 0 )
						{
							$have_templates = true;

							if( !$have_templates_message )
							{
								?>
								
								<p>Below is a list of page templates. Check all of the templates that you wish to copy into participant journals.</p>
								<?php

								$have_templates_message = true;
							}


							?>
							<h4>Templates in '<?php echo wp_title(); ?>'</h4>
							<?php

							?>
							<table class="templates">
							<tr>
								<th width="80%" class="left">Page Template</th>
								<th width="10%">Add to menu</th>
								<th width="10%">Menu order</th>
							</tr>
							<?php
							foreach( $templates as $template )
							{
								$checked = '';
								$menu = '';
								$menu_order = 0;

								if ( isset( $page_list['blog_' . $site['blog_id'] . '-post_' . $template->ID] ) )
								{
									$checked = 'checked';
									if( $page_list['blog_' . $site['blog_id'] . '-post_' . $template->ID]['menu'] == 1 )
									{
										$menu = 'checked';
									}
									$menu_order = $page_list['blog_' . $site['blog_id'] . '-post_' . $template->ID]['menu_order'];
								}
								?>
								<tr>
									<td>
										<input type="checkbox" class="check" id="blog_<?php echo $site['blog_id']; ?>-post_<?php echo $template->ID; ?>" name="blog_<?php echo $site['blog_id']; ?>-post_<?php echo $template->ID; ?>" <?php echo $checked; ?>/>
										<label for="blog_<?php echo $site['blog_id']; ?>-post_<?php echo $template->ID; ?>">
											<?php echo $template->post_title; ?>
										</label>
									</td>
									<td class="center">
										<input type="checkbox" class="menu disabled" disabled id="menu_blog_<?php echo $site['blog_id']; ?>-post_<?php echo $template->ID; ?>" name="menu_blog_<?php echo $site['blog_id']; ?>-post_<?php echo $template->ID; ?>" <?php echo $menu; ?>/>
										<label for="menu_blog_<?php echo $site['blog_id']; ?>-post_<?php echo $template->ID; ?>" class="visuallyhidden">
											Add '<?php echo $template->post_title; ?>' to menu
										</label>
									</td>
									<td class="center">
										<input type="text" class="menu_order disabled" disabled value="<?php echo $menu_order; ?>" id="menu_order_blog_<?php echo $site['blog_id']; ?>-post_<?php echo $template->ID; ?>" name="menu_order_blog_<?php echo $site['blog_id']; ?>-post_<?php echo $template->ID; ?>"/>
										<label for="menu_order_blog_<?php echo $site['blog_id']; ?>-post_<?php echo $template->ID; ?>" class="visuallyhidden">
											Menu order for '<?php echo $template->post_title; ?>'
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
				if( !$have_templates )
				{
					?>
						<p><strong>No page templates have been created as yet.</strong></p>
						<p>To create a page template, follow these steps:</p>
						<ol>
							<li>In one of the journals, or the master website, create a <strong>page</strong> titled 'Page Templates'</li>
							<li>Create your page templates (using the pages menu option) and ensure that their parent page is set as the 'Page Templates' page</li>
							<li>You do not have to publish these pages, this system will find them and list them here</li> 
						</ol>
					<?php
				}
			?>
			<br/><br/>
			<hr/>
			<br/>
			<h3>Blog post templates</h3>
			<?php 

				// Find all  pages (by looking in each journal for a page with the slug 'page-templates' )
				foreach( $sites as $site)
				{
					switch_to_blog( $site['blog_id'] );

					$template = get_page_by_path( 'post-templates' );
					$template_id = is_object( $template ) ? $template->ID : NULL;

					// If there is a page titled 'Post Templates' in this blog
					if( $template_id != null )
					{
						$args = array(
							'sort_order' => 'ASC',
							'sort_column' => 'post_title',
							'parent' => $template_id,
							'post_type' => 'page',
							'post_status' => array( 'publish', 'pending', 'draft', 'private' )
						); 
						$templates = get_pages( $args );

						// If there are some assignments
						if( count( $templates ) > 0 )
						{
							$have_posts = true;

							if( !$have_posts_message )
							{
								?>
								
								<p>Below is a list of blog post templates. Check all of the templates that you wish to copy into participant journals.</p>
								<?php

								$have_posts_message = true;
							}


							?>
							<h4>Templates in '<?php echo wp_title(); ?>'</h4>
							<?php

							?>
							<table class="templates">
							<tr>
								<th width="100%" class="left">Post Template</th>
							</tr>
							<?php
							foreach( $templates as $template )
							{
								$checked = '';

								if ( isset( $page_list['post_blog_' . $site['blog_id'] . '-post_' . $template->ID] ) )
								{
									$checked = 'checked';
								}
								?>
								<tr>
									<td>
										<input type="checkbox" id="post_blog_<?php echo $site['blog_id']; ?>-post_<?php echo $template->ID; ?>" name="post_blog_<?php echo $site['blog_id']; ?>-post_<?php echo $template->ID; ?>" <?php echo $checked; ?>/>
										<label for="post_blog_<?php echo $site['blog_id']; ?>-post_<?php echo $template->ID; ?>">
											<?php echo $template->post_title; ?>
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
						<p>To create a post template, follow these steps:</p>
						<ol>
							<li>In one of the journals, or the master website, create a <strong>page</strong> titled 'Post Templates'</li>
							<li>Create your page templates (using the pages menu option) and ensure that their parent page is set as the 'Post Templates' page</li>
							<li>You do not have to publish these pages, this system will find them and list them here</li> 
						</ol>
					<?php
				}
			?>
			<br/><br/>
			<hr/>
			<?php submit_button('Save defaults'); ?>
			</form>
		</div>
	<?php
}