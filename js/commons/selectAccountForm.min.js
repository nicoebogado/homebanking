(function($) {
	$('form#select-account-form').on('submit', function(e) {
		var newAction = $(this).attr('action');

		// asegurar que el atributo action termina en /
		if(newAction.substr(-1, 1) != '/') {newAction += '/';}

		newAction += $(this).find('select').val();
		$(this).attr('action', newAction);
	});
})
(jQuery);