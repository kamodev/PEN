<?php
/**
 * Preparedness Education Network theme functions.
 *
 * @package PEN
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

define( 'PEN_VERSION', '1.3.0' );
define( 'PEN_DIR', get_template_directory() );
define( 'PEN_URI', get_template_directory_uri() );

require PEN_DIR . '/inc/icons.php';
require PEN_DIR . '/inc/post-types.php';
require PEN_DIR . '/inc/meta-boxes.php';
require PEN_DIR . '/inc/settings.php';
require PEN_DIR . '/inc/layout.php';
require PEN_DIR . '/inc/customizer.php';
require PEN_DIR . '/inc/template-tags.php';

// Plugin integrations. Each file checks that its plugin is active.
require PEN_DIR . '/inc/woocommerce/woocommerce.php';
require PEN_DIR . '/inc/integrations/elementor.php';
require PEN_DIR . '/inc/integrations/amelia.php';
require PEN_DIR . '/inc/integrations/mailpoet.php';

if ( is_admin() ) {
	require PEN_DIR . '/inc/admin-settings.php';
}

/**
 * Theme setup.
 */
function pen_setup() {
	load_theme_textdomain( 'pen', PEN_DIR . '/languages' );

	add_theme_support( 'automatic-feed-links' );
	add_theme_support( 'title-tag' );
	add_theme_support( 'post-thumbnails' );
	add_theme_support( 'responsive-embeds' );
	add_theme_support( 'align-wide' );
	add_theme_support( 'wp-block-styles' );
	add_theme_support( 'editor-styles' );
	add_theme_support( 'customize-selective-refresh-widgets' );
	add_theme_support(
		'html5',
		array( 'search-form', 'comment-form', 'comment-list', 'gallery', 'caption', 'style', 'script', 'navigation-widgets' )
	);
	add_theme_support(
		'custom-logo',
		array(
			'height'      => 112,
			'width'       => 320,
			'flex-height' => true,
			'flex-width'  => true,
		)
	);

	add_image_size( 'pen-card', 720, 450, true );
	add_image_size( 'pen-square', 600, 600, true );
	add_image_size( 'pen-hero', 1920, 1080, true );

	register_nav_menus(
		array(
			'primary' => __( 'Primary Menu', 'pen' ),
			'footer'  => __( 'Footer Legal Menu', 'pen' ),
		)
	);

	add_theme_support(
		'editor-color-palette',
		array(
			array( 'name' => __( 'Accent', 'pen' ), 'slug' => 'blaze', 'color' => pen_setting( 'color_accent' ) ),
			array( 'name' => __( 'Secondary', 'pen' ), 'slug' => 'olive', 'color' => pen_setting( 'color_olive' ) ),
			array( 'name' => __( 'Muted Accent', 'pen' ), 'slug' => 'coyote', 'color' => pen_setting( 'color_sand' ) ),
			array( 'name' => __( 'Dark', 'pen' ), 'slug' => 'gunmetal', 'color' => pen_setting( 'color_dark' ) ),
			array( 'name' => __( 'Background', 'pen' ), 'slug' => 'paper', 'color' => pen_setting( 'color_bg' ) ),
		)
	);

	add_editor_style(
		array_merge(
			array( pen_fonts_url() ),
			array_map(
				function ( $part ) {
					return 'assets/css/' . $part . '.css';
				},
				array( 'tokens', 'base', 'buttons', 'content', 'wordpress' )
			)
		)
	);
}
add_action( 'after_setup_theme', 'pen_setup' );

/**
 * Content width.
 */
function pen_content_width() {
	$GLOBALS['content_width'] = apply_filters( 'pen_content_width', 780 );
}
add_action( 'after_setup_theme', 'pen_content_width', 0 );

/**
 * Google Fonts URL.
 */
function pen_fonts_url() {
	return 'https://fonts.googleapis.com/css2?family=Inter:wght@400;600;700&family=Oswald:wght@500;600;700&display=swap';
}

/**
 * Widget areas.
 */
function pen_widgets_init() {
	$shared = array(
		'before_widget' => '<section id="%1$s" class="widget %2$s">',
		'after_widget'  => '</section>',
		'before_title'  => '<h2 class="widget-title">',
		'after_title'   => '</h2>',
	);

	register_sidebar(
		array_merge(
			$shared,
			array(
				'name'        => __( 'Post & Blog Sidebar', 'pen' ),
				'id'          => 'sidebar-1',
				'description' => __( 'Shown beside single posts, the blog, archives and search results when enabled in Appearance → Theme Settings.', 'pen' ),
			)
		)
	);

	register_sidebar(
		array_merge(
			$shared,
			array(
				'name'        => __( 'Page Sidebar', 'pen' ),
				'id'          => 'sidebar-page',
				'description' => __( 'Shown beside pages when enabled in Appearance → Theme Settings.', 'pen' ),
			)
		)
	);

	for ( $i = 1; $i <= 3; $i++ ) {
		register_sidebar(
			array_merge(
				$shared,
				array(
					/* translators: %d: footer column number. */
					'name' => sprintf( __( 'Footer Column %d', 'pen' ), $i ),
					'id'   => 'footer-' . $i,
				)
			)
		);
	}
}
add_action( 'widgets_init', 'pen_widgets_init' );

/**
 * Stylesheet parts in assets/css/, in load order.
 *
 * @return string[]
 */
function pen_style_parts() {
	return apply_filters(
		'pen_style_parts',
		array( 'tokens', 'base', 'buttons', 'header', 'hero', 'cards', 'schedule', 'sections', 'content', 'footer', 'wordpress' )
	);
}

/**
 * Scripts and styles.
 */
function pen_scripts() {
	wp_enqueue_style( 'pen-fonts', pen_fonts_url(), array(), null );

	// Each part depends on the previous one so they print in order.
	$previous = 'pen-fonts';
	foreach ( pen_style_parts() as $part ) {
		$handle = 'pen-' . $part;
		wp_enqueue_style( $handle, PEN_URI . '/assets/css/' . $part . '.css', array( $previous ), PEN_VERSION );
		$previous = $handle;
	}

	// Colors from Theme Settings (site, then network, then theme defaults) override the tokens.
	wp_add_inline_style( 'pen-tokens', pen_color_css() );

	// Child themes' style.css loads after the parent's parts.
	if ( is_child_theme() ) {
		wp_enqueue_style( 'pen-child', get_stylesheet_uri(), array( $previous ), wp_get_theme()->get( 'Version' ) );
	}

	wp_enqueue_script( 'pen-main', PEN_URI . '/assets/js/main.js', array(), PEN_VERSION, true );

	if ( is_singular() && comments_open() && get_option( 'thread_comments' ) ) {
		wp_enqueue_script( 'comment-reply' );
	}
}
add_action( 'wp_enqueue_scripts', 'pen_scripts' );

/**
 * Apply Theme Settings colors inside the block editor.
 */
function pen_editor_colors() {
	wp_add_inline_style( 'wp-block-library', '.editor-styles-wrapper' . substr( pen_color_css(), 5 ) );
}
add_action( 'enqueue_block_editor_assets', 'pen_editor_colors' );

/**
 * Preconnect to Google Fonts.
 *
 * @param array  $urls          URLs to print for resource hints.
 * @param string $relation_type The relation type the URLs are printed for.
 * @return array
 */
function pen_resource_hints( $urls, $relation_type ) {
	if ( 'preconnect' === $relation_type && wp_style_is( 'pen-fonts', 'queue' ) ) {
		$urls[] = array( 'href' => 'https://fonts.gstatic.com', 'crossorigin' );
	}
	return $urls;
}
add_filter( 'wp_resource_hints', 'pen_resource_hints', 10, 2 );

/**
 * Excerpt length and suffix.
 */
add_filter(
	'excerpt_length',
	function () {
		return 24;
	}
);
add_filter(
	'excerpt_more',
	function () {
		return '&hellip;';
	}
);
