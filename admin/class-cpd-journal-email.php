<?php
/**
 * The dashboard-specific functionality of the plugin.
 *
 * @link       http://makedo.in
 * @since      1.0.0
 *
 * @package    CPD
 * @subpackage CPD/admin
 */

/**
 * The dashboard-specific functionality of the plugin.
 *
 * Defines the plugin name, version, and enqueue the dashboard-specific stylesheet and JavaScript.
 *
 * @package    CPD
 * @subpackage CPD/admin
 * @author     Make Do <hello@makedo.in>
 */
class CPD_Journal_Email extends MKDO_Class {

	/**
	 * Initialize the class and set its properties.
	 *
	 * @since    1.0.0
	 * @var      string    $instance       The name of this plugin.
	 * @var      string    $version    The version of this plugin.
	 */
	public function __construct( $instance, $version ) {
		parent::__construct( $instance, $version );
	}

	public function set_html_content_type() {
		return 'text/html';
	}

	public function send_mail_on_update( $post_id ) {

		// If this is just a revision, don't send the email.
		if ( wp_is_post_revision( $post_id ) )
			return;

		$saved_post 	= get_post( $post_id );

		// Only email if the post is published
		if( $saved_post->post_status == 'publish' )
		{
			$post_title 	= $saved_post->post_title;
			$post_url 		= get_permalink( $post_id );
			$post_author_id	= $saved_post->post_author; 
			$post_author 	= get_userdata( $post_author_id );
			$subject 		= $post_author->display_name . ' has updated their journal \'' . wp_title( '', false ) . '\'';
			$message 		= '';

			//Create the message
			$message		.= '<p>The participant <strong>' . $post_author->display_name . '</strong> had updated their journal \'<strong>' . wp_title( '', false ) . '</strong>\' with the entry: <strong><a href="'. $post_url .'">'. $post_title .'</a></strong></p>';
			$message		.= '<p>Options:</p>';
			$message		.= '<ul>';
			$message		.= '<li><a href="'. $post_url .'">View the journal entry: <strong>'. $post_title .'</strong></a></li>';
			$message		.= '<li><a href="'. $post_url .'#reply-title">Leave a comment on: <strong>'. $post_title .'</strong></a></li>';
			$message		.= '</ul>';

			// Get the supervisors of the author
			$supervisors = $this->get_supervisors( $post_author_id );

			// Email each supervisor
			foreach ( $supervisors as $supervisor )
			{
				$supervisor = get_userdata( $supervisor['ID'] );

				$message = '<p>Dear <strong>'. $supervisor->display_name .'</strong>,</p>' . $message;
				add_filter( 'wp_mail_content_type',array($this,'set_html_content_type') );
				wp_mail( $supervisor->user_email, $subject, $message );
				remove_filter( 'wp_mail_content_type', array($this,'set_html_content_type'));
			}
		}
	}

	public function unassigned_users_email() {

		global $wpdb;

		$orphaned_participants 		= 	array();
		$redundant_supervisors		= 	array();

		$mu_users 			= 	$wpdb->get_results( "SELECT ID, user_nicename FROM $wpdb->users" );

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

		ob_start();
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
		$report = ob_get_clean();

		if( count( $orphaned_participants ) || count( $redundant_supervisors ) ) {
			$admin_email 	= 	get_option('admin_email');
			add_filter( 'wp_mail_content_type', array($this,'set_html_content_type') );
			wp_mail( $admin_email, 'CPD Unassigned Users Report', $report );
			remove_filter( 'wp_mail_content_type', array( $this,'set_html_content_type' ) );
		}

	}
}