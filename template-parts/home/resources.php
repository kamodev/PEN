<?php
/**
 * Home: latest resources (blog posts).
 *
 * @package PEN
 */

if ( ! pen_mod( 'pen_resources_enable' ) ) {
	return;
}

$posts_query = new WP_Query(
	array(
		'post_type'           => 'post',
		'posts_per_page'      => 3,
		'ignore_sticky_posts' => true,
		'no_found_rows'       => true,
	)
);

if ( ! $posts_query->have_posts() ) {
	return;
}

$blog_id  = (int) get_option( 'page_for_posts' );
$blog_url = $blog_id ? get_permalink( $blog_id ) : home_url( '/?post_type=post' );
?>
<section class="pen-section" id="resources">
	<div class="pen-container">
		<div class="pen-section-head">
			<span class="pen-eyebrow"><?php esc_html_e( 'Resource Library', 'pen' ); ?></span>
			<h2><?php esc_html_e( 'Guides, Checklists & Field Notes', 'pen' ); ?></h2>
			<p><?php esc_html_e( 'Free education from our instructors — start preparing today.', 'pen' ); ?></p>
		</div>
		<div class="pen-grid pen-grid--3">
			<?php
			while ( $posts_query->have_posts() ) :
				$posts_query->the_post();
				get_template_part( 'template-parts/content', 'card' );
			endwhile;
			wp_reset_postdata();
			?>
		</div>
		<div class="pen-section-foot">
			<a class="pen-btn pen-btn--dark" href="<?php echo esc_url( $blog_url ); ?>"><?php esc_html_e( 'Browse All Resources', 'pen' ); ?></a>
		</div>
	</div>
</section>
