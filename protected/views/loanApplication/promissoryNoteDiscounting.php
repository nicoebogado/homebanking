<?php
HScript::register('loans/promissoryNoteDiscounting');
$this->pageTitle=Yii::app()->name . ' - ' . Yii::t('loans', 'Solicitud de Descuento de Pagarés');
?>
<h3>
	<?php echo Yii::t('loans', 'Solicitud de Descuento de Pagarés'); ?>
</h3>
<?php echo $this->renderPartial('_wizardForm', array(
	'form'=>$form,
	'wizardOptions'=>$wizardOptions,
));?>

<script type="text/javascript">
	jQuery(document).ready(function($) {
		$("#PromissoryNoteDiscountingForm_beneficiaryDocType").val(<?php echo Yii::app()->user->getState('clientArea')['tipodocumento'] ?>);
		$('#PromissoryNoteDiscountingForm_beneficiaryDocType').attr('disabled', 'disabled');
		$("#PromissoryNoteDiscountingForm").submit(function(e){
			e.preventDefault();
			$('#PromissoryNoteDiscountingForm_beneficiaryDocType').removeAttr('disabled');
			this.submit();
		});
	});
</script>
