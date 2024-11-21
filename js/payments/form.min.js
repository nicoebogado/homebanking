(function ($) {
	$('form#salary-payment-form').on('change', '#SalaryPaymentForm_controlMode', function(e) {
		var form = $('form#salary-payment-form');
		var select = $('#SalaryPaymentForm_controlMode', form);
		// ocultar/mostrar campo para carga de archivo
		$('#SalaryPaymentForm_paymentFile', form).parent().toggle();
		// cambiar action del form
		form.attr('action', select.val() == 'L' ? 'salariesManualLoading' : 'salariesVerification');
	});
})(jQuery);