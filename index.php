<?php
/**
 * Main template: blog, archives and search results.
 *
 * @package PEN
 */

get_header();

if ( is_search() ) {
	/* translators: %s: search query. */
	$pen_title = sprintf( __( 'Search: %s', 'pen' ), get_search_query() );
	$pen_sub   = '';
} elseif ( is_archive() ) {
	$pen_title = wp_strip_all_tags( get_the_archive_title() );
	$pen_sub   = get_the_archive_description();
} elseif ( is_home() && get_option( 'page_for_posts' ) ) {
	$pen_title = get_the_title( get_option( 'page_for_posts' ) );
	$pen_sub   = '';
} else {
	$pen_title = __( 'Resources', 'pen' );
	$pen_sub   = __( 'Guides, checklists and field notes from our instructors.', 'pen' );
}

pen_page_hero( $pen_title, $pen_sub );
?>
<div class="pen-section">
	<div class="pen-container <?php echo esc_attr( pen_layout_class() ); ?>">
		<div>
			<?php if ( have_posts() ) : ?>
				<div class="pen-grid pen-grid--<?php echo pen_get_sidebar() ? '2' : '3'; ?>">
					<?php
					while ( have_posts() ) :
						the_post();
						get_template_part( 'template-parts/content', 'card' );
					endwhile;
					?>
				</div>
				<?php pen_pagination(); ?>
			<?php else : ?>
				<?php get_template_part( 'template-parts/content', 'none' ); ?>
			<?php endif; ?>
		</div>
		<?php get_sidebar(); ?>
	</div>
</div>
<?php
get_footer();
