<?php
/**
 * Template Name: Full Width (no banner)
 * Template Post Type: page
 *
 * Edge-to-edge canvas for block-built landing pages.
 *
 * @package PEN
 */

get_header();

while ( have_posts() ) :
	the_post();
	?>
	<article id="post-<?php the_ID(); ?>" <?php post_class( 'pen-canvas' ); ?>>
		<?php the_content(); ?>
	</article>
	<?php
endwhile;

get_footer();
