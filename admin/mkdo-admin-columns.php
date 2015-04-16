<?php
/**
 * Adds additional fields to profile page
 *
 * @link       http://makedo.in
 * @since      1.0.0
 *
 * @package    MKDO_Admin
 * @subpackage MKDO_Admin/admin
 */

/**
 * Admin profile
 *
 * Adds additional fields to profile page
 *
 * @package    MKDO_Admin
 * @subpackage MKDO_Admin/admin
 * @author     Make Do <hello@makedo.in>
 */
class MKDO_Admin_Columns{

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
	 * Remove columns
	 */
	public function remove_columns( $columns ) {

		if( ! MKDO_Helper_User::is_elevated_user() ) {
			
			$mkdo_columns = apply_filters(
				'mkdo_remove_columns',
				array()
			);
			
			foreach( $mkdo_columns as $column ) {
				unset( $columns[$column] );
			}	
		}
		return $columns;
	}

	/**
	 * Hide columns
	 */
	public function hide_columns( $user_login, $user ) {

		$screens	 = 	get_post_types();

		$mkdo_columns = apply_filters(
			'mkdo_hide_columns',
			array(
				'comments',
				'tags',
				'wpseo-score',
				'wpseo-title',
				'wpseo-metadesc',
				'wpseo-focuskw',
				'google_last30',
				'twitter_shares',
				'linkedin_shares',
				'facebook_likes',
				'facebook_shares',
				'total_shares',
				'decay_views',
				'decay_shares',
				'seotitle',
				'seodesc',
				'seokeywords',
			)
		);

		foreach( $screens as $screen ) {
		
			$hidden_columns 	= get_user_option( 'manageedit-' . $screen . 'columnshidden',  $user->ID );

			foreach( $mkdo_columns as $column ) 
			{
				if( !in_array( $column, (array) $hidden_columns ) ){

					$hidden_columns[] 		= $column;
				} 
			}

			$hidden_columns[] = array();

			update_user_meta( $user->ID, 'manageedit-' . $screen . 'columnshidden', $hidden_columns );
		}
	}

	/**
	 * Remove columns for each post type
	 */
	public function remove_custom_post_columns() {

		foreach( get_post_types() as $post_type )
		{
			add_filter( 'manage_' . $post_type . '_posts_columns', 	array( $this, 'remove_columns' ), 9998, 1 );
		}
	}

	/**
	 * Remove column filters
	 */
	public function remove_column_filters() {
		
		global $wpseo_metabox;

		if ( $wpseo_metabox ) {
			remove_action( 'restrict_manage_posts', array( $wpseo_metabox, 'posts_filter_dropdown' ) );
		}
	}
}