<?php
/**
 * Template to render Latest Posts Dashboard Widget
 */

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