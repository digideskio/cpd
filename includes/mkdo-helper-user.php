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
 * A helper plugin for common user queries
 *
 * @package    MKDO_Objects
 * @subpackage MKDO_Objects/includes
 * @author     Make Do <hello@makedo.in>
 */
class MKDO_Helper_User {

	/**
	 * Is MKDO user
	 *
	 * @since   1.0.0
	 * @return	bool 	$is_mkdo_user 		Is the user a MKDO user
	 */
	public static function is_mkdo_user() {
		
		$user_id = get_current_user_id();
		
		if( get_user_meta( $user_id, 'mkdo_user', TRUE ) == '1' ) {
			return TRUE;
		} else {
			return FALSE;
		}
	}

}