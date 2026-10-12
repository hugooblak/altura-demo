<?php
/**
 * Title: Section: enquire
 * Slug: altura/section-enquire
 * Categories: altura-sections
 * Description: Wide photograph, a short invitation and the enquiry form.
 */
?>
<!-- wp:group {"tagName":"section","anchor":"enquire","className":"enq","layout":{"type":"default"}} -->
<section id="enquire" class="wp-block-group enq"><!-- wp:image {"sizeSlug":"full","linkDestination":"none","className":"enq__wide"} -->
<figure class="wp-block-image size-full enq__wide"><img src="<?php echo esc_url( get_theme_file_uri( 'assets/img/enquire-wide.jpg' ) ); ?>" alt="Villa terrace at blue hour with a lit glass pavilion mirrored in a long still pool"/></figure>
<!-- /wp:image -->

<!-- wp:group {"className":"enq__inner","layout":{"type":"default"}} -->
<div class="wp-block-group enq__inner"><!-- wp:group {"className":"enq__intro","layout":{"type":"default"}} -->
<div class="wp-block-group enq__intro"><!-- wp:paragraph {"className":"eyebrow reveal"} -->
<p class="eyebrow reveal">Enquire</p>
<!-- /wp:paragraph -->

<!-- wp:heading {"className":"enq__title reveal"} -->
<h2 class="wp-block-heading enq__title reveal">Begin a private <em>conversation.</em></h2>
<!-- /wp:heading -->

<!-- wp:paragraph {"className":"enq__note reveal"} -->
<p class="enq__note reveal">Tell us what you are looking for. A member of the team replies personally. This is a demo: the enquiry is saved in the site's admin and no email leaves the site.</p>
<!-- /wp:paragraph --></div>
<!-- /wp:group -->

<!-- wp:altura/lead-form /--></div>
<!-- /wp:group --></section>
<!-- /wp:group -->
