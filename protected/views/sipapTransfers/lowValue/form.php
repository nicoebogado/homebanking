<?php
$this->pageTitle=Yii::app()->name . ' - ' . Yii::t('transfers', 'Transferencia en Compensación (ACH)');
?>

<h3>
	<?php echo Yii::t('transfers', 'Transferencia en Compensación (ACH)'); ?>
</h3>

<?php echo $this->renderPartial('/commons/_form', array('form'=>$form));?>