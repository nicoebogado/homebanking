<?php
$this->pageTitle=Yii::app()->name . ' - '. Yii::t('extractOnRequest', 'Extracto a Requerimiento');
?>

<h3><?php echo Yii::t('extractOnRequest', 'Extracto a Requerimiento') ?></h3>

<div class="form">
	<?php echo $this->renderPartial('/commons/_form', array('form'=>$form));?>
</div>