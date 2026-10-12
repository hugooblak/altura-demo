/**
 * Hero slider (about 1.5 KB, no libraries).
 *
 * Without this file the first photo shows and nothing else changes. With it: previous/next
 * buttons, the left/right arrow keys, the counter and caption, and a polite announcement for
 * screen readers. No autoplay: moving content without being asked is a problem for many people.
 */
( function () {
	'use strict';

	var hero = document.querySelector( '.wp-block-altura-hero' );
	if ( ! hero ) return;
	var slides = Array.prototype.slice.call( hero.querySelectorAll( '.slide' ) );
	if ( slides.length < 2 ) return;

	var track = hero.querySelector( '.slides' );
	var controls = hero.querySelector( '[data-controls]' );
	var cur = hero.querySelector( '.counter__cur' );
	var caption = hero.querySelector( '[data-caption]' );
	var capName = hero.querySelector( '.caption__name' );
	var capLoc = hero.querySelector( '.caption__loc' );
	var status = hero.querySelector( '#slide-status' );
	var index = 0;
	var leaving = null;
	var leaveTimer = 0;

	function pad( n ) { return String( n ).padStart( 2, '0' ); }
	function roll( el ) {
		el.classList.remove( 'roll' );
		void el.offsetWidth; // restart the animation
		el.classList.add( 'roll' );
	}

	function show( next, dir ) {
		var to = ( next + slides.length ) % slides.length;
		if ( to === index ) return;
		var from = slides[ index ];
		index = to;
		clearTimeout( leaveTimer );
		if ( leaving ) leaving.classList.remove( 'is-leaving' );
		track.dataset.dir = dir < 0 ? 'prev' : 'next';
		from.classList.remove( 'is-active' );
		from.classList.add( 'is-leaving' );
		from.setAttribute( 'aria-hidden', 'true' );
		leaving = from;
		leaveTimer = setTimeout( function () { from.classList.remove( 'is-leaving' ); leaving = null; }, 1250 );

		var s = slides[ index ];
		s.classList.remove( 'is-leaving' );
		s.classList.add( 'is-active' );
		s.setAttribute( 'aria-hidden', 'false' );
		var img = s.querySelector( 'img' );
		if ( img ) img.loading = 'eager';

		cur.textContent = pad( index + 1 );
		capName.textContent = s.dataset.name;
		capLoc.textContent = s.dataset.loc;
		if ( caption && s.dataset.url ) caption.href = s.dataset.url;
		[ cur, capName, capLoc ].forEach( roll );
		status.textContent = 'Slide ' + ( index + 1 ) + ' of ' + slides.length + ': ' + s.dataset.name + ', ' + s.dataset.loc;
	}

	if ( controls ) {
		controls.hidden = false;
		controls.querySelectorAll( '.controls__btn' ).forEach( function ( b ) {
			b.addEventListener( 'click', function () {
				var d = Number( b.dataset.dir );
				show( index + d, d );
			} );
		} );
	}
	hero.addEventListener( 'keydown', function ( e ) {
		if ( e.key === 'ArrowRight' ) { show( index + 1, 1 ); e.preventDefault(); }
		if ( e.key === 'ArrowLeft' ) { show( index - 1, -1 ); e.preventDefault(); }
	} );
	window.addEventListener( 'load', function () {
		slides.forEach( function ( s ) { var img = s.querySelector( 'img' ); if ( img ) img.loading = 'eager'; } );
	} );
}() );
