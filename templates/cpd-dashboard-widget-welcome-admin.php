<?php
/**
 * Template to render Welcome Dashboard Widget for Admins
 */

	$user_id 			= get_current_user_id();
	$is_elevated_user 	= get_user_meta( $user_id, 'elevated_user', TRUE ) == '1';
?>

<p>
	Welcome to the admin dashboard. You can perform the following tasks:
</p>
<ul class="tax-list">
	<?php
		if( is_super_admin() ) {
			?>
				<li>
					<span class="dashicons-before dashicons-groups"></span> 
					<a href="<?php echo network_admin_url();?>">
						Manage Supervisors and Participants
					</a>
				</li>
			<?php
		}
		if( $is_elevated_user ) {
			?>	
				<li>
					<span class="dashicons-before dashicons-admin-appearance"></span> 
					<a href="<?php echo home_url() . '/wp-admin/themes.php';?>">
						Manage Journal look and feel
					</a>
				</li>

				<li>
					<span class="dashicons-before dashicons-admin-plugins"></span> 
					<a href="<?php echo home_url() . '/wp-admin/plugins.php';?>">
						Manage Journal plugins
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
					<a href="<?php echo home_url() . '/wp-admin/setting.php';?>">
						Alter Journal settings
					</a>
				</li>

			<?php
		}

		if( $is_elevated_user || is_super_admin() ) {
			?>
				<li>
					<span class="dashicons-before dashicons-admin-users"></span> 
					<a href="<?php echo home_url() . '/wp-admin/users.php';?>">
						Manage Journal users
					</a>
				</li>
			<?php
		}

		if( is_super_admin() ) {
			?>
				<li>
					<span class="dashicons-before dashicons-admin-site"></span> 
					<a href="<?php echo home_url() . '/wp-admin/network';?>">
						Manage global settings
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