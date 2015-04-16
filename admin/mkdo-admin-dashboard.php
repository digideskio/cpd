<?php
/**
 * The admin bar
 *
 * @link       http://makedo.in
 * @since      1.0.0
 *
 * @package    MKDO_Admin
 * @subpackage MKDO_Admin/admin
 */

/**
 * The admin bar
 *
 * Changes the default functionality of the admin bar
 *
 * @package    MKDO_Admin
 * @subpackage MKDO_Admin/admin
 * @author     Make Do <hello@makedo.in>
 */
class MKDO_Admin_Dashboard{

	/**
	 * Initialize the class and set its properties.
	 *
	 * @since    1.0.0
	 * @var      string    $instance       	The name of this plugin.
	 * @var      string    $version    		The version of this plugin.
	 */
	public function __construct() {
		
	}

	public function remove_dashboard_meta() {

		$meta = array(
					array (
						'id' 			=> 	'welcome_panel',
						'page' 			=> 	'dashboard',
						'context' 		=> 	'normal'
					),
					array (
						'id' 			=> 	'dashboard_incoming_links',
						'page' 			=> 	'dashboard',
						'context' 		=> 	'normal'
					),
					array (
						'id' 			=> 	'dashboard_plugins',
						'page' 			=> 	'dashboard',
						'context' 		=> 	'normal'
					),
					array (
						'id' 			=> 	'dashboard_primary',
						'page' 			=> 	'dashboard',
						'context' 		=> 	'side'
					),
					array (
						'id' 			=> 	'dashboard_secondary',
						'page' 			=> 	'dashboard',
						'context' 		=> 	'normal'
					),
					array (
						'id' 			=> 	'dashboard_quick_press',
						'page' 			=> 	'dashboard',
						'context' 		=> 	'side'
					),
					array (
						'id' 			=> 	'dashboard_recent_drafts',
						'page' 			=> 	'dashboard',
						'context' 		=> 	'side'
					),
					array (
						'id' 			=> 	'dashboard_recent_comments',
						'page' 			=> 	'dashboard',
						'context' 		=> 	'normal'
					),
					array (
						'id' 			=> 	'dashboard_right_now',
						'page' 			=> 	'dashboard',
						'context' 		=> 	'normal'
					),
					array (
						'id' 			=> 	'dashboard_activity',
						'page' 			=> 	'dashboard',
						'context' 		=> 	'normal'
					),
					array (
						'id' 			=> 	'network_dashboard_right_now',
						'page' 			=> 	'dashboard-network',
						'context' 		=> 	'side'
					),
					array (
						'id' 			=> 	'dashboard_primary',
						'page' 			=> 	'dashboard-network',
						'context' 		=> 	'side'
					),
					
				);

		$dashboard_meta_items = apply_filters(
			'cpd_remove_dashboard_meta_filter',
			$meta
		);

		foreach( $dashboard_meta_items as $meta ) {

			if( $meta['id'] == 'welcome_panel' ) {
				remove_action( 'welcome_panel', 'wp_welcome_panel' );
			}

			remove_meta_box(
				$meta['id'],
				$meta['page'],
				$meta['context']
			);
		}
	}
}