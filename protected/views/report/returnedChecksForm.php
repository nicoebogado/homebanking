<?php
$this->pageTitle=Yii::app()->name . ' - '. Yii::t('returnedChecks', 'Cheques Devueltos');
?>

<h3><?php echo Yii::t('returnedChecks', 'Cheques Devueltos') ?></h3>

<?php echo $this->renderPartial('/commons/_form', array('form'=>$form)); ?>