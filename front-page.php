<?php
/**
 * Front page: hero, stats, training tracks, upcoming classes, mission,
 * instructors, testimonials, gear, resources and newsletter.
 *
 * If a static front page is set and has content, that content is shown
 * after the mission section.
 *
 * @package PEN
 */

get_header();

$sections = apply_filters(
	'pen_front_page_sections',
	array( 'hero', 'stats', 'tracks', 'classes', 'mission', 'page-content', 'instructors', 'testimonials', 'shop', 'resources', 'newsletter' )
);

foreach ( $sections as $section ) {
	get_template_part( 'template-parts/home/' . $section );
}

get_footer();
