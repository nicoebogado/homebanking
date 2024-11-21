(function($) {
    $(document).ready(function() {
        var form = $('#creditCardPaymentForm'),
            amountInput = $('#CreditCardPaymentForm_amount', form),
            amountInputGroup = amountInput.closest('div.form-group');

        amountInputGroup.hide();
        
        $(form).on('change', 'input[type=radio][name="amountOption"]', function () {
            // si se selecciona "Otro monto"
            if ($(this).val() == 'om') {
                amountInput.val(0);
                amountInputGroup.show();
            } else {
                amountInput.val($(this).val());
                amountInputGroup.hide();
            }
        });
    });
}) (jQuery);
