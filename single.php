<?php
/**
 * Single post.
 *
 * @package PEN
 */

get_header();

while ( have_posts() ) :
	the_post();
	?>
	<header class="pen-page-hero">
		<div class="pen-container">
			<?php pen_breadcrumbs(); ?>
			<h1><?php the_title(); ?></h1>
			<?php if ( 'post' === get_post_type() ) : ?>
				<div class="entry-meta"><?php pen_posted_on(); ?></div>
			<?php endif; ?>
		</div>
	</header>

	<div class="pen-section">
		<div class="pen-container pen-layout<?php echo is_active_sidebar( 'sidebar-1' ) ? '' : ' pen-layout--full'; ?>">
			<article id="post-<?php the_ID(); ?>" <?php post_class(); ?>>
				<?php if ( has_post_thumbnail() ) : ?>
					<figure class="pen-featured entry-content"><?php the_post_thumbnail( 'large' ); ?></figure>
				<?php endif; ?>
				<div class="entry-content">
					<?php
					the_content();
					wp_link_pages();
					the_tags( '<p class="entry-meta">' . esc_html__( 'Tags: ', 'pen' ), ', ', '</p>' );
					?>
				</div>
				<div class="entry-content">
					<?php
					the_post_navigation(
						array(
							'prev_text' => '&larr; %title',
							'next_text' => '%title &rarr;',
						)
					);
					if ( comments_open() || get_comments_number() ) {
						comments_template();
					}
					?>
				</div>
			</article>
			<?php get_sidebar(); ?>
		</div>
	</div>
	<?php
endwhile;

get_footer();
