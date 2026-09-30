<?php
/**
 * Search form.
 *
 * @package PEN
 */

$pen_search_id = wp_unique_id( 'pen-search-' );
?>
<form role="search" method="get" class="pen-search-form" action="<?php echo esc_url( home_url( '/' ) ); ?>">
	<label class="screen-reader-text" for="<?php echo esc_attr( $pen_search_id ); ?>"><?php esc_html_e( 'Search for:', 'pen' ); ?></label>
	<input type="search" id="<?php echo esc_attr( $pen_search_id ); ?>" name="s" value="<?php echo esc_attr( get_search_query() ); ?>" placeholder="<?php esc_attr_e( 'Search classes, guides and gear…', 'pen' ); ?>">
	<button type="submit"><?php esc_html_e( 'Search', 'pen' ); ?></button>
</form>
