(function($){
	$('#people-login-form').on('change', '#LoginForm_1_dataType', function(e) {
		var desc = $(this).children('option:selected').text();
		var label =  desc + ' <span class="required">*</span>';
		$('#LoginForm_1_data')
			.parent()
			.prev('label').html(label);
	});
	$('#legal-login-form').on('change', '#LoginForm_2_dataType', function(e) {
		var desc = $(this).children('option:selected').text();
		var label =  desc + ' <span class="required">*</span>';
		$('#LoginForm_2_data')
			.parent()
			.prev('label').html(label);
	});

	// borrar datos del formulario al cerrar ventana de login
	$('#peopleLoginModal, #legalLoginModal').on('hidden.bs.modal', function(){
		$('#people-login-form').get(0).reset();
		$('#legal-login-form').get(0).reset();
	})
})(jQuery);