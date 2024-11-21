(function($){

    $(document).ready(function() {

        // mascara para monto, por defecto para guaraníes
        $('#TransferForm_amount').mask('#.##0', {reverse: true});

        // mascara para fecha
        $('#TransferForm_date').mask('00/00/0000');

        $('.frequentAccounts tbody tr').css('cursor','pointer');

    	// definir si se debe mostrar los campos de contrato de cambio
    	$('form#sipapTransferForm')
        .on('change', '#TransferForm_debitAccount, #TransferForm_currency', function(e) {

            var context = 'form#sipapTransferForm';
    		var creditInp = $('#TransferForm_creditAccount', context);

    		// recuperar las monedas a partir de los labels de los selects
            var debitInp = $('#TransferForm_debitAccount');
    		var debitCurrency = debitInp.children('option:selected').text().split(' ');
            debitCurrency = debitCurrency[debitCurrency.length-2];// el penúltimo elemento es la moneda de la cuenta
    		var creditCurrency = $("#TransferForm_currency").val();
    		var hasAgreementGroup = $('#TransferForm_hasAgreement', context).parent();
            var exchangeContract = $('#TransferForm_exchangeContract', context).parent();
            var creditQuotation = $('#TransferForm_creditQuotation', context).parent();

            // cambiar mascara para monto según la moneda seleccionada
            $('#TransferForm_amount').mask((creditCurrency == 'GS' ? '#.##0' : '#.##0,00'), {reverse: true});
            
            $('#TransferForm_hasAgreement', context).val(0);
            exchangeContract.addClass('hidden');

    		if (debitCurrency !== creditCurrency) {
    			hasAgreementGroup.removeClass('hidden');
                // $(".wizard > .content").animate({height: 500}, 'slow');
            } else {
    		  hasAgreementGroup.addClass('hidden');
              creditQuotation.addClass('hidden');
              // $(".wizard > .content").animate({height: 500}, 'slow');
    		}
    	})
        .on('change', '#TransferForm_hasAgreement', function(e) {
            var context = 'form#sipapTransferForm';
            var exchangeContract = $('#TransferForm_exchangeContract', context).parent();
            var creditQuotation = $('#TransferForm_creditQuotation', context).parent();

        	if ($(this).val() == 1) {
        	    exchangeContract.removeClass('hidden');
              creditQuotation.removeClass('hidden');
          	} else {
        	    exchangeContract.addClass('hidden');
              creditQuotation.addClass('hidden');
          	}
    	})
    	// completar el ultimo step del wizard con datos cargados por el usuario
        .on('change', '#TransferForm_debitAccount', function(e) {
    		$('tr.debitAccountDetailView>td', 'form#sipapTransferForm').text($(this).find('option:selected').text());
    	})
        .on('change', '#TransferForm_creditAccount,#TransferForm_name,#TransferForm_currency', function(e) {
    		$('tr.creditAccountDetailView>td', 'form#sipapTransferForm').text($('#TransferForm_creditAccount').val()+' '+$('#TransferForm_name').val().toUpperCase()+' - '+$('#TransferForm_currency').val());
    	})
        .on('change', '#TransferForm_amount', function(e) {
    		$('tr.amountDetailView>td', 'form#sipapTransferForm').text($(this).val());
    	})
        .on('change', '#TransferForm_date', function(e) {
    		$('tr.dateDetailView>td', 'form#sipapTransferForm').text($(this).val());
    	})
        .on('changeDate', '#TransferForm_date', function(){
            $('tr.dateDetailView>td', 'form#sipapTransferForm').text($(this).val());
        })
        .on('change', '#TransferForm_concept', function(e) {
    		$('tr.conceptDetailView>td', 'form#sipapTransferForm').text($(this).val());
    	})
        .on('change', '#TransferForm_documentData', function(e) {
    		$('tr.documentDataDetailView>td', 'form#sipapTransferForm').text($('#TransferForm_documentType option:selected').text()+' '+$(this).val());
    	})
        .on('change', '#TransferForm_financialEntity', function(e) {
    		$('tr.financialEntityDetailView>td', 'form#sipapTransferForm').text($('#TransferForm_financialEntity option:selected').text());
    	})
        .on('click','.frequentAccounts tbody tr td', function(e){

    		if (typeof($(this).find('a').html()) != "undefined"){
    			return;
    		}

    		var account = $(this).parent().find('td > input').prop('checked', true).val();
            account = JSON.parse(CryptoJSAesDecrypt(suid, account));

    		$('#TransferForm_creditAccount').val(account.cuentabeneficiario);
    		$('#TransferForm_documentType').val($(this).parent().find('td').eq(2).html());
    		$('#TransferForm_documentData').val($(this).parent().find('td').eq(3).html());
    		$('#TransferForm_name').val($(this).parent().find('td').eq(4).html());
    		$('#TransferForm_address').val($(this).parent().find('td').eq(5).html());

    		var banco=$(this).parent().find('td').eq(6).html();
    		banco=banco.split('-');
    		$('#TransferForm_financialEntity').val(banco[0]);
            $('tr.financialEntityDetailView>td', 'form#sipapTransferForm').text(banco[1]);

    		$('tr.creditAccountDetailView>td', 'form#sipapTransferForm').text($('#TransferForm_creditAccount').val()+' '+$('#TransferForm_name').val().toUpperCase()+' - '+$('#TransferForm_currency').val());
    		$('tr.documentDataDetailView>td', 'form#sipapTransferForm').text($('#TransferForm_documentType option:selected').text()+' '+$('#TransferForm_documentData').val());

    	})
        .on('click','.deleteAccount', function(e){
    		  e.preventDefault();
    			var formId=$(this).attr('id');
    			var url=$(this).attr('data-url');
    			deleteFrequent(formId,url);
    	});

    });

    function deleteFrequent(formId,url) {
        bootbox.confirm("Está seguro que desea borrar la Transferencia Frecuente?", function(result) {
          if(result==true){
                $.ajax({
                    url: url,
                    type: 'POST',
                    dataType: 'html',
                    data: {
                        'YII_CSRF_TOKEN': csrfToken,
                        id : formId
                    }
                }).done(function(data) {
                    var message = data.split("*");
                    if(message.length>0){
                        $("#"+message[2]).parent().parent().remove();
                        bootbox.alert(message[1]);
                    }else{
                        bootbox.alert(data);
                    }
                });
            }
        });
    }

})(jQuery);
