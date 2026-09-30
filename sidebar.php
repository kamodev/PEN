<?php
/**
 * Sidebar.
 *
 * @package PEN
 */

if ( ! is_active_sidebar( 'sidebar-1' ) ) {
	return;
}
?>
<aside class="pen-sidebar" aria-label="<?php esc_attr_e( 'Sidebar', 'pen' ); ?>">
	<?php dynamic_sidebar( 'sidebar-1' ); ?>
</aside>
