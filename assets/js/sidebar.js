(function(){
	document.addEventListener('DOMContentLoaded', function() {
		// Collapsibles.
		document.querySelectorAll( '.sidebar-section-collapsible' ).forEach( ( section ) => initCollapsible( section ) )

		// Cite This click-to-highlight.
		document.querySelectorAll( '.cite-this-citation' ).forEach( ( citation ) => initClickToHighlight( citation ) )
	})

	const initCollapsible = ( section ) => {
		var toggle = document.createElement( 'button' )
		toggle.classList.add( 'section-toggle' )

		var toggleText = document.createElement( 'span' )
		toggleText.classList.add( 'screen-reader-text' )

		toggle.append( toggleText )

		toggle.addEventListener( 'click', handleClick )

		section.append( toggle )

		setButtonText( section )
	}

	const setButtonText = ( section ) => {
		const toggleText = section.querySelector( '.section-toggle span' )

		toggleText.innerHTML = section.classList.contains( 'section-open' )
			? RAMPSidebar.buttonTextShowLess
			: RAMPSidebar.buttonTextShowMore
	}

	const handleClick = ( event ) => {
		const section = event.target.closest( '.sidebar-section-collapsible' )

		section.classList.toggle( 'section-open' )
		section.classList.toggle( 'section-closed' )

		setButtonText( section )
	}

	const initClickToHighlight = ( element ) => {
		element.addEventListener( 'click', ( event ) => {
			var doc = document
					, text = event.target
					, range, selection
			;
			if (doc.body.createTextRange) { //ms
					range = doc.body.createTextRange();
					range.moveToElementText(text);
					range.select();
			} else if (window.getSelection) { //all others
					selection = window.getSelection();
					range = doc.createRange();
					range.selectNodeContents(text);
					selection.removeAllRanges();
					selection.addRange(range);
			}
		} )
	}
}())
