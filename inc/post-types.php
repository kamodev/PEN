<?php
/**
 * Custom post types and taxonomies: Classes, Instructors, Testimonials.
 *
 * Note: content types normally belong in a plugin. They live here so the theme
 * works out of the box; if you move them to a plugin, keep the same slugs.
 *
 * @package PEN
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Register post types and taxonomies.
 */
function pen_register_content_types() {
	register_post_type(
		'pen_course',
		array(
			'labels'        => array(
				'name'          => __( 'Classes', 'pen' ),
				'singular_name' => __( 'Class', 'pen' ),
				'add_new_item'  => __( 'Add New Class', 'pen' ),
				'edit_item'     => __( 'Edit Class', 'pen' ),
				'all_items'     => __( 'All Classes', 'pen' ),
				'view_item'     => __( 'View Class', 'pen' ),
				'search_items'  => __( 'Search Classes', 'pen' ),
				'menu_name'     => __( 'Classes', 'pen' ),
			),
			'public'        => true,
			'has_archive'   => 'classes',
			'rewrite'       => array( 'slug' => 'classes', 'with_front' => false ),
			'menu_icon'     => 'dashicons-calendar-alt',
			'menu_position' => 5,
			'show_in_rest'  => true,
			'supports'      => array( 'title', 'editor', 'excerpt', 'thumbnail', 'revisions' ),
		)
	);

	register_taxonomy(
		'pen_track',
		array( 'pen_course' ),
		array(
			'labels'            => array(
				'name'          => __( 'Training Tracks', 'pen' ),
				'singular_name' => __( 'Training Track', 'pen' ),
				'add_new_item'  => __( 'Add New Track', 'pen' ),
				'menu_name'     => __( 'Tracks', 'pen' ),
			),
			'hierarchical'      => true,
			'show_admin_column' => true,
			'show_in_rest'      => true,
			'rewrite'           => array( 'slug' => 'track', 'with_front' => false ),
		)
	);

	register_post_type(
		'pen_instructor',
		array(
			'labels'        => array(
				'name'          => __( 'Instructors', 'pen' ),
				'singular_name' => __( 'Instructor', 'pen' ),
				'add_new_item'  => __( 'Add New Instructor', 'pen' ),
				'edit_item'     => __( 'Edit Instructor', 'pen' ),
			),
			'public'        => true,
			'has_archive'   => 'instructors',
			'rewrite'       => array( 'slug' => 'instructors', 'with_front' => false ),
			'menu_icon'     => 'dashicons-id',
			'menu_position' => 6,
			'show_in_rest'  => true,
			'supports'      => array( 'title', 'editor', 'excerpt', 'thumbnail', 'page-attributes' ),
		)
	);

	register_post_type(
		'pen_testimonial',
		array(
			'labels'              => array(
				'name'          => __( 'Testimonials', 'pen' ),
				'singular_name' => __( 'Testimonial', 'pen' ),
				'add_new_item'  => __( 'Add New Testimonial', 'pen' ),
				'edit_item'     => __( 'Edit Testimonial', 'pen' ),
			),
			'public'              => false,
			'show_ui'             => true,
			'exclude_from_search' => true,
			'menu_icon'           => 'dashicons-format-quote',
			'menu_position'       => 7,
			'show_in_rest'        => true,
			'supports'            => array( 'title', 'editor', 'thumbnail', 'page-attributes' ),
		)
	);
}
add_action( 'init', 'pen_register_content_types' );

/**
 * Flush rewrite rules when the theme is activated so /classes/ works immediately.
 */
function pen_flush_rewrites() {
	pen_register_content_types();
	flush_rewrite_rules();
}
add_action( 'after_switch_theme', 'pen_flush_rewrites' );

/**
 * Class archives: show upcoming classes soonest-first; ?when=past shows past classes.
 *
 * @param WP_Query $query Main query.
 */
function pen_course_archive_query( $query ) {
	if ( is_admin() || ! $query->is_main_query() ) {
		return;
	}
	if ( ! $query->is_post_type_archive( 'pen_course' ) && ! $query->is_tax( 'pen_track' ) ) {
		return;
	}

	$past = isset( $_GET['when'] ) && 'past' === sanitize_key( wp_unslash( $_GET['when'] ) ); // phpcs:ignore WordPress.Security.NonceVerification.Recommended

	$query->set( 'meta_key', '_pen_start_date' );
	$query->set( 'orderby', 'meta_value' );
	$query->set( 'order', $past ? 'DESC' : 'ASC' );
	$query->set( 'posts_per_page', 20 );
	$query->set(
		'meta_query',
		array(
			array(
				'key'     => '_pen_start_date',
				'value'   => current_time( 'Y-m-d' ),
				'compare' => $past ? '<' : '>=',
				'type'    => 'CHAR', // Y-m-d strings sort and compare correctly as text.
			),
		)
	);
}
add_action( 'pre_get_posts', 'pen_course_archive_query' );

/**
 * Query upcoming classes (used on the front page).
 *
 * @param int $count Number of classes.
 * @return WP_Query
 */
function pen_upcoming_courses( $count = 5 ) {
	return new WP_Query(
		array(
			'post_type'           => 'pen_course',
			'posts_per_page'      => $count,
			'ignore_sticky_posts' => true,
			'no_found_rows'       => true,
			'meta_key'            => '_pen_start_date', // phpcs:ignore WordPress.DB.SlowDBQuery.slow_db_query_meta_key
			'orderby'             => 'meta_value',
			'order'               => 'ASC',
			'meta_query'          => array( // phpcs:ignore WordPress.DB.SlowDBQuery.slow_db_query_meta_query
				array(
					'key'     => '_pen_start_date',
					'value'   => current_time( 'Y-m-d' ),
					'compare' => '>=',
					'type'    => 'CHAR', // Y-m-d strings sort and compare correctly as text.
				),
			),
		)
	);
}

/**
 * Admin list columns for classes.
 *
 * @param array $columns Columns.
 * @return array
 */
function pen_course_columns( $columns ) {
	$new = array();
	foreach ( $columns as $key => $label ) {
		$new[ $key ] = $label;
		if ( 'title' === $key ) {
			$new['pen_date']  = __( 'Class Date', 'pen' );
			$new['pen_seats'] = __( 'Seats Left', 'pen' );
		}
	}
	return $new;
}
add_filter( 'manage_pen_course_posts_columns', 'pen_course_columns' );

/**
 * Render admin list columns for classes.
 *
 * @param string $column  Column key.
 * @param int    $post_id Post ID.
 */
function pen_course_column_content( $column, $post_id ) {
	if ( 'pen_date' === $column ) {
		$date = get_post_meta( $post_id, '_pen_start_date', true );
		echo $date ? esc_html( date_i18n( get_option( 'date_format' ), strtotime( $date ) ) ) : '&mdash;';
	} elseif ( 'pen_seats' === $column ) {
		$seats = get_post_meta( $post_id, '_pen_seats_left', true );
		echo '' === $seats ? '&mdash;' : esc_html( $seats );
	}
}
add_action( 'manage_pen_course_posts_custom_column', 'pen_course_column_content', 10, 2 );
