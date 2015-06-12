<?php
/**
 * The template for displaying the PPD archive pages
 * Template Name: PPD Archive
 */

get_header(); ?>

    <section id="primary" class="content-area">
        <main id="main" class="site-main" role="main">

        <?php if ( have_posts() ) : ?>

            <header class="page-header">
                <?php
                    the_archive_title( '<h1 class="page-title">', '</h1>' );
                    the_archive_description( '<div class="taxonomy-description">', '</div>' );
                ?>
            </header><!-- .entry-header -->

            <article class="ppd-archive-container">

                <table id="ppd-archive" class="ppd-archive">
                    <thead>
                        <tr class="header-row">
<!--                             <th class="date">Date <br/>Completed</th>
                            <th class="activity">PPD <br/>Activity</th>
                            <th class="description">Activity <br/>Description</th>
                            <th>Value </br>Obtained</th>
                            <th class="points">Points <br/>Awarded</th>
                            <th class="evidence">Evidence <br/>Gathered</th>
                            <th class="categories">Development </br>Categories</th> -->

                            <th class="date">Date</th>
                            <th class="activity">Activity</th>
                            <th class="description">Description</th>
                            <!-- <th>Value </br>Obtained</th> -->
                            <th class="points">Points</th>
                            <th class="evidence">Evidence</th>
                            <th class="categories">Categories</th>
                        </tr>
                    </thead>
                    <?php
                    $i = 1;
                    // Start the Loop.
                    while ( have_posts() ) : the_post();
                        $date_completed    = get_post_meta( $post->ID, '_cpd_date_completed', true);
                        $points            = get_post_meta( $post->ID, '_cpd_points', true);
                        $evidence_group    = get_post_meta( $post->ID, '_cpd_group', false);
                        $terms             = wp_get_post_terms( $post->ID, 'development-category');

                        if ($i % 2 == 0) {
                            $row = 'even';
                        } else {
                            $row = 'odd';
                        }
                        ?>
                            <tr class="<?php echo $row; ?>">
                                <td class="date">
                                    <?php

                                        if ( empty( $date_completed ) ) {
                                            ?>
                                            Ongoing
                                            <?php
                                        } else {
                                            echo date( 'F jS, Y', $date_completed );
                                        }

                                    ?>
                                </td>
                                <td class="activity">
                                    <a href="<?php the_permalink(); ?>"><?php the_title();?></a>
                                </td>
                                <td class="description">
                                    <?php
                                    $string  = wp_trim_words(get_the_excerpt(), 30, '');
                                    $excerpt = trim($string, '"\':;,');
                                    echo $excerpt; ?>&hellip;

                                    <a class="more" href="<?php echo get_the_permalink(); ?>">Read More <span class="screen-reader-text">about '<?php the_title();?>'</span></a>
                                </td>
                                <!-- <td>
                                    <?php //the_content();?>
                                </td> -->
                                <td class="points">
                                    <?php echo $points;?>
                                </td>
                                <td class="evidence">
                                    <?php
                                        if (    is_array( $evidence_group ) && count( $evidence_group ) > 0 ) {
                                            ?>
                                            <ul>
                                                <?php
                                                foreach ($evidence_group as $evidence) {
                                                    if ($evidence['_cpd_evidence_type'] == 'upload') {

                                                        $link  = wp_get_attachment_url( $evidence['_cpd_evidence_file'] );
                                                        $title = $link;

                                                        if ( !empty( $evidence['_cpd_evidence_title'] ) ) {
                                                            $title = $evidence['_cpd_evidence_title'];
                                                        }

                                                        ?>
                                                        <li><a href="<?php echo $link;?>" target="_blank"><?php echo $title;?></a></li>
                                                        <?php

                                                    } elseif ($evidence['_cpd_evidence_type'] == 'journal') {

                                                        $journal = get_post( $evidence['_cpd_evidence_journal'] );
                                                        $link    = get_permalink( $journal->ID );
                                                        $title   = $journal->post_title;

                                                        ?>
                                                        <li><a href="<?php echo $link;?>"><?php echo $title;?></a></li>
                                                        <?php
                                                    } elseif ($evidence['_cpd_evidence_type'] == 'url') {
                                                        $link  = $evidence['_cpd_evidence_url'];
                                                        $title = $link;

                                                        if ( !empty( $evidence['_cpd_evidence_title'] ) ) {
                                                            $title = $evidence['_cpd_evidence_title'];
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
                                <td class="categories">
                                    <?php
                                    if ( is_array( $terms ) && count( $terms ) > 0 ) {
                                        ?>
                                            <ul>
                                                <?php
                                                foreach ($terms as $term) {
                                                    ?>
                                                    <li><a href="<?php echo get_term_link( $term, 'development-category' );?>"><?php echo $term->name;?></a></li>
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
                    $i++;
                    endwhile;
                    ?>
                </table>

            </article>

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
