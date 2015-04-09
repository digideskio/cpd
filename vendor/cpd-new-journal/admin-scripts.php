<?php
/**
 * @package CPD New Journal
 */

/**
 * 
 * @since  		1.0.0
 * 
 * Add scripts and styles to the admin boxes
 * 
 */
function cpdnj_enqueue_scripts( $hook ) 
{
	// Custom styles
	wp_enqueue_style( 'cpdnj_admin_styles', plugins_url( 'assets/css/styles.css' , __FILE__ ) );

	// Custom scripts
	wp_enqueue_script( 'cpdnj_admin_scripts', plugins_url( 'assets/js/scripts.min.js' , __FILE__ ), array( 'jquery' ), '1.0', true );
}
add_action( 'admin_enqueue_scripts', 'cpdnj_enqueue_scripts' );
?>