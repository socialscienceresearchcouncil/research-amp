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

	const positionSecondaryNav = () => {
		const secondaryNav = document.querySelector( '.secondary-nav' )

		if ( ! secondaryNav ) {
			return
		}

		const secondaryNavContents = secondaryNav.querySelector( '.wp-block-navigation__responsive-container-content' )

		if ( ! secondaryNav ) {
			return
		}

		const primaryNavResponsiveContents = document.querySelector( '.nav-and-search .wp-block-navigation__responsive-container-content' )

		if ( ! primaryNavResponsiveContents ) {
			return
		}

		secondaryNavContents.classList.add( 'secondary-nav-contents' )
		primaryNavResponsiveContents.classList.add( 'primary-nav-responsive-contents' )
		primaryNavResponsiveContents.after( secondaryNavContents )
	}

	const addLogoToMobileNav = () => {
		const siteLogoImg = document.querySelector( '.wp-block-site-logo img' )
		const primaryNavResponsiveContainer = document.querySelector( '.nav-and-search .wp-block-navigation__responsive-container' )

		if ( ! siteLogoImg || ! primaryNavResponsiveContainer ) {
			return
		}

		const wpadminbar = document.querySelector( '#wpadminbar' )
		if ( wpadminbar ) {
			wpadminbarRect = wpadminbar.getBoundingClientRect()
			primaryNavResponsiveContainer.style.top = wpadminbarRect.height + 'px'
		}

		primaryNavResponsiveContainer.style.backgroundImage = 'url(' + siteLogoImg.src + ')'

		const logoRect = siteLogoImg.getBoundingClientRect()
		primaryNavResponsiveContainer.style.backgroundSize = logoRect.width + 'px ' + logoRect.height + 'px'

		const logoTop = wpadminbar ? logoRect.y - wpadminbarRect.height : logoRect.y
		primaryNavResponsiveContainer.style.backgroundPosition = 'top ' + logoTop + 'px left ' + logoRect.x + 'px'
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

		// Mobile workarounds
		positionSecondaryNav()
		addLogoToMobileNav()
	} )
})()
