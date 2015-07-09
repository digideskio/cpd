<?php

// Exit if accessed directly
defined( 'ABSPATH' ) || exit;

if( !class_exists( 'CPD_Admin' ) ) {

/**
 * Admin
 *
 * Defines the admin settings
 *
 * @package    CPD
 * @subpackage CPD/admin
 * @author     Make Do <hello@makedo.in>
 */
class CPD_Admin {

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
	 * @param      string    $text_domain       The text domain of the plugin.
	 */
	public function set_text_domain( $text_domain ) { 
		$this->text_domain = $text_domain;
	}

	/**
	 * Remove menu items from the admin bar
	 *
	 * @hook 	filter_cpd_remove_admin_bar_menus		filter the menu items to remove from the admin bar
	 */
	public function remove_admin_bar_menus() {

		global $wp_admin_bar;

		$admin_bar_menus 	=	array(
									'wp-logo',
									'my-sites',
									'site-name',
									'wpseo-menu',
									'new-content',
									'comments',
									'updates',
									'search'
								);

    	$menus 				=	apply_filters(
		    						'filter_cpd_remove_admin_bar_menus',
		    						$admin_bar_menus
		    					);

    	foreach( $menus as $menu ) {
    		$wp_admin_bar->remove_menu( $menu );
    	}

    	// Remove 'Howdy'
    	$profile 			=	$wp_admin_bar->get_node('my-account');
		$title				= 	str_replace( 'Howdy,', '', $profile->title );

		$wp_admin_bar->add_node( 
			array(
				'id' => 'my-account',
				'title' => $title,
			) 
		);
	}

	/**
	 * Add custom menu switcher to the admin bar
	 */
	public function add_admin_bar_menu_switcher() {

		global $wp_admin_bar;

		if( is_admin() ) {

			$site_link = home_url();
			$link_name = __( 'View Site', $this->text_domain );

		} else {

			$site_link = admin_url( 'index.php' );
			$link_name = __( 'Site Admin', $this->text_domain );

		}

		$wp_admin_bar->add_menu(
			array(
				'id' => 'cpd_menu_switcher',
				'title' => $link_name,
				'href' => $site_link
			)
		);
	}

	/**
	 * Remove admin footer text
	 */
	public function remove_admin_footer_text() {
		return '';
	}

	/**
	 * Remove footer version
	 */
	public function remove_admin_version() {
		return '';
	}

	/**
	 * Rename page titles
	 * 
	 * @param  string 	$translation 	The translated text
	 * @param  string 	$text        	The text to be translated
	 * @param  string 	$domain      	The domain of the text we are translating
	 * 
	 * @return string 	$translation 	The translated text
	 */
	public function rename_page_titles( $translation, $text, $domain )
	{
	    if ( $domain == 'default' && $text == 'Sites' )
		{
			remove_filter( 'gettext', 'rename_sites_page' );
			return 'Journals';
		}

		if ( $domain == 'default' && $text == 'My Sites' )
		{
			remove_filter( 'gettext', 'rename_sites_page' );
			return 'My Journals';
		}

		if ( $domain == 'default' && $text == 'Primary Site' )
		{
			remove_filter( 'gettext', 'rename_sites_page' );
			return 'Primary Journal';
		}

		if ( $domain == 'default' && $text == 'Posts' )
		{
			remove_filter( 'gettext', 'rename_sites_page' );
			return 'Journal Entries';
		}

		return $translation;
	}


	/**
	 * Rename post object
	 */
	function rename_post_object() {
		
		global $wp_post_types;	

		$blog_id = get_current_blog_id();
		
		if( SITE_ID_CURRENT_SITE != $blog_id ) {

			$labels = &$wp_post_types['post']->labels;
			$labels->name = 'Journal Entries';
			$labels->singular_name = 'Journal Entry';
			$labels->add_new = 'Add Journal Entry';
			$labels->add_new_item = 'Add Journal Entry';
			$labels->edit_item = 'Edit Journal Entry';
			$labels->new_item = 'Journal Entry';
			$labels->view_item = 'View Journal Entry';
			$labels->search_items = 'Search Journal Entries';
			$labels->not_found = 'No Journal Entries found';
			$labels->not_found_in_trash = 'No Journal Entries found in Trash';
			$labels->all_items = 'All Journal Entries';
			$labels->menu_name = 'Journal Entries';
			$labels->name_admin_bar = 'Journal Entries';
		}
	}

	/**
	 * Add about link to the menu bar
	 */
	public function add_admin_bar_about_link() {
		global $wp_admin_bar;

		$wp_admin_bar->add_menu(
			array(
				'id'    => 'aspire-logo',
				'title' => '<span class="ab-icon"></span><span class="screen-reader-text">About Aspire CPD</span>',
				'href'  => 'http://aspirecpd.org',
				'meta'  => array(
					'target' => '_blank'
				)
			)
		);

	}

	/**
	 * Add custom logo to the admin bar
	 */
	public function add_admin_bar_logo() {
		
		if( is_admin_bar_showing() ) {
			$template_name 						= 	'cpd-admin-bar-logo';
			$template_path 						= 	CPD_Templates::get_template_path( $template_name );

			if( $template_path !== FALSE ) {
				include $template_path;
			}
		}
	}
}
}