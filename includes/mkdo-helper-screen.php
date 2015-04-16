<?php
/**
 * A helper plugin
 *
 * @link       http://makedo.in
 * @since      1.0.0
 *
 * @package    MKDO_Objects
 * @subpackage MKDO_Objects/admin
 */

/**
 * A helper plugin
 *
 * A helper plugin for common screen queries
 *
 * @package    MKDO_Objects
 * @subpackage MKDO_Objects/includes
 * @author     Make Do <hello@makedo.in>
 */
class MKDO_Helper_Screen {

	/**
	 * Get Screen base
	 *
	 * @since   1.0.0
	 * @return	bool 	$screen_base 		The screen base
	 */
	public static function get_screen_base() {
		
		$screen 		= get_current_screen();
		$screen_base 	= $screen->base;
		
		return $screen_base;
	}

}