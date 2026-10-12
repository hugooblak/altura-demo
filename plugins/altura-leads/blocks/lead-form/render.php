<?php
/**
 * Enquiry form markup (server-rendered, so it works without JavaScript).
 *
 * Name, email, telephone, property, budget and message. JavaScript only adds instant error
 * messages; with it switched off the form still submits, still validates and still saves.
 *
 * @var array $attributes Block attributes.
 * @package ALTURAProperties
 */

defined( 'ABSPATH' ) || exit;

// phpcs:disable WordPress.Security.NonceVerification.Recommended -- reading a prefill value from the URL only.
$npl_state   = npl_current_state();
$npl_errors  = $npl_state['errors'];
$npl_values  = $npl_state['values'];
$npl_choices = npl_choices();
$npl_fields  = npl_form_fields();

// Prefill from the URL: /enquire/?property=villa-kastello, used by every Enquire button.
if ( empty( $npl_values['property'] ) && isset( $_GET['property'] ) ) {
	$npl_values['property'] = sanitize_title( wp_unslash( $_GET['property'] ) );
}
// phpcs:enable

$npl_val = function ( $key ) use ( $npl_values ) {
	if ( ! isset( $npl_values[ $key ] ) ) {
		return '';
	}
	return is_array( $npl_values[ $key ] ) ? $npl_values[ $key ] : (string) $npl_values[ $key ];
};

// Unique IDs, so two forms on one page never share label targets.
$GLOBALS['npl_form_count'] = ( $GLOBALS['npl_form_count'] ?? 0 ) + 1;
$npl_uid                   = 'npl' . $GLOBALS['npl_form_count'];

$npl_button = ! empty( $attributes['buttonText'] ) ? $attributes['buttonText'] : __( 'Send enquiry', 'altura-leads' );

$npl_wrapper = get_block_wrapper_attributes( array( 'class' => 'npl-form' ) );
?>
<div <?php echo $npl_wrapper; // phpcs:ignore WordPress.Security.EscapeOutput ?>>
	<form class="npl-fields" method="post" action="<?php echo esc_url( admin_url( 'admin-post.php' ) ); ?>" novalidate data-npl-form>
		<input type="hidden" name="action" value="npl_submit">
		<input type="hidden" name="npl_token" value="<?php echo esc_attr( npl_form_token() ); ?>">
		<?php // Honeypot: hidden from people and screen readers. Bots fill every field they find. ?>
		<div class="npl-hp" aria-hidden="true">
			<label for="<?php echo esc_attr( $npl_uid ); ?>-hp">Company website</label>
			<input id="<?php echo esc_attr( $npl_uid ); ?>-hp" type="text" name="company_website" tabindex="-1" autocomplete="off">
		</div>

		<?php if ( $npl_errors ) : ?>
			<div class="npl-summary" role="alert" tabindex="-1" data-npl-summary>
				<p><strong><?php esc_html_e( 'Please check these answers:', 'altura-leads' ); ?></strong></p>
				<ul>
					<?php foreach ( $npl_errors as $npl_key => $npl_msg ) : ?>
						<li><a href="#<?php echo esc_attr( $npl_uid . '-' . $npl_key ); ?>"><?php echo esc_html( $npl_msg ); ?></a></li>
					<?php endforeach; ?>
				</ul>
			</div>
		<?php endif; ?>

		<?php
		foreach ( $npl_fields as $npl_key => $npl_f ) :
			$npl_id       = $npl_uid . '-' . $npl_key;
			$npl_describe = $npl_id . '-err' . ( $npl_f['hint'] ? ' ' . $npl_id . '-hint' : '' );
			$npl_invalid  = isset( $npl_errors[ $npl_key ] ) ? ' aria-invalid="true"' : '';
			$npl_req      = ! empty( $npl_f['required'] ) ? ' required' : '';
			$npl_optional = empty( $npl_f['required'] ) ? ' <span class="npl-optional">' . esc_html__( '(optional)', 'altura-leads' ) . '</span>' : '';

			?>
			<div class="npl-field<?php echo 'textarea' === $npl_f['type'] ? ' npl-field--wide' : ''; ?>">
				<label class="npl-label" for="<?php echo esc_attr( $npl_id ); ?>"><?php echo wp_kses_post( $npl_f['label'] . $npl_optional ); ?></label>
				<?php if ( $npl_f['hint'] ) : ?>
					<p class="npl-hint" id="<?php echo esc_attr( $npl_id ); ?>-hint"><?php echo esc_html( $npl_f['hint'] ); ?></p>
				<?php endif; ?>

				<?php if ( 'select' === $npl_f['type'] ) : ?>
					<select class="npl-input npl-select" id="<?php echo esc_attr( $npl_id ); ?>" name="<?php echo esc_attr( $npl_key ); ?>"<?php echo esc_attr( $npl_req ); ?> aria-describedby="<?php echo esc_attr( $npl_describe ); ?>"<?php echo $npl_invalid; // phpcs:ignore WordPress.Security.EscapeOutput ?>>
						<?php if ( ! empty( $npl_f['required'] ) ) : ?><option value=""><?php esc_html_e( 'Please choose…', 'altura-leads' ); ?></option><?php endif; ?>
						<?php foreach ( $npl_choices[ $npl_key ] as $npl_value => $npl_label ) : ?>
							<option value="<?php echo esc_attr( $npl_value ); ?>"<?php selected( $npl_val( $npl_key ), $npl_value ); ?>><?php echo esc_html( $npl_label ); ?></option>
						<?php endforeach; ?>
					</select>
				<?php elseif ( 'textarea' === $npl_f['type'] ) : ?>
					<textarea class="npl-input" id="<?php echo esc_attr( $npl_id ); ?>" name="<?php echo esc_attr( $npl_key ); ?>" rows="4"<?php echo esc_attr( $npl_req ); ?> aria-describedby="<?php echo esc_attr( $npl_describe ); ?>"<?php echo $npl_invalid; // phpcs:ignore WordPress.Security.EscapeOutput ?>><?php echo esc_textarea( $npl_val( $npl_key ) ); ?></textarea>
				<?php else : ?>
					<input class="npl-input" id="<?php echo esc_attr( $npl_id ); ?>" name="<?php echo esc_attr( $npl_key ); ?>"
						type="<?php echo esc_attr( $npl_f['type'] ); ?>"
						<?php echo $npl_f['autocomplete'] ? 'autocomplete="' . esc_attr( $npl_f['autocomplete'] ) . '"' : ''; ?>
						<?php echo 'tel' === $npl_f['type'] ? 'inputmode="tel"' : ''; ?>
						<?php echo esc_attr( $npl_req ); ?> aria-describedby="<?php echo esc_attr( $npl_describe ); ?>"<?php echo $npl_invalid; // phpcs:ignore WordPress.Security.EscapeOutput ?>
						value="<?php echo esc_attr( $npl_val( $npl_key ) ); ?>">
				<?php endif; ?>

				<p class="npl-error" id="<?php echo esc_attr( $npl_id ); ?>-err"<?php echo isset( $npl_errors[ $npl_key ] ) ? '' : ' hidden'; ?>><?php echo esc_html( $npl_errors[ $npl_key ] ?? '' ); ?></p>
			</div>
		<?php endforeach; ?>

		<div class="npl-nav">
			<button type="submit" class="npl-btn wp-element-button" data-npl-submit><?php echo esc_html( $npl_button ); ?></button>
		</div>
		<p class="npl-fineprint"><?php echo esc_html( npl_consent_text() ); ?></p>
	</form>
</div>
