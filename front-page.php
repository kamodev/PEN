<?php
/**
 * Front page: hero, stats, training tracks, upcoming classes, mission,
 * instructors, testimonials, resources and newsletter. The WooCommerce
 * integration adds a gear section (inc/woocommerce/hooks.php).
 *
 * If a static front page is set and has content, that content is shown
 * after the mission section.
 *
 * @package PEN
 */

get_header();

$sections = apply_filters(
	'pen_front_page_sections',
	array( 'hero', 'stats', 'tracks', 'classes', 'mission', 'page-content', 'instructors', 'testimonials', 'resources', 'newsletter' )
);

foreach ( $sections as $section ) {
	// Integrations render their own sections (e.g. the WooCommerce gear grid).
	if ( has_action( 'pen_home_section_' . $section ) ) {
		do_action( 'pen_home_section_' . $section );
	} else {
		get_template_part( 'template-parts/home/' . $section );
	}
}

get_footer();
