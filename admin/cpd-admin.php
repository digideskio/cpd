<?php
/**
 * The admin-specific functionality of the plugin.
 *
 * @link       http://makedo.in
 * @since      1.0.0
 *
 * @package    CPD
 * @subpackage CPD/admin
 */

// Exit if accessed directly
defined( 'ABSPATH' ) || exit;

if( !class_exists( 'CPD_Admin' ) ) {

/**
 * The admin-specific functionality of the plugin.
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

	/**
	 * Remove menu items from the admin bar
	 *
	 * @hook 	filter_cpd_remove_admin_bar_menus		filter the menu items to remove from the admin bar
	 * 
	 * @since 	2.0.0
	 */
	public function remove_admin_bar_menus() {

		global $wp_admin_bar;

		$admin_bar_menus 	=	array(
									'my-sites',
									'site-name',
									'wpseo-menu',
									'new-content',
									// 'comments',
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
	 *
	 * @since 	2.0.0
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
	 *
	 * @since 	2.0.0
	 */
	public function remove_admin_footer_text() {
		return '';
	}

	/**
	 * Remove footer version
	 *
	 * @since 	2.0.0
	 */
	public function remove_admin_version() {
		return '';
	}
}
}