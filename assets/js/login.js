(function($){
	$( document ).ready(
		function(){
			var $passmail = $( '#reg_passmail' );
			$passmail.insertBefore( '#nav' );

			$( '#user_email' ).after( '<p class="description">Will not be made public.</p>' );
		}
	);
}(jQuery));
