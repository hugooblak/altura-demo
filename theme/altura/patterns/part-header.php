<?php
/**
 * Title: Part: header
 * Slug: altura/part-header
 * Inserter: no
 *
 * Template part markup lives in a pattern so links use this site's own address.
 */

// Point the menu at the site's "Main menu" (created by the demo setup, editable in Appearance > Editor > Navigation).
$al_menu = get_posts( array( 'post_type' => 'wp_navigation', 'title' => 'Main menu', 'numberposts' => 1, 'fields' => 'ids', 'post_status' => 'publish' ) );
$al_ref  = $al_menu ? '"ref":' . (int) $al_menu[0] . ',' : '';
?>
<!-- wp:group {"className":"site-header","layout":{"type":"default"}} -->
<div class="wp-block-group site-header"><!-- wp:group {"className":"brand","layout":{"type":"default"}} -->
<div class="wp-block-group brand"><!-- wp:site-title {"level":0,"className":"brand__name"} /-->

<!-- wp:paragraph {"className":"brand__sub"} -->
<p class="brand__sub">Properties</p>
<!-- /wp:paragraph --></div>
<!-- /wp:group -->

<!-- wp:navigation {<?php echo $al_ref; // phpcs:ignore ?>"textColor":"ink","overlayBackgroundColor":"cream","overlayTextColor":"ink","overlayMenu":"mobile","className":"nav","layout":{"type":"flex","justifyContent":"center"}} /-->

<!-- wp:paragraph {"className":"enquire-link"} -->
<p class="enquire-link"><a href="<?php echo esc_url( home_url( '/enquire/' ) ); ?>">Enquire</a></p>
<!-- /wp:paragraph --></div>
<!-- /wp:group -->
