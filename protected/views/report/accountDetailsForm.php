<?php
$this->pageTitle=Yii::app()->name . ' - ' . Yii::t('accountDetails', 'Detalles de Cuentas');
?>

<h3>
	<?php echo Yii::t('accountDetails', 'Detalles de Cuentas') ?>
</h3>

<?php echo $this->renderPartial('/commons/_form', array('form'=>$form)); ?>
