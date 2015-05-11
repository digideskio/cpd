<?php
/**
 * Template to render Welcome Dashboard Widget for Network Admins
 */

	$user_id 			= get_current_user_id();
	$is_elevated_user 	= get_user_meta( $user_id, 'elevated_user', TRUE ) == '1';
?>

<p>
	Welcome to the network settings dashboard. You can perform the following tasks:
</p>
<ul class="tax-list">
	<?php
		if( is_super_admin() ) {
			?>
				<li>
					<span class="dashicons-before dashicons-groups"></span> 
					<a href="<?php echo network_admin_url( 'users.php' );?>">
						Manage Supervisors and Participants
					</a>
				</li>
				<li>
					<span class="dashicons-before dashicons-book"></span> 
					<a href="<?php echo network_admin_url( 'sites.php' );?>">
						Manage all Journals
					</a>
				</li>
				<li>
					<span class="dashicons-before dashicons-admin-site"></span> 
					<a href="<?php echo network_admin_url( 'settings.php?page=cpd_settings' );?>">
						Manage Journal Defaults
					</a>
				</li>
				<li>
					<span class="dashicons-before dashicons-admin-plugins"></span> 
					<a href="<?php echo network_admin_url( 'plugins.php' );?>">
						Manage plugins
					</a>
				</li>
				<li>
					<br/>
					<span class="dashicons-before dashicons-undo"></span> 
					<a href="<?php echo home_url() . '/wp-admin/';?>">
						Manage main site
					</a>
				</li>

				
			<?php
		}
	?>
</ul>