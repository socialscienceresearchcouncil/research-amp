(function($) {
	$(document).ready(function() {
		$('.load-more-button a').on(
			'click',
			function(e) {
				e.preventDefault();

				var $clicked = $(this);

				var href = this.href;
				$.ajax({
					url: href,
					success: function( response ) {
						var parser = new DOMParser();
						var doc = parser.parseFromString(response, 'text/html');
						var newItems = doc.querySelectorAll('.load-more-list li');
						var newLoadMore = doc.querySelector('.load-more-button a');

						if ( newItems ) {
							$('.load-more-list').append(newItems);
						}

						if ( newLoadMore ) {
							$clicked.attr('href', newLoadMore.href);
						}
					}
				});

			}
		);
	});
}(jQuery))
