(function($){
	$(document).ready(function(){
		$('.pf-btns').each(function(k, v){
			var $btns = $(v);
			var articleId = $btns.closest('article').attr('id');

			var citationButton = '<button class="btn btn-small schema-switchable disinfo-send-to-citation-library" data-article-id="' + articleId + '" data-original-title="Send to Citation Library"><i class="icon-folder-open"></i></button>';
			$btns.append( citationButton );

			var eventsButton = '<button class="btn btn-small schema-switchable disinfo-send-to-events" data-article-id="' + articleId + '" data-original-title="Send to Events"><i class="icon-calendar"></i></button>';
			$btns.append( eventsButton );
		});

		$('.disinfo-send-to-citation-library').on('click',function(){
			var $clicked = $(this);

			$clicked.attr('disabled','disabled');

			$.ajax( {
				method: 'POST',
				url: DisinfoPressForward.restBase + '/citation/',
				beforeSend: function ( xhr ) {
					xhr.setRequestHeader( 'X-WP-Nonce', DisinfoPressForward.restNonce );
				},
				data: {
					articleId: $clicked.data('articleId')
				},
				success: function(response) {
					$clicked.addClass('btn-info');
				}
			} );
		});

		$('.disinfo-send-to-events').on('click',function(){
			var $clicked = $(this);

			$clicked.attr('disabled','disabled');

			$.ajax( {
				method: 'POST',
				url: DisinfoPressForward.restBase + '/event/',
				beforeSend: function ( xhr ) {
					xhr.setRequestHeader( 'X-WP-Nonce', DisinfoPressForward.restNonce );
				},
				data: {
					articleId: $clicked.data('articleId')
				},
				success: function(response) {
					$clicked.addClass('btn-info');
				}
			} );
		});

		refreshNominationStatus();

		$('.disinfo-send-to-citation-library').tooltip({
			placement: 'top',
			trigger: 'hover',
			title: 'Send to Citation Library'
		});
		$('.disinfo-send-to-events').tooltip({
			placement: 'top',
			trigger: 'hover',
			title: 'Send to Events'
		});
	});

	var refreshNominationStatus = function() {
		itemIds = [];
		$('article.feed-item').each(function(k,v){
			itemIds.push(v.id);
		});

		if ( itemIds.length > 0 ) {
			$.ajax( {
				url: DisinfoPressForward.restBase + '/nomination-status/',
				method: 'POST',
				beforeSend: function ( xhr ) {
					xhr.setRequestHeader( 'X-WP-Nonce', DisinfoPressForward.restNonce );
				},
				data: {
					itemIds: itemIds
				},
				success: function(response) {
					for ( var itemId in response.has_citation ) {
						$('article#' + itemId)
							.find('.disinfo-send-to-citation-library')
							.addClass('btn-info')
							.attr('disabled', 'disabled');
					}
					for ( var itemId in response.has_event ) {
						$('article#' + itemId)
							.find('.disinfo-send-to-events')
							.addClass('btn-info')
							.attr('disabled', 'disabled');
					}
				}
			} );
		}
	}
}(jQuery));
