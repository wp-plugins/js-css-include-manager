jQuery(document).ready(function($) {

	$('.jcim #postbox-container-1 .toggle-width').on('click', function( ev ) {
		
		var Action = 'jcim_donation_toggle';
		$('.jcim').toggleClass('full-width');

		if( $('.jcim').hasClass('full-width') ) {
			$.post(ajaxurl, {
				'action': Action,
				'f': 1,
			});
		} else {
			$.post(ajaxurl, {
				'action': Action,
				'f': 0,
			});
		}
		
		return false;

	});

});
