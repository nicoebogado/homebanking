<?php
HScript::register('loans/checkDiscounting');
$this->pageTitle=Yii::app()->name . ' - ' . Yii::t('loans', 'Solicitud de Descuento de Cheques');
?>
<h3>
	<?php echo Yii::t('loans', 'Solicitud de Descuento de Cheques'); ?>
</h3>
<?php echo $this->renderPartial('_wizardForm', array(
	'form'=>$form,
	'wizardOptions'=>$wizardOptions,
));?>

<script type="text/javascript">
	jQuery(document).ready(function($) {
		$("#CheckDiscountingForm_beneficiaryDocType").val(<?php echo Yii::app()->user->getState('clientArea')['tipodocumento'] ?>);
		$('#CheckDiscountingForm_beneficiaryDocType').attr('disabled', 'disabled');
		$("#CheckDiscountingForm").submit(function(e){
			e.preventDefault();
			$('#CheckDiscountingForm_beneficiaryDocType').removeAttr('disabled');
			this.submit();
		});
	});
</script>
