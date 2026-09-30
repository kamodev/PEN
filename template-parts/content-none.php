<?php
/**
 * No results.
 *
 * @package PEN
 */

?>
<div class="pen-callout">
	<h3><?php esc_html_e( 'Nothing found', 'pen' ); ?></h3>
	<?php if ( is_search() ) : ?>
		<p><?php esc_html_e( 'No matches for that search. Try different keywords.', 'pen' ); ?></p>
		<?php get_search_form(); ?>
	<?php else : ?>
		<p><?php esc_html_e( 'There is nothing here yet. Check back soon.', 'pen' ); ?></p>
	<?php endif; ?>
</div>
