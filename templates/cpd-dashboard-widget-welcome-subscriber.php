<?php
/**
 * Template to render Welcome Dashboard Widget for Subscribers
 */
$current_user 	= 	wp_get_current_user();
?>

<p>
	Welcome <strong>Subscriber</strong> to the Journal '<?php echo get_bloginfo( 'title' );?>'. 
</p>
<div class="alert alert-warning">
	<p><strong>NOTE: </strong>As a subscriber, you cannot make changes to this Journal. If you belive that this is an error, please contact a system administrator.</p>
</div>
<?php

	$blog_list 	= get_blogs_of_user( $current_user->ID );
	$blogs 		= array();

	foreach( $blog_list as $blog ) {

		if( $blog->userblog_id != get_current_blog_id() ) {

			switch_to_blog( $blog->userblog_id );

			if ( user_can( $current_user, 'supervisor' ) || user_can( $current_user, 'participant' ) ) {
	        	$blogs[] = $blog;
			}

	        restore_current_blog();
	    }
	}

	if( is_array( $blogs ) && !empty( $blogs ) ) {
		if( count( $blogs ) > 1 ) {
			?>
				<p>
					The journals you manage are:
				</p>
			<?php
		}
		else{
			?>
				<p>
					Your Journal is:
				</p>
			<?php
		}

		?>
			<ul class="tax-list">
				<?php
					foreach( $blogs as $blog ) {
						?>
							<li>
								<span class="dashicons-before dashicons-book"></span> 
								<a href="<?php echo $blog->siteurl . '/wp-admin/';?>">
									<?php echo $blog->blogname;?>
								</a>
							</li>
						<?php
					} 
				?>
			</ul>
		<?php
	}

?>

