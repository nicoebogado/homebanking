<?php
$this->pageTitle=Yii::app()->name . ' - ' . Yii::t('transfers', 'Transferencia Alto Valor (LBTR)');
?>

<h3>
	<?php echo Yii::t('transfers', 'Transferencia Alto Valor (LBTR)'); ?>
</h3>

<?php echo $this->renderPartial('/commons/_form', array('form'=>$form));?>