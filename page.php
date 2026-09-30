<?php
/**
 * Page.
 *
 * @package PEN
 */

get_header();

while ( have_posts() ) :
	the_post();
	pen_page_hero( get_the_title(), has_excerpt() ? get_the_excerpt() : '' );
	?>
	<div class="pen-section">
		<div class="pen-container <?php echo esc_attr( pen_layout_class() ); ?>">
			<article id="post-<?php the_ID(); ?>" <?php post_class(); ?>>
				<div class="entry-content">
					<?php
					the_content();
					wp_link_pages();
					?>
				</div>
				<?php if ( comments_open() || get_comments_number() ) : ?>
					<div class="entry-content"><?php comments_template(); ?></div>
				<?php endif; ?>
			</article>
			<?php get_sidebar(); ?>
		</div>
	</div>
	<?php
endwhile;

get_footer();
