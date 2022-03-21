(function($) {
	$(document).ready(function() {
		$('.load-more-button a').on(
			'click',
			function(e) {
				e.preventDefault();

				var $clicked = $(this);

				var queryArg = $clicked.data('queryArg');

				var href = this.href;
				$.ajax({
					url: href,
					success: function( response ) {
						var parser = new DOMParser();
						var doc = parser.parseFromString(response, 'text/html');
						var newItems = doc.querySelectorAll('.uses-query-arg-' + queryArg + ' .load-more-list li');
						var newLoadMore = doc.querySelector('.uses-query-arg-' + queryArg + '.load-more-button a');

						if ( newItems ) {
							$clicked.closest('.load-more-container').find('.load-more-list').append(newItems);
						}

						if ( newLoadMore ) {
							$clicked.attr('href', newLoadMore.href);
						} else {
							$clicked.remove();
						}
					}
				});

			}
		);
	});
}(jQuery))
