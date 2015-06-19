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
            $guidance          = get_post_meta( $post->ID, '_cpd_guidance', TRUE);
            $feedback          = get_post_meta( $post->ID, '_cpd_feedback', TRUE);    
            $criteria_group    = get_post_meta( $post->ID, '_cpd_criteria_group', FALSE);
            $submitted         = get_post_meta( $post->ID, '_cpd_submit', TRUE );
            $complete          = get_post_meta( $post->ID, '_cpd_complete', TRUE );
            $submitted_date    = get_post_meta( $post->ID, '_cpd_submitted_date', TRUE );
            $completed_date    = get_post_meta( $post->ID, '_cpd_completed_date', TRUE );
            $total_score       = get_post_meta( $post->ID, '_cpd_score', TRUE );
            $evidence_group    = get_post_meta( $post->ID, '_cpd_group', false);
            $terms             = wp_get_post_terms( $post->ID, 'competency-category');
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
                                    <h2 class="section-title">Status</h2>
                                    <p>
                                        <?php
                                            if( !$submitted && !$complete ) {
                                                ?>
                                                    Ongoing
                                                <?php
                                            } else if( $submitted && !$complete ) {
                                                ?>
                                                    Submitted - <?php echo date( 'F jS, Y', strtotime( $submitted_date ) );?>
                                                <?php
                                            } else if( $complete ) {
                                                ?>
                                                    Completed - <?php echo date( 'F jS, Y', strtotime ( $completed_date ) );?>
                                                <?php
                                            }
                                        ?>
                                    </p>
                                </section>
                                <?php
                                    if ( !empty( $total_score ) ) {
                                        ?>
                                        <section>
                                            <h2>Overall Score</h2>
                                            <p><?php  echo $total_score;?></p>
                                        </section>
                                        <?php
                                    }
                                ?>
                            </div>
                            <h2 class="section-title">Guidance</h2>
                            <?php echo wpautop( $guidance );?>
                            <?php
                            if (    is_array( $criteria_group ) && count( $criteria_group ) > 0 ) {
                                ?>
                                <section>
                                    <h3>Criteria</h3>
                                    <?php
                                    foreach ($criteria_group as $criteria) {
                                        ?>
                                        <div class="desc">
                                        <?php
                                        if( isset( $criteria['_cpd_criteria_max_score'] ) ) {
                                            $max_score = $criteria['_cpd_criteria_max_score'];
                                            if( !empty($max_score)) {
                                                ?> 
                                                <div class="panel">
                                                <section>
                                                <h4>Points available</h4>
                                                <p><?php echo $max_score;?></p>
                                                </section>
                                                </div>
                                                <?php
                                            }
                                        }
                                        if( isset( $criteria['_cpd_criteria_guidance'] ) ) {
                                            echo wpautop( $criteria['_cpd_criteria_guidance'] );
                                        }
                                        ?>
                                        <hr/>
                                        </div>
                                        <?php
                                    }
                                    ?>
                                </section>
                                <?php
                            }
                            ?>
                        </section>

                        <section class="title">

                            <?php 
                            if( !empty( $post->post_content ) ) {
                                ?>
                                <h2 class="section-title">Response</h2>
                                <?php
                                the_content();
                            }
    						
                            $show_response = FALSE;

                            foreach ($criteria_group as $criteria) {
                                if( isset( $criteria['_cpd_criteria_response'] ) && !empty( $criteria['_cpd_criteria_response'] ) ) {
                                    $show_response = TRUE;
                                    break;
                                }
                            }
                            if ( is_array( $criteria_group ) && count( $criteria_group ) > 0 && $show_response ) {
                                ?>
                                <section>
                                    <h3>Criteria Response</h3>
                                    <?php
                                    foreach ($criteria_group as $criteria) {
                                        ?>
                                        <div class="desc">
                                        <?php
                                        if( ( isset( $criteria['_cpd_criteria_participants_score'] ) && !empty( $criteria['_cpd_criteria_participants_score'] ) ) || ( isset( $criteria['_cpd_criteria_supervisors_score'] ) && !empty( $criteria['_cpd_criteria_supervisors_score'] ) ) ) {
                                            ?>
                                            <div class="panel">
                                            <?php
                                            if( isset( $criteria['_cpd_criteria_participants_score'] ) ) {
                                                $self_score = $criteria['_cpd_criteria_participants_score'];
                                                if( !empty($max_score)) {
                                                    ?> 
                                                    
                                                    <section>
                                                    <h4>Self assessment score</h4>
                                                    <p><?php echo $self_score;?></p>
                                                    </section>
                                                    
                                                    <?php
                                                }
                                            }
                                            if( isset( $criteria['_cpd_criteria_supervisors_score'] ) ) {
                                                $score = $criteria['_cpd_criteria_supervisors_score'];
                                                if( !empty($score)) {
                                                    ?> 
                                                    
                                                    <section>
                                                    <h4>Points awarded</h4>
                                                    <p><?php echo $score;?></p>
                                                    </section>
                                                    
                                                    <?php
                                                }
                                            }
                                            ?>
                                            </div>
                                            <?php
                                        }
                                        if( isset( $criteria['_cpd_criteria_response'] ) && !empty( $criteria['_cpd_criteria_response'] ) ) {
                                            echo wpautop( $criteria['_cpd_criteria_response'] );
                                        }
                                        if( isset( $criteria['_cpd_criteria_feedback'] ) && !empty( $criteria['_cpd_criteria_feedback'] ) ) {
                                            ?>
                                            <h4>Supervisor Feedback</h4>
                                            <?php
                                            echo wpautop( $criteria['_cpd_criteria_feedback'] );
                                        }
                                        ?>
                                            <hr/>
                                            </div>
                                        <?php
                                    }
                                    ?>
                                </section>
                                <?php
                            }
                            ?>
                        </section>

                        <?php 
                        if( !empty( $feedback ) ) {
                            ?>
                            <section class="title">
                                <h2 class="section-title">Overall Feedback</h2>
                                <?php echo wpautop( $feedback );?>
                            </section>
                            <?php
                        }
                        ?>
                        
						<?php
                        if ( is_array( $evidence_group ) && count( $evidence_group ) > 0 ) {
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
