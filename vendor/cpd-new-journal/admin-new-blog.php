<?php
/**
 * @package CPD New Journal
 */

/**
 * 
 * @since  		1.0.0
 * 
 * Run the following actions when the new blog is created
 * 
 */
function cpdnj_new_blog( $blog_id /*, $user_id, $domain, $path, $site_id, $meta*/ ) 
{
	global $wpdb;

	$page_list = get_option('_cpdnj_journal_defaults');

	usort($page_list, "cpdnj_sort_by_menu_order");

	// Switch the newly created blog
	switch_to_blog( $blog_id );

	// Delete all posts to get rid of default blog data
	$posts = get_posts( array( 'post_type' => array( 'post', 'page' ) ) );
	
	foreach( $posts as $post )
	{
		wp_delete_post( $post->ID, true );
	}

	restore_current_blog();

	// Copy pages and posts into journal

	foreach( $page_list as $copy_item )
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
		
		// Switch to blog we are copying to ($blog_id)
		switch_to_blog( $blog_id );

		// Get the id of the participant that owns the journal (we have to do this with the title because the user hasnt been assigned to the journal at this point)
		$participant_id 	= 0;
		$user_name 			= str_replace( 'CPD Journal for ', '', wp_title( '', false ) );
		$user 				= get_userdatabylogin( $user_name );

		$participant_id 	= $user->ID;

		// Check that a post with that name dosnt already exist
		$existing_post = get_page_by_title( $post_to_copy->post_title );

		if( empty( $existing_post ) )
		{
			// Insert the post and copy all the data
			$post_args = array(
				'post_title'		=> $post_to_copy->post_title,
				'post_type'			=> $copy_item['type'],
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

				// Add page to menu
				if( $copy_item['menu'] === 1 )
				{
					// Check to see if a menu exists, if not create a new one and add it to the correct location
					$menu_id = wp_get_nav_menu_object( 'Main Menu' );
					if( !$menu_id )
					{
						$menu_id = wp_create_nav_menu( 'Main Menu' );
						$locations = get_theme_mod('nav_menu_locations');
						$locations['primary'] = $menu_id;
						set_theme_mod( 'nav_menu_locations', $locations );
					}
					else
					{
						$menu_id = $menu_id->term_id;
					}

					$menu_item =  array(
						'menu-item-object-id' 	=> $insert_id,
						'menu-item-parent-id' 	=> 0,
						'menu-item-position'  	=> $copy_item['menu_order'],
						'menu-item-object' 		=> $copy_item['type'],
						'menu-item-type'      	=> 'post_type',
						'menu-item-status'   	=> 'publish'
					);

					wp_update_nav_menu_item( $menu_id, 0, $menu_item );
				}
			}
		}

		// Switch back to current blog
		restore_current_blog();
	}
	 
	// Restore to the current blog
	restore_current_blog();
}
add_action( 'wpmu_new_blog', 'cpdnj_new_blog' );

/**
 * 
 * @since  		1.0.0
 * 
 * Sort the posts by the menu order
 * 
 */
function cpdnj_sort_by_menu_order($a, $b)
{
	return $a["menu_order"] - $b["menu_order"];
}