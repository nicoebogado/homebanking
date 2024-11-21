<?php
HScript::register([
	'sipapTransfers/form.min',
	'plugins/jquery.mask.min'
]);
if($tipo==='C'){
	$this->pageTitle=Yii::app()->name . ' - ' . Yii::t('transfers', 'Transferencia interbancaria SIPAP');
}else{
	$this->pageTitle=Yii::app()->name . ' - ' . Yii::t('transfers', 'Transferencia interbancaria SIPAP programada (ACH)');
}
?>
<h3>
	<?php
		if($tipo==='C'){
			echo Yii::t('transfers', 'Transferencia interbancaria SIPAP');
		}else{
			echo Yii::t('transfers', 'Transferencia interbancaria SIPAP programada (ACH)');
		}
	?>
</h3>

<p style="background:#FFFFFF;padding:15px;border:1px solid #CCC">
	<?php echo Yii::t('sipapTransfer','Importante: Los pagos son procesados a fecha de acreditación y deberá contar con disponibilidad de fondos para ser procesados. Las solicitudes con fechas de días feriados y/o fines de semana serán procesadas al día hábil siguiente.'); ?></p>
<?php echo $this->renderPartial('/commons/_wizardForm', array(
	'form'=>$form,
	'wizardOptions'=>$wizardOptions,
	'frequentAccounts'=>$frequentAccounts,
	'entities'=>$entities,
    'isSipap' => true,
	));
?>
