( function () {
	document.addEventListener( 'DOMContentLoaded', function () {
		// Collapsibles.
		document
			.querySelectorAll( '.sidebar-section-collapsible' )
			.forEach( ( section ) => initCollapsible( section ) );

		// Cite This click-to-highlight.
		document
			.querySelectorAll( '.cite-this-citation' )
			.forEach( ( citation ) => initClickToHighlight( citation ) );

		// Social buttons.
		document
			.querySelectorAll( '.social-button' )
			.forEach( ( button ) => initSocialButton( button ) );
	} );

	const initCollapsible = ( section ) => {
		const toggle = document.createElement( 'button' );
		toggle.classList.add( 'section-toggle' );

		const toggleText = document.createElement( 'span' );
		toggleText.classList.add( 'screen-reader-text' );

		toggle.append( toggleText );

		toggle.addEventListener( 'click', handleClick );

		section.append( toggle );

		setButtonText( section );
	};

	const setButtonText = ( section ) => {
		const toggleText = section.querySelector( '.section-toggle span' );

		toggleText.innerHTML = section.classList.contains( 'section-open' )
			? RAMPSidebar.buttonTextShowLess
			: RAMPSidebar.buttonTextShowMore;
	};

	const handleClick = ( event ) => {
		const section = event.target.closest( '.sidebar-section-collapsible' );

		section.classList.toggle( 'section-open' );
		section.classList.toggle( 'section-closed' );

		setButtonText( section );
	};

	const initClickToHighlight = ( element ) => {
		element.addEventListener( 'click', ( event ) => {
			let doc = document,
				text = event.target,
				range,
				selection;
			if ( doc.body.createTextRange ) {
				//ms
				range = doc.body.createTextRange();
				range.moveToElementText( text );
				range.select();
			} else if ( window.getSelection ) {
				//all others
				selection = window.getSelection();
				range = doc.createRange();
				range.selectNodeContents( text );
				selection.removeAllRanges();
				selection.addRange( range );
			}
		} );
	};

	const initSocialButton = ( button ) => {
		const { url, title } = button.closest(
			'.social-buttons-links'
		).dataset;

		if ( button.classList.contains( 'social-button-facebook' ) ) {
			const facebookUrl =
				'https://www.facebook.com/sharer/sharer.php?u=' +
				encodeURIComponent( url ) +
				'&t=' +
				encodeURIComponent( title );
			button.setAttribute( 'href', facebookUrl );
		} else if ( button.classList.contains( 'social-button-twitter' ) ) {
			const twitterUrl =
				'https://twitter.com/intent/tweet?url=' +
				encodeURIComponent( url ) +
				'&text=' +
				encodeURIComponent( title );
			button.setAttribute( 'href', twitterUrl );
		} else {
			return;
		}

		button.addEventListener( 'click', ( event ) => {
			event.preventDefault();
			window.open(
				event.target.href,
				'_blank',
				'height=450, width=550, toolbar=0, location=0, menubar=0, directories=0,scrollbars=0'
			);
		} );
	};
} )();
