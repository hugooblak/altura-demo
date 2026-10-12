<?php
/**
 * Title: Page: Thank you
 * Slug: altura/thank-you
 * Categories: altura-pages
 * Description: Shown after an enquiry is sent.
 */
?>
<!-- wp:group {"tagName":"section","className":"dark-page thanks","layout":{"type":"default"}} -->
<section class="wp-block-group dark-page thanks"><!-- wp:paragraph {"className":"eyebrow"} -->
<p class="eyebrow">Enquiry received</p>
<!-- /wp:paragraph -->

<!-- wp:heading {"level":1,"className":"dark-page__title"} -->
<h1 class="wp-block-heading dark-page__title">Thank you. <em>We will be in touch.</em></h1>
<!-- /wp:heading -->

<!-- wp:paragraph {"className":"dark-page__lede"} -->
<p class="dark-page__lede">Your enquiry is saved in the admin under Enquiries. On a real site it is also emailed to the agent as soon as you send it.</p>
<!-- /wp:paragraph -->

<!-- wp:buttons -->
<div class="wp-block-buttons"><!-- wp:button -->
<div class="wp-block-button"><a class="wp-block-button__link wp-element-button" href="<?php echo esc_url( home_url( '/properties/' ) ); ?>">Back to the properties</a></div>
<!-- /wp:button --></div>
<!-- /wp:buttons --></section>
<!-- /wp:group -->
