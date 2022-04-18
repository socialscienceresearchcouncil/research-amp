(function(){
	let navSearchContainers

	const handleToggleClick = ( event ) => {
		const navSearch = event.target.closest( '.wp-block-ramp-nav-search' )
		navSearch.classList.toggle( 'search-open' )
		navSearch.querySelector( '.nav-search-input' ).focus()

		setNavSearchWidth( navSearch )
	}

	const setNavSearchWidth = ( navSearch ) => {
		// Grow to the size of the nav-and-search parent
		const navAndSearch = navSearch.closest( '.nav-and-search' )
		if ( ! navAndSearch ) {
			return
		}

		const navAndSearchRect = navAndSearch.getBoundingClientRect()

		const isMobile = window.matchMedia( '(max-width: 600px)' ).matches
		const newWidth = isMobile ? ( navAndSearchRect.width + 150 ) + 'px' : ( navAndSearchRect.width - 60 ) + 'px'

		navSearch.querySelector( '.nav-search-fields' ).style.maxWidth = newWidth
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
			navSearchContainer.querySelector( 'input[type=text]' ).addEventListener( 'keydown', handleKeydown )
		}

		document.addEventListener( 'click', handleDocumentClick )

		const subnavToggles = document.querySelectorAll( '.wp-block-navigation-submenu__toggle' )
		for ( const subnavToggle of subnavToggles ) {
			subnavToggle.addEventListener( 'click', (event) => {
				const parentNavItem = event.target.closest( '.has-child' )
				parentNavItem.classList.toggle( 'subnav-is-expanded' )
			} )
		}
	} )
})()
