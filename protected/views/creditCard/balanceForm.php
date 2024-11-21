<?php
$this->pageTitle=Yii::app()->name . ' - '. Yii::t('creditCardBalance', 'Extracto de Tarjetas de Crédito');
?>

<h3><?php echo Yii::t('creditCardBalance', 'Extracto de Tarjetas de Crédito') ?></h3>

<div class="form">
	<?php echo $this->renderPartial('/commons/_form', array('form'=>$form));?>
</div>