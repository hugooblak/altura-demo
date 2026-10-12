/**
 * Enquiry form behaviour (about 1.5 KB, no libraries).
 *
 * - Checks each field as you leave it, with messages linked to the field for screen readers.
 *
 * The form works with JavaScript switched off. This file only makes it friendlier.
 */
( function () {
	'use strict';

	var MSG = {
		name: 'Enter your name.',
		email: 'Enter an email address, like name@example.com.',
		phone: 'Enter a telephone number with the country code, like +44 20 7946 0000, or leave it empty.',
		property: 'Choose a property, or "General enquiry".',
		message: 'Write a few words about what you are looking for.'
	};

	function fieldValid( input ) {
		var v = ( input.value || '' ).trim();
		switch ( input.name ) {
			case 'name': return v.length >= 2;
			case 'email': return /^[^\s@]+@[^\s@]+\.[^\s@]{2,}$/.test( v );
			case 'phone': if ( v === '' ) return true; var d = v.replace( /\D/g, '' ); return d.length >= 9 && d.length <= 15;
			case 'property': return v !== '';
			case 'message': return v.length >= 5;
			default: return true;
		}
	}

	function message( input ) {
		return MSG[ input.name ];
	}

	function showError( form, name, show ) {
		var input = form.querySelector( '[name="' + name + '"]' );
		if ( ! input ) return;
		var err = document.getElementById( ( input.getAttribute( 'aria-describedby' ) || '' ).split( ' ' )[ 0 ] );
		if ( err ) { err.textContent = show ? message( input ) : ''; err.hidden = ! show; }
		if ( show ) input.setAttribute( 'aria-invalid', 'true' ); else input.removeAttribute( 'aria-invalid' );
	}

	document.querySelectorAll( '[data-npl-form]' ).forEach( function ( form ) {
		var submit = form.querySelector( '[data-npl-submit]' );
		var submitText = submit ? submit.textContent : '';

		form.addEventListener( 'input', function ( e ) {
			if ( e.target.getAttribute( 'aria-invalid' ) === 'true' && fieldValid( e.target ) ) showError( form, e.target.name, false );
		} );

		// Check a field once the person has finished with it, not while they type.
		form.addEventListener( 'focusout', function ( e ) {
			if ( ! e.target.name || ! MSG[ e.target.name ] ) return;
			if ( ( e.target.value || '' ).trim() === '' ) return;
			showError( form, e.target.name, ! fieldValid( e.target ) );
		} );

		form.addEventListener( 'change', function ( e ) {
			if ( e.target.name === 'property' ) {
				if ( ( e.target.value || '' ).trim() !== '' ) showError( form, e.target.name, ! fieldValid( e.target ) );
			}
		} );

		form.addEventListener( 'submit', function ( e ) {
			var bad = [];
			Object.keys( MSG ).forEach( function ( name ) {
				var input = form.querySelector( '[name="' + name + '"]' );
				if ( ! input ) return;
				var ok = fieldValid( input );
				showError( form, name, ! ok );
				if ( ! ok ) bad.push( name );
			} );
			if ( bad.length ) {
				e.preventDefault();
				var first = form.querySelector( '[name="' + bad[ 0 ] + '"]' );
				if ( first ) first.focus();
				return;
			}
			submit.disabled = true;
			submit.textContent = 'Sending…';
		} );

		var summary = form.querySelector( '[data-npl-summary]' );
		if ( summary ) {
			// Focus the error list so screen readers announce it. Again after load, because the #book jump can take focus away.
			summary.focus();
			window.addEventListener( 'load', function () { setTimeout( function () { summary.focus(); }, 0 ); } );
		}

		// Coming back with the browser's Back button: make the submit button usable again.
		window.addEventListener( 'pageshow', function () {
			submit.disabled = false;
			submit.textContent = submitText;
		} );
	} );
} )();
