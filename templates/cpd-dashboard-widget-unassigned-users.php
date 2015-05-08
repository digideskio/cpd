<?php
/**
 * Template to render Unassigned Users Dashboard Widget
 */

global $wpdb;

$orphaned_participants 		= 	array();
$redundant_supervisors		= 	array();

$mu_users 					= 	CPD_Users::get_multisite_users();

foreach( $mu_users as $mu_user ) {

	$mu_cpd_role 			= 	get_user_meta( $mu_user->ID, 'cpd_role', TRUE );

	if( $mu_cpd_role == 'participant' ) {
		$mu_user_related_supervisors = get_user_meta( $mu_user->ID, 'cpd_related_supervisors', TRUE );
		if( !is_array( $mu_user_related_supervisors ) || count( $mu_user_related_supervisors ) == 0 ) {
			$orphaned_participants[] = $mu_user;
		}
	}

	if( $mu_cpd_role == 'supervisor' ) {
		$mu_user_related_participants = get_user_meta( $mu_user->ID, 'cpd_related_participants', TRUE );
		if( !is_array( $mu_user_related_participants ) || count( $mu_user_related_participants ) == 0 ) {
			$redundant_supervisors[] = $mu_user;
		}

	}
}
?>
<p><strong>Participants</strong></p>
<?php
if( count( $orphaned_participants ) ) { 
	?>
	<p>The following participants have no supervisor assigned to them:</p>
	<table>
		<tr>
			<th>Name</th>
			<th>Journal</th>
			<th>Dashboard</th>
		</tr> 
		<?php 
			foreach( $orphaned_participants as $participant ) {

				$journal 			= 	get_active_blog_for_user( $participant->ID );
				$edit_url			= 	add_query_arg( array( 'user_id' => $participant->ID ), network_admin_url( 'user-edit.php#cpd_profile' ) );
				$current_scheme		=	is_ssl() ? 'https://' : 'http://';
				$site_url 			= 	$current_scheme . $journal->domain . $journal->path;
				$site_admin_url 	= 	$site_url . 'wp-admin';
				$user 				= 	get_user_by( 'id', $participant->ID );
				$name 				= 	$user->user_firstname . ' ' . $user->user_lastname;
				if( empty( trim( $name ) ) ) {
					$name = $user->display_name;
				}
				?>
				<tr>
					<td>
						<a href="<?php echo $edit_url ?>">
							<?php echo $name; ?>
						</a>
					</td>
					<td>
						<a href="<?php echo $site_url?>">
							<?php echo $site_url; ?>
						</a>
					</td>
					<td>
						<a href="<?php echo $site_admin_url?>">
							Dashboard
						</a>
					</td>
				</tr>
				<?php 
			} 
		?>
	</table>
	<?php 
} 
else {

	?>
	<p>All participants have supervisors assigned to them.</p>
	<?php
}
?>

<p><strong>Supervisors</strong></p>
<?php
if( count( $redundant_supervisors ) ) { 
	?>
	<p>The following supervisors have no participants assigned to them:</p>
	<table>
		<tr>
			<th>Name</th>
		</tr>
		<?php 
			foreach( $redundant_supervisors as $supervisor ) {
				$edit_url			= 	add_query_arg( array( 'user_id' => $supervisor->ID ), network_admin_url( 'user-edit.php#cpd_profile' ) );
				$user 				= 	get_user_by( 'id', $supervisor->ID );
				$name 				= 	$user->user_firstname . ' ' . $user->user_lastname;
				if( empty( trim( $name ) ) ) {
					$name = $user->display_name;
				}
				?>
				<tr>
					<td>
						<a href="<?php echo $edit_url ?>">
							<?php echo $name; ?>
						</a>
					</td>
				</tr>
				<?php
			} 
		?>
	</table>
	<?php
} 
else {
	?>
	<p>All supervisors have pariticpants assigned to them.</p>
	<?php
}
?>