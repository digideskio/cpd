<?php
/**
 * Template to render Privacy Widget
 */

?>
<div>
	<div class="content-description alert alert-success">
	<?php 
	$cpd_login_to_view = get_option( 'cpd_login_to_view', NULL );
	if( $cpd_login_to_view == 'true' ) {
		?>
		<p>Visitors must be <strong>logged in</strong> to view this journal.</p>
		<?php
	}
	else {
		?>
		<p><strong>Anyone</strong> can view this journal (including members of the public).</p>
		<?php
	}
	?>
	</div>

	<p><a class="button" href="admin.php?page=cpd_settings_privacy">Manage Privacy Settings</a></p>
	
</div>