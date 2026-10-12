<?php
/**
 * Facts for one property: place, price, status, bedrooms and so on, plus the Enquire button.
 *
 * @var array    $attributes Block attributes.
 * @var string   $content    Inner content.
 * @var WP_Block $block      Block instance (carries the post it sits in).
 * @package ALTURAProperties
 */

defined( 'ABSPATH' ) || exit;

$al_id = isset( $block->context['postId'] ) ? (int) $block->context['postId'] : (int) get_the_ID();
if ( ! $al_id || 'al_property' !== get_post_type( $al_id ) ) {
	return;
}
$al_post     = get_post( $al_id );
$al_status   = get_post_meta( $al_id, 'status', true ) ?: 'available';
$al_statuses = al_property_statuses();
$al_facts    = al_facts( $al_id );
$al_ref      = get_post_meta( $al_id, 'ref', true );
$al_place    = get_post_meta( $al_id, 'place', true );
$al_wrapper  = get_block_wrapper_attributes( array( 'class' => 'al-facts' ) );
?>
<div <?php echo $al_wrapper; // phpcs:ignore WordPress.Security.EscapeOutput ?>>
	<div class="al-facts__head">
		<?php if ( $al_place ) : ?>
			<p class="al-facts__place"><?php echo esc_html( $al_place ); ?></p>
		<?php endif; ?>
		<p class="al-facts__price"><?php echo esc_html( al_price( $al_id ) ); ?></p>
		<p class="al-facts__status al-facts__status--<?php echo esc_attr( $al_status ); ?>"><?php echo esc_html( $al_statuses[ $al_status ] ?? '' ); ?></p>
	</div>
	<?php if ( $al_facts ) : ?>
		<dl class="al-facts__list">
			<?php foreach ( $al_facts as $al_label => $al_value ) : ?>
				<div><dt><?php echo esc_html( $al_label ); ?></dt><dd><?php echo esc_html( $al_value ); ?></dd></div>
			<?php endforeach; ?>
			<?php if ( $al_ref ) : ?>
				<div><dt><?php esc_html_e( 'Reference', 'altura-leads' ); ?></dt><dd><?php echo esc_html( $al_ref ); ?></dd></div>
			<?php endif; ?>
		</dl>
	<?php endif; ?>
	<p class="al-facts__cta">
		<?php if ( 'sold' === $al_status ) : ?>
			<span class="al-facts__note"><?php esc_html_e( 'This property has been sold. Ask about similar residences.', 'altura-leads' ); ?></span>
			<a class="al-btn" href="<?php echo esc_url( al_enquire_url() ); ?>"><?php esc_html_e( 'Ask about similar', 'altura-leads' ); ?></a>
		<?php else : ?>
			<a class="al-btn" href="<?php echo esc_url( al_enquire_url( $al_post->post_name ) ); ?>"><?php esc_html_e( 'Enquire about this property', 'altura-leads' ); ?></a>
		<?php endif; ?>
	</p>
</div>
