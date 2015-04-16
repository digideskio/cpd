<?php
/**
 * The menus
 *
 * @link       http://makedo.in
 * @since      1.0.0
 *
 * @package    MKDO_Admin
 * @subpackage MKDO_Admin/admin
 */

/**
 * The menus
 *
 * Creates the MKDO menu items
 *
 * @package    MKDO_Admin
 * @subpackage MKDO_Admin/admin
 * @author     Make Do <hello@makedo.in>
 */
class MKDO_Admin_Menus extends MKDO_Menu {

	/**
	 * Initialize the class and set its properties.
	 *
	 * @since    1.0.0
	 * @var      string    $instance       The name of this plugin.
	 * @var      string    $version    The version of this plugin.
	 */
	public function __construct() {

		$args 								= 	array(
														'page_title' 			=> 	'Content',
														'menu_title' 			=> 	'Content',
														'capibility' 			=> 	'edit_posts',
														'slug' 					=> 	'mkdo_content_menu',
														'function'				=> 	array( $this, 'mkdo_content_menu'),
														'icon' 					=> 	'dashicons-admin-page',
														'position' 				=> 	'26',
														'add_menus'				=> 	array(),
														'remove_menus'			=> 	array(),
														'remove_sub_menus'		=> 	array(),
													);

		$args['add_menus'][] 				= 	array( 
														'post_type'							=>		'post',
														'post_name' 						=> 		'Posts',
														'menu_name' 						=> 		'Posts',
														'capability' 						=> 		'edit_posts',
														'function' 							=> 		'edit.php',
														'admin_add'							=>		FALSE,
														'mkdo_add'							=> 		FALSE,
														'remove_original_menu' 				=> 		TRUE,
														'remove_original_sub_menu' 			=> 		FALSE,
														'remove_original_sub_menu_parent' 	=> 		'',
														'admin_remove'						=>		TRUE,
														'mkdo_remove'						=> 		TRUE,
														'add_to_dashboard'					=> 		TRUE,
														'add_to_dashboard_slug'				=> 		'mkdo_content_menu',
													);

		$args['add_menus'][] 				= 	array( 
														'post_type'							=>		'page',
														'post_name' 						=> 		'Pages',
														'menu_name' 						=> 		'Pages',
														'capability' 						=> 		'edit_posts',
														'function' 							=> 		defined('CMS_TPV_URL') ? 'edit.php?post_type=page&page=cms-tpv-page-page' : 'edit.php?post_type=page',
														'admin_add'							=>		FALSE,
														'mkdo_add'							=> 		FALSE,
														'remove_original_menu' 				=> 		TRUE,
														'remove_original_sub_menu' 			=> 		FALSE,
														'remove_original_sub_menu_parent' 	=> 		'',
														'admin_remove'						=>		TRUE,
														'mkdo_remove'						=> 		TRUE,
														'add_to_dashboard'					=> 		TRUE,
														'add_to_dashboard_slug'				=> 		'mkdo_content_menu',
													);

		$args['remove_menus'][] 			= 	array( 
														'menu' 			=> 		'edit.php?post_type=page',
														'admin_remove'	=>		TRUE,
														'mkdo_remove'	=> 		TRUE
													);

		$args['remove_menus'][] 			= 	array( 
														'menu' 			=> 		'edit-comments.php',
														'admin_remove'	=>		TRUE,
														'mkdo_remove'	=> 		TRUE
													);

		$args['remove_menus'][] 			= 	array( 
														'menu' 			=> 		'seperator1',
														'admin_remove'	=>		TRUE,
														'mkdo_remove'	=> 		FALSE
													);

		$args['remove_menus'][] 			= 	array( 
														'menu' 			=> 		'tools.php',
														'admin_remove'	=>		TRUE,
														'mkdo_remove'	=> 		FALSE
													);

		$args['remove_menus'][] 			= 	array( 
													'menu' 			=> 		'options-general.php',
													'admin_remove'	=>		TRUE,
													'mkdo_remove'	=> 		FALSE
												);

		$args['remove_menus'][] 			= 	array( 
													'menu' 			=> 		'plugins.php',
													'admin_remove'	=>		TRUE,
													'mkdo_remove'	=> 		FALSE
												);

		$args['remove_menus'][] 			= 	array( 
													'menu' 			=> 		'wpseo_dashboard',
													'admin_remove'	=>		TRUE,
													'mkdo_remove'	=> 		FALSE
												);

		$args['remove_menus'][] 			= 	array( 
													'menu' 			=> 		'all-in-one-seo-pack/aioseop_class.php',
													'admin_remove'	=>		TRUE,
													'mkdo_remove'	=> 		FALSE
												);

		$args['remove_menus'][] 			= 	array( 
													'menu' 			=> 		'activity_log_page',
													'admin_remove'	=>		TRUE,
													'mkdo_remove'	=> 		FALSE
												);

		$args['remove_menus'][] 			= 	array( 
													'menu' 			=> 		'edit.php?post_type=acf',
													'admin_remove'	=>		TRUE,
													'mkdo_remove'	=> 		FALSE
												);

		$args['remove_menus'][] 			= 	array( 
													'menu' 			=> 		'wp-user-avatar',
													'admin_remove'	=>		TRUE,
													'mkdo_remove'	=> 		FALSE
												);

		$args['remove_sub_menus'][] 		= 	array( 
													'parent' 		=> 		'themes.php',
													'child' 		=> 		'themes.php',
													'admin_remove'	=>		TRUE,
													'mkdo_remove'	=> 		FALSE
												);

		$args['remove_sub_menus'][] 		= 	array( 
													'parent' 		=> 		'themes.php',
													'child' 		=> 		'customize.php',
													'admin_remove'	=>		TRUE,
													'mkdo_remove'	=> 		FALSE
												);

		$args['remove_sub_menus'][] 		= 	array( 
													'parent' 		=> 		'themes.php',
													'child' 		=> 		'theme-editor.php',
													'admin_remove'	=>		TRUE,
													'mkdo_remove'	=> 		FALSE
												);

		parent::__construct( $args );
	
	}

	/**
	 * Rename media menu
	 */
	public function rename_media_menu() {
	
		global $menu;
		
		$menu[10][0] = 'Assets';
	}

	/**
	 * Rename media menu
	 */
	public function rename_media_page( $translation, $text, $domain )
	{
	    if ( 'default' == $domain and 'Media Library' == $text )
	    {
	        // Once is enough.
	        remove_filter( 'gettext', 'rename_media_page' );
	        return 'Assets Library';
	    }
	    return $translation;
	}

	/**
	 * Render menu dashboard 
	 */
	public function mkdo_content_menu() {

		$mkdo_content_menu_path 		= 	dirname(__FILE__) . '/partials/mkdo-content-menu.php';
		$theme_path 					= 	get_stylesheet_directory() . '/mkdo-admin/mkdo-content-menu.php';
		$partials_path					= 	get_stylesheet_directory() . '/partials/mkdo-content-menu.php';

		if( file_exists( $theme_path ) ) {
			$mkdo_content_menu_path = 	$theme_path;
		}
		else if( file_exists( $partials_path ) ) { 
			$mkdo_content_menu_path =  	$partials_path;
		}

		include $mkdo_content_menu_path;
	}

	/**
	 * Render menu dashboard blocks
	 */
	public function mkdo_content_menu_render_blocks() {

		$mkdo_content_blocks = apply_filters(
			$this->slug . '_blocks',
			array()
		);

		if( !empty( $mkdo_content_blocks ) ) {

			$counter = 1;
	
			foreach( $mkdo_content_blocks as $block ) {

				$function_name = 'mkdo_content_menu_widget_' . $counter;
				$$function_name = function() use ( $block ){

					$post_listing 	= $block[ 'link' ];
					$post_new 		= $block[ 'call_to_action_link' ];

					if ( $block[ 'post_type' ] == 'page' && defined('CMS_TPV_URL') ) { 
						if( $post_listing = 'edit.php?post_type=page' ) {
							$post_listing = 'edit.php?post_type=page&page=cms-tpv-page-page';
						}
					}
					
					$css_block_class = $block[ 'css_class' ];
																
					?>

					<div class="<?php echo esc_attr( $css_block_class ); ?>">
						
						<?php
						if( !empty( $block[ 'post_type' ] ) ) {
							?>
							<p><a class="button button-primary" href="<?php echo esc_url( $post_new ); ?>"><?php echo esc_html( $block[ 'call_to_action_text' ] );?></a></p>

							<?php
						}
						?>

						<div class="content-description">
		
							<?php echo $block[ 'desc' ]; ?>
						
						</div>
						
						<?php
							
							if( $block[ 'show_tax' ] == true ) {
								
								$taxonomies = get_object_taxonomies( $block[ 'post_type' ], 'objects' );

								unset( $taxonomies[ 'post_format' ] );
								unset( $taxonomies[ 'post_status' ] );
								unset( $taxonomies[ 'ef_editorial_meta' ] );
								unset( $taxonomies[ 'following_users' ] );
								unset( $taxonomies[ 'ef_usergroup' ] );
								
								if( ! empty( $taxonomies ) ) {
									
									?>
									<h4 class="tax-title">Associated Taxonomies</h4>
									
									<ul class="tax-list">
									<?php
										
										foreach( $taxonomies as $tax ) {
											?>
											<li class="<?php echo esc_attr( sanitize_title( $tax->name ) ); ?>">
												<span class="dashicons-before dashicons-category"></span>
												<a href="<?php echo admin_url( 'edit-tags.php?taxonomy=' . $tax->name ); ?>"><?php echo esc_html( $tax->labels->name ); ?></a>
											</li>
											<?php
										}
										
									?>
									</ul>
										
									<?php
								}
								
							}	
							
						?>
						
						<p class="footer-button"><a class="button" href="<?php echo esc_url( $post_listing ); ?>"><?php echo esc_html( $block[ 'button_label' ] ); ?></a></p>
						
					</div>
					
					<?php
				};
				
				$position = 'side';
				$is_even = ( $counter % 2 == 0 );

				if( $is_even ) {
					$position = 'normal';
				}
				$screen = get_current_screen();

				add_meta_box('mkdo_content_menu_widget_' . $counter, '<span class="mkdo-block-title dashicons-before ' . esc_attr( $block[ 'dashicon' ] ) . '"></span> ' . esc_html( $block[ 'title' ] ), $$function_name, $screen, $position );

				$counter++;
			}
		} 
	}
}
