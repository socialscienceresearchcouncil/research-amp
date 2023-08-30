( function ( $ ) {
	$( document ).ready( function () {
		$( '.load-more-button a' ).on( 'click', function ( e ) {
			e.preventDefault();

			const $clicked = $( this );

			const queryArg = $clicked.data( 'queryArg' );

			const href = this.href;
			$.ajax( {
				url: href,
				success( response ) {
					const parser = new DOMParser();
					const doc = parser.parseFromString( response, 'text/html' );

					let newItems, newLoadMore;

					// Special case for 'paged', which indicates the search page.
					if ( 'paged' === queryArg ) {
						newItems = doc.querySelectorAll(
							'.wp-block-post-template li'
						);
						newLoadMore = doc.querySelector(
							'.wp-block-research-amp-search-load-more .load-more-button a'
						);
					} else {
						newItems = doc.querySelectorAll(
							'.uses-query-arg-' +
								queryArg +
								' .load-more-list li'
						);
						newLoadMore = doc.querySelector(
							'.uses-query-arg-' +
								queryArg +
								' .load-more-button a'
						);
					}

					if ( newItems ) {
						const parentList =
							'paged' === queryArg
								? $( '.wp-block-post-template' )
								: $clicked
										.closest( '.load-more-container' )
										.find( '.load-more-list' );

						parentList.append( newItems );
					}

					if ( newLoadMore ) {
						$clicked.attr( 'href', newLoadMore.href );
					} else {
						$clicked.remove();
					}
				},
				error: () => {
					// Miscellaneous failures may mean there's no more items to show. Hide button.
					$clicked.remove();
				},
			} );
		} );
	} );
} )( jQuery );
