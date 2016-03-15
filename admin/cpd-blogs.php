<?php

// Exit if accessed directly
defined( 'ABSPATH' ) || exit;

if ( !class_exists( 'CPD_Blogs' ) ) {

	/**
	 * Blogs
	 *
	 * Methods affecting blogs
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
		 */
		public function __construct() {

		}

		/**
		 * Set the text domain
		 *
		 * @param string  $text_domain The text domain of the plugin.
		 */
		public function set_text_domain( $text_domain ) {
			$this->text_domain = $text_domain;
		}

		/**
		 * On creation of new blog
		 *
		 * @param int     $blog_id The newly created blog id
		 */
		function new_blog( $blog_id /*, $user_id, $domain, $path, $site_id, $meta*/ ) {
			global $wpdb;

			$success_message  = '';
			$post_list    = get_option( 'cpd_default_posts' );
			$new_list     = array();

			if( !is_array( $post_list ) || empty( $post_list ) ) {
				$post_list = array();
			}
			foreach ( $post_list as $key=>&$blog_posts ) {
				foreach ( $blog_posts as &$blog_post ) {
					$blog_post['blog_id'] = $key;
				}
				$new_list = array_merge( $new_list, $blog_posts );
			}
			$post_list = $new_list;

			usort( $post_list, array( $this, 'sort_by_menu_order' ) );

			// Switch the newly created blog
			switch_to_blog( $blog_id );

			// Delete all posts to get rid of default blog data
			$posts = get_posts( array( 'post_type' => array( 'post', 'page' ) ) );

			foreach ( $posts as $post ) {
				wp_delete_post( $post->ID, true );
			}

			restore_current_blog();

			// Copy pages and posts into journal

			foreach ( $post_list as $copy_item ) {

				if ( !isset( $copy_item['type'] ) ) {
					$copy_item['type'] = 'page';
				}

				$success_message .= '<li>';

				// Switch to blog we are copying from
				switch_to_blog( $copy_item['blog_id'] );

				// Get the post
				$post_to_copy  = get_post( $copy_item['post_id'] );

				if ( NULL != $post_to_copy ) {

					$taxonomies  = get_object_taxonomies( $post_to_copy->post_type );
					$post_meta   = $wpdb->get_results( "SELECT meta_key, meta_value FROM $wpdb->postmeta WHERE post_id=$post_to_copy->ID" );

					// Switch back to current blog
					restore_current_blog();

					// Switch to blog we are copying to ($blog_id)
					switch_to_blog( $blog_id );

					// Get the id of the participant that owns the journal (we have to do this with the title because the user hasnt been assigned to the journal at this point)
                    $participant_id = 0;
                    $user_name      = str_replace( 'CPD Journal for ', '', wp_title( '', false ) );
                    $user           = get_user_by( 'login', $user_name );
                    $participant_id = $user->ID;

					// Check that a post with that name dosnt already exist
					$existing_post = get_page_by_title( $post_to_copy->post_title );

					if ( empty( $existing_post ) ) {
						// Insert the post and copy all the data
						$post_args = array(
                            'post_title'     => $post_to_copy->post_title,
                            'post_type'      => $copy_item['type'],
                            'post_status'    => 'draft',
                            'comment_status' => $post_to_copy->comment_status,
                            'ping_status'    => $post_to_copy->ping_status,
                            'post_content'   => $post_to_copy->post_content,
                            'post_excerpt'   => $post_to_copy->post_excerpt,
                            'post_name'      => $post_to_copy->post_name,
                            'post_password'  => $post_to_copy->post_password,
                            'to_ping'        => $post_to_copy->to_ping,
                            'post_author'    => $participant_id
						);

						// Insert the post
						$insert_id = wp_insert_post( $post_args );

						// If the post was successfully inserted
						if ( $insert_id != 0 ) {
							// Copy all the taxonomy data accross
							foreach ( $taxonomies as $taxonomy ) {
								$post_terms = wp_get_object_terms( $post_to_copy->ID, $taxonomy, array( 'fields' => 'slugs' ) );
								wp_set_object_terms( $insert_id, $post_terms, $taxonomy, false );
							}

							// Copy all the post meta accross
							if ( count( $post_meta )!=0 ) {
								$sql_query = "INSERT INTO $wpdb->postmeta ( post_id, meta_key, meta_value ) ";
								foreach ( $post_meta as $meta ) {
									$meta_key = $meta->meta_key;
									$meta_value = addslashes( $meta->meta_value );
									$sql_query_sel[] = "SELECT $insert_id, '$meta_key', '$meta_value'";
								}
								$sql_query.= implode( " UNION ALL ", $sql_query_sel );
								$wpdb->query( $sql_query );
							}

							// Add page to menu
							if ( $copy_item['in_menu'] === 'true' ) {
								// Check to see if a menu exists, if not create a new one and add it to the correct location
								$menu_id = wp_get_nav_menu_object( 'Main Menu' );
								if ( !$menu_id ) {
									$menu_id = wp_create_nav_menu( 'Main Menu' );
									$locations = get_theme_mod( 'nav_menu_locations' );
									$locations['primary'] = $menu_id;
									set_theme_mod( 'nav_menu_locations', $locations );
								}
								else {
									$menu_id = $menu_id->term_id;
								}

								$menu_item =  array(
									'menu-item-object-id'  => $insert_id,
									'menu-item-parent-id'  => 0,
									'menu-item-position'   => $copy_item['menu_order'],
									'menu-item-object'   => $copy_item['type'],
									'menu-item-type'       => 'post_type',
									'menu-item-status'    => 'publish'
								);

								wp_update_nav_menu_item( $menu_id, 0, $menu_item );
							}
						}
					}
				}

				// Switch back to current blog
				restore_current_blog();
			}

			// Restore to the current blog
			restore_current_blog();
		}

		/**
		 * Copy the blog
		 *
		 * @param string  $domain       url of the new blog
		 * @param string  $title        title of the new blog
		 * @param int     $from_blog_id ID of the blog being copied from.
		 * @param bool    $copy_files   true if files should be copied
		 * @return string status message
		 */
		public function copy_blog( $domain, $title, $from_blog_id = 0, $copy_files = true ) {
			global $wpdb, $current_site, $base;

            $email   = get_blog_option( $from_blog_id, 'admin_email' );
            $user_id = email_exists( sanitize_email( $email ) );
			if ( !$user_id ) {
				// Use current user instead
				$user_id = get_current_user_id();
			}
			// The user id of the user that will become the blog admin of the new blog.
			$user_id = apply_filters( 'copy_blog_user_id', $user_id, $from_blog_id );

			if ( is_subdomain_install() ) {
				$newdomain = $domain.".".$current_site->domain;
				$path = $base;
			} else {
				$newdomain = $current_site->domain;
				$path = trailingslashit( $base ) . trailingslashit( $domain );
			}

			// The new domain that will be created for the destination blog.
			$newdomain = apply_filters( 'copy_blogtext_domain', $newdomain, $domain );

			// The new path that will be created for the destination blog.
			$path = apply_filters( 'copy_blog_path', $path, $domain );

			$wpdb->hide_errors();
            $to_blog_id = wpmu_create_blog( $newdomain, $path, $title, $user_id , array( "public" => 1 ), $current_site->id );
			$wpdb->show_errors();

			if ( !is_wp_error( $to_blog_id ) ) {
				// $dashboard_blog = get_dashboard_blog();
				// if ( !is_super_admin() && get_user_option( 'primary_blog', $user_id ) == $dashboard_blog->blog_id )
				// 	update_user_option( $user_id, 'primary_blog', $to_blog_id, true );

				// now copy
				if ( $from_blog_id ) {

					$this->copy_blog_data( $from_blog_id, $to_blog_id );

					if ( $copy_files ) {

						// $this->copy_blog_files( $from_blog_id, $to_blog_id );
						$this->replace_content_urls( $from_blog_id, $to_blog_id );

					}
                    $msg = sprintf( __( 'Copied: %s in %s seconds', $this->text_domain ), '<a href="http://'.$newdomain.'" target="_blank">'.$title.'</a>', number_format_i18n( timer_stop() ) );
					do_action( 'log', __( 'Copy Complete!', $this->text_domain ), $this->text_domain, $msg );
					do_action( 'copy_blog_complete', $from_blog_id, $to_blog_id );
				}
			} else {
				$msg = $to_blog_id->get_error_message();
			}
			return $msg;
		}

		/**
		 * Copy blog data from one blog to another
		 *
		 * @param int     $from_blog_id ID of the blog being copied from.
		 * @param int     $to_blog_id   ID of the blog being copied to.
		 */
		private function copy_blog_data( $from_blog_id, $to_blog_id ) {
			global $wpdb, $wp_version;
			if ( $from_blog_id ) {
                $from_blog_prefix         = $this->get_blog_prefix( $from_blog_id );
                $to_blog_prefix           = $this->get_blog_prefix( $to_blog_id );
                $from_blog_prefix_length  = strlen( $from_blog_prefix );
                $to_blog_prefix_length    = strlen( $to_blog_prefix );
                $from_blog_escaped_prefix = str_replace( '_', '\_', $from_blog_prefix );

				// Grab key options from new blog.
				$saved_options = array(
                    'siteurl'         => '',
                    'home'            => '',
                    'upload_path'     => '',
                    'fileupload_url'  => '',
                    'upload_url_path' => '',
                    'admin_email'     => '',
                    'blogname'        => ''
				);
				// Options that should be preserved in the new blog.
				$saved_options = apply_filters( 'copy_blog_data_saved_options', $saved_options );
				foreach ( $saved_options as $option_name => $option_value ) {
					$saved_options[$option_name] = get_blog_option( $to_blog_id, $option_name );
				}

				// Copy over ALL the tables.
				$query = $wpdb->prepare( 'SHOW TABLES LIKE %s', $from_blog_escaped_prefix.'%' );
				do_action( 'log', $query, $this->text_domain );
				$old_tables = $wpdb->get_col( $query );

				foreach ( $old_tables as $k => $table ) {

					$raw_table_name = substr( $table, $from_blog_prefix_length );
					$newtable = $to_blog_prefix . $raw_table_name;

					$query = "DROP TABLE IF EXISTS {$newtable}";
					do_action( 'log', $query, $this->text_domain );
					$wpdb->get_results( $query );

					$query = "CREATE TABLE IF NOT EXISTS {$newtable} LIKE {$table}";
					do_action( 'log', $query, $this->text_domain );
					$wpdb->get_results( $query );

					$query = "INSERT {$newtable} SELECT * FROM {$table}";
					do_action( 'log', $query, $this->text_domain );
					$wpdb->get_results( $query );
				}

				switch_to_blog( $to_blog_id );

				// caches will be incorrect after direct DB copies
				wp_cache_delete( 'notoptions', 'options' );
				wp_cache_delete( 'alloptions', 'options' );

				// apply key options from new blog.
				foreach ( $saved_options as $option_name => $option_value ) {
					update_option( $option_name, $option_value );
				}

				/// fix all options with the wrong prefix...
                $query   = $wpdb->prepare( "SELECT * FROM {$wpdb->options} WHERE option_name LIKE %s", $from_blog_escaped_prefix.'%' );
                $options = $wpdb->get_results( $query );
				do_action( 'log', $query, $this->text_domain, count( $options ).' results found.' );
				if ( $options ) {
					foreach ( $options as $option ) {
						$raw_option_name = substr( $option->option_name, $from_blog_prefix_length );
						$wpdb->update( $wpdb->options, array( 'option_name' => $to_blog_prefix . $raw_option_name ), array( 'option_id' => $option->option_id ) );
					}

					// caches will be incorrect after direct DB copies
					wp_cache_delete( 'notoptions', 'options' );
					wp_cache_delete( 'alloptions', 'options' );
				}

				// Fix GUIDs on copied posts
				$this->replace_guid_urls( $from_blog_id, $to_blog_id );

				$attachments = get_posts(
					array(
                        'posts_per_page' => -1,
                        'post_type'      => 'attachment'
					)
				);

				foreach( $attachments as $attachment ) {
					wp_delete_post( $attachment->ID );
				}

				$nav_menus = get_posts(
					array(
                        'posts_per_page' => -1,
                        'post_type'      => 'nav_menu_item'
					)
				);

                $old_blog_details = get_blog_details( $from_blog_id );
                $old_slug         = stripslashes( $old_blog_details->path );

				$new_blog_details = get_blog_details( $to_blog_id );
                $new_slug         = stripslashes( $new_blog_details->path );


				foreach( $nav_menus as $menu ) {
					$url = get_post_meta( $menu->ID, '_menu_item_url', true );
					$url = str_replace( $old_slug, $new_slug, $url );
					update_post_meta( $menu->ID, '_menu_item_url', $url );
				}

				// Get the id of the participant that owns the journal (we have to do this with the title because the user hasnt been assigned to the journal at this point)
				switch_to_blog( $to_blog_id );

				$participant_id = 0;
				$user_name      = str_replace( 'CPD Journal for ', '', $new_blog_details->blogname );
				$user           = get_user_by( 'login', $user_name );
				$participant_id = $user->ID;

				$post_types = get_post_types();
                $ps         = get_posts(
					array(
                        'posts_per_page' => -1,
                        'post_type'      => $post_types,
						'post_status'    => 'any'
					)
				);

				foreach( $ps as $p ) {
					$my_post = array(
						'ID'           => $p->ID,
						'post_author'  => $participant_id
					);

					// Update the post into the database
 					wp_update_post( $my_post );
				}

				restore_current_blog();
			}
		}

		/**
		 * Copy files from one blog to another.
		 *
		 * @param int     $from_blog_id ID of the blog being copied from.
		 * @param int     $to_blog_id   ID of the blog being copied to.
		 */
		// private function copy_blog_files( $from_blog_id, $to_blog_id ) {
		// 	set_time_limit( 2400 ); // 60 seconds x 10 minutes
		// 	@ini_set( 'memory_limit', '2048M' );
		//
		// 	// Path to source blog files.
		// 	switch_to_blog( $from_blog_id );
		// 	$dir_info = wp_upload_dir();
		// 	$from = str_replace( ' ', "\\ ", trailingslashit( $dir_info['basedir'] ).'*' ); // * necessary with GNU cp, doesn't hurt anything with BSD cp
		// 	restore_current_blog();
		// 	$from = apply_filters( 'copy_blog_files_from', $from, $from_blog_id );
		//
		// 	// Path to destination blog files.
		// 	switch_to_blog( $to_blog_id );
		// 	$dir_info = wp_upload_dir();
		// 	$to = str_replace( ' ', "\\ ", trailingslashit( $dir_info['basedir'] ) );
		// 	restore_current_blog();
		// 	$to = apply_filters( 'copy_blog_files_to', $to, $to_blog_id );
		//
		// 	// Shell command used to copy files.
		// 	$command = apply_filters( 'copy_blog_files_command', sprintf( "cp -Rfp %s %s", $from, $to ), $from, $to );
		// 	exec( $command );
		// }

		/**
		 * Replace URLs in post content
		 *
		 * @param int     $from_blog_id ID of the blog being copied from.
		 * @param int     $to_blog_id   ID of the blog being copied to.
		 */
		private function replace_content_urls( $from_blog_id, $to_blog_id ) {
			global $wpdb;
			$to_blog_prefix = $this->get_blog_prefix( $to_blog_id );
			$from_blog_url = get_blog_option( $from_blog_id, 'siteurl' );
			$to_blog_url = get_blog_option( $to_blog_id, 'siteurl' );
			$query = $wpdb->prepare( "UPDATE {$to_blog_prefix}posts SET post_content = REPLACE(post_content, '%s', '%s')", $from_blog_url, $to_blog_url );
			do_action( 'log', $query, $this->text_domain );
			$wpdb->query( $query );

			$query = $wpdb->prepare( "UPDATE {$to_blog_prefix}postmeta SET meta_value = REPLACE(meta_value, '%s', '%s') WHERE meta_key = '_menu_item_url'", $from_blog_url, $to_blog_url );
			do_action( 'log', $query, $this->text_domain );
			$wpdb->query( $query );
		}

		/**
		 * Replace URLs in post GUIDs
		 *
		 * @param int     $from_blog_id ID of the blog being copied from.
		 * @param int     $to_blog_id   ID of the blog being copied to.
		 */
		private function replace_guid_urls( $from_blog_id, $to_blog_id ) {
			global $wpdb;
			$to_blog_prefix = $this->get_blog_prefix( $to_blog_id );
			$from_blog_url = get_blog_option( $from_blog_id, 'siteurl' );
			$to_blog_url = get_blog_option( $to_blog_id, 'siteurl' );
			$query = $wpdb->prepare( "UPDATE {$to_blog_prefix}posts SET guid = REPLACE(guid, '%s', '%s')", $from_blog_url, $to_blog_url );
			do_action( 'log', $query, $this->text_domain );
			$wpdb->query( $query );
		}

		/**
		 * Get the database prefix for a blog
		 *
		 * @param int     $blog_id ID of the blog.
		 * @return string prefix
		 */
		private function get_blog_prefix( $blog_id ) {
			global $wpdb;
			if ( is_callable( array( &$wpdb, 'get_blog_prefix' ) ) ) {
				$prefix = $wpdb->get_blog_prefix( $blog_id );
			} else {
				$prefix = $wpdb->base_prefix . $blog_id . '_';
			}
			return $prefix;
		}

		/**
		 * Sort by menu order
		 *
		 * @param object  $a Object a
		 * @param object  $b Object b
		 *
		 * @return sort order
		 */
		private function sort_by_menu_order( $a, $b ) {
			return $a["menu_order"] - $b["menu_order"];
		}

		/**
		 * Check if current blog is template
		 *
		 * @return true if is template
		 */
		public static function blog_is_template() {
			$blog_id = get_current_blog_id();
			$blog    = get_blog_details( $blog_id );

			if( strrpos( $blog->path, '/template-' ) === 0 ) {
				return true;
			}
			return false;
		}

		/**
		 * Check if current blog is primary template
		 *
		 * @return true if is template
		 */
		public static function blog_is_primary_template() {
			$blog_id = get_current_blog_id();
			$blog    = get_blog_details( $blog_id );

			if( strrpos( $blog->path, '/template-default/' ) === 0 ) {
				return true;
			}
			return false;
		}

		/**
		 * Check if user has templates
		 *
		 * @return true if has template
		 */
		public static function user_has_templates( $user ) {

			$blogs         = get_blogs_of_user( $user->ID );
			$has_templates = false;

			if( !is_array( $blogs ) ) {
				$blogs = array();
			}

			foreach( $blogs as $blog ) {
				if( strrpos( $blog->path, '/template-' ) === 0 ) {
					$has_templates = true;
					break;
				}
			}

			return $has_templates;
		}

	}
}
