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
class CPD_Journal_Content_Blocks{

	/**
	 * Initialize the class and set its properties.
	 *
	 * @since    1.0.0
	 * @var      string    $instance       	The name of this plugin.
	 * @var      string    $version    		The version of this plugin.
	 */
	public function __construct() {

		
	}

	public function add_cpd_dashboard_widgets() {
		global $wp_meta_boxes;

		$latest_posts_title  			= 'All posts by week';
		$posts_by_participant_title  	= 'All posts by user';

		$current_user 					= wp_get_current_user();
		$roles 							= $current_user->roles;

		if( in_array( 'supervisor', $roles ) ) {
			$latest_posts_title  			= 'Your participants posts by week';
			$posts_by_participant_title  	= 'All your participants posts';
		}
		else if( in_array( 'participant', $roles ) ) {
			$latest_posts_title  			= 'Your posts by week';
		}

		add_meta_box( 'cpd_admin_dashboard_widget', '<span class="mkdo-block-title dashicons-before dashicons-groups"></span> CPD Relationships', array( $this, 'unassigned_users_widget' ), 'dashboard-network', 'side' );
		wp_add_dashboard_widget( 'latest_posts_histogram_widget', '<span class="mkdo-block-title dashicons-before dashicons-chart-bar"></span> ' . $latest_posts_title, array($this,'latest_posts_histogram_widget'), array($this,'latest_posts_histogram_widget_config') );
		
		if( in_array( 'supervisor', $roles ) || is_super_admin( $current_user->ID ) ) {
			wp_add_dashboard_widget( 'posts_by_participants_barchart_widget', '<span class="mkdo-block-title dashicons-before dashicons-chart-bar"></span> ' . $posts_by_participant_title, array($this,'posts_by_participants_barchart_widget'), array($this,'posts_by_participants_barchart_widget_config'));
		}
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
		
		$user 				= 	wp_get_current_user();
		$cpd_role 			= 	get_user_meta( $user->ID, 'cpd_role', TRUE );
		$weeks 				= 	intval( get_option( 'latest_posts_histogram_widget_weeks' ) );
		$posts 				= 	array();
		$post_group 		= 	array();
		$blogs 				= 	wp_get_sites();
		$start_date 		= 	new DateTime();
		$end_date 			= 	new DateTime();
		$day_of_week  		=  	$start_date->format("w");
		$biggest 			=   0;
		$biggest_count 		=   0;
		
		$start_date->modify( "-$day_of_week day" );
		$end_date->modify( "-$day_of_week day" );
		$end_date->modify( "+6 day" );

		foreach ( $blogs as $blog ){
		    switch_to_blog( $blog['blog_id'] );
		    $post_args 						=  	array(
													'post_type' 		=> 'post',
													'posts_per_page' 	=>  -1
					    						);

		    if( $cpd_role == 'participant' ) {
				$post_args['author__in'] 	= 	array( $user->ID );
			}

			if( $cpd_role == 'supervisor' ) {
				$related_participants 		= 	get_user_meta( $user->ID, 'cpd_related_participants', TRUE );
				$post_args['author__in'] 	= 	$related_participants;
			}

		    $blog_posts	 					= 	get_posts( $post_args );
		    $posts 							= 	array_merge( $posts, $blog_posts );
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
						<ul>
							<?php
							foreach( $post_group[ $i ] as $post ) {
								$user 		= 	get_user_by( 'id', $post->post_author );
								$name 		= 	$user->user_firstname . ' ' . $user->user_lastname;
								if( empty( trim( $name ) ) ) {
									$name = $user->display_name;
								}
								$edit_url	= 	add_query_arg( array( 'user_id' => $user->ID ), network_admin_url( 'user-edit.php#cpd_profile' ) );

								?>
									<li><a href="<?php echo $edit_url;?>"><?php echo $name;?></a> posted <a href="<?php echo get_permalink( $post->ID );?>"><?php echo $post->post_title;?></a> on <?php echo get_the_time( 'jS, F Y', $post );?></li>
								<?php
							}
							?>
						</ul>
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
		
		if( empty( $weeks ) ) {
			$weeks = 4;
		}

		if( !isset( $_POST['latest_posts_histogram_widget_config'] ) ) {
			?>
			<input type="hidden" name="latest_posts_histogram_widget_config" value="1">
			<label for="weeks">Ammount of weeks shown</label>
			<select name="weeks" id="weeks">
				<option <?php echo $weeks == 4 	? 'selected' : '';?> value="4">4</option>
				<option <?php echo $weeks == 8 	? 'selected' : '';?> value="8">8</option>
				<option <?php echo $weeks == 12 ? 'selected' : '';?> value="12">12</option>
				<option <?php echo $weeks == 24 ? 'selected' : '';?> value="24">24</option>
			</select>
			<?php
		} 
		else {
			update_option( 'latest_posts_histogram_widget_weeks', $_POST['weeks'] );
		}
	}

	function posts_by_participants_barchart_widget() {
		
		$user 				= 	wp_get_current_user();
		$cpd_role 			= 	get_user_meta( $user->ID, 'cpd_role', TRUE );
		$break 				= 	intval( get_option( 'posts_by_participants_barchart_widget_count' ) );
		$order 				= 	get_option( 'posts_by_participants_barchart_widget_order' ) == 'asc' ? 'asc' : 'desc';
		$posts 				= 	array();
		$post_group 		= 	array();
		$blogs 				= 	wp_get_sites();
		$biggest 			=   0;
		$biggest_count 		=   0;
		
		foreach ( $blogs as $blog ){
		    switch_to_blog( $blog['blog_id'] );
		    $post_args 						=  	array(
													'post_type' 		=> 'post',
													'posts_per_page' 	=>  -1
					    						);

		    if( $cpd_role == 'participant' ) {
				$post_args['author__in'] 	= 	array( $user->ID );
			}

			if( $cpd_role == 'supervisor' ) {
				$related_participants 		= 	get_user_meta( $user->ID, 'cpd_related_participants', TRUE );
				$post_args['author__in'] 	= 	$related_participants;
			}

		    $blog_posts	 					= 	get_posts( $post_args );
		    $posts 							= 	array_merge( $posts, $blog_posts );
		    restore_current_blog();
		}

		if( empty( $count ) ) {
			$count = 0;
		}

		if( $break != 0 ) {
			?>
			<p>Showing <?php echo $order == 'asc' ? 'bottom' : 'top';?> <?php echo $break;?> users</p>
			<?php
		}
		?>
		<table>
			<?php

			foreach( $posts as $post ) {
				$post_group[ $post->post_author ][] = $post;
			}

			foreach( $post_group as $key=>$arr ) {
				if( count( $post_group[ $key ] ) > $biggest ) {
					$biggest 		= 	$key;
					$biggest_count 	= 	count( $post_group[ $key ] );
				}
			}

			uasort( $post_group, function( $a, $b ) { 
				return count( $b ) - count( $a );
			});

			if( $order == 'asc' ) {
				$post_group = array_reverse( $post_group, TRUE );
			}

			$i = 0;
			foreach( $post_group as $key=>$arr ) {

				$percent 	= 	0;
				$count 		= 	count( $post_group[ $key ] );
				$user 		= 	get_user_by( 'id', $key );
				$name 		= 	$user->user_firstname . ' ' . $user->user_lastname;

				if( $count > 0 ) {
					$percent = ( $count / $biggest_count ) * 100 ;
				}

				if( empty( trim( $name ) ) ) {
					$name = $user->display_name;
				}
				?>
				<tr>
				<th><?php echo $name;?></th>
					<td>
						<div id='posts_by_<?php echo $user->user_nicename ?>' class='user_posts_barchart_bar' style='width:<?php echo $percent; ?>%'><?php echo $count;?></div>
						<ul>
							<?php
							foreach( $arr as $post ) {
								?>
									<li><a href="<?php echo get_permalink( $post->ID );?>"><?php echo $post->post_title;?></a> on <?php echo get_the_time( 'jS, F Y', $post );?></li>
								<?php
							}
							?>
						</ul>
					</td>
				</tr>
				<?php

				$i++;
				if( $break != 0 && $i >= $break - 1 ) {
					break;
				}
			}

			?>
		</table>
		<?php

	}

	function posts_by_participants_barchart_widget_config() {

		$count 	= 	intval( get_option( 'posts_by_participants_barchart_widget_count' ) );
		$order 	= 	get_option( 'posts_by_participants_barchart_widget_order' ) == 'asc' ? 'asc' : 'desc';
		
		if( empty( $count ) ) {
			$count = 0;
		}

		if( !isset( $_POST['posts_by_participants_barchart_widget_count'] ) ) {
			?>
			<input type="hidden" name="posts_by_participants_barchart_widget_count" value="1">
			<label for="count">Ammount of participants to show</label>
			<select name="count" id="count">
				<option <?php echo $count == 0 		? 'selected' : '';?> value="0">All</option>
				<option <?php echo $count == 10 	? 'selected' : '';?> value="10">10</option>
				<option <?php echo $count == 20 	? 'selected' : '';?> value="20">20</option>
				<option <?php echo $count == 30 	? 'selected' : '';?> value="30">30</option>
			</select>

			<label for="order">Order by</label>
			<select name="order" id="order">
				<option <?php echo $order == 'desc' 	? 'selected' : '';?> value="desc">Most posts</option>
				<option <?php echo $order == 'asc' 		? 'selected' : '';?> value="asc">Least posts</option>
			</select>
			<?php
		} 
		else {
			update_option( 'posts_by_participants_barchart_widget_count', $_POST['count'] );
			update_option( 'posts_by_participants_barchart_widget_order', $_POST['order'] == 'desc' ? 'desc' : 'asc' );
		}
	}
}