<?php
/**
 * Metaboxes
 *
 * @link       http://makedo.in
 * @since      1.0.0
 *
 * @package    MKDO_Admin
 * @subpackage MKDO_Admin/admin
 */

/**
 * Metaboxes
 *
 * Functions relating to metaboxes
 *
 * @package    MKDO_Admin
 * @subpackage MKDO_Admin/admin
 * @author     Make Do <hello@makedo.in>
 */
class MKDO_Admin_Metaboxes{

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
	 * Remove metaboxes
	 */
	public function remove_metaboxes() {
	
		/* if the current user is not a mkdo super user */
		if( ! MKDO_Helper_User::is_elevated_user() ) {
			
			$metaboxes = apply_filters(
				'mkdo_remove_metaboxes',
				array()
			);
			
			foreach( $metaboxes as $metabox ) {
				
				if( $metabox[ 'page' ] == 'all' ) {
					$pages = get_post_types();

					foreach( $pages as $page )
					{
						remove_meta_box( $metabox[ 'id' ], $page , $metabox[ 'context' ] );
					}
				}
				else {
					foreach( $metabox[ 'page' ] as $page )
					{
						remove_meta_box( $metabox[ 'id' ], $page , $metabox[ 'context' ] );
					}
				}
			}
			
		}
	}

	/** 
	 * Hide metaboxes
	 */
	public function hide_metaboxes( $hidden, $screen ) {

		$hidden 	= 	apply_filters(
							'mkdo_hide_metaboxes',
							array(
								'postcustom',
								'commentsdiv',
								'commentstatusdiv',
								'slugdiv',
								'trackbacksdiv',
								'revisionsdiv',
								'tagsdiv-post_tag',
								'authordiv',
								'wpseo_meta',
								'relevanssi_hidebox',
								'aiosp'
							)
						);

		return $hidden;
	}
}