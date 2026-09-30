<?php
/**
 * Content without the theme's page banner or sidebar (Elementor layouts and
 * FunnelKit funnel steps).
 *
 * Elementor layouts run edge to edge because Elementor sets its own widths.
 * Other content (for example a funnel step written in the block editor)
 * keeps a centered column so it doesn't touch the screen edges.
 *
 * @package PEN
 */

?>
<article id="post-<?php the_ID(); ?>" <?php post_class( 'pen-bare' ); ?>>
	<?php if ( pen_is_built_with_elementor( get_the_ID() ) ) : ?>
		<?php the_content(); ?>
	<?php else : ?>
		<div class="pen-section">
			<div class="pen-container">
				<div class="entry-content pen-bare__content"><?php the_content(); ?></div>
			</div>
		</div>
	<?php endif; ?>
</article>
