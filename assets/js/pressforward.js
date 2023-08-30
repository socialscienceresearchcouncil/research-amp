( function ( $ ) {
	$( document ).ready( function () {
		$( '.pf-btns' ).each( function ( k, v ) {
			const $btns = $( v );
			const articleId = $btns.closest( 'article' ).attr( 'id' );

			const citationButton =
				'<button class="btn btn-small schema-switchable ramp-send-to-citation-library" data-article-id="' +
				articleId +
				'" data-original-title="Send to Citation Library"><i class="icon-folder-open"></i></button>';
			$btns.append( citationButton );

			const eventsButton =
				'<button class="btn btn-small schema-switchable ramp-send-to-events" data-article-id="' +
				articleId +
				'" data-original-title="Send to Events"><i class="icon-calendar"></i></button>';
			$btns.append( eventsButton );
		} );

		$( '.ramp-send-to-citation-library' ).on( 'click', function () {
			const $clicked = $( this );

			$clicked.attr( 'disabled', 'disabled' );

			$.ajax( {
				method: 'POST',
				url: RAMPPressForward.restBase + '/citation/',
				beforeSend( xhr ) {
					xhr.setRequestHeader(
						'X-WP-Nonce',
						RAMPPressForward.restNonce
					);
				},
				data: {
					articleId: $clicked.data( 'articleId' ),
				},
				success( response ) {
					$clicked.addClass( 'btn-info' );
				},
			} );
		} );

		$( '.ramp-send-to-events' ).on( 'click', function () {
			const $clicked = $( this );

			$clicked.attr( 'disabled', 'disabled' );

			$.ajax( {
				method: 'POST',
				url: RAMPPressForward.restBase + '/event/',
				beforeSend( xhr ) {
					xhr.setRequestHeader(
						'X-WP-Nonce',
						RAMPPressForward.restNonce
					);
				},
				data: {
					articleId: $clicked.data( 'articleId' ),
				},
				success( response ) {
					$clicked.addClass( 'btn-info' );
				},
			} );
		} );

		refreshNominationStatus();

		$( '.ramp-send-to-citation-library' ).tooltip( {
			placement: 'top',
			trigger: 'hover',
			title: 'Send to Citation Library',
		} );
		$( '.ramp-send-to-events' ).tooltip( {
			placement: 'top',
			trigger: 'hover',
			title: 'Send to Events',
		} );
	} );

	var refreshNominationStatus = function () {
		itemIds = [];
		$( 'article.feed-item' ).each( function ( k, v ) {
			itemIds.push( v.id );
		} );

		if ( itemIds.length > 0 ) {
			$.ajax( {
				url: RAMPPressForward.restBase + '/nomination-status/',
				method: 'POST',
				beforeSend( xhr ) {
					xhr.setRequestHeader(
						'X-WP-Nonce',
						RAMPPressForward.restNonce
					);
				},
				data: {
					itemIds,
				},
				success( response ) {
					for ( var itemId in response.has_citation ) {
						$( 'article#' + itemId )
							.find( '.ramp-send-to-citation-library' )
							.addClass( 'btn-info' )
							.attr( 'disabled', 'disabled' );
					}
					for ( var itemId in response.has_event ) {
						$( 'article#' + itemId )
							.find( '.ramp-send-to-events' )
							.addClass( 'btn-info' )
							.attr( 'disabled', 'disabled' );
					}
				},
			} );
		}
	};
} )( jQuery );
