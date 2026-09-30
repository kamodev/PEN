<?php
/**
 * Seed demo content for previewing the theme.
 *
 * Usage: wp eval-file bin/seed-demo.php
 *
 * Creates training tracks, instructors, upcoming classes, testimonials,
 * resource posts, About/Contact pages and a primary menu. Safe to re-run:
 * it skips anything that already exists by title.
 *
 * @package PEN
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Create a post unless one with the same title and type exists.
 *
 * @param array $args wp_insert_post args (+ 'meta', 'terms').
 * @return int Post ID.
 */
function pen_seed_post( $args ) {
	$existing = get_posts(
		array(
			'post_type'      => $args['post_type'],
			'title'          => $args['post_title'],
			'post_status'    => 'any',
			'posts_per_page' => 1,
			'fields'         => 'ids',
		)
	);
	if ( $existing ) {
		return $existing[0];
	}

	$meta  = isset( $args['meta'] ) ? $args['meta'] : array();
	$terms = isset( $args['terms'] ) ? $args['terms'] : array();
	unset( $args['meta'], $args['terms'] );

	$id = wp_insert_post( array_merge( array( 'post_status' => 'publish' ), $args ) );
	foreach ( $meta as $key => $value ) {
		update_post_meta( $id, $key, $value );
	}
	foreach ( $terms as $taxonomy => $slugs ) {
		wp_set_object_terms( $id, $slugs, $taxonomy );
	}
	return $id;
}

// Training tracks.
$tracks = array(
	'emergency-medical'   => array( 'Emergency Medical', 'Stop the Bleed, trauma first aid, CPR and austere medicine.' ),
	'personal-protection' => array( 'Personal Protection', 'Situational awareness, de-escalation and defensive skills.' ),
	'family-preparedness' => array( 'Family Preparedness', 'Go-bags, home plans, water, food storage and backup power.' ),
	'comms-navigation'    => array( 'Comms & Navigation', 'Ham radio, land navigation and grid-down communication plans.' ),
);
foreach ( $tracks as $slug => $track ) {
	if ( ! term_exists( $slug, 'pen_track' ) ) {
		wp_insert_term( $track[0], 'pen_track', array( 'slug' => $slug, 'description' => $track[1] ) );
	}
}

// Instructors.
$instructors = array(
	array( 'Marcus Hale', 'Lead Medical Instructor', 'NREMT-P · Former Army Combat Medic', 1 ),
	array( 'Dana Whitfield', 'Preparedness Director', 'FEMA CERT Trainer · 15 yrs Emergency Management', 2 ),
	array( 'Luis Ortega', 'Protection Instructor', 'Retired Law Enforcement · Defensive Tactics', 3 ),
	array( 'Grace Kim', 'Comms & Navigation', 'Amateur Extra (KJ4PEN) · Search & Rescue', 4 ),
);
$instructor_ids = array();
foreach ( $instructors as $i ) {
	$instructor_ids[] = pen_seed_post(
		array(
			'post_type'    => 'pen_instructor',
			'post_title'   => $i[0],
			'post_content' => sprintf( '%s has spent years teaching everyday people the skills that matter when help is minutes away. Their classes are practical, patient and built around realistic scenarios.', $i[0] ),
			'menu_order'   => $i[3],
			'meta'         => array( '_pen_role' => $i[1], '_pen_credentials' => $i[2] ),
		)
	);
}

// Classes, scheduled relative to today so they always appear as upcoming.
$classes = array(
	array( 'Stop the Bleed: Hemorrhage Control', 6, '', '9:00 AM – 12:00 PM', '3 hours', 'Community Center – Raleigh, NC', '$45', 16, 9, 'beginner', 'emergency-medical', array( 0 ) ),
	array( 'Family Emergency Plan Workshop', 11, '', '1:00 PM – 4:00 PM', '3 hours', 'Online (Live)', 'Free', 50, 32, '', 'family-preparedness', array( 1 ) ),
	array( 'Situational Awareness & De-escalation', 17, '', '8:30 AM – 4:30 PM', '8 hours', 'Training Hall – Durham, NC', '$125', 20, 2, 'beginner', 'personal-protection', array( 2 ) ),
	array( 'Trauma First Aid Level II', 24, 25, '8:00 AM – 5:00 PM', '2 days', 'Field Site – Sanford, NC', '$295', 12, 0, 'intermediate', 'emergency-medical', array( 0, 1 ) ),
	array( 'Intro to Ham Radio & Emergency Comms', 31, '', '9:00 AM – 3:00 PM', '6 hours', 'Community Center – Raleigh, NC', '$75', 24, 14, 'beginner', 'comms-navigation', array( 3 ) ),
	array( 'Land Navigation Field Course', 45, '', '7:30 AM – 3:30 PM', '8 hours', 'State Forest – Hoffman, NC', '$150', 14, 3, 'advanced', 'comms-navigation', array( 3, 2 ) ),
	array( '72-Hour Kit Build Night', -20, '', '6:00 PM – 8:30 PM', '2.5 hours', 'Community Center – Raleigh, NC', '$35', 30, 0, '', 'family-preparedness', array( 1 ) ),
);
$today = current_time( 'timestamp' );
foreach ( $classes as $c ) {
	pen_seed_post(
		array(
			'post_type'    => 'pen_course',
			'post_title'   => $c[0],
			'post_excerpt' => 'Hands-on, instructor-led training.',
			'post_content' => "<!-- wp:paragraph -->\n<p>This hands-on class takes you from the fundamentals to confident action under realistic, time-pressured scenarios. Expect short lectures, lots of repetition, and plenty of time to ask questions.</p>\n<!-- /wp:paragraph -->\n\n<!-- wp:heading -->\n<h2>What You Will Learn</h2>\n<!-- /wp:heading -->\n\n<!-- wp:list -->\n<ul><li>Core principles and why they work</li><li>Step-by-step skills practice with coaching</li><li>Scenario drills that build decision-making</li><li>A take-home reference card and resource list</li></ul>\n<!-- /wp:list -->",
			'meta'         => array(
				'_pen_start_date'   => gmdate( 'Y-m-d', $today + $c[1] * DAY_IN_SECONDS ),
				'_pen_end_date'     => $c[2] ? gmdate( 'Y-m-d', $today + $c[2] * DAY_IN_SECONDS ) : '',
				'_pen_time'         => $c[3],
				'_pen_duration'     => $c[4],
				'_pen_location'     => $c[5],
				'_pen_price'        => $c[6],
				'_pen_capacity'     => $c[7],
				'_pen_seats_left'   => $c[8],
				'_pen_level'        => $c[9],
				'_pen_bring'        => "Water and a packed lunch\nComfortable clothes you can kneel in\nNotebook and pen",
				'_pen_prereqs'      => 'intermediate' === $c[9] || 'advanced' === $c[9] ? 'Completion of an introductory course in this track, or equivalent experience.' : '',
				'_pen_instructors'  => array_map(
					function ( $idx ) use ( $instructor_ids ) {
						return $instructor_ids[ $idx ];
					},
					$c[11]
				),
			),
			'terms'        => array( 'pen_track' => array( $c[10] ) ),
		)
	);
}

// Testimonials.
$quotes = array(
	array( 'Jessica R.', 'I took Stop the Bleed on a Saturday and used a tourniquet on a neighbor two months later. The instructors made it stick.', 'Stop the Bleed', 'Cary, NC' ),
	array( 'Tom & Linda P.', 'Our family finally has a real plan. The workshop broke it into small steps we could actually finish in a weekend.', 'Family Emergency Plan', 'Wake Forest, NC' ),
	array( 'Andre M.', 'Professional, no ego, and incredibly practical. Best training day I have spent in years.', 'Situational Awareness', 'Durham, NC' ),
);
foreach ( $quotes as $i => $q ) {
	pen_seed_post(
		array(
			'post_type'    => 'pen_testimonial',
			'post_title'   => $q[0],
			'post_content' => $q[1],
			'menu_order'   => $i,
			'meta'         => array( '_pen_rating' => '5', '_pen_course' => $q[2], '_pen_hometown' => $q[3] ),
		)
	);
}

// Resource posts.
$cats = array();
foreach ( array( 'Checklists', 'Medical', 'Home Prep' ) as $name ) {
	$term          = term_exists( $name, 'category' );
	$cats[ $name ] = $term ? (int) $term['term_id'] : (int) wp_insert_term( $name, 'category' )['term_id'];
}
$posts = array(
	array( 'The 72-Hour Kit Checklist Every Household Needs', 'Checklists' ),
	array( 'Tourniquets 101: Choosing and Carrying the Right One', 'Medical' ),
	array( 'Storing Water at Home: How Much, How Long, and Where', 'Home Prep' ),
	array( 'Building a Family Communication Plan in One Evening', 'Checklists' ),
);
foreach ( $posts as $i => $p ) {
	pen_seed_post(
		array(
			'post_type'     => 'post',
			'post_title'    => $p[0],
			'post_date'     => gmdate( 'Y-m-d H:i:s', $today - ( $i + 1 ) * 3 * DAY_IN_SECONDS ),
			'post_category' => array( $cats[ $p[1] ] ),
			'post_content'  => "<!-- wp:paragraph -->\n<p>Preparedness does not start with gear — it starts with a plan. In this guide our instructors walk through the essentials, what to prioritize first, and the common mistakes they see in class.</p>\n<!-- /wp:paragraph -->\n\n<!-- wp:heading -->\n<h2>Start With the Basics</h2>\n<!-- /wp:heading -->\n\n<!-- wp:paragraph -->\n<p>Focus on the threats most likely where you live, then build outward. Small, consistent steps beat one big shopping trip.</p>\n<!-- /wp:paragraph -->",
		)
	);
}

// Pages.
$about   = pen_seed_post(
	array(
		'post_type'    => 'page',
		'post_title'   => 'About',
		'post_name'    => 'about',
		'post_excerpt' => 'A network of instructors helping everyday people become capable and prepared.',
		'post_content' => "<!-- wp:paragraph -->\n<p>The Preparedness Education Network was founded on a simple idea: preparedness is a skill, and skills can be taught. We bring together experienced instructors and practical curriculum so anyone can go from uncertain to ready.</p>\n<!-- /wp:paragraph -->",
	)
);
$contact = pen_seed_post(
	array(
		'post_type'    => 'page',
		'post_title'   => 'Contact',
		'post_name'    => 'contact',
		'post_excerpt' => 'Questions, private training requests and partnership inquiries.',
		'post_content' => "<!-- wp:paragraph -->\n<p>Email <a href=\"mailto:info@preparednesseducation.network\">info@preparednesseducation.network</a> and we will get back to you within two business days.</p>\n<!-- /wp:paragraph -->",
	)
);
$blog    = pen_seed_post(
	array(
		'post_type'  => 'page',
		'post_title' => 'Resources',
		'post_name'  => 'resources',
	)
);
$home    = pen_seed_post(
	array(
		'post_type'  => 'page',
		'post_title' => 'Home',
		'post_name'  => 'home',
	)
);
update_option( 'show_on_front', 'page' );
update_option( 'page_on_front', $home );
update_option( 'page_for_posts', $blog );

// Primary menu.
$menu_name = 'Primary';
$menu      = wp_get_nav_menu_object( $menu_name );
if ( ! $menu ) {
	$menu_id = wp_create_nav_menu( $menu_name );
	$classes_item = wp_update_nav_menu_item(
		$menu_id,
		0,
		array(
			'menu-item-title'  => 'Classes',
			'menu-item-url'    => get_post_type_archive_link( 'pen_course' ),
			'menu-item-type'   => 'custom',
			'menu-item-status' => 'publish',
		)
	);
	foreach ( $tracks as $slug => $track ) {
		$term = get_term_by( 'slug', $slug, 'pen_track' );
		wp_update_nav_menu_item(
			$menu_id,
			0,
			array(
				'menu-item-title'     => $track[0],
				'menu-item-object'    => 'pen_track',
				'menu-item-object-id' => $term->term_id,
				'menu-item-type'      => 'taxonomy',
				'menu-item-parent-id' => $classes_item,
				'menu-item-status'    => 'publish',
			)
		);
	}
	wp_update_nav_menu_item(
		$menu_id,
		0,
		array(
			'menu-item-title'  => 'Instructors',
			'menu-item-url'    => get_post_type_archive_link( 'pen_instructor' ),
			'menu-item-type'   => 'custom',
			'menu-item-status' => 'publish',
		)
	);
	foreach ( array( $blog, $about, $contact ) as $page_id ) {
		wp_update_nav_menu_item(
			$menu_id,
			0,
			array(
				'menu-item-object'    => 'page',
				'menu-item-object-id' => $page_id,
				'menu-item-type'      => 'post_type',
				'menu-item-status'    => 'publish',
			)
		);
	}
	$locations            = get_theme_mod( 'nav_menu_locations', array() );
	$locations['primary'] = $menu_id;
	set_theme_mod( 'nav_menu_locations', $locations );
}

// Sidebar widgets (block widgets) for the post/blog and page sidebars.
$sidebars = get_option( 'sidebars_widgets', array() );
$blocks   = get_option( 'widget_block', array() );
$next     = $blocks ? max( array_filter( array_keys( $blocks ), 'is_int' ) + array( 0 ) ) + 1 : 2;
$widgets  = array(
	'sidebar-1'    => array(
		'<!-- wp:search {"label":"Search resources","buttonText":"Search"} /-->',
		'<!-- wp:heading {"level":3} --><h3>Latest Guides</h3><!-- /wp:heading --><!-- wp:latest-posts {"postsToShow":4} /-->',
		'<!-- wp:heading {"level":3} --><h3>Train With Us</h3><!-- /wp:heading --><!-- wp:paragraph --><p>Hands-on classes in emergency medicine, protection and family preparedness.</p><!-- /wp:paragraph --><!-- wp:buttons --><div class="wp-block-buttons"><!-- wp:button --><div class="wp-block-button"><a class="wp-block-button__link wp-element-button" href="' . esc_url( get_post_type_archive_link( 'pen_course' ) ) . '">View Classes</a></div><!-- /wp:button --></div><!-- /wp:buttons -->',
	),
	'sidebar-page' => array(
		'<!-- wp:heading {"level":3} --><h3>Need Help?</h3><!-- /wp:heading --><!-- wp:paragraph --><p>Email info@preparednesseducation.network for private and group training.</p><!-- /wp:paragraph -->',
		'<!-- wp:heading {"level":3} --><h3>Quick Links</h3><!-- /wp:heading --><!-- wp:page-list /-->',
	),
);
foreach ( $widgets as $sidebar_id => $contents ) {
	if ( ! empty( $sidebars[ $sidebar_id ] ) ) {
		continue;
	}
	$sidebars[ $sidebar_id ] = array();
	foreach ( $contents as $content ) {
		$blocks[ $next ]           = array( 'content' => $content );
		$sidebars[ $sidebar_id ][] = 'block-' . $next;
		++$next;
	}
}
$blocks['_multiwidget'] = 1;
update_option( 'widget_block', $blocks );
update_option( 'sidebars_widgets', $sidebars );

// WooCommerce: store pages and a few demo products.
if ( class_exists( 'WooCommerce' ) ) {
	if ( class_exists( 'WC_Install' ) ) {
		WC_Install::create_pages();
	}
	$products = array(
		array( 'IFAK Trauma Kit', '89.00', '74.00', 'A compact individual first aid kit with tourniquet, pressure dressing and chest seals.' ),
		array( 'Stop the Bleed Tourniquet', '32.00', '', 'The windlass tourniquet we train with in every Stop the Bleed class.' ),
		array( '72-Hour Family Go-Bag', '149.00', '', 'Water, food, light, shelter and first aid for two people for three days.' ),
		array( 'Handheld Emergency Radio', '59.00', '', 'Hand-crank and solar NOAA weather radio with phone charging.' ),
	);
	foreach ( $products as $p ) {
		if ( get_posts( array( 'post_type' => 'product', 'title' => $p[0], 'post_status' => 'any', 'fields' => 'ids' ) ) ) {
			continue;
		}
		$product = new WC_Product_Simple();
		$product->set_name( $p[0] );
		$product->set_status( 'publish' );
		$product->set_regular_price( $p[1] );
		if ( $p[2] ) {
			$product->set_sale_price( $p[2] );
		}
		$product->set_short_description( $p[3] );
		$product->set_description( $p[3] . ' Chosen and tested by our instructors.' );
		$product->save();
	}
}

// Demo newsletter action so the form renders (replace with your provider's URL).
if ( ! get_theme_mod( 'pen_news_action' ) ) {
	set_theme_mod( 'pen_news_action', home_url( '/?newsletter=demo' ) );
}

flush_rewrite_rules();

if ( defined( 'WP_CLI' ) && WP_CLI ) {
	WP_CLI::success( 'Demo content ready.' );
}
