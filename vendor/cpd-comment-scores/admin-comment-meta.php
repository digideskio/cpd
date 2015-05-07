<?php
/**
 * @package CPD Comment Scores
 */





/**
 * 
 * @since  		2.0.0
 * 
 * Add the score column
 * 
 */
function cpd_comment_columns( $columns )
{
	$columns['score'] = 'Score';

	return $columns;
}
add_filter( 'manage_edit-comments_columns', 'cpd_comment_columns' );




/**
 * 
 * @since  		2.0.0
 * 
 * Put data in the score column
 * 
 */
function cpd_comment_column( $column, $comment_ID )
{
	if ( 'score' == $column ) {
		echo '<strong>' . get_comment_meta( $comment_ID, 'score', true ) . '<strong>';
	}
}
add_filter( 'manage_comments_custom_column', 'cpd_comment_column', 10, 2 );




/**
 * 
 * @since  		2.0.0
 * 
 * Add a meta box to the comment
 * 
 */
function cpd_add_meta_box()
{
	// Get the current user
	$current_user = wp_get_current_user();

	// If they are not a user, return
	if ( !($current_user instanceof WP_User) )
		return;

	// Check to make sure they are a supervisor
	if( in_array( 'supervisor', $current_user->roles ) )
	{
		add_meta_box( 'cpd_score', 'Score', 'cpd_meta_box', 'comment', 'normal', 'high' );
	}
}
add_action( 'add_meta_boxes_comment', 'cpd_add_meta_box' );



/**
 * 
 * @since  		2.0.0
 * 
 * Render the comment meta box
 * 
 */
function cpd_meta_box( $comment )
{
	$score = get_comment_meta( $comment->comment_ID, 'score', true );
	wp_nonce_field( 'cpd_score_nonce', 'cpd_score_nonce', false );
	?>
	<p>
		<label for="score">Score</label>;
		<input type="text" id="score" name="score" value="<?php echo esc_attr( $score ); ?>" class="widefat"/>
	</p>
	<?php
}


/**
 * 
 * @since  		2.0.0
 * 
 * Save data from the meta box
 * 
 */
function cpd_meta_box_handle_data( $comment_content ) 
{
	if( isset( $_POST['score'] ) )
	{
		$comment_id = $_POST['c'];
		$score = wp_filter_nohtml_kses( $_POST['score'] );
		update_comment_meta( $comment_id, 'score', $score );
	}
	return $comment_content;
}
add_filter( 'comment_save_pre', 'cpd_meta_box_handle_data' );

/**
 * 
 * @since  		2.0.0
 * 
 * Render the comment meta box
 * 
 */
function cpd_prevent_supervisor_edits()
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
			$supervisors = get_user_meta( wp_get_current_user()->ID, 'cpd_related_participants', TRUE );

			// If the author of the comment is a supervisor, and they are associated with the participant, prevent edit of the content
			foreach ( $supervisors as $supervisor )
			{
				if( $supervisor == $comment_author->ID )
				{
					wp_die( 'You do not have permission to edit this post. To make changes please contact your supervisor.', 'Insufficient permissions' );
				}
			}
		}
	}
}
add_action( 'current_screen','cpd_prevent_supervisor_edits', 10, 1 );


/**
 * 
 * @since  		2.0.0
 * 
 * Force users to be logged in to leave a comment
 * 
 */
function cpd_prevent_comments_if_not_logged_in()
{
	if( get_option( 'comment_registration', NULL ) !== NULL ) {
		update_option( 'comment_registration', TRUE );
	}
	else {
		add_option( 'comment_registration', TRUE );
	}
}
add_action( 'init','cpd_prevent_comments_if_not_logged_in', 10, 1 );


/**
 * 
 * @since  		2.0.0
 * 
 * Use TinyMCE editor for comment form
 * 
 */
function cpd_tinymce_comment_form ( $args ) {
    
    ob_start();
    wp_editor( '', 'comment', array('tinymce') );
    
    $args['comment_field'] = ob_get_clean();
    
    return $args;
}
add_filter( 'comment_form_defaults', 'cpd_tinymce_comment_form' );


/**
 * 
 * @since  		2.0.0
 * 
 * Add styles to TinyMCE so we can see the editable area
 * 
 */
function cpd_add_styles() {

    wp_register_style( 'cpd-styles', plugins_url( 'styles.css', __FILE__ ) );
    wp_enqueue_style( 'cpd-styles' );
}
add_action( 'wp_enqueue_scripts', 'cpd_add_styles' );

add_filter('preprocess_comment','fa_allow_tags_in_comments');

function fa_allow_tags_in_comments($data) {
	global $allowedtags;
	$allowedtags['img'] = array('src'=>array(),'alt'=>array());
	return $data;
}


/**
 * 
 * @since  		1.0.1
 * 
 * Prevent participant from disabling comments by removing the menu
 * 
 */
function cpd_remove_admin_menus() 
{
	$user = wp_get_current_user();

	$user_type = get_user_meta( $user->ID, 'cpd_role', true );

	if( $user_type == 'participant' )
	{
		remove_submenu_page( 'options-general.php', 'options-discussion.php' );
	}
}
add_action( 'admin_menu', 'cpd_remove_admin_menus' );