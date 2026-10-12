<?php
/**
 * The enquiry form's questions and answer choices, in one place.
 *
 * Change a label here and it changes in the form, the admin inbox and the CSV export.
 * The Property choices are not listed here: they are read from the Properties screen in the
 * admin, so adding a property adds it to the form.
 *
 * @package ALTURAProperties
 */

defined( 'ABSPATH' ) || exit;

/**
 * Answer choices for the fields that have a fixed list.
 *
 * @return array
 */
function npl_choices() {
	$properties = array( 'general' => __( 'General enquiry', 'altura-leads' ) );
	foreach ( al_properties() as $property ) {
		$place                                = get_post_meta( $property->ID, 'place', true );
		$properties[ $property->post_name ] = $place ? $property->post_title . ', ' . $place : $property->post_title;
	}

	return apply_filters(
		'npl_choices',
		array(
			'property' => $properties,
			'budget'   => array(
				''         => __( 'Prefer not to say', 'altura-leads' ),
				'under-2m' => __( 'Under €2 million', 'altura-leads' ),
				'2-5m'     => __( '€2 – 5 million', 'altura-leads' ),
				'5-10m'    => __( '€5 – 10 million', 'altura-leads' ),
				'over-10m' => __( 'Over €10 million', 'altura-leads' ),
			),
		)
	);
}

/**
 * The questions, in the order they appear.
 *
 * @return array
 */
function npl_form_fields() {
	return apply_filters(
		'npl_form_fields',
		array(
			'name'     => array(
				'label'        => __( 'Name', 'altura-leads' ),
				'type'         => 'text',
				'autocomplete' => 'name',
				'hint'         => '',
				'required'     => true,
			),
			'email'    => array(
				'label'        => __( 'Email', 'altura-leads' ),
				'type'         => 'email',
				'autocomplete' => 'email',
				'hint'         => '',
				'required'     => true,
			),
			'phone'    => array(
				'label'        => __( 'Telephone', 'altura-leads' ),
				'type'         => 'tel',
				'autocomplete' => 'tel',
				'hint'         => '',
				'required'     => false,
			),
			'property' => array(
				'label'        => __( 'Property of interest', 'altura-leads' ),
				'type'         => 'select',
				'autocomplete' => '',
				'hint'         => '',
				'required'     => true,
			),
			'budget'   => array(
				'label'        => __( 'Budget', 'altura-leads' ),
				'type'         => 'select',
				'autocomplete' => '',
				'hint'         => '',
				'required'     => false,
			),
			'message'  => array(
				'label'        => __( 'Message', 'altura-leads' ),
				'type'         => 'textarea',
				'autocomplete' => '',
				'hint'         => '',
				'required'     => true,
			),
		)
	);
}

/**
 * Human-readable label for a stored answer.
 *
 * @param string $field Field key.
 * @param string $value Stored value.
 * @return string
 */
function npl_label( $field, $value ) {
	$choices = npl_choices();
	return isset( $choices[ $field ][ $value ] ) ? $choices[ $field ][ $value ] : (string) $value;
}

/**
 * The consent sentence shown under the submit button. Saved with each enquiry as a record of what the person agreed to.
 *
 * @return string
 */
function npl_consent_text() {
	return apply_filters(
		'npl_consent_text',
		sprintf(
			/* translators: %s: site name. */
			__( 'By sending this you agree that %s may reply by email or telephone about your enquiry. Your details are not sold or shared, and nothing is sent to you unless you ask for it.', 'altura-leads' ),
			get_bloginfo( 'name' )
		)
	);
}

/**
 * Statuses for simple follow-up tracking in the admin.
 *
 * @return array
 */
function npl_statuses() {
	return array(
		'new'       => __( 'New', 'altura-leads' ),
		'contacted' => __( 'Contacted', 'altura-leads' ),
		'viewing'   => __( 'Viewing arranged', 'altura-leads' ),
		'offer'     => __( 'Offer made', 'altura-leads' ),
		'lost'      => __( 'Closed, no sale', 'altura-leads' ),
		'spam'      => __( 'Suspected spam', 'altura-leads' ),
	);
}

/**
 * Where the enquiry email goes. Set in Settings > ALTURA, so no address is written into a file.
 *
 * @return string
 */
function npl_notify_email() {
	$email = get_option( 'npl_notify_email' );
	if ( ! is_email( $email ) ) {
		$email = get_option( 'admin_email' );
	}
	return apply_filters( 'npl_notify_email', $email );
}

/**
 * Page people land on after a successful submit.
 *
 * @return string
 */
function npl_thank_you_url() {
	$page_id = (int) get_option( 'npl_thank_you_page' );
	$url     = $page_id ? get_permalink( $page_id ) : home_url( '/thank-you/' );
	return apply_filters( 'npl_thank_you_url', $url );
}
