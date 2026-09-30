<?php
/**
 * Home: instructors.
 *
 * @package PEN
 */

if ( ! pen_mod( 'pen_instructors_enable' ) ) {
	return;
}

$instructors = new WP_Query(
	array(
		'post_type'      => 'pen_instructor',
		'posts_per_page' => 4,
		'orderby'        => array( 'menu_order' => 'ASC', 'title' => 'ASC' ),
		'no_found_rows'  => true,
	)
);

if ( ! $instructors->have_posts() ) {
	return;
}
?>
<section class="pen-section pen-section--alt" id="instructors">
	<div class="pen-container">
		<div class="pen-section-head pen-section-head--center">
			<span class="pen-eyebrow"><?php esc_html_e( 'Cadre', 'pen' ); ?></span>
			<h2><?php esc_html_e( 'Learn From People Who Have Done It', 'pen' ); ?></h2>
			<p><?php esc_html_e( 'Medics, first responders, veterans and lifelong teachers.', 'pen' ); ?></p>
		</div>
		<div class="pen-grid pen-grid--4">
			<?php
			while ( $instructors->have_posts() ) :
				$instructors->the_post();
				get_template_part( 'template-parts/instructor-card' );
			endwhile;
			wp_reset_postdata();
			?>
		</div>
	</div>
</section>
