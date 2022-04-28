(function(){
	const initChangelog = (changelog) => {
		const entries = changelog.querySelectorAll( '.wp-block-ramp-changelog-entry' )

		// Get last entry for "last updated" timestamp
		const lastEntry = entries[ entries.length - 1 ]
		const lastEntryDate = lastEntry.querySelector( '.changelog-entry-date' )
		if ( lastEntryDate ) {
			const lastUpdatedElement = document.createElement( 'span' )
			lastUpdatedElement.classList.add( 'changelog-last-updated' )
			lastUpdatedElement.innerHTML = window.RAMPChangelog.lastUpdated.replace( '%s', lastEntryDate.innerHTML )

			changelog.querySelector( '.changelog-header' ).append( lastUpdatedElement )
		}

		// Collapse changelogs with more than 3 entries.
		if ( entries.length > 3 ) {
			changelog.classList.add( 'changelog-has-hidden-entries' )

			for ( let i = 3; i < entries.length; i++ ) {
				entries[ i ].classList.add( 'changelog-entry-hidden' )
			}

			const showFullLogElement = document.createElement( 'button' )
			showFullLogElement.classList.add( 'changelog-show-full-log' )
			showFullLogElement.innerHTML = window.RAMPChangelog.showFullLog
			showFullLogElement.addEventListener( 'click', expandChangelog )

			changelog.append( showFullLogElement )
		}
	}

	const expandChangelog = ( e ) => {
		const showButton = e.target

		showButton.closest( '.changelog-has-hidden-entries' ).classList.remove( 'changelog-has-hidden-entries' )
		showButton.remove()
	}

	window.addEventListener( 'DOMContentLoaded', () => {
		const changelogs = document.querySelectorAll( '.wp-block-ramp-changelog' )
		for ( const changelog of changelogs ) {
			initChangelog( changelog )
		}
	} )
})()
