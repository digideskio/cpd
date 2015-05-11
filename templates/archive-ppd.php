<?php
/**
 * The template for displaying the PPD archive pages
 */

get_header(); ?>

	<section id="primary" class="content-area">
		<main id="main" class="site-main" role="main">

		<?php if ( have_posts() ) : ?>

			<header class="page-header">
			<h1 class="page-title">Activity Logs</h1>
			</header><!-- .page-header -->

			<article>
				<header class="entry-header">
				</header><!-- .entry-header -->
				<div class="entry-content">
				<table style="font-size:10px;">
					<tr>
						<th>Date completed</th>
						<th>PPD activity</th>
						<th>Description</th>
						<th>Value obtained</th>
						<th>Points awarded</th>
						<th>Evidence</th>
						<th>Category of development</th>
					</tr>
					<?php
					// Start the Loop.
					while ( have_posts() ) : the_post();
						$date_completed 	= get_post_meta( $post->ID, '_cpd_date_completed', TRUE);
						$points 			= get_post_meta( $post->ID, '_cpd_points', TRUE);
						$evidence_group 	= get_post_meta( $post->ID, '_cpd_group', FALSE);
						$terms 				= get_terms('development-category');
						?>
							<tr>
								<td>
									<?php 

										if( empty( $date_completed ) ) {
											?>
											Ongoing
											<?php
										} else 
										{
											echo date( 'jS F, Y', $date_completed );
										}

									?>
								</td>
								<td>
									<?php the_title();?>
								</td>
								<td>
									<?php the_excerpt();?>
								</td>
								<td>
									<?php the_content();?>
								</td>
								<td>
									<?php echo $points;?>
								</td>
								<td>
									<?php
										if(	is_array( $evidence_group ) && count( $evidence_group ) > 0 ) {
											?>
											<ul>
												<?php
												foreach( $evidence_group as $evidence ) {
													if( $evidence['_cpd_evidence_type'] == 'upload' ) {

														$link 			=	wp_get_attachment_url( $evidence['_cpd_evidence_file'] );
														$title 			= 	$link;

														if( !empty( $evidence['_cpd_evidence_title'] ) ) {
															$title 		= 	$evidence['_cpd_evidence_title'];
														}

														?>
														<li><a href="<?php echo $link;?>" target="_blank"><?php echo $title;?></a></li>
														<?php

													} else if( $evidence['_cpd_evidence_type'] == 'journal' ) {

														$journal 		=	get_post( $evidence['_cpd_evidence_journal'] );

														$link 			=	get_permalink( $journal->ID );
														$title 			= 	$journal->post_title;

														?>
														<li><a href="<?php echo $link;?>"><?php echo $title;?></a></li>
														<?php
													} else if( $evidence['_cpd_evidence_type'] == 'url' ) {
														$link 			=	$evidence['_cpd_evidence_url'];
														$title 			= 	$link;

														if( !empty( $evidence['_cpd_evidence_title'] ) ) {
															$title 		= 	$evidence['_cpd_evidence_title'];
														}

														?>
														<li><a href="<?php echo $link;?>" target="_blank"><?php echo $title;?></a></li>
														<?php
													}
												}
												?>
											</ul>
											<?php
										}
									?>
								</td>
								<td>
									<?php
									if( is_array( $terms ) && count( $terms ) > 0 ) {
										?>
											<ul>
												<?php
												foreach ( $terms as $term ) {
													?>
													<li><?php echo $term->name;?></li>
													<?php
												}
												?>
											</ul>
										<?php
									}
									?>
								</td>
							</tr>

						<?php

					// End the loop.
					endwhile;
					?>
				</table>
				</div><!-- .entry-content -->
			</article><!-- #post-## -->
			<?php
			// Previous/next page navigation.
			the_posts_pagination( array(
				'prev_text'          => __( 'Previous page', 'twentyfifteen' ),
				'next_text'          => __( 'Next page', 'twentyfifteen' ),
				'before_page_number' => '<span class="meta-nav screen-reader-text">' . __( 'Page', 'twentyfifteen' ) . ' </span>',
			) );

		// If no content, include the "No posts found" template.
		else :
			get_template_part( 'content', 'none' );

		endif;
		?>

		</main><!-- .site-main -->
	</section><!-- .content-area -->

<?php get_footer(); ?>
