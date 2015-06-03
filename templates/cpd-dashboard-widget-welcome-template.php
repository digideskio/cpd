<?php
/**
 * Template to render Welcome Dashboard Widget for Admins
 */

	$user_id 			= get_current_user_id();
	$is_elevated_user 	= get_user_meta( $user_id, 'elevated_user', TRUE ) == '1';
?>

<p>
	Welcome to the <strong>Template</strong> dashboard. This Template can be chosen when creating a new participant Journal.
</p>
<div class="alert alert-warning">
	<p>
		<strong>NOTE:</strong>  All settings and content you create will be copied accross to the newly created Journal.
	</p>
</div>
<ul class="tax-list">
	<li>
		<span class="dashicons-before dashicons-admin-appearance"></span> 
		<a href="<?php echo home_url() . '/wp-admin/themes.php';?>">
			Manage Journal look and feel
		</a>
	</li>

	<li>
		<span class="dashicons-before dashicons-admin-page"></span> 
		<a href="<?php echo home_url() . '/wp-admin/admin.php?page=cpd_content_menu';?>">
			Set default content
		</a>
	</li>

	<li>
		<span class="dashicons-before dashicons-shield"></span> 
		<a href="<?php echo home_url() . '/wp-admin/admin.php?page=cpd_settings_privacy';?>">
			Set privacy settings
		</a>
	</li>
</ul>