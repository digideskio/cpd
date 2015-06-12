<?php

// Exit if accessed directly
defined( 'ABSPATH' ) || exit;

if ( !class_exists( 'CPD_CPT_Assessment' ) ) {

/**
 * Admin Scripts
 *
 * Load Admin Scripts
 *
 * @package    CPD
 * @subpackage CPD/admin
 * @author     Make Do <hello@makedo.in>
 */
class CPD_CPT_Assessment {

	private static $instance = null;
	private $text_domain;

	private $dash_icon;
	private $name_singular;
	private $name_plural;
	private $cpt_name;
	private $args;
	private $slug;
	private $image_metabox_title;
	private $menu_postition;
	private $show_in_menu;

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

		$this->args        =  array(
			'cpt_name'     => 'assessment',
			'dash_icon'    => 'dashicons-yes',
			'name_singular'   => 'Assesment',
			'name_plural'    => 'Assessments',
			'slug'      => 'assessments',
			'image_metabox_title' => '',
			'menu_postition'  => 20,
			'post_type_args'  => array(),
			'show_in_menu'   => TRUE,
		);

		$this->cpt_name       =  $this->args[ 'cpt_name'    ];
		$this->dash_icon       =  $this->args[ 'dash_icon'   ];
		$this->name_singular      =  $this->args[ 'name_singular'  ];
		$this->name_plural       =  $this->args[ 'name_plural'   ];
		$this->slug        =  $this->args[ 'slug'     ];
		$this->image_metabox_title     =  $this->args[ 'image_metabox_title' ];
		$this->menu_postition      =  $this->args[ 'menu_postition'  ];
		$this->show_in_menu      =  $this->args[ 'show_in_menu'   ];

		// Lets check whether our custom content menu has been created
		if ( $this->show_in_menu && class_exists( 'MKDO_Admin' ) && $this->use_mkdo_menu ) {
			// Set this post type to show in the custom content menu
			$this->show_in_menu      = 'mkdo_content_menu';
		}

		$post_type_args       = array(
			'description'   =>  '',
			'public'    =>  TRUE,
			'publicly_queryable' =>  TRUE,
			'show_in_nav_menus'  =>  TRUE,
			'show_in_admin_bar'  =>  TRUE,
			'exclude_from_search' =>  FALSE,
			'show_ui'    =>  TRUE,
			'show_in_menu'   =>  'cpd_content_menu',
			'can_export'   =>  TRUE,
			'delete_with_user'  =>  TRUE,
			'hierarchical'   =>  FALSE,
			'has_archive'   =>  TRUE,
			'menu_icon'    =>  $this->dash_icon,
			'query_var'    =>  $this->cpt_name,
			'menu_position'   =>  $this->menu_postition,

			'rewrite'     =>  array(
				'slug' => $this->slug
			),

			'supports'     =>  array(
				'title',
				'editor',
				// 'author',
				'thumbnail',
				// 'excerpt',
				// 'trackbacks',
				// 'custom-fields',
				// 'comments',
				// 'revisions',
				'page-attributes',
				// 'post-formats'
			),

			'label'     => __( $this->name_plural, $this->text_domain  ),
			'labels'     => array(
				'name'     => __( $this->name_plural,               $this->text_domain  ),
				'singular_name'   => __( $this->name_singular,              $this->text_domain  ),
				'menu_name'    => __( $this->name_plural,               $this->text_domain  ),
				'name_admin_bar'  => __( $this->name_plural,               $this->text_domain  ),
				'add_new'    => __( 'Add New',                 $this->text_domain  ),
				'add_new_item'   => __( 'Add New '     . $this->name_singular,       $this->text_domain  ),
				'edit_item'    => __( 'Edit '      . $this->name_singular,       $this->text_domain  ),
				'new_item'    => __( 'New '      . $this->name_singular,       $this->text_domain  ),
				'view_item'    => __( 'View '      . $this->name_singular,       $this->text_domain  ),
				'search_items'   => __( 'Search '    . $this->name_plural,        $this->text_domain  ),
				'not_found'    => __( 'No '      . $this->name_plural  .  ' found',    $this->text_domain  ),
				'not_found_in_trash' => __( 'No '      . $this->name_plural  .  ' found in trash',  $this->text_domain  ),
			)
		);

		$this->args['post_type_args']    =  array_merge( $post_type_args, $this->args[ 'post_type_args'] );
	}

	/**
	 * Set the text domain
	 *
	 * @param string  $text_domain The text domain of the plugin.
	 */
	public function set_text_domain( $text_domain ) {
		$this->text_domain = $text_domain;
	}

	/**
	 * Register the CPT
	 */
	public function register_post_type() {
		$blog_id = get_current_blog_id();

		if ( SITE_ID_CURRENT_SITE != $blog_id ) {
			register_post_type( $this->cpt_name, $this->args['post_type_args'] );
		}

	}

	/**
	 * Move advanced metaboxes above the editor
	 */
	public function move_advanced_metaboxes_above_editor() {

		global $post, $wp_meta_boxes;

		$screen = get_current_screen();

		if ( $screen->id == $this->cpt_name ) {
			echo '<br/>';
			do_meta_boxes( get_current_screen(), 'advanced', $post );
			unset( $wp_meta_boxes[get_post_type( $post )]['advanced'] );
		}
	}

	/**
	 * Change the name of the featured image meta box so it is more relevent to the plugin
	 */
	public function set_featured_image_metabox_title() {

		remove_meta_box( 'postimagediv', $this->cpt_name, 'side' );

		if ( empty( $this->image_metabox_title ) ) {
			add_meta_box( 'postimagediv', __( $this->name_singular . ' Image' ), 'post_thumbnail_meta_box', $this->cpt_name, 'side', 'default' );
		}
		else {
			add_meta_box( 'postimagediv', __( $this->image_metabox_title ), 'post_thumbnail_meta_box', $this->cpt_name, 'side', 'default' );
		}
	}

	/**
	 * Insert text above the title
	 */
	public function add_title_helper_text() {

		global $post, $wp_meta_boxes;

		$html = '<h3>Assessment Title</h3>';
		$html .= '<p class="cmb_metabox_description">The title should be the name of the assessment.</p>';

		if ( !empty( $html ) ) {
			$screen = get_current_screen();

			if ( $screen->id == $this->cpt_name ) {
				echo $html;
			}
		}
	}

	/**
	 * Insert text above the editor
	 */
	public function add_editor_helper_text() {

		global $post, $wp_meta_boxes;


		$html = '<h2>Response</h2>';
		$html .= '<div class="cmb_metabox_description">';
		$html .= '<p>General response to the assessment. When responding remember to detail the following:</p>';
		$html .= '<ul>';
		$html .= '<li> - In the area that you work, <strong>why</strong> do you need to apply this knowlege and understanding?</li>';
		$html .= '<li> - <strong>What</strong> is it that you need to understand?</li>';
		$html .= '<li> - <strong>How</strong> did you apply this knowlege?</li>';
		$html .= '</ul>';
		$html .= '<p>More information can be provided against each critera in the \'Critera\' section (if they have been provided).</p>';
		$html .= '</div>';

		if ( !empty( $html ) ) {
			$screen = get_current_screen();

			if ( $screen->id == $this->cpt_name ) {
				echo $html;
			}
		}
	}

	/**
	 * Fallback template for the single CPT post type
	 */
	public function fallback_template_single( $template ) {

		$template_parts  = explode( '/', $template );
		$template_end   = end( $template_parts );

		if ( is_singular( $this->cpt_name ) && ( $template_end == 'index.php' || $template_end == 'single.php' ) ) {
			return plugin_dir_path( __FILE__ ) . '../templates/single-' . $this->cpt_name . '.php';
		}

		return $template;
	}

	/**
	 * Fallback template for the archive CPT post type
	 */
	public function fallback_template_archive( $template ) {

		$template_parts  = explode( '/', $template );
		$template_end   = end( $template_parts );

		if ( ( is_tax( 'competency-category' ) || is_post_type_archive( $this->cpt_name ) ) && ( $template_end == 'index.php' || $template_end == 'archive.php' ) ) {
			return plugin_dir_path( __FILE__ ) . '../templates/archive-' . $this->cpt_name . '.php';
		}

		return $template;
	}

	/**
	 * Prevent submission edits
	 *
	 * @param int     $post_id the post id
	 * @param object  $data    the data
	 */
	public function prevent_assessment_publish( $post_id, $data ) {

		// Target only 'assessment' post types:
		if ( get_post_type( $post_id ) !== 'assessment' ) {
			return;
		}

		$submitted    =  get_post_meta( $post_id, '_cpd_submit', TRUE );
		$user_id      =  get_current_user_id();
		$user_type    =  get_user_meta( $user_id, 'cpd_role', TRUE );

		// Halt if the post has been submitted
		if ( $user_type == 'participant' && $submitted ) {
			wp_die( __( "This assessment has been submitted, and is no longer editable. If you need to edit the assessment please contact your <strong>supervisor</strong>." ) );
		}

	}

	public function prevent_assessment_publish_button() {

		$post_id = -1;
		$submitted = FALSE;
		if ( isset( $_GET['post'] ) ) {
			$post_id   = $_GET['post'];
			$submitted = get_post_meta( $post_id, '_cpd_submit', TRUE );
		}
		$user_id      =  get_current_user_id();
		$user_type    =  get_user_meta( $user_id, 'cpd_role', TRUE );

		// Halt if the post has been submitted
		if ( $user_type == 'participant' && $submitted ) {

			remove_meta_box( 'submitdiv', 'assessment' , 'side' );
			add_meta_box(
				'submitdivmod',
				__( 'Publish' ),
				array( $this, 'prevent_assessment_publish_box' ),
				'assessment',
				'side',
				'core'
			);
		}
	}

	public function prevent_assessment_publish_box() {
		_e( "This assessment has been submitted, and is no longer editable.<br/><br/>If you need to edit the assessment please contact your <strong>supervisor</strong>." );
	}
}
}