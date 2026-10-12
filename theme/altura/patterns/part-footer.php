<?php
/**
 * Title: Part: footer
 * Slug: altura/part-footer
 * Inserter: no
 *
 * Template part markup lives in a pattern so links use this site's own address.
 */

$al_menu = get_posts( array( 'post_type' => 'wp_navigation', 'title' => 'Main menu', 'numberposts' => 1, 'fields' => 'ids', 'post_status' => 'publish' ) );
$al_ref  = $al_menu ? '"ref":' . (int) $al_menu[0] . ',' : '';
?>
<!-- wp:group {"className":"footer","layout":{"type":"default"}} -->
<div class="wp-block-group footer"><!-- wp:group {"className":"footer__brand","layout":{"type":"default"}} -->
<div class="wp-block-group footer__brand"><!-- wp:site-title {"level":0,"className":"brand__name"} /-->

<!-- wp:paragraph {"className":"brand__sub"} -->
<p class="brand__sub">Properties</p>
<!-- /wp:paragraph --></div>
<!-- /wp:group -->

<!-- wp:navigation {<?php echo $al_ref; // phpcs:ignore ?>"overlayMenu":"never","className":"footer__nav","layout":{"type":"flex","justifyContent":"center"}} /-->

<!-- wp:paragraph {"className":"footer__note"} -->
<p class="footer__note">Fictional agency, built as a portfolio demo. All properties, places and photographs are invented. <a href="<?php echo esc_url( home_url( '/privacy-policy/' ) ); ?>">Privacy</a></p>
<!-- /wp:paragraph --></div>
<!-- /wp:group -->
