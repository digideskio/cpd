<?php

// Exit if accessed directly
defined( 'ABSPATH' ) || exit;

if( !class_exists( 'CPD_Comments' ) ) {

/**
 * Comments
 *
 * Handle comments in the back end
 *
 * @package    CPD
 * @subpackage CPD/admin
 * @author     Make Do <hello@makedo.in>
 */
class CPD_Comments {

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
	 * Prevent supervisor from editing the comments
	 */
	public function prevent_participants_editing_supervisor_comments()
	{
		$screen = get_current_screen();

		if( $screen->id == 'comment' )
		{
			$comment_id = $_GET['c'];
			$comment_author = get_user_by( 'login', get_comment_author( $comment_id ) );

			// If they are not a user, return
			if ( !( $comment_author instanceof WP_User ) )
				return;

			// If the person logged in is the supervisor, return
			if( wp_get_current_user() == $comment_author )
				return;

			// Check to make sure they are a supervisor
			if( in_array( 'supervisor', $comment_author->roles ) )
			{
				$supervisors = get_user_meta( wp_get_current_user()->ID, 'cpd_related_supervisors', TRUE );

				// If the author of the comment is a supervisor, and they are associated with the participant, prevent edit of the content
				foreach ( $supervisors as $supervisor )
				{
					if( $supervisor == $comment_author->ID )
					{
						wp_die( __('You do not have permission to edit this post. To make changes please contact your supervisor.' , $this->text_domain ), __( 'Insufficient permissions', $this->text_domain ) );
					}
				}
			}
		}
	}

	/**
	 * Add the score to the bottom of the comment when previewed
	 *
	 * @param      array    $actions       Array of actions
	 */
	public function prevent_participants_editing_supervisor_comments_ui( $actions ) 
	{
		global $comment;

		// Output the score if it has been set
		$score = get_comment_meta( $comment->comment_ID, 'score', true );

		if( !empty( $score ) )
		{
			echo '<div class="comment-meta"><strong>Score: ';
			echo get_comment_meta( $comment->comment_ID, 'score', true );
			echo '</strong></div>';
		}

		$comment_id = $comment->comment_ID;

		// Disable edit options if submitted by a supervisor
		$comment_author = get_user_by( 'login', get_comment_author( $comment_id ) );

		// If they are not a user, return
		if ( !( $comment_author instanceof WP_User ) )
			return $actions;

		// If the person logged in is the supervisor, return
		if( wp_get_current_user() == $comment_author )
			return $actions;

		// Check to make sure they are a supervisor
		if( in_array( 'supervisor', $comment_author->roles ) )
		{
			$supervisors = get_user_meta( wp_get_current_user()->ID, 'cpd_related_supervisors', TRUE );

			// If the author of the comment is a supervisor, and they are associated with the participant, prevent edit of the content
			foreach ( (array)$supervisors as $supervisor )
			{
				if( $supervisor == $comment_author->ID )
				{
					unset( $actions['approve'], $actions['unapprove'], $actions['quickedit'], $actions['edit'], $actions['spam'], $actions['trash'] );
				}
			}
		}

		return $actions;
	}

	/**
	 * Add the score column
	 *
	 * @param      array    $columns       Array of columns
	 */
	public function add_score_column( $columns )
	{
		$columns['score'] = 'Score';

		return $columns;
	}

	/**
	 * Add data to score column
	 *
	 * @param      string    $column       Column name
	 * @param      int    $comment_ID      Column ID
	 */
	public function add_score_column_data( $column, $comment_ID )
	{
		if ( 'score' == $column ) {
			echo '<strong>' . get_comment_meta( $comment_ID, 'score', true ) . '<strong>';
		}
	}

	/**
	 * Add a meta box to the comment
	 */
	public function add_comment_metabox_score()
	{
		// Get the current user
		$current_user = wp_get_current_user();

		// If they are not a user, return
		if ( !($current_user instanceof WP_User) )
			return;

		// Check to make sure they are a supervisor
		if( in_array( 'supervisor', $current_user->roles ) )
		{
			add_meta_box( 'cpd_score', 'Score', array( $this, 'render_comment_metabox_score' ), 'comment', 'normal', 'high' );
		}
	}

	/**
	 * Render the comment meta box
	 *
	 * @param      object    $comment     The comment
	 */
	function render_comment_metabox_score( $comment )
	{
		$score = get_comment_meta( $comment->comment_ID, 'score', true );
		wp_nonce_field( 'cpdcs_score_nonce', 'cpdcs_score_nonce', false );
		?>
		<p>
			<label for="score"><?php _e('Score');?></label>;
			<input type="text" id="score" name="score" value="<?php echo esc_attr( $score ); ?>" class="widefat"/>
		</p>
		<?php
	}

	/**
	 * Save data from the meta box
	 *
	 * @param      string    $comment_content      Comment content
	 */
	public function save_comment_metabox_score( $comment_content ) 
	{
		if( isset( $_POST['score'] ) )
		{
			$comment_id = $_POST['c'];
			$score = wp_filter_nohtml_kses( $_POST['score'] );
			update_comment_meta( $comment_id, 'score', $score );
		}
		return $comment_content;
	}

	/**
	 * Set comment options
	 */
	public function set_comment_options()
	{
		if( get_option( 'comment_registration', NULL ) !== NULL ) {
			update_option( 'comment_registration', TRUE );
		}
		else {
			add_option( 'comment_registration', TRUE );
		}
	}

	/**
	 * Use TinyMCE editor for comment form
	 *
	 * @param      array    $args      array of fields
	 */
	public function enable_comment_tinymce ( $args ) {
	    
	    ob_start();
	    wp_editor( '', 'comment', array('tinymce') );
	    
	    $args['comment_field'] = ob_get_clean();
	    
	    return $args;
	}

	/**
	 * Enable tags in tinyMCE comments
	 *
	 * @param      array    $data      array of data
	 */
	public function enable_comment_tags( $data ) {
		
		global $allowedtags;
		
		$allowedtags['img'] = array('src'=>array(),'alt'=>array());
		
		return $data;
	}

	/**
	 * Enable comments on journal entries
	 * 
	 * @param  ojbect $data Post Data
	 */
	public function comments_on_journal_entries( $data ) {
    	
    	$blog_id = get_current_blog_id();

		if ( SITE_ID_CURRENT_SITE != $blog_id ) {
			if( $data['post_type'] == 'post' ) {
				$data['comment_status'] = 'open';
			}
		}

		return $data;
	}
}
}