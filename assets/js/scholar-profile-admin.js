(function($){
	$(document).ready(function(){
		var $selector = $('#associated-user')

		$selector.select2({
			data: DisinfoScholarProfileUsers.users,
			placeholder: 'Select a user',
			allowClear: true
		});

		if ( DisinfoScholarProfileUsers.selectedUserId ) {
			$selector.val( DisinfoScholarProfileUsers.selectedUserId );
			$selector.trigger( 'change' );
		}
	});
}(jQuery));
