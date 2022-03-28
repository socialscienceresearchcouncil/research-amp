(function(){
	document.addEventListener('DOMContentLoaded', function() {
		const collapsibleSections = document.querySelectorAll( '.sidebar-section-collapsible' )

		collapsibleSections.forEach( ( section ) => initCollapsible( section ) )
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
}())
