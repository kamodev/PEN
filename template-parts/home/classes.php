<?php
/**
 * Home: upcoming classes.
 *
 * @package PEN
 */

$classes = pen_upcoming_courses( max( 1, (int) pen_mod( 'pen_classes_count' ) ) );
?>
<section class="pen-section pen-section--alt" id="schedule">
	<div class="pen-container">
		<div class="pen-section-head">
			<span class="pen-eyebrow"><?php esc_html_e( 'Class Schedule', 'pen' ); ?></span>
			<h2><?php echo esc_html( pen_mod( 'pen_classes_title' ) ); ?></h2>
			<p><?php echo esc_html( pen_mod( 'pen_classes_text' ) ); ?></p>
		</div>

		<?php if ( $classes->have_posts() ) : ?>
			<div class="pen-schedule">
				<?php
				while ( $classes->have_posts() ) :
					$classes->the_post();
					get_template_part( 'template-parts/course-row' );
				endwhile;
				wp_reset_postdata();
				?>
			</div>
		<?php else : ?>
			<div class="pen-callout">
				<h3><?php esc_html_e( 'New dates coming soon', 'pen' ); ?></h3>
				<p><?php esc_html_e( 'Join the newsletter to be the first to know when registration opens.', 'pen' ); ?></p>
			</div>
		<?php endif; ?>

		<div class="pen-section-foot">
			<a class="pen-btn pen-btn--dark" href="<?php echo esc_url( get_post_type_archive_link( 'pen_course' ) ); ?>"><?php esc_html_e( 'Full Class Schedule', 'pen' ); ?></a>
		</div>
	</div>
</section>
