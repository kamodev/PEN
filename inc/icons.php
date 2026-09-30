<?php
/**
 * Inline SVG icons.
 *
 * @package PEN
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Return an inline SVG icon.
 *
 * @param string $name Icon name.
 * @return string SVG markup (trusted, theme-defined).
 */
function pen_get_icon( $name ) {
	$paths = array(
		'menu'      => '<path d="M3 6h18M3 12h18M3 18h18"/>',
		'close'     => '<path d="M6 6l12 12M18 6L6 18"/>',
		'search'    => '<circle cx="11" cy="11" r="7"/><path d="M20 20l-3.5-3.5"/>',
		'cart'      => '<path d="M3 4h2l2.4 11.2a2 2 0 0 0 2 1.6h7.7a2 2 0 0 0 2-1.5L21 8H6.2"/><circle cx="10" cy="20" r="1.3"/><circle cx="17" cy="20" r="1.3"/>',
		'user'      => '<circle cx="12" cy="8" r="4"/><path d="M4 21c1.5-4 4.5-6 8-6s6.5 2 8 6"/>',
		'calendar'  => '<rect x="3" y="5" width="18" height="16" rx="1"/><path d="M3 10h18M8 3v4M16 3v4"/>',
		'clock'     => '<circle cx="12" cy="12" r="9"/><path d="M12 7v5l3 2"/>',
		'pin'       => '<path d="M12 21s-7-6.2-7-11.5A7 7 0 0 1 19 9.5C19 14.8 12 21 12 21z"/><circle cx="12" cy="9.5" r="2.5"/>',
		'users'     => '<circle cx="9" cy="8" r="3.5"/><path d="M2 20c1-3.5 3.7-5.5 7-5.5s6 2 7 5.5"/><path d="M16 4.5a3.5 3.5 0 0 1 0 7M18 14.5c2 .7 3.3 2.6 4 5.5"/>',
		'arrow'     => '<path d="M5 12h14M13 6l6 6-6 6"/>',
		'up'        => '<path d="M12 19V5M6 11l6-6 6 6"/>',
		'shield'    => '<path d="M12 3l8 3v6c0 4.5-3.4 8.3-8 9-4.6-.7-8-4.5-8-9V6z"/><path d="M9 12l2 2 4-4"/>',
		'medical'   => '<rect x="3" y="6" width="18" height="14" rx="1"/><path d="M9 6V4h6v2M12 10v6M9 13h6"/>',
		'target'    => '<circle cx="12" cy="12" r="9"/><circle cx="12" cy="12" r="5"/><circle cx="12" cy="12" r="1"/>',
		'home'      => '<path d="M3 11l9-7 9 7"/><path d="M5 10v10h14V10"/><path d="M10 20v-6h4v6"/>',
		'radio'     => '<rect x="3" y="9" width="18" height="12" rx="1"/><path d="M7 9l10-5"/><circle cx="8" cy="15" r="2"/><path d="M14 14h4M14 17h4"/>',
		'compass'   => '<circle cx="12" cy="12" r="9"/><path d="M15.5 8.5l-2 5-5 2 2-5z"/>',
		'fire'      => '<path d="M12 22c4 0 7-2.8 7-7 0-3.5-2.5-6-4-8-.5 2-1.5 3-3 3 0-3-1-6-4-8 0 4-4 7-4 12 0 4.2 3.5 8 8 8z"/>',
		'water'     => '<path d="M12 3s-6 7-6 11a6 6 0 0 0 12 0c0-4-6-11-6-11z"/>',
		'book'      => '<path d="M4 4h6a2 2 0 0 1 2 2v14a2 2 0 0 0-2-2H4zM20 4h-6a2 2 0 0 0-2 2v14a2 2 0 0 1 2-2h6z"/>',
		'facebook'  => '<path d="M14 8h3V4h-3a4 4 0 0 0-4 4v3H7v4h3v7h4v-7h3l1-4h-4V8z"/>',
		'instagram' => '<rect x="3" y="3" width="18" height="18" rx="5"/><circle cx="12" cy="12" r="4"/><circle cx="17.5" cy="6.5" r=".6"/>',
		'youtube'   => '<rect x="2" y="5" width="20" height="14" rx="4"/><path d="M10 9l5 3-5 3z"/>',
		'x'         => '<path d="M4 4l16 16M20 4L4 20"/>',
		'rumble'    => '<path d="M8 6l9 6-9 6z"/><circle cx="12" cy="12" r="10"/>',
		'podcast'   => '<circle cx="12" cy="10" r="3"/><path d="M12 13v8M6.3 15.5A8 8 0 1 1 17.7 15.5"/>',
		'email'     => '<rect x="3" y="5" width="18" height="14" rx="1"/><path d="M3 7l9 6 9-6"/>',
	);

	if ( ! isset( $paths[ $name ] ) ) {
		return '';
	}

	return '<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true" focusable="false">' . $paths[ $name ] . '</svg>';
}

/**
 * Echo an inline SVG icon.
 *
 * @param string $name Icon name.
 */
function pen_icon( $name ) {
	echo pen_get_icon( $name ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- theme-defined static SVG.
}

/**
 * Icon choices for training tracks.
 *
 * @return array
 */
function pen_track_icon_choices() {
	return array(
		'shield'  => __( 'Shield (self-defense)', 'pen' ),
		'medical' => __( 'Medical kit', 'pen' ),
		'target'  => __( 'Target (firearms)', 'pen' ),
		'home'    => __( 'Home (family prep)', 'pen' ),
		'radio'   => __( 'Radio (comms)', 'pen' ),
		'compass' => __( 'Compass (land nav)', 'pen' ),
		'fire'    => __( 'Fire (bushcraft)', 'pen' ),
		'water'   => __( 'Water (sustainment)', 'pen' ),
		'book'    => __( 'Book (education)', 'pen' ),
		'users'   => __( 'People (community)', 'pen' ),
	);
}
