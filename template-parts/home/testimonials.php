<?php
/**
 * Home: testimonials.
 *
 * @package PEN
 */

if ( ! pen_mod( 'pen_testimonials_enable' ) ) {
	return;
}

$quotes = new WP_Query(
	array(
		'post_type'      => 'pen_testimonial',
		'posts_per_page' => 3,
		'orderby'        => array( 'menu_order' => 'ASC', 'date' => 'DESC' ),
		'no_found_rows'  => true,
	)
);

if ( ! $quotes->have_posts() ) {
	return;
}
?>
<section class="pen-section pen-section--dark" id="testimonials">
	<div class="pen-container">
		<div class="pen-section-head pen-section-head--center">
			<span class="pen-eyebrow"><?php esc_html_e( 'After Action Reports', 'pen' ); ?></span>
			<h2><?php esc_html_e( 'What Students Say', 'pen' ); ?></h2>
		</div>
		<div class="pen-grid pen-grid--3">
			<?php
			while ( $quotes->have_posts() ) :
				$quotes->the_post();
				$rating   = max( 1, min( 5, (int) get_post_meta( get_the_ID(), '_pen_rating', true ) ?: 5 ) );
				$course   = get_post_meta( get_the_ID(), '_pen_course', true );
				$hometown = get_post_meta( get_the_ID(), '_pen_hometown', true );
				?>
				<figure class="pen-quote">
					<div class="pen-quote__stars" role="img" aria-label="<?php echo esc_attr( sprintf( /* translators: %d: rating */ __( '%d out of 5 stars', 'pen' ), $rating ) ); ?>"><?php echo esc_html( str_repeat( '★', $rating ) . str_repeat( '☆', 5 - $rating ) ); ?></div>
					<blockquote><?php echo wp_kses_post( wpautop( get_the_content() ) ); ?></blockquote>
					<figcaption>
						<?php if ( has_post_thumbnail() ) : ?>
							<?php the_post_thumbnail( 'thumbnail', array( 'alt' => '' ) ); ?>
						<?php endif; ?>
						<span>
							<span class="pen-quote__name"><?php the_title(); ?></span>
							<span class="pen-quote__course"><?php echo esc_html( implode( ' · ', array_filter( array( $course, $hometown ) ) ) ); ?></span>
						</span>
					</figcaption>
				</figure>
			<?php endwhile; ?>
			<?php wp_reset_postdata(); ?>
		</div>
	</div>
</section>
