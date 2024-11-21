<?php
$this->pageTitle=Yii::app()->name . ' - '. Yii::t('commons', 'Extracto de Cuentas');
?>

<h3><?php echo Yii::t('commons', 'Extracto de Cuentas') ?></h3>

<div class="form">
	<?php echo $this->renderPartial('/commons/_form', array('form'=>$form));?>
</div>