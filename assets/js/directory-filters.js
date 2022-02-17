var toggles = document.querySelectorAll('.directory-filter-toggle');

toggles.forEach(function(toggle){
	var toggleContainer =	toggle.closest( '.directory-filters-container' );
	toggleContainer.classList.remove( 'no-js' );

	// Mobile toggle.
	toggle.addEventListener(
		'click',
		function() {
			toggleContainer.classList.toggle( 'toggle-closed' );
		}
	);
});

// Enable pretty select
// @todo Don't use select2 - see https://github.com/Choices-js/Choices
document.querySelectorAll('.directory-filter.pretty-select').forEach(function(prettySelect){

});

