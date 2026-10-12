<?php
/**
 * Hero slider, server-rendered from the Properties screen.
 *
 * Without JavaScript the first photo shows and the page works; the script only adds the arrows,
 * the counter and the keyboard keys. The first photo is loaded at high priority, the rest lazily.
 *
 * @var array $attributes Block attributes.
 * @package ALTURAProperties
 */

defined( 'ABSPATH' ) || exit;

$al_count  = max( 1, min( 12, (int) ( $attributes['count'] ?? 6 ) ) );
$al_slides = array();
foreach ( al_properties() as $al_p ) {
	if ( get_post_thumbnail_id( $al_p ) ) {
		$al_slides[] = $al_p;
	}
	if ( count( $al_slides ) >= $al_count ) {
		break;
	}
}
$al_wrapper = get_block_wrapper_attributes(
	array(
		'class'               => 'hero',
		'id'                  => 'top',
		'aria-roledescription' => 'carousel',
		'aria-label'          => __( 'Featured properties', 'altura-leads' ),
	)
);
if ( ! $al_slides ) {
	printf( '<section %s><p style="position:relative;z-index:9;padding:2rem">%s</p></section>', $al_wrapper, esc_html__( 'No properties with a photo yet. Add one under Properties and set its Featured image.', 'altura-leads' ) ); // phpcs:ignore WordPress.Security.EscapeOutput
	return;
}
$al_total = count( $al_slides );
$al_first = $al_slides[0];
?>
<section <?php echo $al_wrapper; // phpcs:ignore WordPress.Security.EscapeOutput ?>>
	<div class="hero__panel" aria-hidden="true"></div>

	<div class="hero__content">
		<p class="counter" aria-hidden="true"><span class="counter__cur">01</span><span class="counter__tot"> / <?php echo esc_html( str_pad( (string) $al_total, 2, '0', STR_PAD_LEFT ) ); ?></span></p>
		<div class="hero__title">
			<h1 class="headline"><span class="headline__a"><?php echo esc_html( $attributes['headlineA'] ?? 'Beyond' ); ?></span><em class="headline__b"><?php echo esc_html( $attributes['headlineB'] ?? 'Extraordinary' ); ?></em></h1>
			<p class="sub"><?php echo esc_html( $attributes['subA'] ?? '' ); ?><br><?php echo esc_html( $attributes['subB'] ?? '' ); ?></p>
		</div>
	</div>

	<div class="rail">
		<span class="rail__line" aria-hidden="true"></span>
		<span class="rail__dot" aria-hidden="true"></span>
		<span class="rail__label" aria-hidden="true"><?php esc_html_e( 'Discover', 'altura-leads' ); ?></span>
		<a class="rail__btn" href="#philosophy" aria-label="<?php esc_attr_e( 'Scroll to our philosophy', 'altura-leads' ); ?>">
			<svg viewBox="0 0 24 24" width="16" height="16" fill="none" stroke="currentColor" stroke-width="1.2" aria-hidden="true"><path d="M7 17 17 7M8 7h9v9"/></svg>
		</a>
	</div>

	<div class="hero__media">
		<div class="slides">
			<?php
			foreach ( $al_slides as $al_i => $al_p ) :
				$al_focus = get_post_meta( $al_p->ID, 'focus', true );
				$al_focus = '' === $al_focus ? 50 : max( 0, min( 100, (int) $al_focus ) );
				$al_img   = al_property_img(
					$al_p,
					'full',
					array(
						'class'         => '',
						'loading'       => 0 === $al_i ? 'eager' : 'lazy',
						'fetchpriority' => 0 === $al_i ? 'high' : 'low',
						'sizes'         => '(min-width: 960px) 68vw, 100vw',
					)
				);
				?>
				<figure class="slide<?php echo 0 === $al_i ? ' is-active' : ''; ?>" role="group" aria-roledescription="slide"
					aria-label="<?php echo esc_attr( sprintf( /* translators: 1: slide number, 2: total. */ __( '%1$d of %2$d', 'altura-leads' ), $al_i + 1, $al_total ) ); ?>"
					<?php echo 0 === $al_i ? '' : 'aria-hidden="true"'; ?>
					data-name="<?php echo esc_attr( $al_p->post_title ); ?>" data-loc="<?php echo esc_attr( get_post_meta( $al_p->ID, 'place', true ) ); ?>"
					data-url="<?php echo esc_url( get_permalink( $al_p ) ); ?>"
					style="--pos:<?php echo (int) $al_focus; ?>%">
					<?php echo $al_img; // phpcs:ignore WordPress.Security.EscapeOutput ?>
				</figure>
			<?php endforeach; ?>
		</div>
		<div class="controls" data-controls hidden>
			<button class="controls__btn" type="button" data-dir="-1" aria-label="<?php esc_attr_e( 'Previous property', 'altura-leads' ); ?>">
				<svg viewBox="0 0 40 12" width="40" height="12" fill="none" stroke="currentColor" stroke-width="1" aria-hidden="true"><path d="M39 6H1M6 1 1 6l5 5"/></svg>
			</button>
			<span class="controls__rule" aria-hidden="true"></span>
			<button class="controls__btn" type="button" data-dir="1" aria-label="<?php esc_attr_e( 'Next property', 'altura-leads' ); ?>">
				<svg viewBox="0 0 40 12" width="40" height="12" fill="none" stroke="currentColor" stroke-width="1" aria-hidden="true"><path d="M1 6h38M34 1l5 5-5 5"/></svg>
			</button>
		</div>
	</div>

	<a class="caption" href="<?php echo esc_url( get_permalink( $al_first ) ); ?>" data-caption>
		<span class="caption__name"><?php echo esc_html( $al_first->post_title ); ?></span>
		<span class="caption__loc"><?php echo esc_html( get_post_meta( $al_first->ID, 'place', true ) ); ?></span>
	</a>
	<p class="sr-only" id="slide-status" aria-live="polite"></p>
</section>
