<?php
/**
 * Theme settings: colors and sidebar layout.
 *
 * Values resolve in three layers:
 *   1. the site's own value (Appearance → Theme Settings or the Customizer),
 *   2. the network default (multisite only, Network Admin → Themes → PEN Theme Defaults),
 *   3. the theme default.
 * An empty site value means "inherit". A network admin can lock colors or
 * layout so every site uses the network values.
 *
 * @package PEN
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Editable colors: key => label, default, CSS custom property, help text.
 *
 * @return array
 */
function pen_color_fields() {
	return array(
		'color_accent'       => array( __( 'Accent', 'pen' ), '#e0621b', '--pen-accent', __( 'Buttons, highlights, announcement bar.', 'pen' ) ),
		'color_accent_hover' => array( __( 'Accent hover', 'pen' ), '#c24f10', '--pen-accent-hover', __( 'Buttons when hovered.', 'pen' ) ),
		'color_accent_ink'   => array( __( 'Text on accent', 'pen' ), '#ffffff', '--pen-accent-ink', __( 'Button and announcement text.', 'pen' ) ),
		'color_olive'        => array( __( 'Secondary', 'pen' ), '#4b5320', '--pen-olive', __( 'Stats strip, header rule, active filters, links.', 'pen' ) ),
		'color_sand'         => array( __( 'Muted accent', 'pen' ), '#c8b88a', '--pen-sand', __( 'Taglines, breadcrumbs and captions on dark areas.', 'pen' ) ),
		'color_dark'         => array( __( 'Header & footer', 'pen' ), '#16181a', '--pen-dark', __( 'Header, footer, page banners, dark buttons.', 'pen' ) ),
		'color_dark_2'       => array( __( 'Dark panels', 'pen' ), '#22261f', '--pen-dark-2', __( 'Testimonials, dropdowns, image placeholders.', 'pen' ) ),
		'color_bg'           => array( __( 'Page background', 'pen' ), '#f4f2ec', '--pen-bg', '' ),
		'color_surface'      => array( __( 'Cards & panels', 'pen' ), '#ffffff', '--pen-surface', '' ),
		'color_surface_alt'  => array( __( 'Alternate sections', 'pen' ), '#e9e6dc', '--pen-surface-alt', __( 'Banded sections and callouts.', 'pen' ) ),
		'color_ink'          => array( __( 'Text', 'pen' ), '#1b1d1a', '--pen-ink', '' ),
		'color_ink_soft'     => array( __( 'Secondary text', 'pen' ), '#4a4d45', '--pen-ink-soft', __( 'Excerpts, meta and intros.', 'pen' ) ),
		'color_border'       => array( __( 'Borders', 'pen' ), '#d6d1c2', '--pen-border', '' ),
	);
}

/**
 * Sidebar contexts: key => label.
 *
 * @return array
 */
function pen_sidebar_contexts() {
	return array(
		'post'    => __( 'Posts', 'pen' ),
		'page'    => __( 'Pages', 'pen' ),
		'archive' => __( 'Blog, archives & search', 'pen' ),
	);
}

/**
 * Layout fields: key => choices, default.
 *
 * @return array
 */
function pen_layout_fields() {
	$fields   = array();
	$defaults = array(
		'post'    => 'show',
		'page'    => 'hide',
		'archive' => 'show',
	);
	foreach ( $defaults as $context => $default ) {
		$fields[ $context . '_sidebar' ]          = array(
			array(
				'show' => __( 'Show sidebar', 'pen' ),
				'hide' => __( 'No sidebar (full width)', 'pen' ),
			),
			$default,
		);
		$fields[ $context . '_sidebar_position' ] = array(
			array(
				'right' => __( 'Right', 'pen' ),
				'left'  => __( 'Left', 'pen' ),
			),
			'right',
		);
	}
	return $fields;
}

/**
 * Theme defaults for every setting.
 *
 * @return array
 */
function pen_setting_defaults() {
	$defaults = array();
	foreach ( pen_color_fields() as $key => $field ) {
		$defaults[ $key ] = $field[1];
	}
	foreach ( pen_layout_fields() as $key => $field ) {
		$defaults[ $key ] = $field[1];
	}
	return apply_filters( 'pen_setting_defaults', $defaults );
}

/**
 * Settings group for a key.
 *
 * @param string $key Setting key.
 * @return string 'colors' or 'layout'.
 */
function pen_setting_group( $key ) {
	return 0 === strpos( $key, 'color_' ) ? 'colors' : 'layout';
}

/**
 * Network defaults (empty array outside multisite).
 *
 * @return array
 */
function pen_network_settings() {
	return is_multisite() ? (array) get_site_option( 'pen_network_settings', array() ) : array();
}

/**
 * This site's saved settings.
 *
 * Before the first save, colors from the old Customizer theme mods are used.
 *
 * @return array
 */
function pen_site_settings() {
	$settings = get_option( 'pen_settings', false );
	if ( false === $settings ) {
		return pen_legacy_settings();
	}
	return (array) $settings;
}

/**
 * Colors saved by theme version 1.0 as Customizer theme mods.
 *
 * @return array
 */
function pen_legacy_settings() {
	$map      = array(
		'pen_accent_color' => 'color_accent',
		'pen_olive_color'  => 'color_olive',
		'pen_dark_color'   => 'color_dark',
	);
	$settings = array();
	foreach ( $map as $mod => $key ) {
		$value = sanitize_hex_color( (string) get_theme_mod( $mod, '' ) );
		if ( $value && strtolower( $value ) !== pen_setting_defaults()[ $key ] ) {
			$settings[ $key ] = $value;
		}
	}
	return $settings;
}

/**
 * Move 1.0 theme-mod colors into the settings option once.
 */
function pen_migrate_legacy_settings() {
	if ( false !== get_option( 'pen_settings', false ) ) {
		return;
	}
	$legacy = pen_legacy_settings();
	if ( $legacy ) {
		update_option( 'pen_settings', $legacy );
	}
	foreach ( array( 'pen_accent_color', 'pen_olive_color', 'pen_dark_color' ) as $mod ) {
		remove_theme_mod( $mod );
	}
}
add_action( 'admin_init', 'pen_migrate_legacy_settings' );

/**
 * Whether the network admin has locked a settings group.
 *
 * @param string $group 'colors' or 'layout'.
 * @return bool
 */
function pen_is_locked( $group ) {
	$network = pen_network_settings();
	return ! empty( $network[ 'lock_' . $group ] );
}

/**
 * Value a site inherits when it leaves a setting blank.
 *
 * @param string $key Setting key.
 * @return string
 */
function pen_inherited_setting( $key ) {
	$network  = pen_network_settings();
	$defaults = pen_setting_defaults();
	if ( isset( $network[ $key ] ) && '' !== $network[ $key ] ) {
		return $network[ $key ];
	}
	return isset( $defaults[ $key ] ) ? $defaults[ $key ] : '';
}

/**
 * Resolved value of a setting for the current site.
 *
 * @param string $key Setting key.
 * @return string
 */
function pen_setting( $key ) {
	if ( ! pen_is_locked( pen_setting_group( $key ) ) ) {
		$site = pen_site_settings();
		if ( isset( $site[ $key ] ) && '' !== $site[ $key ] ) {
			return $site[ $key ];
		}
	}
	return pen_inherited_setting( $key );
}

/**
 * Sanitize settings, keeping existing values for keys not in the input.
 *
 * Merging lets each settings tab save only its own fields, and lets the
 * Customizer save single colors.
 *
 * @param array $input      Raw input.
 * @param array $existing   Current saved values.
 * @param bool  $with_locks Whether lock flags are allowed (network only).
 * @return array
 */
function pen_sanitize_settings( $input, $existing, $with_locks = false ) {
	$input = is_array( $input ) ? $input : array();
	$out   = array();

	foreach ( pen_color_fields() as $key => $field ) {
		$value = array_key_exists( $key, $input ) ? $input[ $key ] : ( isset( $existing[ $key ] ) ? $existing[ $key ] : '' );
		$value = sanitize_hex_color( trim( (string) $value ) );
		if ( $value ) {
			$out[ $key ] = strtolower( $value );
		}
	}

	foreach ( pen_layout_fields() as $key => $field ) {
		$value = array_key_exists( $key, $input ) ? $input[ $key ] : ( isset( $existing[ $key ] ) ? $existing[ $key ] : '' );
		if ( array_key_exists( (string) $value, $field[0] ) ) {
			$out[ $key ] = (string) $value;
		}
	}

	if ( $with_locks ) {
		foreach ( array( 'colors', 'layout' ) as $group ) {
			$out[ 'lock_' . $group ] = ! empty( $input[ 'lock_' . $group ] );
		}
	}

	return $out;
}

/**
 * Sanitize callback for the site option.
 *
 * @param array $input Raw input.
 * @return array
 */
function pen_sanitize_site_settings( $input ) {
	$existing = get_option( 'pen_settings', array() );
	return pen_sanitize_settings( $input, is_array( $existing ) ? $existing : array() );
}

/**
 * Register the site option.
 */
function pen_register_settings() {
	register_setting(
		'pen_settings',
		'pen_settings',
		array(
			'type'              => 'object',
			'sanitize_callback' => 'pen_sanitize_site_settings',
			'default'           => array(),
			'show_in_rest'      => false,
		)
	);
}
add_action( 'admin_init', 'pen_register_settings' );
add_action( 'rest_api_init', 'pen_register_settings' );

/**
 * CSS custom properties for the resolved colors.
 *
 * @return string
 */
function pen_color_css() {
	$css = ':root{';
	foreach ( pen_color_fields() as $key => $field ) {
		$value = sanitize_hex_color( pen_setting( $key ) );
		if ( $value ) {
			$css .= $field[2] . ':' . $value . ';';
		}
	}
	return $css . '}';
}

/**
 * Color presets for the settings screens.
 *
 * @return array name => [label, colors]
 */
function pen_color_presets() {
	$presets = array(
		'field'    => array( __( 'Field Olive (default)', 'pen' ), array() ),
		// Navy & Blaze uses the NRDS brand orange.
		'navy'     => array(
			__( 'Navy & Blaze', 'pen' ),
			array(
				'color_accent'       => '#f26a1b',
				'color_accent_hover' => '#d4550c',
				'color_accent_ink'   => '#ffffff',
				'color_olive'        => '#16325a',
				'color_sand'         => '#9fb3cf',
				'color_dark'         => '#061326',
				'color_dark_2'       => '#0b1f3a',
				'color_bg'           => '#f5f6f8',
				'color_surface'      => '#ffffff',
				'color_surface_alt'  => '#e7ebf1',
				'color_ink'          => '#0f1a2a',
				'color_ink_soft'     => '#45526a',
				'color_border'       => '#d3d9e3',
			),
		),
		'woodland' => array(
			__( 'Woodland', 'pen' ),
			array(
				'color_accent'       => '#a85a14',
				'color_accent_hover' => '#8c4a0f',
				'color_accent_ink'   => '#ffffff',
				'color_olive'        => '#3b5a2e',
				'color_sand'         => '#b9c79a',
				'color_dark'         => '#141a13',
				'color_dark_2'       => '#1f2a1d',
				'color_bg'           => '#f1f3ee',
				'color_surface'      => '#ffffff',
				'color_surface_alt'  => '#e1e7dc',
				'color_ink'          => '#1a2118',
				'color_ink_soft'     => '#4a5646',
				'color_border'       => '#cfd8c8',
			),
		),
		'desert'   => array(
			__( 'Desert Tan', 'pen' ),
			array(
				'color_accent'       => '#b5541c',
				'color_accent_hover' => '#96440f',
				'color_accent_ink'   => '#ffffff',
				'color_olive'        => '#6f6034',
				'color_sand'         => '#d9c39a',
				'color_dark'         => '#2b2219',
				'color_dark_2'       => '#3a2e22',
				'color_bg'           => '#f6f1e7',
				'color_surface'      => '#fffdf8',
				'color_surface_alt'  => '#ece2cf',
				'color_ink'          => '#2a2118',
				'color_ink_soft'     => '#5d5040',
				'color_border'       => '#dccfb7',
			),
		),
	);

	// The default preset is the theme defaults.
	foreach ( pen_color_fields() as $key => $field ) {
		$presets['field'][1][ $key ] = $field[1];
	}

	return apply_filters( 'pen_color_presets', $presets );
}
