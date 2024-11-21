<?php
HScript::register('loans/personalLoan');
$this->pageTitle=Yii::app()->name . ' - ' . Yii::t('loans', 'Solicitud de Préstamo Personal');
?>
<h3>
	<?php echo Yii::t('loans', 'Solicitud de Préstamo Personal'); ?>
</h3>
<?php echo $this->renderPartial('_wizardForm', array(
	'form'=>$form,
	'wizardOptions'=>$wizardOptions,
));?>

<script type="text/javascript">
	jQuery(document).ready(function($) {
		$("#PersonalLoanForm_beneficiaryDocType").val(<?php echo Yii::app()->user->getState('clientArea')['tipodocumento'] ?>);
		$('#PersonalLoanForm_beneficiaryDocType').attr('disabled', 'disabled');
		$("#PersonalLoanForm").submit(function(e){
			e.preventDefault();
			$('#PersonalLoanForm_beneficiaryDocType').removeAttr('disabled');
			this.submit();
		});
	});
</script>
