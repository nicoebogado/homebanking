(function ($) {

	$('.frequentAccounts').hide();

	// campo de isThird
	$('form#transferForm').on('change', '#TransferForm_isThird', function (e) {
		var context = 'form#transferForm';
		var thirdCreditAccount_E = $('#TransferForm_thirdCreditAccount', context).parent();
		var thirdDocType_E = $('#TransferForm_thirdDocType', context).parent();
		var thirdDocNumber_E = $('#TransferForm_thirdDocNumber', context).parent();
		var creditAccount_E = $('#TransferForm_creditAccount', context).parent();
		var hasAgreementGroup = $('#TransferForm_hasAgreement', context).parent();
		var exchContract = $('#TransferForm_exchangeContract, #TransferForm_creditQuotation', context).parent();
		var saveFrequentGroup = $('#TransferForm_saveFrequent', context).parent();
		var beneficiaryNameGroup = $('#TransferForm_beneficiaryName', context).parent();

		if ($(this).val() == 1) {
			thirdCreditAccount_E.removeClass('hidden').children('input').val('');
			thirdDocType_E.removeClass('hidden').children('input').val('');
			thirdDocNumber_E.removeClass('hidden').children('input').val('');
			hasAgreementGroup.removeClass('hidden');
			creditAccount_E.addClass('hidden').children('select').val(0);
			$('.frequentAccounts').show();
			hasAgreementGroup.removeClass('hidden');
			saveFrequentGroup.removeClass('hidden');
			beneficiaryNameGroup.removeClass('hidden');
			$(".wizard > .content").animate({ height: height }, 'slow');
		} else {
			$('.frequentAccounts').hide();
			thirdCreditAccount_E.addClass('hidden').children('input').val('');
			thirdDocType_E.addClass('hidden').children('input').val('');
			thirdDocNumber_E.addClass('hidden').children('input').val('');
			hasAgreementGroup.addClass('hidden').children('select').val(0);
			exchContract.addClass('hidden');
			saveFrequentGroup.addClass('hidden');
			beneficiaryNameGroup.addClass('hidden');
			creditAccount_E.removeClass('hidden');
		}
	});

	$('form#transferForm').on('click', '.frequentAccounts table tbody tr td', function (e) {

		if (typeof ($(this).find('a').html()) != "undefined") {
			return;
		}

		var account = $(this).parent().find('td > input').prop('checked', true).val();
		account = JSON.parse(CryptoJSAesDecrypt(suid, account));

		$('#TransferForm_thirdCreditAccount').val(account.cuentabeneficiario);
		$('#TransferForm_beneficiaryName').val(account.nombrebeneficiario);
		$('#TransferForm_thirdDocType').val(account.tipodocbeneficiario);
		$('#TransferForm_thirdDocNumber').val(account.numerodocbeneficario);
		$('tr.creditAccountDetailView>td', 'form#transferForm')
			.text(account.cuentabeneficiario + ' - ' + account.nombrebeneficiario);

	});

	// definir si se debe mostrar los campos de contrato de cambio
	$('form#transferForm').on('change', '#TransferForm_debitAccount, #TransferForm_creditAccount', function (e) {
		var context = 'form#transferForm';
		var debitInp = $('#TransferForm_debitAccount', context);
		var creditInp = $('#TransferForm_creditAccount', context);

		// Si uno de los selects no tiene valor evitar el proceso
		if (debitInp.val() === '' || creditInp.val() === '') {
			return false;
		}

		// recuperar las monedas a partir de los labels de los selects
		var debitCurrency = debitInp.children('option:selected').text().split(' ').pop();
		var creditCurrency = creditInp.children('option:selected').text().split(' ').pop();
		var hasAgreementGroup = $('#TransferForm_hasAgreement', context).parent();

		if (debitCurrency !== creditCurrency) {
			hasAgreementGroup.removeClass('hidden');
		} else {
			hasAgreementGroup.addClass('hidden');
		}
	});

	$('form#transferForm').on('change', '#TransferForm_hasAgreement', function (e) {
		var exchContract = $('#TransferForm_exchangeContract, #TransferForm_creditQuotation', 'form#transferForm').parent();
		if ($(this).val() == 1) {
			exchContract.removeClass('hidden');
		} else {
			exchContract.addClass('hidden');
		}
	});

	// completar el ultimo step del wizard con datos cargados por el usuario
	$('form#transferForm').on('change', '#TransferForm_debitAccount', function (e) {
		var selectedOption = $(this).find('option:selected'),
			optionText = selectedOption.text(),
			arrayText = optionText.split(' '),
			optionMoney = arrayText[arrayText.length - 2];

		// optionMoney tiene la moneda de la cuenta seleccionada
		// por ejemplo, del texto "1401881 ENRIQUE ALCIDES KOPANSKY MALDONADO - USD 67,26"
		// extrae el subtexto "USD"

		$('tr.debitAccountDetailView>td', 'form#transferForm').text(optionText);

		// aplicar máscara a la entrada de monto
		// si la moneda es GS, no se usará valores decimales
		$('#TransferForm_amount', 'form#transferForm')
			.mask((optionMoney == 'GS' ? '#.##0' : '#.##0,00'), { reverse: true });

		// ocultar la cuenta seleccionada entre la lista de destinos
		$('#TransferForm_creditAccount')
			.val('')
			.children('option')
			.show()
			.filter('option[value=' + selectedOption.val() + ']')
			.hide();
	});
	$('form#transferForm').on('change', '#TransferForm_creditAccount', function (e) {
		$('tr.creditAccountDetailView>td', 'form#transferForm').text($(this).find('option:selected').text());
	});

	$('form#transferForm').on('change',
		'#TransferForm_thirdCreditAccount, #TransferForm_thirdDocType, #TransferForm_thirdDocNumber',
		function (e) {
			var context = 'form#transferForm';
			var thirdCreditAccount_E = $('#TransferForm_thirdCreditAccount', context);
			var thirdDocType_E = $('#TransferForm_thirdDocType', context);
			var thirdDocNumber_E = $('#TransferForm_thirdDocNumber', context);
			getAccountDesc(thirdCreditAccount_E.val(), thirdDocType_E.val(), thirdDocNumber_E.val());
		}
	);

	$('form#transferForm').on('change', '#TransferForm_amount', function (e) {
		$('tr.amountDetailView>td', 'form#transferForm').text($(this).val());
	});

	$('form#transferForm').on('click', '.deleteAccount', function (e) {
		e.preventDefault();
		var formId = $(this).attr('id');
		var url = $(this).attr('data-url');
		deleteFrequent(formId, url);
	});

})(jQuery);

function getAccountDesc(accountNumber, documentType, documentNumber) {
	if (accountNumber && documentType && documentNumber) {
		$.ajax({
			url: URL,
			type: 'POST',
			dataType: 'html',
			data: {
				'YII_CSRF_TOKEN': csrfToken,
				accountNumber: CryptoJSAesEncrypt(suid, accountNumber),
				documentType: documentType,
				documentNumber: CryptoJSAesEncrypt(suid, documentNumber)
			}
		})
			.done(function (data) {
				if (data.length > 2) {
					$('#TransferForm_beneficiaryName').val(data);

					var text = $('#TransferForm_thirdCreditAccount').val() + ' - ' + data;
					$('tr.creditAccountDetailView>td', 'form#transferForm').text(text);
				}
			})
			.fail(function (jqXHR, textStatus, errorThrown) {
				var msg = 'No se encontraron datos del destinatario';
				$('#TransferForm_beneficiaryName').val(msg);

				$('tr.creditAccountDetailView>td', 'form#transferForm').text(msg);
			});
	}
}

function deleteFrequent(formId, url) {
	bootbox.confirm("Está seguro que desea borrar la Transferencia Frecuente?", function (result) {
		if (result == true) {
			$.ajax({
				url: url,
				type: 'POST',
				dataType: 'html',
				data: {
					'YII_CSRF_TOKEN': csrfToken,
					id: formId
				}
			}).done(function (data) {
				var message = data.split("*");
				if (message.length > 0) {
					$("#" + message[2]).parent().parent().remove();
					bootbox.alert(message[1]);
				} else {
					bootbox.alert(data);
				}
			});
		}
	});
}
