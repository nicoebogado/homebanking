(function($){

	/*$('tr.beneficiaryNameDetailView>td').text($('#PersonalLoanForm_beneficiaryName').val());
	$('tr.beneficiaryDocumentDetailView>td').text($('#PersonalLoanForm_beneficiaryDocument').val());
	$('tr.branchOfficeDetailView>td').text($('#PersonalLoanForm_branchOffice option:selected').text());

	$('form#PersonalLoanForm').on('change', '#PersonalLoanForm_beneficiaryDocument', function(e) {
		$('tr.beneficiaryDocumentDetailView>td', 'form#PersonalLoanForm').text($(this).val());
	});

	$('form#PersonalLoanForm').on('change', '#PersonalLoanForm_timeLimit', function(e) {
		$('tr.timeLimitDetailView>td', 'form#PersonalLoanForm').text($(this).val());
	});

	$('form#PersonalLoanForm').on('change', '#PersonalLoanForm_amount', function(e) {
		$('tr.amountDetailView>td', 'form#PersonalLoanForm').text($(this).val());
	});

	$('form#PersonalLoanForm').on('change', '#PersonalLoanForm_branchOffice', function(e) {
		$('tr.branchOfficeDetailView>td', 'form#PersonalLoanForm').text($(this).val());
	});*/

})(jQuery);

function updateDetails(){
	$('tr.beneficiaryNameDetailView>td').text($('#PersonalLoanForm_beneficiaryName').val());
	$('tr.beneficiaryDocumentDetailView>td').text($('#PersonalLoanForm_beneficiaryDocument').val());
	$('tr.branchOfficeDetailView>td').text($('#PersonalLoanForm_branchOffice option:selected').text());
	$('tr.timeLimitDetailView>td').text($('#PersonalLoanForm_timeLimit').val());
	$('tr.amountDetailView>td').text($('#PersonalLoanForm_amount').val());
}
