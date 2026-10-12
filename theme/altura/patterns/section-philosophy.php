<?php
/**
 * Title: Section: our philosophy
 * Slug: altura/section-philosophy
 * Categories: altura-sections
 * Description: Dark section with the agency's belief and three photo cards.
 */

$al_img = function ( $name ) {
	return esc_url( get_theme_file_uri( 'assets/img/' . $name ) );
};
?>
<!-- wp:group {"tagName":"section","anchor":"philosophy","className":"philo","layout":{"type":"default"}} -->
<section id="philosophy" class="wp-block-group philo"><!-- wp:group {"className":"philo__inner","layout":{"type":"default"}} -->
<div class="wp-block-group philo__inner"><!-- wp:group {"className":"philo__text","layout":{"type":"default"}} -->
<div class="wp-block-group philo__text"><!-- wp:paragraph {"className":"eyebrow reveal"} -->
<p class="eyebrow reveal">Our philosophy</p>
<!-- /wp:paragraph -->

<!-- wp:heading {"className":"philo__title reveal"} -->
<h2 class="wp-block-heading philo__title reveal">We believe extraordinary living begins with <em>exceptional spaces.</em></h2>
<!-- /wp:heading -->

<!-- wp:paragraph {"className":"philo__body reveal"} -->
<p class="philo__body reveal">At ALTURA Properties we curate more than homes. We curate ways of living. Each property is chosen for its architecture, its setting and its ability to inspire.</p>
<!-- /wp:paragraph -->

<!-- wp:paragraph {"className":"more reveal"} -->
<p class="more reveal"><a href="<?php echo esc_url( home_url( '/philosophy/' ) ); ?>">Read our philosophy</a></p>
<!-- /wp:paragraph --></div>
<!-- /wp:group -->

<!-- wp:group {"className":"cards","layout":{"type":"default"}} -->
<div class="wp-block-group cards"><!-- wp:group {"className":"card reveal","layout":{"type":"default"}} -->
<div class="wp-block-group card reveal"><!-- wp:image {"sizeSlug":"large","linkDestination":"none","className":"card__img"} -->
<figure class="wp-block-image size-large card__img"><img src="<?php echo $al_img( 'card-1.jpg' ); // phpcs:ignore ?>" alt="Living room with curved bouclé seating, a travertine table and a floor-to-ceiling view of the sea at dusk"/></figure>
<!-- /wp:image -->

<!-- wp:group {"className":"card__body","layout":{"type":"default"}} -->
<div class="wp-block-group card__body"><!-- wp:paragraph {"className":"card__num"} -->
<p class="card__num">01</p>
<!-- /wp:paragraph -->

<!-- wp:separator {"className":"card__rule"} -->
<hr class="wp-block-separator has-alpha-channel-opacity card__rule"/>
<!-- /wp:separator -->

<!-- wp:heading {"level":3,"className":"card__title"} -->
<h3 class="wp-block-heading card__title">Curated<br>Selection</h3>
<!-- /wp:heading --></div>
<!-- /wp:group --></div>
<!-- /wp:group -->

<!-- wp:group {"className":"card reveal","layout":{"type":"default"}} -->
<div class="wp-block-group card reveal"><!-- wp:image {"sizeSlug":"large","linkDestination":"none","className":"card__img"} -->
<figure class="wp-block-image size-large card__img"><img src="<?php echo $al_img( 'card-2.jpg' ); // phpcs:ignore ?>" alt="Tall arched opening in a travertine wall framing an olive tree and the sea"/></figure>
<!-- /wp:image -->

<!-- wp:group {"className":"card__body","layout":{"type":"default"}} -->
<div class="wp-block-group card__body"><!-- wp:paragraph {"className":"card__num"} -->
<p class="card__num">02</p>
<!-- /wp:paragraph -->

<!-- wp:separator {"className":"card__rule"} -->
<hr class="wp-block-separator has-alpha-channel-opacity card__rule"/>
<!-- /wp:separator -->

<!-- wp:heading {"level":3,"className":"card__title"} -->
<h3 class="wp-block-heading card__title">Tailored<br>Experiences</h3>
<!-- /wp:heading --></div>
<!-- /wp:group --></div>
<!-- /wp:group -->

<!-- wp:group {"className":"card reveal","layout":{"type":"default"}} -->
<div class="wp-block-group card reveal"><!-- wp:image {"sizeSlug":"large","linkDestination":"none","className":"card__img"} -->
<figure class="wp-block-image size-large card__img"><img src="<?php echo $al_img( 'card-3.jpg' ); // phpcs:ignore ?>" alt="Board-formed concrete wall crossed by a blade of warm sunlight above a still black water channel"/></figure>
<!-- /wp:image -->

<!-- wp:group {"className":"card__body","layout":{"type":"default"}} -->
<div class="wp-block-group card__body"><!-- wp:paragraph {"className":"card__num"} -->
<p class="card__num">03</p>
<!-- /wp:paragraph -->

<!-- wp:separator {"className":"card__rule"} -->
<hr class="wp-block-separator has-alpha-channel-opacity card__rule"/>
<!-- /wp:separator -->

<!-- wp:heading {"level":3,"className":"card__title"} -->
<h3 class="wp-block-heading card__title">Discretion &amp;<br>Trust</h3>
<!-- /wp:heading --></div>
<!-- /wp:group --></div>
<!-- /wp:group --></div>
<!-- /wp:group --></div>
<!-- /wp:group --></section>
<!-- /wp:group -->
