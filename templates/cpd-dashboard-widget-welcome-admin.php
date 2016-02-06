<?php
/**
 * Template to render Welcome Dashboard Widget for Admins
 */

	$user_id 			= get_current_user_id();
	$is_elevated_user 	= get_user_meta( $user_id, 'elevated_user', TRUE ) == '1';
	$journal_name       = 'journal';

	$blog_id = get_current_blog_id();

	if( SITE_ID_CURRENT_SITE == $blog_id ) {
		$journal_name = 'site';
	}
?>

<p>
	Welcome to the <strong>admin dashboard</strong>. You can perform the following tasks:
</p>

<ul class="tax-list">
	<?php
		?>
		<li>
			<span class="dashicons-before dashicons-admin-page"></span>
			<a href="<?php echo home_url() . '/wp-admin/admin.php?page=cpd_content_menu';?>">
				Manage <?php echo $journal_name;?> content
			</a>
		</li>
		<li>
			<span class="dashicons-before dashicons-admin-comments"></span>
			<a href="<?php echo home_url() . '/wp-admin/edit-comments.php';?>">
				Manage <?php echo $journal_name;?> comments
			</a>
		</li>
		<?php
		if( $is_elevated_user ) {
			?>
				<li>
					<span class="dashicons-before dashicons-admin-appearance"></span>
					<a href="<?php echo home_url() . '/wp-admin/themes.php';?>">
						Manage <?php echo $journal_name;?> look and feel
					</a>
				</li>

				<li>
					<span class="dashicons-before dashicons-admin-plugins"></span>
					<a href="<?php echo home_url() . '/wp-admin/plugins.php';?>">
						Manage <?php echo $journal_name;?> plugins
					</a>
				</li>

				<li>
					<span class="dashicons-before dashicons-admin-tools"></span>
					<a href="<?php echo home_url() . '/wp-admin/tools.php';?>">
						Perform maintainance with web tools
					</a>
				</li>

				<li>
					<span class="dashicons-before dashicons-admin-settings"></span>
					<a href="<?php echo home_url() . '/wp-admin/settings.php';?>">
						Alter <?php echo $journal_name;?> settings
					</a>
				</li>

			<?php
		}

		if( $is_elevated_user || is_super_admin() ) {
			?>
				<!-- <li>
					<span class="dashicons-before dashicons-admin-users"></span>
					<a href="<?php echo home_url() . '/wp-admin/users.php';?>">
						Manage <?php echo $journal_name;?> users
					</a>
				</li> -->
			<?php
		}
		if( is_super_admin() ) {
			?>
			<li>
				<span class="dashicons-before dashicons-groups"></span>
				<a href="<?php echo admin_url( 'users.php?page=cpd_settings_users_participants' );?>">
					Manage Participants
				</a>
			</li>
			<li>
				<span class="dashicons-before dashicons-groups"></span>
				<a href="<?php echo admin_url( 'users.php?page=cpd_settings_users_supervisors' );?>">
					Manage Supervisors
				</a>
			</li>
			<?php
		}

		if( is_super_admin() ) {
			?>
				<li>
					<span class="dashicons-before dashicons-admin-site"></span>
					<a href="<?php echo home_url() . '/wp-admin/network';?>">
						Manage network settings
					</a>
				</li>

				<li>
					<span class="dashicons-before dashicons-book"></span>
					<a href="<?php echo network_admin_url( 'sites.php' );?>">
						Manage all Journals
					</a>
				</li>
			<?php
		}
	?>
</ul>
