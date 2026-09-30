<?php
/**
 * Sidebar layout for posts, pages and archives.
 *
 * Each context has its own on/off and left/right setting (Appearance →
 * Theme Settings → Layout). Individual posts and pages can override the
 * default in the "Sidebar" box on the edit screen.
 *
 * @package PEN
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Widget area used by each context.
 *
 * @return array context => sidebar ID
 */
function pen_sidebar_areas() {
	return array(
		'post'    => 'sidebar-1',
		'page'    => 'sidebar-page',
		'archive' => 'sidebar-1',
	);
}

/**
 * Current sidebar context, or null where the theme uses its own layout
 * (front page, classes, instructors, WooCommerce, full-width template).
 *
 * @return string|null
 */
function pen_sidebar_context() {
	if ( is_front_page() || ( function_exists( 'is_woocommerce' ) && is_woocommerce() ) ) {
		return null;
	}
	if ( is_singular( 'post' ) ) {
		return 'post';
	}
	if ( is_page() ) {
		return is_page_template( 'page-full-width.php' ) ? null : 'page';
	}
	if ( is_post_type_archive( array( 'pen_course', 'pen_instructor' ) ) || is_tax( 'pen_track' ) ) {
		return null;
	}
	if ( is_home() || is_archive() || is_search() ) {
		return 'archive';
	}
	return null;
}

/**
 * Sidebar to show on the current view.
 *
 * @return array|null { id, position } or null for no sidebar.
 */
function pen_get_sidebar() {
	$context = pen_sidebar_context();
	if ( ! $context ) {
		return null;
	}

	$show = 'show' === pen_setting( $context . '_sidebar' );

	if ( is_singular() ) {
		$override = get_post_meta( get_queried_object_id(), '_pen_sidebar', true );
		if ( 'show' === $override || 'hide' === $override ) {
			$show = 'show' === $override;
		}
	}

	$areas = pen_sidebar_areas();
	if ( ! $show || ! is_active_sidebar( $areas[ $context ] ) ) {
		return null;
	}

	return array(
		'id'       => $areas[ $context ],
		'position' => 'left' === pen_setting( $context . '_sidebar_position' ) ? 'left' : 'right',
	);
}

/**
 * Classes for the content/sidebar grid.
 *
 * @return string
 */
function pen_layout_class() {
	$sidebar = pen_get_sidebar();
	if ( ! $sidebar ) {
		return 'pen-layout pen-layout--full';
	}
	return 'pen-layout pen-layout--' . $sidebar['position'];
}

/**
 * Body classes for sidebar state.
 *
 * @param array $classes Body classes.
 * @return array
 */
function pen_sidebar_body_class( $classes ) {
	$sidebar   = pen_get_sidebar();
	$classes[] = $sidebar ? 'has-sidebar sidebar-' . $sidebar['position'] : 'no-sidebar';
	return $classes;
}
add_filter( 'body_class', 'pen_sidebar_body_class' );

/**
 * Per-post "Sidebar" box on posts and pages.
 */
function pen_add_sidebar_meta_box() {
	add_meta_box( 'pen-sidebar', __( 'Sidebar', 'pen' ), 'pen_render_sidebar_meta_box', array( 'post', 'page' ), 'side', 'default' );
}
add_action( 'add_meta_boxes', 'pen_add_sidebar_meta_box' );

/**
 * Render the "Sidebar" box.
 *
 * @param WP_Post $post Post being edited.
 */
function pen_render_sidebar_meta_box( $post ) {
	$value   = get_post_meta( $post->ID, '_pen_sidebar', true );
	$context = 'page' === $post->post_type ? 'page' : 'post';
	$default = 'show' === pen_setting( $context . '_sidebar' ) ? __( 'show', 'pen' ) : __( 'hide', 'pen' );

	wp_nonce_field( 'pen_sidebar_meta', 'pen_sidebar_nonce' );
	?>
	<p>
		<label class="screen-reader-text" for="pen-sidebar-choice"><?php esc_html_e( 'Sidebar', 'pen' ); ?></label>
		<select id="pen-sidebar-choice" name="pen_sidebar" style="width:100%">
			<option value="" <?php selected( $value, '' ); ?>>
				<?php
				/* translators: %s: show or hide. */
				echo esc_html( sprintf( __( 'Theme default (%s)', 'pen' ), $default ) );
				?>
			</option>
			<option value="show" <?php selected( $value, 'show' ); ?>><?php esc_html_e( 'Show sidebar', 'pen' ); ?></option>
			<option value="hide" <?php selected( $value, 'hide' ); ?>><?php esc_html_e( 'No sidebar (full width)', 'pen' ); ?></option>
		</select>
	</p>
	<?php if ( current_user_can( 'edit_theme_options' ) ) : ?>
		<p class="description">
			<?php
			printf(
				/* translators: 1: settings page link, 2: widgets page link. */
				wp_kses_post( __( 'Change the default in <a href="%1$s">Theme Settings</a>. Add sidebar content in <a href="%2$s">Widgets</a>.', 'pen' ) ),
				esc_url( admin_url( 'themes.php?page=pen-settings&tab=layout' ) ),
				esc_url( admin_url( 'widgets.php' ) )
			);
			?>
		</p>
	<?php endif; ?>
	<?php
}

/**
 * Save the "Sidebar" box.
 *
 * @param int $post_id Post ID.
 */
function pen_save_sidebar_meta( $post_id ) {
	if ( ! isset( $_POST['pen_sidebar_nonce'] ) || ! wp_verify_nonce( sanitize_key( wp_unslash( $_POST['pen_sidebar_nonce'] ) ), 'pen_sidebar_meta' ) ) {
		return;
	}
	if ( ( defined( 'DOING_AUTOSAVE' ) && DOING_AUTOSAVE ) || ! current_user_can( 'edit_post', $post_id ) ) {
		return;
	}
	$value = isset( $_POST['pen_sidebar'] ) ? sanitize_key( wp_unslash( $_POST['pen_sidebar'] ) ) : '';
	if ( in_array( $value, array( 'show', 'hide' ), true ) ) {
		update_post_meta( $post_id, '_pen_sidebar', $value );
	} else {
		delete_post_meta( $post_id, '_pen_sidebar' );
	}
}
add_action( 'save_post', 'pen_save_sidebar_meta' );
