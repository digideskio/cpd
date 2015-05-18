<?php

// Exit if accessed directly
defined( 'ABSPATH' ) || exit;

if( !class_exists( 'CPD_Comments_UI' ) ) {

/**
 * Comments UI
 *
 * Front End Comment Rendering
 *
 * @package    CPD
 * @subpackage CPD/public
 * @author     Make Do <hello@makedo.in>
 */
class CPD_Comments_UI {

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
	 * Register new comment form fields for logged in users
	 *
	 * @param      string    $commenter
	 * @param      string    $user_identity
	 */
	function add_comment_field_score( $commenter, $user_identity )
	{	
		$comment_fields = array(
			'score' 	=> 	'<div class="span4"><p class="cpd-comment-score"><label for="score">Score <span class="required">*</span></label><input class="span4" id="score" name="score" type="text" value="" size="30" aria-required="true"/></p></div><div class="clearfix"></div>'
		);

		// Get the current user
		$current_user = wp_get_current_user();

		// If they are not a user, return
		if ( !($current_user instanceof WP_User) )
			return;

		// Check to make sure they are a supervisor
		if( in_array( 'supervisor', $current_user->roles ) )
		{
			// Add the additional form fields
			foreach( $comment_fields as $comment_fields_html ){
				echo $comment_fields_html . "\n";
			}
		}
	}

	/** 
	 * Ensure that 'score' is a required field for supervisors
	 *
	 * @param      array    $commentdata 	The comment data
	 */
	function verify_comment_field_score( $commentdata ) 
	{
		// Get the current user
		$current_user = wp_get_current_user();

		// If they are not a user, return
		if ( !($current_user instanceof WP_User) ) {
			return;
		}

		// Check to make sure they are a supervisor
		if( in_array( 'supervisor', $current_user->roles ) )
		{
			if ( !isset( $_POST['score'] ) || empty( $_POST['score'] ) ) {

				wp_die( __( 'Error: please fill out the required field (Score).', $this->text_domain ) );
			}
		}

	    return $commentdata;
	}

	/**
	 * Save the submitted data
	 */
	function add_comment_field_score_meta( $comment_id ) 
	{
		if( isset( $_POST['score'] ) )
		{
			$score = wp_filter_nohtml_kses( $_POST['score'] );
			add_comment_meta( $comment_id, 'score', $score, false );
		}
	}

	/**
	 * Filter comment to show score on GUI
	 *
	 * @param      string    $comments     the comment data
	 */
	function render_comment_field_score( $comments = '' )
	{
		foreach ( $comments as $comment ) 
		{
			$score = get_comment_meta( $comment->comment_ID, 'score', true );

			if( !empty( $score ) )
			{
				$comment->comment_content .= "\r\n" . '<p><strong>Score: ';
				$comment->comment_content .=  get_comment_meta( $comment->comment_ID, 'score', true );
				$comment->comment_content .= '</strong></p>';
			}
			
		}
		return $comments;
	}
}
}
