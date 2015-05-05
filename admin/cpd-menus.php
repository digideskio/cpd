<?php
/**
 * The menu-specific functionality of the plugin.
 *
 * @link       http://makedo.in
 * @since      1.0.0
 *
 * @package    CPD
 * @subpackage CPD/admin
 */

if( !class_exists( 'CPD_Menus' ) ) {

/**
 * The menu-specific functionality of the plugin.
 *
 * @package    CPD
 * @subpackage CPD/admin
 * @author     Make Do <hello@makedo.in>
 */
class CPD_Menus {

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
		

		add_action( 'parent_file', 	array( $this, 'correct_menu_hierarchy'), 9999 );
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

	public function add_content_menu() {
	
		add_object_page(
			'Content',
			'Content',
			'edit_posts',
			'cpd_content_menu',
			array( $this, 'cpd_content_menu'),
			'dashicons-admin-page'
		);
	}

	public function cpd_content_menu() {

		$template_name 						= 	'cpd-content-menu';
		$template_path 						= 	CPD_Templates::get_template_path( $template_name );

		if( $template_path !== FALSE ) {
			include $template_path;
		}
	}

	public function add_content_menu_items() {

		$cpd_content_menu_items 	= 	array();

		// Posts
		$cpd_content_menu_items[] 	= 	array(
			'post_name'		=>	'post',
			'menu_name'		=>	'Journal Entry',
			'capability'	=>	'edit_posts',
			'function'		=>	'edit.php'
		);

		// Pages
		$cpd_content_menu_items[] 	= 	array(
			'post_name'		=>	'page',
			'menu_name'		=>	'Pages',
			'capability'	=>	'edit_posts',
			'function'		=>	defined('CMS_TPV_URL') ? 'edit.php?post_type=page&page=cms-tpv-page-page' : 'edit.php?post_type=page'
		);


		$menus 						=	apply_filters(
											'filter_cpd_content_menu_add_items',
											$cpd_content_menu_items
										);

		foreach( $menus as $menu ) {
			add_submenu_page(
				'cpd_content_menu',
				$menu['post_name'],
				$menu['menu_name'],
				$menu['capability'],
				$menu['function']
			);
		}
	}

	public function add_content_menu_dashboard_widgets() {

		$content_menu_dashboard_widgets 	= 	array();
		$counter 							=	1;
		$template_name 						= 	'cpd-content-menu-dashboard-widget';
		$template_path 						= 	CPD_Templates::get_template_path( $template_name );

		// Posts
		$content_menu_dashboard_widgets[] 	= 	array(
			'title' 				=> __( 'Journal Entries', $this->text_domain ),
			'dashicon' 				=> 'dashicons-admin-post',
			'desc' 					=> '<p>' . __( 'This content type is for managing Journal Entries.</p>', $this->text_domain),
			'post_type' 			=> 'post',
			'button_label' 			=> __( 'Edit / Manage Journal Entries', $this->text_domain),
			'css_class' 			=> 'post',
			'show_tax' 				=> TRUE,
			'link' 					=> admin_url( 'edit.php' ),
			'call_to_action_text'	=> __( 'Add New', $this->text_domain ),
			'call_to_action_link' 	=> admin_url( 'post-new.php' )
		);

		// Pages
		$content_menu_dashboard_widgets[] 	= 	array(
			'title' 				=> __( 'Pages', $this->text_domain ),
			'dashicon' 				=> 'dashicons-admin-page',
			'desc' 					=> '<p>' . __( 'This content type is for managing Pages.</p>', $this->text_domain),
			'post_type' 			=> 'page',
			'button_label' 			=> __( 'Edit / Manage Pages', $this->text_domain),
			'css_class' 			=> 'page',
			'show_tax' 				=> TRUE,
			'link' 					=> admin_url( 'edit.php?post_type=page' ),
			'call_to_action_text'	=> 'Add New',
			'call_to_action_link' 	=> admin_url( 'post-new.php?post_type=page' )
		);


		$widgets 							= 	apply_filters(
													'filter_cpd_content_menu_dashboard_widgets',
													$content_menu_dashboard_widgets
												);

		foreach( $widgets as $widget ) {
			
			$function_name = 'cpd_content_menu_dashboard_widget_' . $counter;
			$$function_name = function() use ( $widget, $template_path ){
				if( $template_path !== FALSE ) {
					include $template_path;
				}
			};
			
			$position = 'side';
			$is_even = ( $counter % 2 == 0 );

			if( $is_even ) {
				$position = 'normal';
			}
			$screen = get_current_screen();

			add_meta_box('cpd_content_menu_dashboard_widget_' . $counter, '<span class="mkdo-block-title dashicons-before ' . esc_attr( $widget[ 'dashicon' ] ) . '"></span> ' . esc_html( $widget[ 'title' ] ), $$function_name, $screen, $position );

			$counter++;
		}

	}

	/** TODO: OLD NEEDS REFACTOR */


	/**
	 * Filter menus
	 */
	public function filter_menu_items( $menus ) {

		foreach( $menus as &$menu ) {

			if( $menu['post_type'] == 'post' ) {

				$menu['post_name'] 					= 	'Journal Entry';
				$menu['menu_name'] 					= 	'Journal Entry';
				$menu['add_to_dashboard_block'] 	= 	array(
															'dashicon' 		=> 'dashicons-book'
														);
			}
		}

		return $menus;
	}

	/**
	 * Rename network sub menus
	 */
	public function filter_network_admin_sub_menus( $menus ) {
		
		foreach( $menus as &$menu ) {
			if( $menu['page_title'] == 'Sites' ) {
				$menu['page_title'] = 'Journals';
				$menu['menu_title'] = 'Journals';
			}
		}

		return $menus;
	}

	/**
	 * Remove sub menus
	 */
	public function remove_admin_sub_menus() {
		
		$user_id 	= get_current_user_id();
		$user_type 	= get_user_meta( $user_id, 'cpd_role', true );

		if( $user_type == 'participant' )
		{
			remove_submenu_page( 'users.php', 'users.php' );
			remove_submenu_page( 'users.php', 'user-new.php' );
			remove_submenu_page( 'tools.php', 'ms-delete-site.php' );
			remove_submenu_page( 'options-general.php', 'options-discussion.php' );
		}

		if( $user_type == 'participant' || $user_type == 'supervisor' ) {
			remove_submenu_page( 'index.php', 'my-sites.php' );
		}
	}

	/**
	 * Correct the heirachy
	 */
	public function correct_menu_hierarchy( $parent_file ) {
	
		global $current_screen;
		global $submenu;

		$pages 		= array();
		$this->slug = 'cpd_content_menu';
		$parent 	= $this->slug;
		
		if ( is_array( $submenu ) && isset( $submenu[$parent] ) ) {

			foreach ( (array) $submenu[$parent] as $item) {

				if ( current_user_can($item[1]) ) {
					$menu_file = $item[2];
					if ( false !== ( $pos = strpos( $menu_file, '?' ) ) ) {
						$menu_file = substr( $menu_file, 0, $pos );
					}
	
					if( $item[2] == 'edit.php' ) {
						$pages[] = 'Journal Entry';
					}
					else if( $item[2] == 'edit.php?post_type=page' || $item[2] == 'edit.php?post_type=page&page=cms-tpv-page-page' ){
						$pages[] = 'Pages';
					}
					else {
						$pages[] = $item[0];
					}
						
				}
			}
		}

		$post_type = get_post_type_object( $current_screen->post_type );
		
		if( isset( $post_type->labels ) && isset( $post_type->labels->name ) ) {
			$post_type = $post_type->labels->name;
		}
		
		/* get the base of the current screen */
		$screenbase = $current_screen->base;

		/* if this is the edit.php base */
		if( ( $screenbase == 'edit' && in_array( $post_type, $pages ) )|| ( $screenbase == 'post' && in_array( $post_type, $pages ) ) ) {

			/* set the parent file slug to the custom content page */
			$parent_file = $this->slug;
			
		}

		if( defined('CMS_TPV_URL') && $screenbase == 'pages_page_cms-tpv-page-page' && in_array( $post_type, $pages ) ) {
			$parent_file = $this->slug;
		}

		global $submenu;
		$screen = get_current_screen();

		if( strpos( $screen->base, '-network' ) ) {
			if ( 
					$parent_file == 'sites.php' 		||
					$parent_file == 'users.php' 		|| 
					$parent_file == 'themes.php' 		|| 
					$parent_file == 'plugins.php' 		|| 
					$parent_file == 'settings.php' 		|| 
					$parent_file == 'update-core.php'
				) {
				$parent_file = 'index.php';
			}
		}
		
		/* return the new parent file */	
		return $parent_file;
	}
}
}