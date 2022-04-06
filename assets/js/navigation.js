(function(){
	let navSearchContainers

	const handleToggleClick = ( event ) => {
		const clicked = event.target
		clicked.closest( '.wp-block-ramp-nav-search' ).classList.toggle( 'search-open' )
	}

	const handleDocumentClick = ( event ) => {
		for ( const navSearchContainer of navSearchContainers ) {
			const isClickInside = navSearchContainer.contains( event.target )

			if ( ! isClickInside ) {
				navSearchContainer.classList.remove( 'search-open' )
			}
		}
	}

	// Close search on escape.
	const handleKeydown = ( evt ) => {
		evt = evt || window.event;

		var isEscape = false;
		if ("key" in evt) {
			isEscape = (evt.key === "Escape" || evt.key === "Esc");
		} else {
			isEscape = (evt.keyCode === 27);
		}

		if ( isEscape ) {
			event.target.closest( '.wp-block-ramp-nav-search' ).classList.remove( 'search-open' );
		}
	}

	document.addEventListener( 'DOMContentLoaded', () => {
		navSearchContainers = document.querySelectorAll( '.wp-block-ramp-nav-search' )

		for ( const navSearchContainer of navSearchContainers ) {
			navSearchContainer.querySelector( 'button' ).addEventListener( 'click', handleToggleClick )
			navSearchContainer.querySelector( 'input[type=search]' ).addEventListener( 'keydown', handleKeydown )
		}

		document.addEventListener( 'click', handleDocumentClick )
	} )
})()
