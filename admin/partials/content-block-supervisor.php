<?php
	$current_user 	= 	wp_get_current_user();
	$supervisors 	=	get_user_meta( $current_user->ID, 'cpd_related_participants', TRUE );
	$name 			= 	$current_user->user_firstname . ' ' . $current_user->user_lastname;
	if( empty( trim( $name ) ) ) {
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
		switch_to_blog( $blog->userblog_id );
        
        if ( user_can( $current_user, 'supervisor' ) ) {
        	$blogs[] = $blog;
        }

        restore_current_blog();
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
					Journal you manage is:
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

