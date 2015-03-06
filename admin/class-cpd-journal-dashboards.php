<?php
/**
 * The dashboard-specific functionality of the plugin.
 *
 * @link       http://makedo.in
 * @since      1.0.0
 *
 * @package    CPD
 * @subpackage CPD/admin
 */

/**
 * The dashboard-specific functionality of the plugin.
 *
 * @package    CPD
 * @subpackage CPD/admin
 * @author     Make Do <hello@makedo.in>
 */
class CPD_Journal_Dashboards extends MKDO_Class {

	/**
	 * Initialize the class and set its properties.
	 *
	 * @since    1.0.0
	 * @var      string    $instance       The name of this plugin.
	 * @var      string    $version    The version of this plugin.
	 */
	public function __construct( $instance, $version ) {
		parent::__construct( $instance, $version );
	}

	/**
	 * Filter the MU dashboad actions
	 * 
	 * @param  string 	$actions 	The actions to be filtered
	 * @return string 	$actions 	The actions to be filtered
	 */
	public function filter_dashboard_actions( $actions ) {
		$actions = str_replace( 'wp-admin/', 'wp-admin/admin.php?page=mkdo_dashboard', $actions );
		return $actions;
	}

	/**
	 * Rename page titles
	 * 
	 * @param  string 	$translation 	The translated text
	 * @param  string 	$text        	The text to be translated
	 * @param  string 	$domain      	The domain of the text we are translating
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
		return $translation;
	}
}