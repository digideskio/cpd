<?php
/**
 * The template-specific functionality of the plugin.
 *
 * @link       http://makedo.in
 * @since      2.0.0
 *
 * @package    CPD
 * @subpackage CPD/includes
 */

// Exit if accessed directly
defined( 'ABSPATH' ) || exit;

if( !class_exists( 'CPD_Templates' ) ) {

/**
 * The template-specific functionality of the plugin.
 *
 * Functions needed for the finding templates
 *
 * @package    MKDO_Chat
 * @subpackage MKDO_Chat/includes
 * @author     Make Do <hello@makedo.in>
 */
class CPD_Templates {

	private static $instance = null;

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

	 * @param      string    $instance    The name of this plugin.
	 * @param      string    $version    	The version of this plugin.
	 */
	public function __construct() {
		
	}

	/**
	 * Find the correct template path
	 *
	 * @hook 	filter_cpd_template_locations 	filter that overrides all other template location filters
	 * 
	 * @param  string 			$template_name      	The name of the template we are looking for
	 * @param  array<string> 	$template_locations 	A list of locations to look in
	 * @return string 			$template_path 			The path of the tempalte
	 */
	public static function get_template_path( $template_name, $template_locations = array() ) {
		
		$template_found 		= FALSE;
		$template_path 			= FALSE;

		$template_locations 	=	apply_filters( 
									'filter_cpd_template_locations',
									$template_locations
									);

		foreach( $template_locations as $location ) {
			$template_path = get_stylesheet_directory() . '/' . $location . '/' . $template_name . '.php';
			if( file_exists( $template_path ) ) {
				$template_found = 	TRUE;
				break;
			}
		}

		if( !$template_found ) {
			$template_path 		= 	plugin_dir_path( __FILE__ ) . '../templates/' . $template_name . '.php';
			if( !file_exists( $template_path ) ) {
				return FALSE;
			}
		}

		return $template_path;
	}
}
}