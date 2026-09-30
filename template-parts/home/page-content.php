<?php
/**
 * Home: content of the static front page, if any.
 *
 * @package PEN
 */

if ( 'page' !== get_option( 'show_on_front' ) || ! have_posts() ) {
	return;
}

the_post();
if ( '' === trim( get_the_content() ) ) {
	return;
}
?>
<section class="pen-section pen-section--alt">
	<div class="pen-container">
		<div class="entry-content"><?php the_content(); ?></div>
	</div>
</section>
