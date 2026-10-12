<?php
/**
 * Properties, drawn from the Properties screen in the admin.
 *
 * Two ways of showing the same content, so a price only ever exists in one place:
 *   grid — photo, number, name and place. The home page.
 *   list — the grid plus price, status and the key facts. The Properties page.
 *
 * Every card is one link to the property's own page. The photo is decorative there (the link
 * already says the name and place), so it carries an empty alt.
 *
 * @var array $attributes Block attributes.
 * @package ALTURAProperties
 */

defined( 'ABSPATH' ) || exit;

$al_display = 'list' === ( $attributes['display'] ?? 'grid' ) ? 'list' : 'grid';
$al_count   = max( 1, min( 24, (int) ( $attributes['count'] ?? 6 ) ) );
$al_items   = array_slice( al_properties(), 0, $al_count );
$al_wrapper = get_block_wrapper_attributes( array( 'class' => 'al-props al-props--' . $al_display ) );

if ( ! $al_items ) {
	printf( '<p %s>%s</p>', $al_wrapper, esc_html__( 'No properties yet. Add one under Properties and it appears here.', 'altura-leads' ) ); // phpcs:ignore WordPress.Security.EscapeOutput
	return;
}
$al_statuses = al_property_statuses();
?>
<ul <?php echo $al_wrapper; // phpcs:ignore WordPress.Security.EscapeOutput ?>>
	<?php
	foreach ( $al_items as $al_i => $al_p ) :
		$al_place  = get_post_meta( $al_p->ID, 'place', true );
		$al_focus  = get_post_meta( $al_p->ID, 'focus', true );
		$al_focus  = '' === $al_focus ? 50 : max( 0, min( 100, (int) $al_focus ) );
		$al_status = get_post_meta( $al_p->ID, 'status', true ) ?: 'available';
		$al_label  = trim( $al_p->post_title . ( $al_place ? ', ' . $al_place : '' ) );
		?>
		<li class="prop reveal" style="--i:<?php echo (int) ( $al_i % 3 ); ?>">
			<a href="<?php echo esc_url( get_permalink( $al_p ) ); ?>">
				<span class="prop__img" style="--pos:<?php echo (int) $al_focus; ?>%"><?php echo al_property_img( $al_p, 'large', array( 'alt' => '', 'loading' => 'lazy', 'sizes' => '(min-width: 960px) 30vw, (min-width: 600px) 45vw, 100vw' ) ); // phpcs:ignore WordPress.Security.EscapeOutput ?></span>
				<span class="prop__num"><?php echo esc_html( str_pad( (string) ( $al_i + 1 ), 2, '0', STR_PAD_LEFT ) ); ?></span>
				<span class="prop__name"><?php echo esc_html( $al_p->post_title ); ?></span>
				<span class="prop__loc"><?php echo esc_html( $al_place ); ?></span>
				<?php if ( 'list' === $al_display ) : ?>
					<span class="prop__price"><?php echo esc_html( al_price( $al_p->ID ) ); ?><?php echo 'available' === $al_status ? '' : ' · ' . esc_html( $al_statuses[ $al_status ] ?? '' ); ?></span>
					<span class="prop__facts"><?php echo esc_html( implode( ' · ', array_map( function ( $label, $value ) { return $value . ' ' . strtolower( $label ); }, array_keys( al_facts( $al_p->ID ) ), al_facts( $al_p->ID ) ) ) ); ?></span>
				<?php endif; ?>
			</a>
		</li>
	<?php endforeach; ?>
</ul>
