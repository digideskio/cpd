<?php
/**
 * @package CPD Comment Scores
 */

/**
 * 
 * @since  		2.0.0
 * 
 * Define additional fields
 * 
 */
global $cpd_comment_fields;
$cpd_comment_fields = array(
	'score' => '<div class="span4"><p class="cpd-comment-score"><label for="score">Score <span class="required">*</span></label><input class="span4" id="score" name="score" type="text" value="" size="30" aria-required="true"/></p></div><div class="clearfix"></div>'
);



/**
 * 
 * @since  		2.0.0
 * 
 * Register new comment form fields
 * 
 */
function cpd_add_comment_fields( $fields )
{
	global $cpd_comment_fields;

	foreach( $cpd_comment_fields as $cpd_comment_field_key => $cpd_comment_fields_html )
	{
		$fields[ $cpd_comment_field_key ] = $cpd_comment_fields_html;
	}
	return $fields;
}
// We only want supervisors to be able to add a score, so commenting out
//add_filter('comment_form_default_fields','cpd_add_comment_fields');



/**
 * 
 * @since  		2.0.0
 * 
 * Register new comment form fields for logged in users
 * 
 */
function cpd_add_comment_fields_logged_in( $commenter, $user_identity )
{	
	global $cpd_comment_fields;

	// Get the current user
	$current_user = wp_get_current_user();

	// If they are not a user, return
	if ( !($current_user instanceof WP_User) )
		return;

	// Check to make sure they are a supervisor
	if( in_array( 'supervisor', $current_user->roles ) )
	{
		// Add the additional form fields
		foreach( $cpd_comment_fields as $cpd_comment_fields_html ){
			echo $cpd_comment_fields_html . "\n";
		}
	}
}
add_action( 'comment_form_logged_in_after', 'cpd_add_comment_fields_logged_in', 10, 2 );




/**
 * 
 * @since  		2.0.0
 * 
 * Ensure that 'score' is a required field for supervisors
 * 
 */
function cpd_verify_comment_meta_data( $commentdata ) 
{
	// Get the current user
	$current_user = wp_get_current_user();

	// If they are not a user, return
	if ( !($current_user instanceof WP_User) )
		return;

	// Check to make sure they are a supervisor
	if( in_array( 'supervisor', $current_user->roles ) )
	{
		if ( ! isset( $_POST['score'] ) )
			wp_die( __( 'Error: please fill the required field (score).' ) );
	}

    return $commentdata;
}
add_filter( 'preprocess_comment', 'cpd_verify_comment_meta_data' );




/**
 * 
 * @since  		2.0.0
 * 
 * Save the submitted data
 * 
 */
function cpd_add_comment_meta( $comment_id ) 
{
	if( isset( $_POST['score'] ) )
	{
		$score = wp_filter_nohtml_kses( $_POST['score'] );
		add_comment_meta( $comment_id, 'score', $score, false );
	}
}
add_action( 'comment_post', 'cpd_add_comment_meta', 1 );




/**
 * 
 * @since  		2.0.0
 * 
 * Filter comment to show score on GUI
 * 
 */
function cpd_show_comment_meta( $comments = '' )
{
	foreach ( $comments as $comment ) 
	{
		$score = get_comment_meta( $comment->comment_ID, 'score', true );

		if( !empty( $score ) )
		{
			$comment->comment_content .= "\r\n" . '<strong>Score: ';
			$comment->comment_content .=  get_comment_meta( $comment->comment_ID, 'score', true );
			$comment->comment_content .= '</strong>';
		}
		
	}
	return $comments;
}
add_filter( 'comments_array', 'cpd_show_comment_meta' );