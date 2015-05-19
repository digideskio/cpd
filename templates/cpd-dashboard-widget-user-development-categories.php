<?php
/**
 * Template to render User Development Categories Dashboard Widget
 */

$user 				= 	wp_get_current_user();
$cpd_role 			= 	get_user_meta( $user->ID, 'cpd_role', TRUE );
$break 				= 	intval( get_option( 'categories_by_participants_barchart_widget_count' ) );
$order 				= 	get_option( 'categories_by_participants_barchart_widget_order' ) == 'asc' ? 'asc' : 'desc';
$blogs 				= 	wp_get_sites();
$categories 		=	array();
$biggest 			=   0;
$biggest_count 		=   0;

foreach ( $blogs as $blog ){
    switch_to_blog( $blog['blog_id'] );
    $post_args 						=  	array(
											'post_type' 		=> 'ppd',
											'posts_per_page' 	=>  -1
			    						);

    $blog_posts	 					= 	get_posts( $post_args );
    foreach( $blog_posts as $blog_post ) {
    	
    	$blog_post->blog_id = $blog['blog_id'];
    	$blog_post->siteurl = get_bloginfo('wpurl');
    	$blog_post->blogname = get_bloginfo( 'name' );
    	$blog_post->permalink = get_permalink( $blog_post->ID );
    	$terms             				= 	wp_get_post_terms( $blog_post->ID, 'development-category');
    	foreach ( $terms as $term ) {
    		$categories[ $term->name ][]  = $blog_post;
    	}
    }
	
    restore_current_blog();
}

if( empty( $count ) ) {
	$count = 0;
}

if( $break != 0 ) {
	?>
	<p>Showing <?php echo $order == 'asc' ? 'bottom' : 'top';?> <?php echo $break;?> categories</p>
	<?php
}
?>
<table>
	<?php

	foreach( $categories as $key=>$arr ) {
		if( count( $categories[ $key ] ) > $biggest_count ) {
			$biggest 		= 	$key;
			$biggest_count 	= 	count( $categories[ $key ] );
		}
	}

	uasort( $categories, function( $a, $b ) {
		return count( $b ) - count( $a );
	});

	if( $order == 'asc' ) {
		$categories = array_reverse( $categories, TRUE );
	}

	$i = 0;
	foreach( $categories as $key=>$arr ) {

		$percent 	= 	0;
		$count 		= 	count( $categories[ $key ] );
		$term 		=	get_term_by( 'name', $key, 'development-category' );

		if( $count > 0 ) {
			$percent = ( $count / $biggest_count ) * 100 ;
		}
		?>
		<tr>
		<th><?php echo $key;?></th>
			<td>
				<div id='category_<?php echo $term->slug ?>' class='user_posts_barchart_bar' style='width:<?php echo $percent; ?>%'><?php echo $count;?></div>
				<ul>
					<?php
					foreach( $arr as $post ) {
						$user 		= 	get_user_by( 'id', $post->post_author );
						$name 		= 	$user->user_firstname . ' ' . $user->user_lastname;
						$name 		=	trim( $name );
						if( empty( $name ) ) {
							$name = $user->display_name;
						}
						$edit_url	= 	add_query_arg( array( 'user_id' => $user->ID ), network_admin_url( 'user-edit.php#cpd_profile' ) );

						?>
							<li><a href="<?php echo $edit_url;?>"><?php echo $name;?></a> posted <a href="<?php echo $post->permalink;?>"><?php echo $post->post_title;?></a> in <a href="<?php echo $post->siteurl;?>"><?php echo $post->blogname;?></a> on <?php echo get_the_time( 'jS, F Y', $post );?></li>
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