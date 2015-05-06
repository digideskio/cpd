<?php
/**
 * Template to render the CPD Content Menu
 *
 * @hook 	cpd_content_menu_before_screen_output 	Render before screen output
 * @hook 	cpd_content_menu_before_widgets 		Render before the widgets
 * @hook 	cpd_content_menu_render_widgets			Render the widgets
 * @hook 	cpd_content_menu_after_widgets			Render after the widgets
 * @hook 	cpd_content_menu_after_screen_output	Render after screen output
 * 
 * @since    2.0.0
 */

$menu_title 			= __( 'Journal Content', $this->text_domain );
$menu_description 		= __( 'Below are your journals content types. They are grouped here for ease of access to allow you to quickly add or edit content.', $this->text_domain );

// Load WordPress dashboard API
require_once( ABSPATH . 'wp-admin/includes/dashboard.php' );

wp_enqueue_script( 'dashboard' );

if ( current_user_can( 'edit_theme_options' ) ){
	wp_enqueue_script( 'customize-loader' );
}

if ( current_user_can( 'install_plugins' ) ){
	wp_enqueue_script( 'plugin-install' );
}

if ( current_user_can( 'upload_files' ) ){
	wp_enqueue_script( 'media-upload' );
}

add_thickbox();

if ( wp_is_mobile() ) {
	wp_enqueue_script( 'jquery-touch-punch' );
}

$title 			= __( 'Content', $this->text_domain );
$parent_file 	= 'admin.php';

$screen 		= get_current_screen();
$screen_base 	= $screen->base;

?>

<div class="wrap">
	
	<h2><?php echo $menu_title;?></h2>
	<p><?php echo $menu_description;?></p>
	
	<?php
		
		do_action( 'cpd_content_menu_before_screen_output', $screen_base );

		do_action( 'cpd_content_menu_before_widgets' );

		do_action( 'cpd_content_menu_render_widgets' );
		
	?>
	
	<div class="clear clearfix"></div>
	<?php

		do_action( 'cpd_content_menu_after_widgets' );	
	?>

	<div id="dashboard-widgets-wrap">
		<?php 
			wp_dashboard();
		 ?>
	</div>

	<?php 
		do_action( 'cpd_content_menu_after_screen_output', $screen_base );
	?>
	
	<div class="clearfix clear"></div>
</div>
<div class="clearfix clear"></div>