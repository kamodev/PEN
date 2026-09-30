<?php
/**
 * Sidebar for posts, pages and archives.
 *
 * Which widget area shows, and whether it shows at all, is decided by
 * pen_get_sidebar() from Appearance → Theme Settings and the per-post
 * "Sidebar" box.
 *
 * @package PEN
 */

$pen_sidebar = pen_get_sidebar();
if ( ! $pen_sidebar ) {
	return;
}
?>
<aside class="pen-sidebar pen-sidebar--<?php echo esc_attr( $pen_sidebar['position'] ); ?>" aria-label="<?php esc_attr_e( 'Sidebar', 'pen' ); ?>">
	<?php dynamic_sidebar( $pen_sidebar['id'] ); ?>
</aside>
