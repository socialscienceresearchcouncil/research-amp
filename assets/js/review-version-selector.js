(function($){
	var $versionSelectorDropdown = $( '.review-version-selector-dropdown' );

	$versionSelectorDropdown.select2({
		minimumResultsForSearch: Infinity
	});

	$versionSelectorDropdown.on(
		'change',
		function() {
			var $optionSelected = $('option:selected', this);
			var versionUrl = $optionSelected.val();

			document.location.href = versionUrl;
		}
	);
}(jQuery));
