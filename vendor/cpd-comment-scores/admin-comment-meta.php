<?php
/**
 * @package CPD Comment Scores
 */

/**
 * 
 * @since  		1.0.0
 * 
 * Add the score to the bottom of the comment when previewed
 * 
 */
function cpdcs_meta_row_action( $actions ) 
{
	global $comment, $cpd;

	// Output the score if it has been set
	$score = get_comment_meta( $comment->comment_ID, 'score', true );

	if( !empty( $score ) )
	{
		echo '<div class="comment-meta"><strong>Score: ';
		echo get_comment_meta( $comment->comment_ID, 'score', true );
		echo '</strong></div>';
	}

	// Disable edit options if submitted by a supervisor
	$comment_author = get_userdatabylogin( get_comment_author( $comment_id ) );

	// If the CPD Journal plugin isnt running, return
	if( !is_object( $cpd ) )
		return $actions;

	// If they are not a user, return
	if ( !( $comment_author instanceof WP_User ) )
		return $actions;

	// If the person logged in is the supervisor, return
	if( wp_get_current_user() == $comment_author )
		return $actions;

	// Check to make sure they are a supervisor
	if( in_array( 'supervisor', $comment_author->roles ) )
	{
		$supervisors = $cpd->get_supervisors( wp_get_current_user()->ID );

		// If the author of the comment is a supervisor, and they are associated with the participant, prevent edit of the content
		foreach ( $supervisors as $supervisor )
		{
			if( $supervisor['ID'] == $comment_author->id )
			{
				unset( $actions['quickedit'], $actions['edit'], $actions['spam'], $actions['unapprove'], $actions['trash'] );
			}
		}
	}

	return $actions;
}
add_filter( 'comment_row_actions', 'cpdcs_meta_row_action', 11, 1 );



/**
 * 
 * @since  		1.0.0
 * 
 * Add the score column
 * 
 */
function cpdcs_comment_columns( $columns )
{
	$columns['score'] = 'Assignment score';

	return $columns;
}
add_filter( 'manage_edit-comments_columns', 'cpdcs_comment_columns' );




/**
 * 
 * @since  		1.0.0
 * 
 * Put data in the score column
 * 
 */
function cpdcs_comment_column( $column, $comment_ID )
{
	if ( 'score' == $column ) {
		echo '<strong>' . get_comment_meta( $comment_ID, 'score', true ) . '<strong>';
	}
}
add_filter( 'manage_comments_custom_column', 'cpdcs_comment_column', 10, 2 );




/**
 * 
 * @since  		1.0.0
 * 
 * Add a meta box to the comment
 * 
 */
function cpdcs_add_meta_box()
{
	// Get the current user
	$current_user = wp_get_current_user();

	// If they are not a user, return
	if ( !($current_user instanceof WP_User) )
		return;

	// Check to make sure they are a supervisor
	if( in_array( 'supervisor', $current_user->roles ) )
	{
		add_meta_box( 'cpdcs_score', 'Assignment score', 'cpdcs_meta_box', 'comment', 'normal', 'high' );
	}
}
add_action( 'add_meta_boxes_comment', 'cpdcs_add_meta_box' );



/**
 * 
 * @since  		1.0.0
 * 
 * Render the comment meta box
 * 
 */
function cpdcs_meta_box( $comment )
{
	$score = get_comment_meta( $comment->comment_ID, 'score', true );
	wp_nonce_field( 'cpdcs_score_nonce', 'cpdcs_score_nonce', false );
	?>
	<p>
		<label for="score">Score</label>;
		<input type="text" id="score" name="score" value="<?php echo esc_attr( $score ); ?>" class="widefat"/>
	</p>
	<?php
}


/**
 * 
 * @since  		1.0.0
 * 
 * Save data from the meta box
 * 
 */
function cpdcs_meta_box_handle_data( $comment_content ) 
{
	if( isset( $_POST['score'] ) )
	{
		$comment_id = $_POST['c'];
		$score = wp_filter_nohtml_kses( $_POST['score'] );
		update_comment_meta( $comment_id, 'score', $score );
	}
	return $comment_content;
}
add_filter( 'comment_save_pre', 'cpdcs_meta_box_handle_data' );

/**
 * 
 * @since  		1.0.0
 * 
 * Render the comment meta box
 * 
 */
function cpdcs_prevent_supervisor_edits()
{
	global $cpd;

	$screen = get_current_screen();

	if( $screen->id == 'comment' )
	{
		$comment_id = $_GET['c'];
		$comment_author = get_userdatabylogin( get_comment_author( $comment_id ) );

		// If the CPD Journal plugin isnt running, return
		if( !is_object( $cpd ) )
			return;

		// If they are not a user, return
		if ( !( $comment_author instanceof WP_User ) )
			return;

		// If the person logged in is the supervisor, return
		if( wp_get_current_user() == $comment_author )
			return;

		// Check to make sure they are a supervisor
		if( in_array( 'supervisor', $comment_author->roles ) )
		{
			$supervisors = $cpd->get_supervisors( wp_get_current_user()->ID );

			// If the author of the comment is a supervisor, and they are associated with the participant, prevent edit of the content
			foreach ( $supervisors as $supervisor )
			{
				if( $supervisor['ID'] == $comment_author->id )
				{
					wp_die( 'You do not have permission to edit this post. To make changes please contact your supervisor.', 'Insufficient permissions' );
				}
			}
		}
	}
}
add_action( 'current_screen','cpdcs_prevent_supervisor_edits', 10, 1 );


/**
 * 
 * @since  		1.0.0
 * 
 * Force users to be logged in to leave a comment
 * 
 */
function cpdcs_prevent_comments_if_not_logged_in()
{
	update_option( 'comment_registration', 1 );
}
add_action( 'init','cpdcs_prevent_comments_if_not_logged_in', 10, 1 );


/**
 * 
 * @since  		1.0.0
 * 
 * Use TinyMCE editor for comment form
 * 
 */
function cpdcs_tinymce_comment_form ( $args ) {
    
    ob_start();
    wp_editor( '', 'comment', array('tinymce') );
    
    $args['comment_field'] = ob_get_clean();
    
    return $args;
}
add_filter( 'comment_form_defaults', 'cpdcs_tinymce_comment_form' );


/**
 * 
 * @since  		1.0.0
 * 
 * Add styles to TinyMCE so we can see the editable area
 * 
 */
function cpdcs_add_styles() {

    wp_register_style( 'cpdcs-styles', plugins_url( 'styles.css', __FILE__ ) );
    wp_enqueue_style( 'cpdcs-styles' );
}
add_action( 'wp_enqueue_scripts', 'cpdcs_add_styles' );

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
function cpdcs_remove_admin_menus() 
{
	$user = wp_get_current_user();

	$user_type = get_user_meta( $user->ID, 'cpd_role', true );

	if( $user_type == 'participant' )
	{
		remove_submenu_page( 'options-general.php', 'options-discussion.php' );
	}
}
add_action( 'admin_menu', 'cpdcs_remove_admin_menus' );