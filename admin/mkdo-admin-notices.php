<?php
/**
 * Admin notices
 *
 * @link       http://makedo.in
 * @since      1.0.0
 *
 * @package    MKDO_Admin
 * @subpackage MKDO_Admin/admin
 */

/**
 * Admin notices
 *
 * Adds additional notice fields to edit.php
 *
 * @package    MKDO_Admin
 * @subpackage MKDO_Admin/admin
 * @author     Make Do <hello@makedo.in>
 */
class MKDO_Admin_Notices{

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
	 * Add taxonomies as a notice
	 */
	public function show_taxonomy_admin_notices() {
	
		global $pagenow; global $typenow;
		
		/* get the current admin page */
		$current_admin_page = $pagenow;
		
		/* check this is the post listing page for this post type */
		if( $current_admin_page == 'edit.php' ) {
			
			/* get all the taxonomies for this post type */
			$taxonomies = get_object_taxonomies( $typenow, 'objects' );
			/* remove post formats */
			unset( $taxonomies[ 'post_format' ] );
			unset( $taxonomies[ 'post_status' ] );
			unset( $taxonomies[ 'ef_editorial_meta' ] );
			unset( $taxonomies[ 'following_users' ] );
			unset( $taxonomies[ 'ef_usergroup' ] );
			
			/* check we have taxonomies to show */
			if( ! empty( $taxonomies ) ) {
			?>
			<div class="updated taxonomies-notice">
				<h3 class="tax-title">Taxonomies:</h3>
				
				<ul class="tax-list">
				<?php
					
					/* loop through each taxonomy */
					foreach( $taxonomies as $tax ) {
						//echo $tax->name;//'<pre>'; var_dump( $tax ); echo '</pre>';
						?>
						<li class="<?php echo esc_attr( sanitize_title( $tax->name ) ); ?>">
							<span class="dashicons-before dashicons-category"></span>
							<a href="<?php echo admin_url( 'edit-tags.php?taxonomy=' . $tax->name ); ?>"><?php echo esc_html( $tax->labels->name ); ?></a>
						</li>
						<?php
						
					} // end loop through taxonomies
					
				?>
				</ul>
			</div>
			<?php
			}
		}
	}

	/**
	 * Tree page view switcher as a notice
	 */
	public function show_tree_page_view_switcher() {

		if ( defined('CMS_TPV_URL') && isset( $_GET['post_type'] ) && $_GET['post_type'] == 'page' ) {
			?>
			<div class="updated view-notice">
				<h3 class="view-title">View:</h3>
				
				<ul class="view-list">
				<?php
					if( isset( $_GET['page'] ) && $_GET['page'] == 'cms-tpv-page-page' ) {
						?>
							<li class="">
								<span class="dashicons-before dashicons-editor-justify"></span>
								<a href="<?php echo admin_url( 'edit.php?post_type=page' ); ?>">Standard View</a>
							</li>
						<?php
					}
					else {
						?>
							<li class="">
								<span class="dashicons-before dashicons-networking"></span>
								<a href="<?php echo admin_url( 'edit.php?post_type=page&page=cms-tpv-page-page' ); ?>">Tree View</a>
							</li>
						<?php
					}
				?>
				</ul>
			</div>
			<?php
		}

	}
}