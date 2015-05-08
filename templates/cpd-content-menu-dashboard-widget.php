<?php
/**
 * Template to render a CPD Content Menu widget
 */

$post_listing 	= $widget[ 'link' ];
$post_new 		= $widget[ 'call_to_action_link' ];

if ( $widget[ 'post_type' ] == 'page' && defined('CMS_TPV_URL') ) { 
	if( $post_listing = 'edit.php?post_type=page' ) {
		$post_listing = 'edit.php?post_type=page&page=cms-tpv-page-page';
	}
}

$css_block_class = $widget[ 'css_class' ];
											
?>

<div class="<?php echo esc_attr( $css_block_class ); ?>">
	
	<?php
	if( !empty( $widget[ 'post_type' ] ) ) {
		?>
		<p><a class="button button-primary" href="<?php echo esc_url( $post_new ); ?>"><?php echo esc_html( $widget[ 'call_to_action_text' ] );?></a></p>

		<?php
	}
	?>

	<div class="content-description">

		<?php echo $widget[ 'desc' ]; ?>
	
	</div>
	
	<?php
		
		if( $widget[ 'show_tax' ] == true ) {
			
			$taxonomies = get_object_taxonomies( $widget[ 'post_type' ], 'objects' );

			unset( $taxonomies[ 'post_format' ] );
			unset( $taxonomies[ 'post_status' ] );
			unset( $taxonomies[ 'ef_editorial_meta' ] );
			unset( $taxonomies[ 'following_users' ] );
			unset( $taxonomies[ 'ef_usergroup' ] );
			
			if( ! empty( $taxonomies ) ) {
				
				?>
				<h4 class="tax-title">Associated Taxonomies</h4>
				
				<ul class="tax-list">
				<?php
					
					foreach( $taxonomies as $tax ) {
						?>
						<li class="<?php echo esc_attr( sanitize_title( $tax->name ) ); ?>">
							<span class="dashicons-before dashicons-category"></span>
							<a href="<?php echo admin_url( 'edit-tags.php?taxonomy=' . $tax->name ); ?>"><?php echo esc_html( $tax->labels->name ); ?></a>
						</li>
						<?php
					}
					
				?>
				</ul>
					
				<?php
			}
			
		}	
		
	?>
	
	<p class="footer-button"><a class="button" href="<?php echo esc_url( $post_listing ); ?>"><?php echo esc_html( $widget[ 'button_label' ] ); ?></a></p>
	
</div>