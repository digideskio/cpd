<?php
/**
 * Template to render Welcome Dashboard Widget for Admins
 */

	$user_id 			= get_current_user_id();
	$is_elevated_user 	= get_user_meta( $user_id, 'elevated_user', TRUE ) == '1';
?>

<p>
	Welcome to the <strong>Default Template</strong> dashboard. This Template is the default from which all other Templates and Journals are built. 
</p>
<div class="alert alert-warning">
	<p>
		<strong>NOTE:</strong>  All settings and content you create (including the theme) will be copied accross to new Templates and Journals created within this network.
	</p>
</div>
<ul class="tax-list">
	<li>
		<span class="dashicons-before dashicons-admin-appearance"></span> 
		<a href="<?php echo home_url() . '/wp-admin/themes.php';?>">
			Manage Template look and feel
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