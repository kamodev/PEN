<?php
/**
 * Post card (blog / resources / search).
 *
 * @package PEN
 */

$terms = 'post' === get_post_type() ? get_the_category() : array();
?>
<article <?php post_class( 'pen-card' ); ?>>
	<a class="pen-card__media" href="<?php the_permalink(); ?>" tabindex="-1" aria-hidden="true">
		<?php
		if ( has_post_thumbnail() ) {
			the_post_thumbnail( 'pen-card', array( 'alt' => '' ) );
		} else {
			echo '<div class="pen-placeholder">' . pen_get_icon( 'book' ) . '</div>'; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
		}
		if ( $terms ) {
			echo '<span class="pen-card__badge">' . esc_html( $terms[0]->name ) . '</span>';
		}
		?>
	</a>
	<div class="pen-card__body">
		<h3 class="pen-card__title"><a href="<?php the_permalink(); ?>"><?php the_title(); ?></a></h3>
		<?php if ( 'post' === get_post_type() ) : ?>
			<div class="pen-card__meta"><time datetime="<?php echo esc_attr( get_the_date( DATE_W3C ) ); ?>"><?php echo esc_html( get_the_date() ); ?></time></div>
		<?php endif; ?>
		<div class="pen-card__excerpt"><?php the_excerpt(); ?></div>
		<div class="pen-card__foot">
			<a class="pen-track__more" href="<?php the_permalink(); ?>"><?php esc_html_e( 'Read more', 'pen' ); ?> &rarr;<span class="screen-reader-text"> <?php the_title(); ?></span></a>
		</div>
	</div>
</article>
