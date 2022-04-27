(function(){
	let navSearchContainers

	const handleToggleClick = ( event ) => {
		const navSearch = event.target.closest( '.wp-block-ramp-nav-search' )
		navSearch.classList.toggle( 'search-open' )
		navSearch.querySelector( '.nav-search-input' ).focus()

		setNavSearchWidth( navSearch )
	}

	const isMobile = () => !! window.matchMedia( '(max-width: 600px)' ).matches

	const setNavSearchWidth = ( navSearch ) => {
		// Grow to the size of the nav-and-search parent
		const navAndSearch = navSearch.closest( '.nav-and-search' )
		if ( ! navAndSearch ) {
			return
		}

		const navAndSearchRect = navAndSearch.getBoundingClientRect()

		const newWidth = isMobile() ? ( navAndSearchRect.width + 150 ) + 'px' : ( navAndSearchRect.width - 60 ) + 'px'

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
		const primaryNavResponsiveContents = document.querySelector( '.nav-and-search .wp-block-navigation__responsive-container-content' )

		if ( ! primaryNavResponsiveContents ) {
			return
		}

		const secondaryNav = document.querySelector( '.secondary-nav' )

		if ( ! secondaryNav ) {
			return
		}

		const secondaryNavContents = secondaryNav.querySelector( '.wp-block-navigation__responsive-container-content' )

		// Nothing to do.
		if ( ! secondaryNav ) {
			return
		}

		const mobileSecondaryNav = secondaryNavContents.cloneNode( true )

		mobileSecondaryNav.id += '-mobile'
		mobileSecondaryNav.classList.add( 'secondary-nav-contents' )
		primaryNavResponsiveContents.classList.add( 'primary-nav-responsive-contents' )
		primaryNavResponsiveContents.after( mobileSecondaryNav )
	}

	const addLogoToMobileNav = () => {
		const siteLogoImg = document.querySelector( '.wp-block-site-logo img' )
		if ( ! siteLogoImg ) {
			return
		}

		const headerContainer = siteLogoImg.closest( '.wp-block-columns' )
		if ( ! headerContainer ) {
			return
		}

		// Duplicate the header for the outer markup, but delete the navigation contents.
		const newHeader = headerContainer.cloneNode( true )
		const newHeaderNavColumn = newHeader.querySelector( '.header-nav-column' )

		newHeaderNavColumn.querySelector( '.wp-block-columns' ).remove()
		newHeaderNavColumn.style.textAlign = 'right'
		newHeaderNavColumn.style.paddingRight = '46px'

		const primaryNavCloseButton = document.querySelector( '.nav-and-search .wp-block-navigation__responsive-container-close' )
		const primaryNavOpenButton = document.querySelector( '.nav-and-search .wp-block-navigation__responsive-container-open' )

		const primaryNavOpenButtonRect = primaryNavOpenButton.getBoundingClientRect()

		newHeaderNavColumn.append( primaryNavCloseButton )

		newHeader.style.width = "calc(100% + 32px)"
		newHeader.style.paddingLeft = 0
		newHeader.style.paddingRight = 0
		newHeader.style.paddingTop = "16px"
		newHeader.style.marginLeft = "-16px"

		newHeader.classList.add( 'mobile-nav-header' )

		document.querySelector( '.nav-and-search .primary-nav-responsive-contents' ).before( newHeader )

		const wpadminbar = document.querySelector( '#wpadminbar' )
		if ( wpadminbar ) {
			const primaryNavResponsiveContainer = document.querySelector( '.nav-and-search .wp-block-navigation__responsive-container' )
			wpadminbarRect = wpadminbar.getBoundingClientRect()
			primaryNavResponsiveContainer.style.top = wpadminbarRect.height + 'px'
		}
	}

	const isAdmin = document.body.classList.contains( 'wp-admin' )

	if ( isAdmin && !! wp && wp.hasOwnProperty( 'domReady' ) ) {
		wp.domReady( () => {
			positionSecondaryNav()
			addLogoToMobileNav()
		} )

	} else {
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
	}
})()
