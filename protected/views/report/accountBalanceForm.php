<?php
$this->pageTitle=Yii::app()->name . ' - '. Yii::t('accountBalance', 'Histórico de Saldos Diarios');
?>

<h3><?php echo Yii::t('accountBalance', 'Histórico de Saldos Diarios') ?></h3>

<div class="form">
	<?php echo $this->renderPartial('/commons/_form', array('form'=>$form));?>
</div>