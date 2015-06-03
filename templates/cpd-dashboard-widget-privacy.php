<?php
/**
 * Template to render Privacy Widget
 */

?>
<div>
	<div class="content-description">
		<div class="alert alert-success">
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
	</div>

	<?php

	

	$private_posts = get_posts(
		array(
			'post_type'        => 'any',
			'posts_per_page'   => -1,
			'post_status'      => 'private'
		)
	);

	if( !is_array( $private_posts ) ) {
		$private_posts = array();
	}

	$piece = 'pieces of content that are';
	if( count( $private_posts ) == 1 ) {
		$piece = 'piece of content that is';
	}

	?>
	<p>This Journal contains <strong><?php echo count( $private_posts );?></strong> <?php echo $piece;?> set as <strong>private</strong>.</p>

	<ul class="tax-list">
		<li>
			<span class="dashicons-before dashicons-lock"></span> 
			<a href="admin.php?page=cpd_settings_privacy#private-content-overview">Private content overview</a>
		</li>
		<li>
			<span class="dashicons-before dashicons-welcome-view-site"></span>
			<a href="admin.php?page=cpd_settings_privacy#private-content-who">Who can view private content?</a>
		</li>
	</ul>

	<p><a class="button" href="admin.php?page=cpd_settings_privacy">Manage Privacy Settings</a></p>
	
</div>