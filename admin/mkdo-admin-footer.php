<?php
/**
 * The footer
 *
 * @link       http://makedo.in
 * @since      1.0.0
 *
 * @package    MKDO_Admin
 * @subpackage MKDO_Admin/admin
 */

/**
 * The footer
 *
 * Custom functionality for the admin footer
 *
 * @package    MKDO_Admin
 * @subpackage MKDO_Admin/admin
 * @author     Make Do <hello@makedo.in>
 */
class MKDO_Admin_Footer{

	/**
	 * Initialize the class and set its properties.
	 *
	 * @since    1.0.0
	 * @var      string    $instance       The name of this plugin.
	 * @var      string    $version    The version of this plugin.
	 */
	public function __construct() {
		
	}

	/**
	 * Remove footer text
	 */
	public function remove_admin_footer() {
		return '';
	}

	/**
	 * Remove footer version
	 */
	public function remove_admin_version() {
		return '';
	}

	/**
	 * Add custom footer text
	 */
	public function add_footer_text() {

		$footer_text = 	apply_filters(
							'mkdo_footer_text',
							'Site created by <a href="http://makedo.in" target="_blank">Make Do</a> using <a href="http://wordpress.org" target="_blank">WordPress</a>'
						);

		return $footer_text;
	}
}