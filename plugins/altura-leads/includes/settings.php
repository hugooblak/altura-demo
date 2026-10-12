<?php
/**
 * One settings screen: Settings > ALTURA.
 *
 * Everything the owner has to fill in after launch lives here, so nothing is written into a file
 * and nothing needs a developer: the address enquiries are emailed to, and the three tracking IDs.
 *
 * Nothing is loaded on the site until an ID is filled in. An empty box means no third-party script.
 *
 * @package ALTURAProperties
 */

defined( 'ABSPATH' ) || exit;

/**
 * Every setting on the screen: option name => [label, type, hint].
 *
 * @return array
 */
function al_settings() {
	return array(
		'npl_notify_email' => array(
			__( 'Send enquiries to', 'altura-leads' ),
			'email',
			__( 'Every enquiry is emailed here the moment it is sent, and saved under Enquiries. Leave empty to use the site admin email.', 'altura-leads' ),
		),
		'al_ga4'           => array(
			__( 'Google Analytics measurement ID', 'altura-leads' ),
			'text',
			__( 'Looks like G-XXXXXXXXXX. Nothing is loaded while this is empty.', 'altura-leads' ),
		),
		'al_pixel'         => array(
			__( 'Meta Pixel ID', 'altura-leads' ),
			'text',
			__( 'The 15 or 16 digit number from Meta Events Manager. Nothing is loaded while this is empty.', 'altura-leads' ),
		),
		'al_gsc'           => array(
			__( 'Google Search Console verification code', 'altura-leads' ),
			'text',
			__( 'Choose the HTML tag method in Search Console and paste only the content value here.', 'altura-leads' ),
		),
	);
}

add_action(
	'admin_menu',
	function () {
		add_options_page(
			__( 'ALTURA settings', 'altura-leads' ),
			__( 'ALTURA', 'altura-leads' ),
			'manage_options',
			'altura',
			'al_settings_page'
		);
	}
);

add_action(
	'admin_init',
	function () {
		foreach ( al_settings() as $option => $field ) {
			register_setting(
				'altura',
				$option,
				array(
					'type'              => 'string',
					'sanitize_callback' => 'email' === $field[1] ? 'sanitize_email' : 'sanitize_text_field',
					'default'           => '',
					'show_in_rest'      => false,
				)
			);
		}
	}
);

/**
 * The settings screen itself.
 */
function al_settings_page() {
	?>
	<div class="wrap">
		<h1><?php esc_html_e( 'ALTURA settings', 'altura-leads' ); ?></h1>
		<p><?php esc_html_e( 'Fill these in once, after launch. Anything left empty is simply switched off.', 'altura-leads' ); ?></p>
		<form method="post" action="options.php">
			<?php settings_fields( 'altura' ); ?>
			<table class="form-table" role="presentation"><tbody>
			<?php foreach ( al_settings() as $option => $field ) : ?>
				<tr>
					<th scope="row"><label for="<?php echo esc_attr( $option ); ?>"><?php echo esc_html( $field[0] ); ?></label></th>
					<td>
						<input type="<?php echo esc_attr( 'email' === $field[1] ? 'email' : 'text' ); ?>"
							id="<?php echo esc_attr( $option ); ?>" name="<?php echo esc_attr( $option ); ?>"
							value="<?php echo esc_attr( get_option( $option ) ); ?>" class="regular-text">
						<p class="description"><?php echo esc_html( $field[2] ); ?></p>
					</td>
				</tr>
			<?php endforeach; ?>
			</tbody></table>
			<?php submit_button(); ?>
		</form>
	</div>
	<?php
}
