<?php
/**
 * The content blocks
 *
 * @link       http://makedo.in
 * @since      1.0.0
 *
 * @package    MKDO_Admin
 * @subpackage MKDO_Admin/admin
 */

/**
 * The content blocks
 *
 * Changes the default functionality of the admin bar
 *
 * @package    MKDO_Admin
 * @subpackage MKDO_Admin/admin
 * @author     Make Do <hello@makedo.in>
 */
class CPD_Journal_Content_Blocks extends MKDO_Class {

	/**
	 * Initialize the class and set its properties.
	 *
	 * @since    1.0.0
	 * @var      string    $instance       	The name of this plugin.
	 * @var      string    $version    		The version of this plugin.
	 */
	public function __construct( $instance, $version ) {

		parent::__construct( $instance, $version );
	}

	/**
	 * Add 'Comments' to the menu dashboard
	 */
	public function add_welcome_content_block() {
		
		$welcome_widget_function 		= 'render_welcome_subscriber';
		$welcome_title 					= 'Welcome to CPD Journals ';
		$dashboard 						= 'dashboard';

		$current_user 					= wp_get_current_user();
		$roles 							= $current_user->roles;

		if( is_network_admin() ) {
			$dashboard 					= 'dashboard-network';
			$welcome_widget_function 	= 'render_welcome_network';
			$welcome_title 				= 'Welcome to the CPD Network Settings ';
		}
		else if( is_super_admin() || MKDO_Helper_User::is_mkdo_user() ) {
			$welcome_widget_function 	= 'render_welcome_admin';
		}
		else if ( user_can( $current_user, 'subscriber' ) ) {
			$welcome_widget_function 	= 'render_welcome_subscriber';
		}
		else if( in_array( 'supervisor', $roles ) ) {
			$welcome_widget_function 	= 'render_welcome_supervisor';
		}
		else if( in_array( 'participant', $roles ) ) {
			$welcome_widget_function 	= 'render_welcome_participant';
		}

		add_meta_box('welcome_widget', '<span class="mkdo-block-title dashicons-before dashicons-book"></span> ' . $welcome_title, array( $this, $welcome_widget_function ), $dashboard, 'normal', 'high' );
	}

	/**
	 * Render 'Comments' block in menu dashboard 
	 */
	public function render_welcome_participant(){
		$mkdo_content_block_path 		= 	dirname(__FILE__) . '/partials/content-block-participant.php';
		$theme_path 					= 	get_stylesheet_directory() . '/cpd/content-block-participant.php';
		$partials_sub_path 				= 	get_stylesheet_directory() . '/partials/cpd/content-block-participant.php';
		$partials_path					= 	get_stylesheet_directory() . '/partials/content-block-participant.php';

		if( file_exists( $theme_path ) ) {
			$mkdo_content_block_path = 	$theme_path;
		}
		else if( file_exists( $partials_sub_path ) ) { 
			$mkdo_content_block_path =  	$partials_sub_path;
		}
		else if( file_exists( $partials_path ) ) { 
			$mkdo_content_block_path =  	$partials_path;
		}

		include $mkdo_content_block_path;
	}

	public function render_welcome_admin(){
		$mkdo_content_block_path 		= 	dirname(__FILE__) . '/partials/content-block-admin.php';
		$theme_path 					= 	get_stylesheet_directory() . '/cpd/content-block-admin.php';
		$partials_sub_path 				= 	get_stylesheet_directory() . '/partials/cpd/content-block-admin.php';
		$partials_path					= 	get_stylesheet_directory() . '/partials/content-block-admin.php';

		if( file_exists( $theme_path ) ) {
			$mkdo_content_block_path = 	$theme_path;
		}
		else if( file_exists( $partials_sub_path ) ) { 
			$mkdo_content_block_path =  	$partials_sub_path;
		}
		else if( file_exists( $partials_path ) ) { 
			$mkdo_content_block_path =  	$partials_path;
		}

		include $mkdo_content_block_path;
	}

	public function render_welcome_supervisor(){
		$mkdo_content_block_path 		= 	dirname(__FILE__) . '/partials/content-block-supervisor.php';
		$theme_path 					= 	get_stylesheet_directory() . '/cpd/content-block-supervisor.php';
		$partials_sub_path 				= 	get_stylesheet_directory() . '/partials/cpd/content-block-supervisor.php';
		$partials_path					= 	get_stylesheet_directory() . '/partials/content-block-supervisor.php';

		if( file_exists( $theme_path ) ) {
			$mkdo_content_block_path = 	$theme_path;
		}
		else if( file_exists( $partials_sub_path ) ) { 
			$mkdo_content_block_path =  	$partials_sub_path;
		}
		else if( file_exists( $partials_path ) ) { 
			$mkdo_content_block_path =  	$partials_path;
		}

		include $mkdo_content_block_path;
	}

	public function render_welcome_subscriber(){
		$mkdo_content_block_path 		= 	dirname(__FILE__) . '/partials/content-block-subscriber.php';
		$theme_path 					= 	get_stylesheet_directory() . '/cpd/content-block-subscriber.php';
		$partials_sub_path 				= 	get_stylesheet_directory() . '/partials/cpd/content-block-subscriber.php';
		$partials_path					= 	get_stylesheet_directory() . '/partials/content-block-subscriber.php';

		if( file_exists( $theme_path ) ) {
			$mkdo_content_block_path = 	$theme_path;
		}
		else if( file_exists( $partials_sub_path ) ) { 
			$mkdo_content_block_path =  	$partials_sub_path;
		}
		else if( file_exists( $partials_path ) ) { 
			$mkdo_content_block_path =  	$partials_path;
		}

		include $mkdo_content_block_path;
	}

	public function render_welcome_network(){
		$mkdo_content_block_path 		= 	dirname(__FILE__) . '/partials/content-block-network.php';
		$theme_path 					= 	get_stylesheet_directory() . '/cpd/content-block-network.php';
		$partials_sub_path 				= 	get_stylesheet_directory() . '/partials/cpd/content-block-network.php';
		$partials_path					= 	get_stylesheet_directory() . '/partials/content-block-network.php';

		if( file_exists( $theme_path ) ) {
			$mkdo_content_block_path = 	$theme_path;
		}
		else if( file_exists( $partials_sub_path ) ) { 
			$mkdo_content_block_path =  	$partials_sub_path;
		}
		else if( file_exists( $partials_path ) ) { 
			$mkdo_content_block_path =  	$partials_path;
		}

		include $mkdo_content_block_path;
	}

	public function add_network_dashboard_widgets() {
		global $wp_meta_boxes;

		add_meta_box('cpd_admin_dashboard_widget', '<span class="mkdo-block-title dashicons-before dashicons-groups"></span> CPD Relationships', array( $this, 'unassigned_users_widget' ), 'dashboard-network', 'side' );
		wp_add_dashboard_widget( 'latest_posts_histogram_widget', '<span class="mkdo-block-title dashicons-before dashicons-chart-bar"></span> Posts by week', array($this,'latest_posts_histogram_widget'), array($this,'latest_posts_histogram_widget_config') );
		// wp_add_dashboard_widget('posts_by_participants_barchart_widget', 'Posts by user', array($this,'posts_by_participants_barchart_widget'), array($this,'posts_by_participants_barchart_widget_config'));
	}

	public function unassigned_users_widget(){

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
	}

	function latest_posts_histogram_widget() {
		
		$weeks 			= 	intval( get_option( 'latest_posts_histogram_widget_weeks' ) );
		$posts 			= 	array();
		$post_group 	= 	array();
		$blogs 			= 	wp_get_sites();
		$start_date 	= 	new DateTime();
		$end_date 		= 	new DateTime();
		$day_of_week  	=  	$start_date->format("w");
		$biggest 		=   0;
		$biggest_count 	=   0;
		
		$start_date->modify( "-$day_of_week day" );
		$end_date->modify( "-$day_of_week day" );
		$end_date->modify( "+6 day" );

		foreach ( $blogs as $blog ){
		    switch_to_blog( $blog['blog_id'] );
		    $post_args 		=  	array(
									'post_type' 		=> 'post',
									'posts_per_page' 	=>  -1
	    						);
		    $blog_posts	 	= 	get_posts( $post_args );
		    $posts 			= 	array_merge( $posts, $blog_posts );
		    restore_current_blog();
		}

		if( empty( $weeks ) ) {
			$weeks = 4;
		}

		?>
		<table>
			<?php

			for( $i = 0; $i < $weeks; $i++ ) {
				$post_group[ $i ] 	= 	array();
				if( $i > 0 ) {
					$start_date->modify( "-7 day" );
					$end_date->modify( "-7 day" );
				}

				$start 		= $start_date->format('Y-m-d');
				$end 		= $end_date->format('Y-m-d');

				foreach( $posts as $post ) {
					
					$post_date 			= 	get_the_time( 'Y-m-d', $post );
					
					if( $post_date >= $start && $post_date <= $end ) {
						$post_group[ $i ][] = $post;
					}
				}
				
				if( count( $post_group[ $i ] ) > $biggest_count ) {
					$biggest 		= 	$i;
					$biggest_count 	= 	count( $post_group[ $i ] );
				}

			}

			for( $i = 0; $i < $weeks; $i++ ) {

				$title  	= 	'';
				$percent 	= 	0;
				$count 		= 	count( $post_group[ $i ] );

				if( $count > 0 ) {
					$percent = ( $count / $biggest_count ) * 100 ;
				}

				if( $i == 0 ) {
					$title 	= 'This week';
				}
				else if( $i == 1 ) {
					$title 	= 'Last week';
				}
				else {
					$title 	= $i . ' weeks ago';
				}

				?>
				<tr>
					<th><?php echo $title;?></th>
					<td>
						<div id='weeks_ago_<?php echo $i ?>' class='latest_posts_histogram_bar' style='width:<?php echo $percent; ?>%'><?php echo $count;?></div>
					</td>
				</tr>
				<?php
			}

			?>
		</table>
		<?php

	}

	function latest_posts_histogram_widget_config() {

		$weeks 	= 	intval( get_option( 'latest_posts_histogram_widget_weeks' ) );
		
		if( !isset( $_POST['latest_posts_histogram_widget_config'] ) ) {
			?>
			<input type="hidden" name="latest_posts_histogram_widget_config" value="1">
			<label for="weeks">Ammount of weeks shown</label>
			<select name="weeks" id="weeks">
				<option <?php echo $weeks == 4 	? 'selected' : '';?>>4</option>
				<option <?php echo $weeks == 8 	? 'selected' : '';?>>8</option>
				<option <?php echo $weeks == 12 ? 'selected' : '';?>>12</option>
				<option <?php echo $weeks == 24 ? 'selected' : '';?>>24</option>
			</select>
			<?php
		} 
		else {
			update_option( 'latest_posts_histogram_widget_weeks', $_POST['weeks'] );
		}
	}
}