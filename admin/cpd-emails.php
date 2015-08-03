<?php

// Exit if accessed directly
defined( 'ABSPATH' ) || exit;

if( !class_exists( 'CPD_Emails' ) ) {
	
/**
 * Emails
 *
 * Email functionality
 *
 * @package    CPD
 * @subpackage CPD/admin
 * @author     Make Do <hello@makedo.in>
 */
class CPD_Emails {

	private static $instance = null;
	private $text_domain;

	/**
	 * Creates or returns an instance of this class.
	 */
	public static function get_instance() {
		/**
		 * If an instance hasn't been created and set to $instance create an instance 
		 * and set it to $instance.
		 */
		if ( null == self::$instance ) {
			self::$instance = new self;
		}
		return self::$instance;
	}


	/**
	 * Initialize the class and set its properties.
	 *
	 * @param      string    $instance       The name of this plugin.
	 * @param      string    $version    The version of this plugin.
	 */
	public function __construct() {
		
	}


	/**
	 * Set the text domain
	 *
	 * @param      string    $text_domain       The text domain of the plugin.
	 */
	public function set_text_domain( $text_domain ) { 
		$this->text_domain = $text_domain;
	}

	/**
	 * Send a mail to supervisors when a participant updates their blog
	 *
	 * @param      int    $post_id       The post id
	 */
	public function send_mail_on_update( $post_id ) {

		// If this is just a revision, don't send the email.
		if ( wp_is_post_revision( $post_id ) ) {
			return;
		}

		$saved_post 	= get_post( $post_id );

		// Only email if the post is published
		if( $saved_post->post_status == 'publish' && $saved_post->post_type == 'post' )
		{
			$post_title 	= $saved_post->post_title;
			$post_url 		= get_permalink( $post_id );
			$post_author_id	= $saved_post->post_author; 
			$post_author 	= get_userdata( $post_author_id );
			$subject 		= $post_author->display_name . __(' has updated their journal', $this->text_domain ) . ' \'' . wp_title( '', false ) . '\'';
			$message 		= '';
			$name 		= $post_author->user_firstname . ' ' . $post_author->user_lastname;
			$name 		= trim( $name );
			if( empty( $name ) ) {
				$name = $post_author->display_name;
			}

			//Create the message
			$message		.= '<p>' . __('The participant', $this->text_domain ) . ' <strong>' . $name. '</strong> ' . __('had updated their journal', $this->text_domain ) .' \'<strong>' . wp_title( '', false ) . '</strong>\' ' . __('with the entry:', $this->text_domain ) .' <strong><a href="'. $post_url .'">'. $post_title .'</a></strong></p>';
			$message		.= '<p>' . __('Options:', $this->text_domain ) . '</p>';
			$message		.= '<ul>';
			$message		.= '<li><a href="'. $post_url .'">' . __('View the journal entry', $this->text_domain ) . ':<strong>'. $post_title .'</strong></a></li>';
			$message		.= '<li><a href="'. $post_url .'#reply-title">' . __('Leave a comment on', $this->text_domain ) .': <strong>'. $post_title .'</strong></a></li>';
			$message		.= '</ul>';

			// Get the supervisors of the author
			$supervisors = get_user_meta( $post_author_id, 'cpd_related_supervisors', TRUE );

			if( !is_array( $supervisors ) ) {
				$supervisors = array();
			}

			// Email each supervisor
			foreach ( $supervisors as $supervisor )
			{
				$supervisor = get_userdata( $supervisor );

				$message = '<p>' . __('Dear', $this->text_domain ) .' <strong>'. $supervisor->display_name .'</strong>,</p>' . $message;
				add_filter( 'wp_mail_from_name', array( $this, 'set_html_email_from_name' ) );
				add_filter( 'wp_mail_content_type',array($this,'set_html_content_type') );
				wp_mail( $supervisor->user_email, $subject, $message );
				remove_filter( 'wp_mail_content_type', array($this,'set_html_content_type'));
				remove_filter( 'wp_mail_from_name', array( $this, 'set_html_email_from_name' ) );
			}
		}
	}

	/**
	 * Send a mail to supervisors when a participant submits an assessment
	 *
	 * @param      int    $post_id       The post id
	 */
	public function send_mail_on_submit_assessment( $post_id ) {


		// If this is just a revision, don't send the email.
		if ( wp_is_post_revision( $post_id ) ) {
			return;
		}

		$saved_post 	= get_post( $post_id );

		// Only email if the post is published
		if( $saved_post->post_status == 'publish' )
		{

			$post_title 	= $saved_post->post_title;
			$post_url 		= get_permalink( $post_id );
			$edit_url 		= get_edit_post_link( $post_id  );
			$post_author_id	= get_current_user_id();
			$post_author 	= get_userdata( $post_author_id );
			$subject 		= $post_author->display_name . ' has submitted an assessment for review';
			$message 		= '';
			$name 		= $post_author->user_firstname . ' ' . $post_author->user_lastname;
			$name 		= trim( $name );
			if( empty( $name ) ) {
				$name = $post_author->display_name;
			}

			//Create the message
			$message		.= '<p>' . __('The participant', $this->text_domain ) .' <strong>' . $name . '</strong> ' . __('has submitted an assessment in the journal', $this->text_domain ) . ' \'<strong>' . wp_title( '', false ) . '</strong>\' ' . __('named', $this->text_domain ) .': <strong><a href="'. $post_url .'">'. $post_title .'</a></strong></p>';
			$message		.= '<p>'.__('Options', $this->text_domain ) .':</p>';
			$message		.= '<ul>';
			$message		.= '<li><a href="'. $post_url .'">' . __('View the assessment', $this->text_domain ).': <strong>'. $post_title . '</strong></a></li>';
			$message		.= '<li><a href="'. $edit_url .'#reply-title">' . __('Provide feedback for the assessment', $this->text_domain ) . ': <strong>'. $post_title .'</strong></a></li>';
			$message		.= '</ul>';

			// Get the supervisors of the author
			$supervisors = get_user_meta( $post_author_id, 'cpd_related_supervisors', TRUE );

			if( !is_array( $supervisors ) ) {
				$supervisors = array();
			}

			// Email each supervisor
			foreach ( $supervisors as $supervisor )
			{
				$supervisor = get_userdata( $supervisor );

				$message = '<p>' . __('Dear', $this->text_domain ) .' <strong>'. $supervisor->display_name .'</strong>,</p>' . $message;
				add_filter( 'wp_mail_from_name', array( $this, 'set_html_email_from_name' ) );
				add_filter( 'wp_mail_content_type',array($this,'set_html_content_type') );
				wp_mail( $supervisor->user_email, $subject, $message );
				remove_filter( 'wp_mail_content_type', array($this,'set_html_content_type'));
				remove_filter( 'wp_mail_from_name', array( $this, 'set_html_email_from_name' ) );
			}
		}
	}

	/**
	 * Send a mail to a participant when assessment is marked
	 *
	 * @param      int    $post_id       The post id
	 */
	public function send_mail_on_marked_assessment( $post_id ) {

		// If this is just a revision, don't send the email.
		if ( wp_is_post_revision( $post_id ) ) {
			return;
		}

		$saved_post 	= get_post( $post_id );

		// Only email if the post is published
		if( $saved_post->post_status == 'publish' )
		{
			$post_title 	= $saved_post->post_title;
			$post_url 		= get_permalink( $post_id );
			$edit_url 		= get_edit_post_link( $post_id  );
			$supervisor_id	= get_current_user_id();
			$supervisor 	= get_userdata( $supervisor_id );
			$subject 		= $supervisor->display_name . __(' has marked your assessment as complete', $this->text_domain );
			$message 		= '';

			$name 		= $supervisor->user_firstname . ' ' . $supervisor->user_lastname;
			$name 		= trim( $name );
			if( empty( $name ) ) {
				$name = $supervisor->display_name;
			}

			//Create the message
			$message		.= '<p>'.__('The supervisor', $this->text_domain ) .' <strong>' . $name . '</strong> ' .__('has marked an assessment as complete in the journal', $this->text_domain ) .' \'<strong>' . wp_title( '', false ) . '</strong>\' '.__('named', $this->text_domain ) .': <strong><a href="'. $post_url .'">'. $post_title .'</a></strong></p>';
			$message		.= '<p>'.__('Options', $this->text_domain ) .':</p>';
			$message		.= '<ul>';
			$message		.= '<li><a href="'. $post_url .'">'.__('View the assessment', $this->text_domain ) .': <strong>'. $post_title .'</strong></a></li>';
			$message		.= '</ul>';

			$post_author_id	= $saved_post->post_author; 
			$post_author 	= get_userdata( $post_author_id );
		
			$message = '<p>'.__('Dear', $this->text_domain ) .' <strong>'. $post_author->display_name .'</strong>,</p>' . $message;
			add_filter( 'wp_mail_from_name', array( $this, 'set_html_email_from_name' ) );
			add_filter( 'wp_mail_content_type',array($this,'set_html_content_type') );
			wp_mail( $post_author->user_email, $subject, $message );
			remove_filter( 'wp_mail_content_type', array($this,'set_html_content_type'));
			remove_filter( 'wp_mail_from_name', array( $this, 'set_html_email_from_name' ) );
		}
	}

	/**
	 * Email the admin with details of supervisor and particpants that are unassigned
	 */
	public function unassigned_users_email() {

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

		ob_start();
		?>
		<p><strong>Participants</strong></p>
		<?php
		if( count( $orphaned_participants ) ) { 
			?>
			<p><?php _e('The following participants have no supervisor assigned to them', $this->text_domain );?>:</p>
			<table>
				<tr>
					<th><?php _e('Name', $this->text_domain );?></th>
					<th><?php _e('Journal', $this->text_domain );?></th>
					<th><?php _e('Dashboard', $this->text_domain );?></th>
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
						$empty_name 		=	trim( $name );
						if( empty( $empty_name ) ) {
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
									<?php _e('Dashboard', $this->text_domain );?>
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
			<p><?php _e('All participants have supervisors assigned to them.', $this->text_domain );?></p>
			<?php
		}
		?>

		<p><strong><?php _e('Supervisors', $this->text_domain );?></strong></p>
		<?php
		if( count( $redundant_supervisors ) ) { 
			?>
			<p><?php _e('The following supervisors have no participants assigned to them', $this->text_domain );?>:</p>
			<table>
				<tr>
					<th><?php _e('Name', $this->text_domain );?></th>
				</tr>
				<?php 
					foreach( $redundant_supervisors as $supervisor ) {
						$edit_url			= 	add_query_arg( array( 'user_id' => $supervisor->ID ), network_admin_url( 'user-edit.php#cpd_profile' ) );
						$user 				= 	get_user_by( 'id', $supervisor->ID );
						$name 				= 	$user->user_firstname . ' ' . $user->user_lastname;
						$empty_name 		=	trim( $name );
						if( empty( $empty_name ) ) {
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
			<p><?php _e('All supervisors have pariticpants assigned to them.', $this->text_domain );?></p>
			<?php
		}
		$report = ob_get_clean();

		if( count( $orphaned_participants ) || count( $redundant_supervisors ) ) {
			$admin_email 	= 	get_option('admin_email');
			add_filter( 'wp_mail_from_name', array( $this, 'set_html_email_from_name' ) );
			add_filter( 'wp_mail_content_type', array($this,'set_html_content_type') );
			wp_mail( $admin_email, __('CPD Unassigned Users Report', $this->text_domain ), $report );
			remove_filter( 'wp_mail_content_type', array( $this,'set_html_content_type' ) );
			remove_filter( 'wp_mail_from_name', array( $this, 'set_html_email_from_name' ) );
		}

	}

	/**
	 * Set the email content type to HTML
	 */
	public function set_html_content_type() {
		return 'text/html';
	}

	/**
	 * Set the email from name
	 */
	public function set_html_email_from_name( $email_from ){
		return __('Aspire CPD', $this->text_domain );
	}
}
}