const toggles = document.querySelectorAll( '.directory-filter-toggle' );

toggles.forEach( function ( toggle ) {
	const toggleContainer = toggle.closest( '.directory-filters-container' );
	toggleContainer.classList.remove( 'no-js' );

	// Mobile toggle.
	toggle.addEventListener( 'click', function () {
		toggleContainer.classList.toggle( 'toggle-closed' );
	} );
} );

// Enable pretty select
( function ( $ ) {
	$( '.pretty-select' ).each( ( k, v ) => {
		const $prettySelect = $( v );

		$prettySelect.select2( {
			minimumResultsForSearch: Infinity,
			width: '100%',
		} );
	} );
} )( jQuery );
