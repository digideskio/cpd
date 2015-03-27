<?php
	$current_user 	= 	wp_get_current_user();
	$supervisors 	=	get_user_meta( $current_user->ID, 'cpd_related_supervisors', TRUE );
	$name 			= 	$current_user->user_firstname . ' ' . $current_user->user_lastname;
	if( empty( trim( $name ) ) ) {
		$name = $current_user->display_name;
	}
?>

<p>
	Welcome <?php echo $name;?> to your Journal.
</p>
<ul class="tax-list">
	<li>
		<span class="dashicons-before dashicons-welcome-write-blog"></span> 
		<a href="<?php echo home_url() . '/wp-admin/post-new.php?post_type=post';?>">
			Add a new journal entry
		</a>
	</li>
	<li>
		<span class="dashicons-before dashicons-book"></span> 
		<a href="<?php echo home_url() . '/wp-admin/edit.php';?>">
			Manage your journal entries
		</a>
	</li>
	<li>
		<span class="dashicons-before dashicons-admin-page"></span> 
		<a href="<?php echo home_url() . '/wp-admin/edit.php?post_type=page';?>">
			Manage your pages
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

	if( is_array( $supervisors ) && !empty( $supervisors ) ) {
		if( count( $supervisors ) > 1 ) {
			?>
				<p>
					Your supervisors are:
				</p>
			<?php
		}
		else{
			?>
				<p>
					Your supervisor is:
				</p>
			<?php
		}

		?>
			<ul class="tax-list">
				<?php
					foreach( $supervisors as $supervisor_id ) {
						$supervisor = get_user_by( 'id', $supervisor_id );
						$name = $supervisor->user_firstname . ' ' . $supervisor->user_lastname;
						if( empty( trim( $name ) ) ) {
							$name = $supervisor->display_name;
						}
						?>
							<li>
								<span class="dashicons-before dashicons-businessman"></span> 
								<a href="mailto:<?php echo $supervisor->user_email;?>">
									<?php echo $name;?>
								</a>
							</li>
						<?php
					} 
				?>
			</ul>
		<?php
	}

?>

