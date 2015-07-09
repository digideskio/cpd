<?php
	$custom_logo_location = plugin_dir_url( __FILE__ ) . 'img/admin-logo.png';
?>
<style type="text/css">
	/*#wpadminbar .ab-icon, #wpadminbar .ab-item:before, */
	#wpadminbar>#wp-toolbar>#wp-admin-bar-root-default #wp-admin-bar-aspire-logo .ab-icon { 
		background-image: url(<?php echo $custom_logo_location;?>) !important; 
		background-position: 0 6px;
		background-repeat: no-repeat; !important;
		width: 20px;
		height: 20px;
		background-size: 20px, 20px;
	}
	#wpadminbar>#wp-toolbar>#wp-admin-bar-root-default .ab-icon:before {
		content: '';
	}
	#wpadminbar #wp-admin-bar-wp-logo.hover > .ab-item .ab-icon {
		background-position: 0 0;
	}
</style>