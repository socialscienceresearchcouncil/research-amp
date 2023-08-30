( function ( $ ) {
	$( document ).ready( function () {
		const $selector = $( '#associated-user' );

		$selector.select2( {
			data: RAMPProfileUsers.users,
			placeholder: 'Select a user',
			allowClear: true,
		} );

		if ( RAMPProfileUsers.selectedUserId ) {
			$selector.val( RAMPProfileUsers.selectedUserId );
			$selector.trigger( 'change' );
		}
	} );
} )( jQuery );
