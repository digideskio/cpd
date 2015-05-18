<?php
/**
 * The template for displaying the PPD single pages
 * Template Name: PPD Single
 */

get_header(); ?>

	<div id="primary" class="content-area">
		<main id="main" class="site-main" role="main">

		<?php
        // Start the loop.
        while ( have_posts() ) : the_post();
            $date_completed    = get_post_meta( $post->ID, '_cpd_date_completed', true);
            $points            = get_post_meta( $post->ID, '_cpd_points', true);
            $evidence_group    = get_post_meta( $post->ID, '_cpd_group', false);
            $terms             = wp_get_post_terms( $post->ID, 'development-category');
            ?>
				<article id="post-<?php the_ID(); ?>" <?php post_class(); ?>>
					<?php
                        // Post thumbnail.
                        twentyfifteen_post_thumbnail();
                    ?>

					<header class="entry-header">
						<?php the_title( '<h1 class="entry-title">', '</h1>' ); ?>
					</header><!-- .entry-header -->

					<div class="entry-content">
                            <section class="desc">

        						<div class="panel">
                                    <section>
                                    <h2 class="section-title">Date Completed</h2>
                                    <p>
                                        <?php
                                            if ( empty( $date_completed ) ) {
                                                ?>
                                                Ongoing
                                                <?php
                                            } else {
                                                echo date( 'F jS, Y', $date_completed );
                                            }

                                        ?>
                                    </p>
                                </section>
                                <?php
                                    if ( !empty( $points ) ) {
                                        ?>
                                        <section>
                                            <h2>Points Awarded</h2>
                                            <p><?php  echo $points;?></p>
                                        </section>
                                        <?php
                                    }
                                ?>
                            </div>
                            <h2 class="section-title">Description</h2>
                            <p><?php echo get_the_excerpt();?></p>
                        </section>

                        <section class="title">
    						<h2 class="section-title">Value Obtained</h2>
    						<?php the_content();?>
                        </section>

						<?php
                        if (    is_array( $evidence_group ) && count( $evidence_group ) > 0 ) {
                            ?>
                            <section class="evidence">
                                <h2 class="section-title">Evidence</h2>
    							<ul class="evidence-list">
    								<?php
                                    foreach ($evidence_group as $evidence) {
                                        if ($evidence['_cpd_evidence_type'] == 'upload') {

                                            $link  = wp_get_attachment_url( $evidence['_cpd_evidence_file'] );
                                            $title = $link;

                                            if ( !empty( $evidence['_cpd_evidence_title'] ) ) {
                                                $title = $evidence['_cpd_evidence_title'];
                                            }

                                            ?>
    										<li><a class="link upload" href="<?php echo $link;?>" target="_blank"><span class="genericon genericon-download"></span> <?php echo $title;?></a></li>
    										<?php

                                        } elseif ($evidence['_cpd_evidence_type'] == 'journal') {

                                            $journal = get_post( $evidence['_cpd_evidence_journal'] );
                                            $link    = get_permalink( $journal->ID );
                                            $title   = $journal->post_title;
                                            $date    = $journal->post_date;

                                            ?>
    										<li><a class="link journal" href="<?php echo $link;?>"><span class="genericon genericon-book"></span> <?php echo $title;?></a></li>
    										<?php
                                        } elseif ($evidence['_cpd_evidence_type'] == 'url') {
                                            $link  = $evidence['_cpd_evidence_url'];
                                            $title = $link;

                                            if ( !empty( $evidence['_cpd_evidence_title'] ) ) {
                                                $title = $evidence['_cpd_evidence_title'];
                                            }

                                            ?>
    										<li><a class="link url" href="<?php echo $link;?>" target="_blank"><span class="genericon genericon-website"></span> <?php echo $title;?></a></li>
    										<?php
                                        }
                                    }
                                    ?>
    							</ul>
                            </section>
							<?php
                        }
                        ?>
						<?php
                        if ( is_array( $terms ) && count( $terms ) > 0 ) {
                            ?>
                                <section class="categories">
                                	<h2 class="section-title">Categories</h2>
    								<ul class="category-list">
    									<?php
                                        foreach ($terms as $term) {
                                            ?>
    										<li><a href="<?php echo get_term_link( $term, 'development-category' );?>"><span class="genericon genericon-category"></span> <?php echo $term->name;?></a></li>
    										<?php
                                        }
                                        ?>
    								</ul>
                                </section>
							<?php
                        }
                        ?>

						<?php
                            wp_link_pages( array(
                                'before'      => '<div class="page-links"><span class="page-links-title">' . __( 'Pages:', 'twentyfifteen' ) . '</span>',
                                'after'       => '</div>',
                                'link_before' => '<span>',
                                'link_after'  => '</span>',
                                'pagelink'    => '<span class="screen-reader-text">' . __( 'Page', 'twentyfifteen' ) . ' </span>%',
                                'separator'   => '<span class="screen-reader-text">, </span>',
                            ) );
                        ?>
					</div><!-- .entry-content -->

					<?php edit_post_link( __( 'Edit', 'twentyfifteen' ), '<footer class="entry-footer"><span class="edit-link">', '</span></footer><!-- .entry-footer -->' ); ?>

				</article><!-- #post-## -->
			<?php

            // If comments are open or we have at least one comment, load up the comment template.
            if ( comments_open() || get_comments_number() ) :
                comments_template();
            endif;

        // End the loop.
        endwhile;
        ?>

		</main><!-- .site-main -->
	</div><!-- .content-area -->

<?php get_footer(); ?>
