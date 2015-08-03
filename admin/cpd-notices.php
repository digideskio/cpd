<?php

// Exit if accessed directly
defined( 'ABSPATH' ) || exit;

if( !class_exists( 'CPD_Notices' ) ) {

/**
 * Notices
 *
 * Notice functionality
 * 
 * @package    CPD
 * @subpackage CPD/admin
 * @author     Make Do <hello@makedo.in>
 */
class CPD_Notices {

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
	 */
	public function __construct() {
		
	}

	/**
	 * Set the text domain
	 *
	 * @param      string    $text_domain       The text domain of the plugin.
	 */
	public function set_text_domain( $text_domain ) { 
		$this->text_domain = $text_domain;
	}

	/**
	 * Add taxonomies as a notice
	 */
	public function add_notice_taxonomy() {
	
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
				<h3 class="tax-title"><?php _e('Taxonomies', $this->text_domain);?>:</h3>
				
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
	public function add_notice_tree_view() {

		if ( defined('CMS_TPV_URL') && isset( $_GET['post_type'] ) && $_GET['post_type'] == 'page' ) {
			?>
			<div class="updated view-notice">
				<h3 class="view-title"><?php _e('View', $this->text_domain);?>:</h3>
				
				<ul class="view-list">
				<?php
					if( isset( $_GET['page'] ) && $_GET['page'] == 'cms-tpv-page-page' ) {
						?>
							<li class="">
								<span class="dashicons-before dashicons-editor-justify"></span>
								<a href="<?php echo admin_url( 'edit.php?post_type=page' ); ?>"><?php _e('Standard View', $this->text_domain);?></a>
							</li>
						<?php
					}
					else {
						?>
							<li class="">
								<span class="dashicons-before dashicons-networking"></span>
								<a href="<?php echo admin_url( 'edit.php?post_type=page&page=cms-tpv-page-page' ); ?>"><?php _e('Tree View', $this->text_domain);?></a>
							</li>
						<?php
					}
				?>
				</ul>
			</div>
			<?php
		}

	}

	/**
	 * Add edting disabled as a notice
	 */
	public function add_notice_editing_disabled() {
	
		global $pagenow; global $typenow; global $post_id;
		
		/* get the current admin page */
		$current_admin_page = $pagenow;

		$post_id = -1;
		$submitted = FALSE;
		if( isset( $_GET['post'] ) ) {
			$post_id   = $_GET['post'];
			$submitted = get_post_meta( $post_id, '_cpd_submit', TRUE );
		}
		$user_id 			= 	get_current_user_id();
		$user_type 			= 	get_user_meta( $user_id, 'cpd_role', TRUE );
		
		/* check this is the post listing page for this post type */
		if( $current_admin_page == 'post.php' && $user_type == 'participant' && $submitted ) {
			
			?>
			<div class="error">
			<p>
			<?php _e('This assessment has been submitted, and is no longer editable. If you need to edit the assessment please contact your', $this->text_domain) .' <strong>'.__('supervisor', $this->text_domain) .'</strong>.';?>
			</p>
			</div>
			<?php
		}
	}
}
}