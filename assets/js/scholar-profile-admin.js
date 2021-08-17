(function($){
	$(document).ready(function(){
		var $selector = $('#associated-user')

		$selector.select2({
			data: RAMPScholarProfileUsers.users,
			placeholder: 'Select a user',
			allowClear: true
		});

		if ( RAMPScholarProfileUsers.selectedUserId ) {
			$selector.val( RAMPScholarProfileUsers.selectedUserId );
			$selector.trigger( 'change' );
		}
	});
}(jQuery));
