<?php
/**
 * Template to render Welcome Dashboard Widget for Supervisors
 */
$current_user 	= 	wp_get_current_user();
$name 			= 	$current_user->user_firstname . ' ' . $current_user->user_lastname;
$name 			= 	trim( $name );
if( empty( $name ) ) {
	$name = $current_user->display_name;
}
?>

<p>
	Welcome <?php echo $name;?> to the Journal '<?php echo get_bloginfo( 'title' );?>'.
</p>
<ul class="tax-list">
	<li>
		<span class="dashicons-before dashicons-format-chat"></span> 
		<a href="<?php echo home_url() . '/wp-admin/edit-comments.php';?>">
			Manage journal feedback
		</a>
	</li>
	<li>
		<span class="dashicons-before dashicons-admin-users"></span> 
		<a href="<?php echo home_url() . '/wp-admin/profile.php';?>">
			Manage your profile
		</a>
	</li>
</ul>

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
					Other Journals you have access to are:
				</p>
			<?php
		}
		else{
			?>
				<p>
					The other Journal you have access to is:
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

