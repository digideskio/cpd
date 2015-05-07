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

if( !class_exists( 'CPD_Blogs' ) ) {

/**
 * The dashboard-specific functionality of the plugin.
 *
 * Defines the plugin name, version, and enqueue the dashboard-specific stylesheet and JavaScript.
 *
 * @package    CPD
 * @subpackage CPD/admin
 * @author     Make Do <hello@makedo.in>
 */
class CPD_Blogs {

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
	 * @since    2.0.0
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

	function new_blog( $blog_id /*, $user_id, $domain, $path, $site_id, $meta*/ ) 
	{
		global $wpdb;

		$page_list = get_option('cpd_default_posts');

		$menu_order = array();
		foreach ( $page_list as $key => $row )
		{
		    $menu_order[$key] = $row['menu_order'];
		}
		array_multisort($menu_order, SORT_DESC, $page_list);

		wp_die(print_r($page_list));

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

		foreach( $page_list as $blog_id => $copy_item )
		{
			if( !isset( $copy_item['type'] ) ) {
				$copy_item['type'] = 'page';
			}

			$success_message .= '<li>';

			// Switch to blog we are copying from
			switch_to_blog( $blog_id  );
			
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
			$user 				= get_user_by( 'login', $user_name );

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
					if( $copy_item['in_menu'] === 'true' )
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

}
}