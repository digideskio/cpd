<?php
/**
 * Template to render User Development Categories Dashboard Widget
 */

$user 				= 	wp_get_current_user();
$cpd_role 			= 	get_user_meta( $user->ID, 'cpd_role', TRUE );
$break 				= 	intval( get_option( 'categories_by_participants_barchart_widget_count' ) );
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

		$name 		=	trim( $name );
		if( empty( $name ) ) {
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