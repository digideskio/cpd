<?php
/**
 * The dashboard-specific functionality of the plugin.
 *
 * @link       http://makedo.in
 * @since      2.0.0
 *
 * @package    CPD
 * @subpackage CPD/admin
 */

// Exit if accessed directly
defined( 'ABSPATH' ) || exit;

if( !class_exists( 'CPD_Dashboards' ) ) {
	
/**
 * The dashboard-specific functionality of the plugin.
 *
 * @package    CPD
 * @subpackage CPD/admin
 * @author     Make Do <hello@makedo.in>
 */
class CPD_Dashboards {

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

	 * @param      string    $instance       The name of this plugin.
	 * @param      string    $version    The version of this plugin.
	 */
	public function __construct() {
		
	}

	/**
	 * Set the text domain
	 *
	 * @param      string    $text_domain       The text domain of the plugin.
	 *

	 **/
	public function set_text_domain( $text_domain ) { 
		$this->text_domain = $text_domain;
	}


	/**
	 * Remove dashboard widgets
	 *
	 * @hook 	filter_cpd_remove_dashboard_widgets 	Filter to remove meta boxes from dashboards
	 * 

	 **/
	public function remove_dashboard_widgets() {

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

		$dashboard_meta 			= apply_filters(
												'filter_cpd_remove_dashboard_widgets',
												$meta
											);

		foreach( $dashboard_meta as $meta ) {

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
}